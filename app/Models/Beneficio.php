<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficio extends Model
{
    // Nombre exacto de tu tabla en phpMyAdmin
    protected $table = 'beneficio';
    
    // Tu llave primaria exacta
    protected $primaryKey = 'id_beneficio';
    
    // Si tu ID no es un número autoincremental, cámbialo a false (usualmente es true)
    public $incrementing = true;

    // Como tu tabla no tiene las columnas created_at y updated_at
    public $timestamps = false;

    /**
     * Reparación de Fillable:
     * Asegúrate de incluir 'nombre_beneficio' que es lo que usas en el Dashboard
     */
    protected $fillable = [
        'nombre_beneficio', // Agregado para que coincida con la vista
        'descripcion', 
        'tipo', 
        'activo'
    ];

    /**
     * Relación inversa (Opcional pero recomendada)
     * Un beneficio puede tener muchas solicitudes
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_beneficio', 'id_beneficio');
    }
}