<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleado';
    
    // Tu tabla usa 'codigo' como llave primaria en lugar de 'id'
    protected $primaryKey = 'codigo';
    
    public $timestamps = false;

    protected $fillable = [
        'cedula', 'nombre1', 'nombre2', 'apellido1', 'apellido2',
        'telefono', 'fecha_nacimiento', 'direccion', 'correo',
        'fecha_ingreso', 'cuenta_bancaria', 'id_estado', 'id_municipio',
        'id_parroquia', 'id_cdt', 'id_cargo', 'id_dept', 'id_supervisor',
        'id_estatus_empleado'
    ];

    // Esto nos ayudará a mostrar el nombre completo en el formulario
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre1} {$this->apellido1}";
    }
}