<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDVSA GAS - Nueva Solicitud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-pdvsa { background-color: #ed1c24; color: white; padding: 15px; font-weight: bold; text-transform: uppercase; }
        .card-header { background-color: #ffffff; color: #ed1c24; border-bottom: 2px solid #ed1c24; }
        .btn-pdvsa { background-color: #ed1c24; color: white; border: none; transition: 0.3s; }
        .btn-pdvsa:hover { background-color: #c81010; color: white; transform: translateY(-1px); }
        .form-label { color: #333; }
    </style>
</head>
<body>

<nav class="navbar-pdvsa mb-4 shadow-sm text-center">
    <div class="container">
        SISTEMA DE GESTIÓN DE BENEFICIOS - PDVSA GAS CARABOBO
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header p-3">
                    <h5 class="mb-0">Nueva Solicitud de Beneficio</h5>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Mensajes de Éxito o Error --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('solicitudes.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Descripción del Requerimiento</label>
                            <textarea name="descripcion" class="form-control border-secondary" rows="3" placeholder="Ej: Solicito el pago de colegio correspondiente al mes de febrero..." required>{{ old('descripcion') }}</textarea>
                            <small class="text-muted">Mínimo 10 caracteres.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Centro de Trabajo (CDT)</label>
                                <select name="id_cdt" class="form-select border-secondary" required>
                                    <option value="">Seleccione el centro...</option>
                                    @foreach($centros as $centro)
                                        <option value="{{ $centro->id_cdt }}">{{ $centro->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipo de Beneficio</label>
                                <select name="id_beneficio" class="form-select border-secondary" required>
                                    <option value="">Seleccione el beneficio...</option>
                                    @foreach($beneficios as $beneficio)
                                        <option value="{{ $beneficio->id_beneficio }}">{{ $beneficio->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            {{-- Si no tienes la ruta index creada, el botón fallará. Asegúrate de tenerla en web.php --}}
                            <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary px-4 me-2">Ver mis Solicitudes</a>
                            <button type="submit" class="btn btn-pdvsa px-5 shadow-sm fw-bold">Enviar a Revisión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>