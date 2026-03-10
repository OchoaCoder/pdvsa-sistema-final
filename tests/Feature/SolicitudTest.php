<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Solicitud;
use App\Models\Empleado; 
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class SolicitudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Schema::dropIfExists('solicitud');
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('empleado');
        Schema::dropIfExists('beneficio');
        Schema::dropIfExists('cdt');
        Schema::dropIfExists('estatus_solicitud');

        // TABLA USUARIO CORREGIDA
        Schema::create('usuario', function ($table) {
            $table->id();
            $table->string('usuario');
            $table->string('password');
            $table->integer('id_rol')->default(1); // CAMBIADO id_nivel por id_rol
            $table->string('id_empleado')->nullable(); 
            $table->integer('activo')->default(1);    // AGREGADO campo activo
            $table->timestamps();
        });

        Schema::create('beneficio', function ($table) {
            $table->id('id_beneficio');
            $table->string('nombre_beneficio');
            $table->timestamps();
        });

        Schema::create('cdt', function ($table) {
            $table->id('id_cdt');
            $table->string('nombre_cdt');
            $table->timestamps();
        });

        Schema::create('estatus_solicitud', function ($table) {
            $table->id('id_estatusSol');
            $table->string('nombre_estatus');
            $table->timestamps();
        });

        Schema::create('solicitud', function ($table) {
            $table->id('id_solicitud'); 
            $table->integer('id_usuario');
            $table->integer('id_cdt');
            $table->integer('id_beneficio');
            $table->integer('id_estatusSol')->default(1);
            $table->text('descripcion');
            $table->dateTime('fecha_solicitud');
            $table->integer('id_dept')->default(1);
            $table->integer('id_cargo')->default(1);
            $table->decimal('monto', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('empleado', function ($table) {
            $table->string('codigo')->primary();
            $table->integer('id_dept')->default(1);
            $table->integer('id_cargo')->default(1);
            $table->timestamps();
        });
    }

    #[Test]
    public function test_login_falla_con_contrasena_incorrecta()
    {
        $response = $this->post('/login', [
            'usuario'  => 'empleado01',
            'password' => 'claveIncorrecta',
        ]);
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function test_solicitud_duplicada_es_bloqueada()
    {
        Empleado::forceCreate(['codigo' => 'EMP100', 'id_dept' => 1, 'id_cargo' => 1]);
        $usuario = User::forceCreate([
            'id' => 3, 'usuario' => 'jose_test', 'password' => bcrypt('123456'), 'id_empleado' => 'EMP100', 'id_rol' => 1, 'activo' => 1
        ]);

        Solicitud::forceCreate([
            'id_usuario' => 3, 'id_cdt' => 1, 'id_beneficio' => 1, 'id_estatusSol' => 1,
            'descripcion' => 'Original', 'fecha_solicitud' => now()
        ]);

        $response = $this->actingAs($usuario)
            ->from('/solicitudes/crear') 
            ->post('/solicitudes/guardar', [
                'id_cdt' => 1, 
                'id_beneficio' => 1, 
                'descripcion' => 'Duplicada test'
            ]);

        $response->assertStatus(302);
    }

    #[Test]
    public function test_solicitud_aprobada_no_puede_cambiar_estado()
    {
        DB::table('estatus_solicitud')->insert(['id_estatusSol' => 2, 'nombre_estatus' => 'Aprobado']);
        
        Empleado::forceCreate(['codigo' => 'EMP001', 'id_dept' => 1, 'id_cargo' => 1]);
        $admin = User::forceCreate(['id' => 1, 'usuario' => 'admin', 'password' => bcrypt('123'), 'id_empleado' => 'EMP001', 'id_rol' => 3, 'activo' => 1]);

        $solicitud = Solicitud::forceCreate([
            'id_usuario' => 3, 'id_cdt' => 1, 'id_beneficio' => 1, 'id_estatusSol' => 2,
            'descripcion' => 'Ya aprobada', 'fecha_solicitud' => now()
        ]);

        $response = $this->actingAs($admin)
            ->post("/solicitudes/actualizar-estatus/{$solicitud->id_solicitud}", [
                'id_estatusSol' => 3 
            ]);

        $this->assertDatabaseHas('solicitud', [
            'id_solicitud' => $solicitud->id_solicitud,
            'id_estatusSol' => 2,
        ]);
    }

    #[Test]
    public function test_pdf_contiene_datos_de_solicitud()
    {
        DB::table('beneficio')->insert(['id_beneficio' => 1, 'nombre_beneficio' => 'Bono']);
        DB::table('cdt')->insert(['id_cdt' => 1, 'nombre_cdt' => 'Sede']);
        DB::table('estatus_solicitud')->insert(['id_estatusSol' => 1, 'nombre_estatus' => 'Pendiente']);
        
        Empleado::forceCreate(['codigo' => 'EMP400', 'id_dept' => 1, 'id_cargo' => 1]);
        $usuario = User::forceCreate(['id' => 4, 'usuario' => 'jose_pdf', 'password' => bcrypt('123'), 'id_empleado' => 'EMP400', 'id_rol' => 1, 'activo' => 1]);

        $solicitud = Solicitud::forceCreate([
            'id_usuario' => 4, 'id_cdt' => 1, 'id_beneficio' => 1, 'id_estatusSol' => 1,
            'descripcion' => 'PDF Test Jose', 'fecha_solicitud' => now(),
        ]);

        $response = $this->actingAs($usuario)->get("/solicitudes/pdf/{$solicitud->id_solicitud}");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    public function test_dashboard_carga_en_menos_de_3_segundos()
    {
        Empleado::forceCreate(['codigo' => 'EMP001', 'id_dept' => 1, 'id_cargo' => 1]);
        $admin = User::forceCreate(['id' => 5, 'usuario' => 'admin_perf', 'password' => bcrypt('123'), 'id_empleado' => 'EMP001', 'id_rol' => 3, 'activo' => 1]);

        $inicio = microtime(true);
        $response = $this->actingAs($admin)->get('/solicitudes'); 
        $fin = microtime(true);

        $tiempoCarga = $fin - $inicio;
        $response->assertStatus(200);
        $this->assertLessThan(3.0, $tiempoCarga);
    }

    #[Test]
    public function analista_puede_crear_usuario_nuevo()
    {
        $analista = User::forceCreate([
            'usuario' => 'analista_test', 
            'password' => bcrypt('123'), 
            'id_rol' => 2, 
            'activo' => 1
        ]);

        Empleado::forceCreate(['codigo' => 'V12345678', 'id_dept' => 1, 'id_cargo' => 1]);

        $response = $this->actingAs($analista)
            ->post('/usuarios/guardar', [
                'cedula' => 'V12345678',
                'usuario' => 'nuevo_user',
                'password' => 'clave123',
                'id_rol' => 1 
            ]);

        $this->assertDatabaseHas('usuario', ['usuario' => 'nuevo_user']);
    }
}