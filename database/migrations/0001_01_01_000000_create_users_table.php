<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiamos 'users' por 'usuario'
        Schema::create('usuario', function (Blueprint $table) {
            $table->id(); 
            $table->string('usuario')->unique(); // Para tu cédula 19217182
            $table->string('password');
            $table->integer('id_empleado')->nullable();
            $table->integer('id_rol')->nullable();
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            // Quitamos timestamps() porque en tu modelo pusiste public $timestamps = false;
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
