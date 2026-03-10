<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Indicamos que la tabla se llama 'usuario'
    protected $table = 'usuario';

    // Campos que permitimos llenar (Agregamos id_empleado para evitar el error anterior)
    protected $fillable = [
        'usuario',
        'password',
        'nivel_acceso',
        'id_empleado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false; // Por si tu tabla no tiene created_at/updated_at
}