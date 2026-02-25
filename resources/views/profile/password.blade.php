@extends('layout') {{-- Ajustado al nombre real de tu archivo --}}

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="mb-3">
                <a href="{{ route('solicitudes.index') }}" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="card-title mb-0 text-center">
                        <i class="fas fa-shield-alt me-2"></i>Seguridad de la Cuenta
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4 text-center">
                        Por tu seguridad, te recomendamos usar una contraseña de al menos 6 caracteres que incluya números y letras.
                    </p>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Contraseña Actual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock-open text-muted"></i></span>
                                <input type="password" name="current_password" class="form-control border-start-0 ps-0" placeholder="Ingresa tu clave actual" required>
                            </div>
                        </div>

                        <hr class="my-4 text-light">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-key text-muted"></i></span>
                                <input type="password" name="new_password" class="form-control border-start-0 ps-0" placeholder="Mínimo 6 caracteres" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted"></i></span>
                                <input type="password" name="new_password_confirmation" class="form-control border-start-0 ps-0" placeholder="Repite tu nueva clave" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-pdvsa w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Actualizar Contraseña
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-muted">Sistema de Gestión de Beneficios - PDVSA Gas Carabobo</small>
            </div>
        </div>
    </div>
</div>
@endsection