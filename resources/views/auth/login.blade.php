<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PDVSA GAS - Acceso al Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #ed1c24 0%, #b30000 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            margin: 0;
        }
        .card-login { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .btn-pdvsa { background-color: #ed1c24; color: white; font-weight: bold; transition: 0.3s; border: none; }
        .btn-pdvsa:hover { background-color: #b30000; color: white; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-login p-4">
                <div class="text-center mb-4">
                    <h4 class="text-danger fw-bold">PDVSA GAS</h4>
                    <p class="text-muted small text-uppercase tracking-wider">Gestión de Solicitudes</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2 border-0 shadow-sm" style="font-size: 0.85rem;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- REPARADO: Apuntamos a login.post que es el método POST en web.php --}}
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">INDICADOR / USUARIO</label>
                        <input type="text" name="usuario" class="form-control" value="{{ old('usuario') }}" placeholder="Ej: JPEREZ01" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">CONTRASEÑA</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-pdvsa w-100 py-2">INICIAR SESIÓN</button>
                </form>
            </div>
            <p class="text-center text-white mt-4 small" style="opacity: 0.8;">
                &copy; {{ date('Y') }} Gerencia de Tecnología - PDVSA GAS Carabobo
            </p>
        </div>
    </div>
</div>
</body>
</html>