@if($hojas->isEmpty())
    <div class="alert alert-info">
        No se encontraron hojas de ruta para los criterios seleccionados.
    </div>
@else

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Resultados de la búsqueda
                <span class="text-muted fw-normal">
                    ({{ $hojas->count() }} registros)
                </span>
            </h5>
        </div>

        <div class="card-body p-0">

            {{-- ================= DESKTOP / TABLET ================= --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Gral</th>
                            <th>N° Unidad</th>
                            <th>Unidad</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-center">Derivaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hojas as $index => $hoja)
                            <tr>
                                <td> <strong>{{ $hoja->idgral }}</strong></td>

                                <td>
                                    <strong>{{ $hoja->numero_unidad }}</strong>
                                </td>

                                <td>
                                    {{ $hoja->unidadOrigen->nombre ?? '—' }}
                                </td>

                                <td class="text-wrap" style="max-width: 300px">
                                    {{ $hoja->asunto ?? '—' }}
                                </td>

                                <td>
                                    {{ optional($hoja->fecha_creacion)->format('d/m/Y') }}
                                </td>

                                <td>
                                    @php
                                        $estadoClass = match ($hoja->estado) {
                                            'PENDIENTE' => 'bg-warning',
                                            'CONCLUIDO' => 'bg-success',
                                            'ANULADO' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $estadoClass }}">
                                        {{ $hoja->estado }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-info">
                                        {{ $hoja->derivaciones->count() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE ================= --}}
            <div class="d-md-none p-3">
                @foreach($hojas as $index => $hoja)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>N° Gral {{ $hoja->idgral }}</strong>

                                @php
                                    $estadoClass = match ($hoja->estado) {
                                        'PENDIENTE' => 'bg-warning',
                                        'CONCLUIDO' => 'bg-success',
                                        'ANULADO' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $estadoClass }}">
                                    {{ $hoja->estado }}
                                </span>
                            </div>

                            <p class="mb-2">
                                <strong>Unidad:</strong><br>
                                {{ $hoja->unidadOrigen->nombre ?? '—' }}
                            </p>

                            <p class="mb-2">
                                <strong>Asunto:</strong><br>
                                {{ $hoja->asunto ?? '—' }}
                            </p>

                            <p class="mb-2">
                                <strong>Fecha:</strong><br>
                                {{ optional($hoja->fecha_creacion)->format('d/m/Y') }}
                            </p>

                            <p class="mb-3">
                                <strong>Derivaciones:</strong>
                                <span class="badge bg-info">
                                    {{ $hoja->derivaciones->count() }}
                                </span>
                            </p>

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

@endif