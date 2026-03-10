@extends('layout')

@section('content')
<div class="container py-4">
    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="fas fa-exclamation-triangle me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-danger text-white py-3">
            <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i> Registrar Nuevo Acceso</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Cédula (Login)</label>
                    <input type="text" name="cedula" class="form-control" placeholder="Ej: 12345678" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Nombre Completo</label>
                    <input type="text" name="usuario_nombre" class="form-control" placeholder="Nombre y Apellido" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Mínimo 4 caracteres" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Nivel</label>
                    <select name="nivel_acceso" class="form-select">
                        <option value="1">Nivel 1 (Solicitante)</option>
                        <option value="2">Nivel 2 (Auditor)</option>
                        <option value="3">Nivel 3 (Administrador)</option>
                    </select>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-danger px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar en Base de Datos
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- LISTADO --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i> Usuarios Registrados</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Usuario (Cédula)</th>
                        <th>Nivel de Acceso</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $u)
                    <tr>
                        <td class="ps-4">
                            <i class="fas fa-id-card text-muted me-2"></i>
                            <strong>{{ $u->usuario }}</strong>
                        </td>
                        <td>
                            @if($u->nivel_acceso == 3)
                                <span class="badge bg-warning text-dark">Administrador</span>
                            @elseif($u->nivel_acceso == 2)
                                <span class="badge bg-info">Auditor</span>
                            @else
                                <span class="badge bg-secondary">Solicitante</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(Auth::id() !== $u->id)
                                <form action="{{ route('usuarios.destroy', $u->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-success">Sesión Actual</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection