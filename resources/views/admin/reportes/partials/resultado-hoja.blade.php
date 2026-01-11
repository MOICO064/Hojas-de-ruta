<div class="card mb-4">
    <div class="card-body">

        <h5 class="mb-3">Datos Básicos de la Hoja</h5>

        <div class="row g-3">
            <div class="col-md-3"><strong>Número:</strong><br>{{ $hoja->idgral }}</div>
            <div class="col-md-3"><strong>Unidad:</strong><br>{{ $hoja->unidadOrigen->nombre ?? '—' }}</div>
            <div class="col-md-3"><strong>Fecha:</strong><br>{{ $hoja->fecha_creacion->format('d/m/Y') }}</div>
            <div class="col-md-3"><strong>Estado:</strong><br><span class="badge bg-secondary">{{ $hoja->estado }}</span></div>
            <div class="col-md-12"><strong>Asunto:</strong><br>{{ $hoja->asunto ?? '—' }}</div>
        </div>

        <hr>

        <h5 class="mb-3">Derivaciones</h5>

        @if($hoja->derivaciones->isEmpty())
            <div class="alert alert-info mb-0">Esta hoja no tiene derivaciones.</div>
        @else
            {{-- ================= DESKTOP / TABLET ================= --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>N° Gral</th>
                            <th>De</th>
                            <th>A</th>
                            <th>Fecha Derivación</th>
                            <th>Fecha Recepción</th>
                            <th>Estado</th>
                            <th>Descripcion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hoja->derivaciones as $derivacion)
                        @php
                            $estadoClass = match($derivacion->estado) {
                                'PENDIENTE' => 'bg-warning',
                                'RECEPCIONADO' => 'bg-success',
                                'ANULADO' => 'bg-danger',
                                default => 'bg-info'
                            };
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $derivacion->unidadOrigen->nombre ?? '—' }}</td>
                            <td>
                                <strong>{{ $derivacion->unidadDestino->nombre ?? '—' }}</strong><br>
                                <small class="text-muted">{{ $derivacion->funcionario?->nombre ?? 'Sin funcionario' }}</small>
                            </td>
                            <td>{{ optional($derivacion->fecha_derivacion)->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>{{ optional($derivacion->fecha_recepcion)->format('d/m/Y H:i') ?? '—' }}</td>
                            <td><span class="badge {{ $estadoClass }}">{{ $derivacion->estado }}</span></td>
                            <td>{{ $derivacion->descripcion ?? $derivacion->observacion ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE ================= --}}
            <div class="d-md-none">
                @foreach($hoja->derivaciones as $derivacion)
                @php
                    $estadoClass = match($derivacion->estado) {
                        'PENDIENTE' => 'bg-warning',
                        'RECEPCIONADO' => 'bg-success',
                        'ANULADO' => 'bg-danger',
                        default => 'bg-info'
                    };
                @endphp
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>N° Gral{{ $loop->iteration }}</strong>
                            <span class="badge {{ $estadoClass }}">{{ $derivacion->estado }}</span>
                        </div>

                        <p class="mb-1"><strong>Unidad Origen:</strong><br>{{ $derivacion->unidadOrigen->nombre ?? '—' }}</p>

                        <p class="mb-1">
                            <strong>Unidad Destino / Funcionario:</strong><br>
                            {{ $derivacion->unidadDestino->nombre ?? '—' }}<br>
                            <small class="text-muted">{{ $derivacion->funcionario?->nombre ?? 'Sin funcionario' }}</small>
                        </p>

                        <p class="mb-1"><strong>Descripción:</strong><br>{{ $derivacion->descripcion ?? $derivacion->observacion ?? '—' }}</p>

                        <p class="mb-1"><strong>Fecha Derivación:</strong><br>{{ optional($derivacion->fecha_derivacion)->format('d/m/Y H:i') ?? '—' }}</p>
                        <p class="mb-1"><strong>Fecha Recepción:</strong><br>{{ optional($derivacion->fecha_recepcion)->format('d/m/Y H:i') ?? '—' }}</p>

                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
