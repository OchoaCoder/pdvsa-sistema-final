<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes - PDVSA GAS COMUNAL CARABOBO
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS (Invitados) ---
Route::middleware('guest')->group(function () {
    // Visualización
    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin']); 

    // Procesamiento
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

    // --- SECCIÓN DE AUDITORÍA Y CONTROL (Lo que faltaba) ---
    // Esta ruta permite al Admin (Nivel 3) aprobar/rechazar desde el modal
    Route::post('/solicitudes/actualizar-estatus/{id}', [SolicitudController::class, 'actualizarEstatus'])
        ->name('solicitudes.status');

    // --- REPORTES ---
    // Descarga masiva (Excel) y comprobantes (PDF)
    Route::get('/exportar-excel', [SolicitudController::class, 'exportarExcel'])->name('solicitudes.excel');
    Route::get('/solicitudes/pdf/{id}', [SolicitudController::class, 'descargarPDF'])->name('solicitudes.pdf');

    // --- PERFIL Y SEGURIDAD ---
    Route::get('/perfil/password', function () {
        return view('profile.password');
    })->name('password.change');

    Route::post('/perfil/password/actualizar', [SolicitudController::class, 'updatePassword'])->name('password.update');
});