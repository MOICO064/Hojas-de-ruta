@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Dashboard de Hojas de Ruta y Derivaciones</h3>
            <span class="text-muted">
                Última actualización: {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>

    {{-- CARDS HOJAS DE RUTA --}}
    <div class="row mb-2">
        <div class="col-12">
            <h5 class="mb-3">Hojas de Ruta</h5>
        </div>
    </div>
    <div class="row mb-4">
        @foreach ([
            'Total Hojas' => $totales['total'],
            'Pendientes' => $totales['pendientes'],
            'En Proceso' => $totales['en_proceso'],
            'Completadas' => $totales['completadas'],
            'Urgentes' => $totales['urgentes']
        ] as $label => $value)
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <small class="text-uppercase fw-semibold">{{ $label }}</small>
                    <h2 class="fw-bold mt-2">{{ $value }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- CARDS DERIVACIONES --}}
    <div class="row mb-2">
        <div class="col-12">
            <h5 class="mb-3">Derivaciones</h5>
        </div>
    </div>
    <div class="row mb-4">
        @foreach ([
            'Total Derivaciones' => $totalesDerivaciones['total'],
            'Pendientes' => $totalesDerivaciones['pendientes'],
            'En Proceso' => $totalesDerivaciones['en_proceso'],
            'Completadas' => $totalesDerivaciones['completadas'],
            'Anuladas' => $totalesDerivaciones['anuladas']
        ] as $label => $value)
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card shadow-sm text-center h-100 border-primary">
                <div class="card-body">
                    <small class="text-uppercase fw-semibold">{{ $label }}</small>
                    <h2 class="fw-bold mt-2">{{ $value }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- GRÁFICOS --}}
    <div class="row mb-4">
        {{-- Por Estado de Hojas --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Hojas de Ruta por Estado</h5>
                </div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="estadoChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Por Unidad de Hojas --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Hojas de Ruta por Unidad</h5>
                </div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="unidadChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Urgentes vs Normales --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Hojas Urgentes vs Normales</h5>
                </div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="urgentesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Por Gestión / Año --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Hojas por Gestión / Año</h5>
                </div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="gestionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Solicitantes --}}
        <div class="col-12 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Top Solicitantes</h5>
                </div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="topSolicitantesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimas Hojas de Ruta --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Últimas Hojas de Ruta</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($ultimas as $hoja)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>#{{ $hoja->idgral }}</strong> - 
                                <span class="badge bg-secondary">{{ $hoja->estado }}</span>
                                <br>
                                <small>
                                    Solicitante: {{ $hoja->solicitante->nombre ?? 'N/A' }} |
                                    Unidad: {{ $hoja->unidadOrigen->nombre ?? 'N/A' }} |
                                    Prioridad: {{ $hoja->urgente ? 'Urgente' : 'Normal' }}
                                </small>
                            </div>
                            <div>
                                <small class="text-muted">{{ $hoja->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">
                        No hay registros
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const estadoLabels = @json($porEstado->keys());
    const estadoData = @json($porEstado->values());

    const unidadLabels = @json($porUnidadNombres);
    const unidadData = @json($porUnidad->values());

    const urgentesData = @json([$totales['urgentes'], $totales['total'] - $totales['urgentes']]);

    const gestionLabels = @json($porGestion->keys());
    const gestionData = @json($porGestion->values());

    const topSolicitantesLabels = @json($topSolicitantes->keys());
    const topSolicitantesData = @json($topSolicitantes->values());

    const chartColors = ['#0d6efd','#ffc107','#198754','#dc3545','#6f42c1','#20c997','#fd7e14','#6610f2'];

    new Chart(document.getElementById('estadoChart'), {
        type: 'doughnut',
        data: { labels: estadoLabels, datasets: [{ data: estadoData, backgroundColor: chartColors }] },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('unidadChart'), {
        type: 'bar',
        data: { labels: unidadLabels, datasets: [{ label: 'Cantidad de Hojas', data: unidadData, backgroundColor: chartColors }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('urgentesChart'), {
        type: 'pie',
        data: { labels: ['Urgentes','Normales'], datasets: [{ data: urgentesData, backgroundColor: ['#dc3545','#0d6efd'] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('gestionChart'), {
        type: 'line',
        data: { labels: gestionLabels, datasets: [{ label: 'Hojas por año', data: gestionData, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.2)', fill: true, tension: 0.3 }] },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('topSolicitantesChart'), {
        type: 'bar',
        data: { labels: topSolicitantesLabels, datasets: [{ label: 'Cantidad de Hojas', data: topSolicitantesData, backgroundColor: chartColors }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, scales: { x: { beginAtZero: true } } }
    });
</script>
@endsection
