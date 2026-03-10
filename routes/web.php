<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| Web Routes - PDVSA GAS COMUNAL CARABOBO
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS (Invitados) ---
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin']); 

    Route::post('/', [LoginController::class, 'login']); 
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// --- RUTAS PRIVADAS (Solo Personal Autenticado) ---
Route::middleware('auth')->group(function () {
    
    // Gestión de Sesión
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard y CRUD de Solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/crear', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes/guardar', [SolicitudController::class, 'store'])->name('solicitudes.store');

    // Gestión de Usuarios (Nivel 2)
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios/guardar', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::delete('/usuarios/eliminar/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // Auditoría y Control
    Route::post('/solicitudes/actualizar-estatus/{id}', [SolicitudController::class, 'actualizarEstatus'])
        ->name('solicitudes.status');

    // Reportes
    Route::get('/solicitudes/exportar-excel', [SolicitudController::class, 'exportarExcel'])
        ->name('solicitudes.exportarExcel');
        
    Route::get('/solicitudes/pdf/{id}', [SolicitudController::class, 'descargarPDF'])
        ->name('solicitudes.pdf');

    // Perfil y Seguridad
    Route::get('/perfil/password', function () {
        return view('profile.password');
    })->name('password.change');

    Route::post('/perfil/password/actualizar', [SolicitudController::class, 'updatePassword'])
        ->name('password.update');
});