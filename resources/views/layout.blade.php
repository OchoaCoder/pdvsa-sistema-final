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
        .dropdown-menu { border: none; shadow: 0 0.5rem 1rem rgba(0,0,0,.15); }
        .btn-pdvsa { background-color: #ed1c24; color: white; border: none; transition: 0.3s; }
        .btn-pdvsa:hover { background-color: #b31217; color: white; transform: translateY(-1px); }
        .alert { border: none; border-left: 5px solid; }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-dark navbar-pdvsa shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand mb-0 h1" href="{{ route('solicitudes.index') }}">
                PDVSA GAS COMUNAL - CARABOBO
            </a>

            @auth
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle border-0 fw-bold" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->usuario }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2" aria-labelledby="userMenu">
                    <li><h6 class="dropdown-header">Opciones de Usuario</h6></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('password.change') }}">
                            <i class="fas fa-key me-2 text-muted"></i> Cambiar Contraseña
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="px-2">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm w-100 text-start">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li><i class="fas fa-exclamation-triangle me-2"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>