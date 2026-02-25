@extends('layout')

@section('content')
<div class="container-fluid">
    {{-- 1. SECCIÓN DE ESTADÍSTICAS --}}
    @if(Auth::user()->nivel_acceso == 3)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-white border-0 shadow-sm p-3">
                <h6 class="text-muted small fw-bold text-uppercase">Total Solicitudes</h6>
                <h3 class="fw-bold mb-0 text-dark">{{ $total }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white border-0 shadow-sm p-3 border-start border-warning border-5">
                <h6 class="text-muted small text-warning fw-bold text-uppercase">Pendientes</h6>
                <h3 class="fw-bold mb-0">{{ $pendientes }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white border-0 shadow-sm p-3 border-start border-success border-5">
                <h6 class="text-muted small text-success fw-bold text-uppercase">Aprobadas</h6>
                <h3 class="fw-bold mb-0">{{ $aprobadas }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white border-0 shadow-sm p-3 border-start border-danger border-5">
                <h6 class="text-muted small text-danger fw-bold text-uppercase">Rechazadas</h6>
                <h3 class="fw-bold mb-0">{{ $rechazadas }}</h3>
            </div>
        </div>
    </div>

    {{-- FILTRO POR FECHA --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form action="{{ route('solicitudes.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary">Desde:</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary">Hasta:</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-pdvsa w-100 fw-bold">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary w-50">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="row">
        {{-- 2. GRÁFICO --}}
        @if(Auth::user()->nivel_acceso >= 2)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100 text-center">
                <h5 class="mb-4 fw-bold text-secondary">Distribución de Estatus</h5>
                <canvas id="solicitudesChart"></canvas>
            </div>
        </div>
        @endif

        {{-- 3. TABLA DE SOLICITUDES --}}
        <div class="{{ Auth::user()->nivel_acceso >= 2 ? 'col-md-8' : 'col-md-12' }} mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="mb-0 fw-bold">
                        {{ Auth::user()->nivel_acceso == 3 ? 'Gestión de Auditoría' : 'Mis Solicitudes' }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('solicitudes.create') }}" class="btn btn-pdvsa btn-sm px-3 shadow-sm">
                            <i class="fas fa-plus"></i> Nueva
                        </a>
                        @if(Auth::user()->nivel_acceso == 3)
                        <a href="{{ route('solicitudes.excel', request()->all()) }}" class="btn btn-success btn-sm px-3 shadow-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Detalles</th>
                                <th>Estatus</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $sol)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">#{{ $sol->id_solicitud }}</td>
                                <td>
                                    <div class="fw-bold">{{ $sol->beneficio->nombre_beneficio ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ date('d/m/Y', strtotime($sol->fecha_solicitud)) }}</small>
                                    @if(Auth::user()->nivel_acceso == 3)
                                        <div class="small text-danger">Solicitante: {{ $sol->usuario->usuario ?? 'N/A' }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $sol->id_estatusSol == 1 ? 'bg-warning text-dark' : ($sol->id_estatusSol == 2 ? 'bg-success' : 'bg-danger') }}">
                                        {{ $sol->estatus->descripcion ?? 'Desconocido' }}
                                    </span>
                                    @if($sol->monto > 0)
                                        <div class="small fw-bold text-dark mt-1">Bs. {{ number_format($sol->monto, 2, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('solicitudes.pdf', $sol->id_solicitud) }}" class="btn btn-sm btn-outline-secondary" title="Ver PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        
                                        {{-- BOTONES DE DECISIÓN: Solo si es Admin y está Pendiente --}}
                                        @if(Auth::user()->nivel_acceso == 3 && $sol->id_estatusSol == 1)
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="openApproveModal({{ $sol->id_solicitud }})" title="Aprobar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            
                                            <form action="{{ route('solicitudes.status', $sol->id_solicitud) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="id_estatusSol" value="3">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Rechazar" onclick="return confirm('¿Seguro que desea rechazar esta solicitud?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No hay solicitudes registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PARA APROBAR CON MONTO --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="approveForm" method="POST" class="modal-content border-0 shadow">
            @csrf
            <input type="hidden" name="id_estatusSol" value="2">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Aprobar Solicitud <span id="modalIdSol"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Monto a Liquidar (Bs.)</label>
                    <input type="number" step="0.01" name="monto" class="form-control form-control-lg" placeholder="0.00" required>
                    <small class="text-muted">Ingrese el monto aprobado para este beneficio.</small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success px-4 fw-bold">PROCESAR APROBACIÓN</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function openApproveModal(id) {
        const form = document.getElementById('approveForm');
        // Asegúrate de que esta ruta coincida con la de web.php
        form.action = `/solicitudes/actualizar-estatus/${id}`;
        document.getElementById('modalIdSol').innerText = '#' + id;
        new bootstrap.Modal(document.getElementById('approveModal')).show();
    }

    @if(Auth::user()->nivel_acceso >= 2)
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('solicitudesChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Aprobadas', 'Rechazadas'],
                datasets: [{
                    data: [{{ $pendientes }}, {{ $aprobadas }}, {{ $rechazadas }}],
                    backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
                cutout: '70%'
            }
        });
    });
    @endif
</script>
@endsection