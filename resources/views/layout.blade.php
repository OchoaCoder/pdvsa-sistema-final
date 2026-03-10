<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDVSA Gas - Gestión de Solicitudes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-pdvsa { background-color: #ed1c24; border-bottom: 4px solid #b31217; padding: 0.8rem 1rem; }
        .navbar-brand { font-weight: 800; letter-spacing: 1px; }
        .dropdown-menu { border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15); }
        .btn-pdvsa { background-color: #ed1c24; color: white; border: none; transition: 0.3s; }
        .btn-pdvsa:hover { background-color: #b31217; color: white; transform: translateY(-1px); }
        .badge-role { background-color: rgba(255, 255, 255, 0.2); color: white; padding: 0.5rem 0.8rem; border-radius: 50px; font-size: 0.8rem; border: 1px solid rgba(255, 255, 255, 0.4); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-pdvsa shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand mb-0 h1" href="{{ route('solicitudes.index') }}">PDVSA GAS COMUNAL - CARABOBO</a>
            @auth
            <div class="d-flex align-items-center gap-3">
                <div class="badge-role d-none d-md-block">
                    @if(Auth::user()->nivel_acceso == 3)
                        <i class="fas fa-user-shield me-1 text-warning"></i> <strong>ADMINISTRADOR</strong>
                    @elseif(Auth::user()->nivel_acceso == 2)
                        <i class="fas fa-user-check me-1 text-info"></i> <strong>AUDITOR</strong>
                    @else
                        <i class="fas fa-user me-1"></i> <strong>SOLICITANTE</strong>
                    @endif
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle border-0 fw-bold" type="button" id="userMenu" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->usuario }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2">
                        <li><h6 class="dropdown-header">Opciones de Usuario</h6></li>
                        
                        {{-- CAMBIO AQUÍ: Ahora solo nivel 2 puede ver esto --}}
                        @if(Auth::user()->nivel_acceso == 2)
                            <li>
                                <a class="dropdown-item" href="{{ route('usuarios.index') }}">
                                    <i class="fas fa-users-cog me-2 text-danger"></i> <strong>Gestión de Usuarios</strong>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        @endif

                        <li><a class="dropdown-item" href="{{ route('password.change') }}"><i class="fas fa-key me-2 text-muted"></i> Cambiar Contraseña</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="px-2">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm w-100 text-start"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </div>
    </nav>
    <div class="container">
        @if(session('success')) <div class="alert alert-success shadow-sm">{{ session('success') }}</div> @endif
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>