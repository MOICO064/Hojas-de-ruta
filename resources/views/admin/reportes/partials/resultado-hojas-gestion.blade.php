@if($hojas->isEmpty())
    <div class="alert alert-info">
        No se encontraron hojas de ruta para los criterios seleccionados.
    </div>
@else
    @php
        // Agrupar hojas por unidad
        $unidadesResumen = $hojas->groupBy(fn($h) => $h->unidadOrigen->nombre ?? '—')->map(function($hojasUnidad) {
            return [
                'PENDIENTE' => $hojasUnidad->where('estado', 'Pendiente')->count(),
                'CONCLUIDO' => $hojasUnidad->where('estado', 'Concluido')->count(),
                'ANULADO' => $hojasUnidad->where('estado', 'Anulado')->count(),
                'EN PROCESO' => $hojasUnidad->where('estado', 'En Proceso')->count(),
                'TOTAL' => $hojasUnidad->count()
            ];
        });

        $totalHojas = $hojas->count();
    @endphp

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Resultados de la búsqueda
                <span class="text-muted fw-normal">({{ $totalHojas }} hojas en total)</span>
            </h5>
        </div>

        <div class="card-body p-0">

            {{-- ================= DESKTOP / TABLET ================= --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Unidad</th>
                            <th>Pendientes</th>
                            <th>Concluidos</th>
                            <th>Anulados</th>
                            <th>En Proceso</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unidadesResumen as $unidad => $resumen)
                            <tr>
                                <td>{{ $unidad }}</td>
                                <td>{{ $resumen['PENDIENTE'] }}</td>
                                <td>{{ $resumen['CONCLUIDO'] }}</td>
                                <td>{{ $resumen['ANULADO'] }}</td>
                                <td>{{ $resumen['EN PROCESO'] }}</td>
                                <td>{{ $resumen['TOTAL'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE ================= --}}
            <div class="d-md-none p-3">
                <p class="fw-semibold mb-2">Total de hojas: {{ $totalHojas }}</p>
                @foreach($unidadesResumen as $unidad => $resumen)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-semibold">{{ $unidad }}</h6>
                            <ul class="list-unstyled mb-0">
                                <li>Pendientes: {{ $resumen['PENDIENTE'] }}</li>
                                <li>Concluidos: {{ $resumen['CONCLUIDO'] }}</li>
                                <li>Anulados: {{ $resumen['ANULADO'] }}</li>
                                <li>En Proceso: {{ $resumen['EN PROCESO'] }}</li>
                                <li><strong>Total: {{ $resumen['TOTAL'] }}</strong></li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endif
