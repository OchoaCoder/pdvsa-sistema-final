<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Obligamos a Laravel a usar tu tabla 'usuario'
    protected $table = 'usuario';

    // Llave primaria de tu tabla
    protected $primaryKey = 'id';

    // Desactivamos timestamps porque tu tabla no tiene created_at/updated_at
    public $timestamps = false;

    // Campos reales de tu tabla 'usuario'
    protected $fillable = [
        'usuario',
        'password',
        'id_empleado',
        'id_rol',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}