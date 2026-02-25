<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    // Nombre exacto de tu tabla según la imagen
    protected $table = 'estatus_solicitud'; 
    
    // Llave primaria exacta según tu captura
    protected $primaryKey = 'id_estatusSol'; 
    
    // IMPORTANTE: Laravel asume que la PK es un entero autoincremental.
    // Lo aseguramos para que las relaciones no devuelvan nulo.
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    // Campos que permite llenar
    protected $fillable = [
        'descripcion', 
        'nivel_acceso', 
        'activo'
    ];

    /**
     * Relación inversa: Un estatus pertenece a muchas solicitudes
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_estatusSol', 'id_estatusSol');
    }
}