<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 'id_solicitud';
    
    // Como tu tabla no usa 'created_at' y 'updated_at' estándar de Laravel
    public $timestamps = false;

    protected $fillable = [
        'id_usuario', 
        'id_cdt', 
        'id_dept', 
        'id_cargo', 
        'id_beneficio', 
        'id_estatusSol', 
        'descripcion', 
        'monto',
        'fecha_solicitud'
    ];

    /**
     * Relación con el Beneficio
     * REPARACIÓN: Especificamos la llave local y la llave foránea exacta de la tabla beneficio
     */
    public function beneficio()
    {
        // En tu DB, la tabla 'beneficio' usa 'id_beneficio' como PK
        return $this->belongsTo(Beneficio::class, 'id_beneficio', 'id_beneficio');
    }

    /**
     * Relación con el Estatus (Tabla estatus_solicitud)
     * REPARACIÓN: Asegurar que apunte a la PK correcta de la tabla de estatus
     */
    public function estatus()
    {
        // En tu DB, la tabla 'estatus_solicitud' usa 'id_estatusSol' como PK
        return $this->belongsTo(Estatus::class, 'id_estatusSol', 'id_estatusSol');
    }

    /**
     * Relación con el Usuario (Dueño de la solicitud)
     * ESTO FALTABA: Para evitar el error de relación no encontrada
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    /**
     * Relación con CDT (Centro de Trabajo)
     */
    public function cdt()
    {
        return $this->belongsTo(Cdt::class, 'id_cdt', 'id_cdt');
    }
}