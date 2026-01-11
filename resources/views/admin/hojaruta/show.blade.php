@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h3 class="mb-0">Detalle de Hoja de Ruta</h3>
        <span class="text-muted fs-6">
            Hoja de Ruta N° {{ $hojaRuta->idgral }}
        </span>
    </div>

    <div class="d-flex gap-2 align-items-center">
        @if(auth()->user()->id=== $hojaRuta->creado_por && $hojaRuta->estado!=="Concluido" && $hojaRuta->estado!=="Anulado")
        <span></span>
        {{-- EDITAR --}}
        <a href="{{ route('admin.hojaruta.edit', $hojaRuta->id) }}" class="btn btn-warning d-flex align-items-center"
            title="Editar Hoja de Ruta">
            <i data-feather="edit" class="me-2 icon-xxs d-none d-md-inline"></i>
            <span class="d-none d-md-inline">Editar</span>
            <i data-feather="edit" class="d-inline d-md-none"></i>
        </a>

        {{-- ANULAR --}}
        <button type="button" class="btn btn-danger d-flex align-items-center" title="Anular Hoja de Ruta"
            onclick="abrirModalAnulacion('hoja', {{ $hojaRuta->id }})">
            <i data-feather="trash-2" class="me-2 icon-xxs d-none d-md-inline"></i>
            <span class="d-none d-md-inline">Anular</span>
            <i data-feather="trash-2" class="d-inline d-md-none"></i>
        </button>
        @endif
        {{-- IMPRIMIR --}}
        <a href="{{ route('admin.reportes.hoja-ruta', $hojaRuta->id) }}"
            class="btn btn-success d-flex align-items-center" target="_blank" title="Imprimir Hoja de Ruta">
            <i data-feather="printer" class="me-2 icon-xxs d-none d-md-inline"></i>
            <span class="d-none d-md-inline">Imprimir</span>
            <i data-feather="printer" class="d-inline d-md-none"></i>
        </a>

        {{-- VOLVER --}}
        @if(auth()->user()->hasRole('DIRECTOR'))
        <a href="{{ route('admin.hojaruta.index',$hojaRuta->gestion) }}" class="btn btn-outline-secondary d-flex align-items-center"
            title="Volver">
            <i data-feather="arrow-left" class="me-2 icon-xxs d-none d-md-inline"></i>
            <span class="d-none d-md-inline">Volver</span>
            <i data-feather="arrow-left" class="d-inline d-md-none"></i>
        </a>
@else
<a href="{{ route('admin.buzon.salida') }}" class="btn btn-outline-secondary d-flex align-items-center"
            title="Volver">
            <i data-feather="arrow-left" class="me-2 icon-xxs d-none d-md-inline"></i>
            <span class="d-none d-md-inline">Volver</span>
            <i data-feather="arrow-left" class="d-inline d-md-none"></i>
        </a>
@endif
    </div>
    @include('admin.anulacion.modal')
</div>

{{-- DATOS GENERALES --}}
<div class="row mb-4">
    <div class="col-xl-12 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Datos Generales</h4>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <strong>N° General:</strong><br>
                        <span class="badge bg-primary fs-6">
                            {{ $hojaRuta->idgral }}
                        </span>
                    </div>

                    <div class="col-md-4">
                        <strong>N° Unidad:</strong><br>
                        <span class="badge bg-secondary fs-6">
                            {{ $hojaRuta->numero_unidad ?? '-' }}
                        </span>
                    </div>

                    <div class="col-md-4">
                        <strong>Gestión:</strong><br>
                        <span class="badge bg-secondary fs-6">
                            {{ $hojaRuta->gestion }}
                        </span>
                    </div>

                    <div class="col-md-4">
                        <strong>Solicitante:</strong><br>
                        {{ $hojaRuta->solicitante?->nombre ?? $hojaRuta->nombre_solicitante }}
                    </div>

                    <div class="col-md-4">
                        <strong>Unidad Origen:</strong><br>
                        {{ $hojaRuta->unidadOrigen->nombre ?? '-' }}
                    </div>

                    <div class="col-md-4">
                        <strong>CITE:</strong><br>
                        {{ $hojaRuta->cite ?? '-' }}
                    </div>

                    <div class="col-md-4">
                        <strong>Estado:</strong><br>
                        <span class="badge bg-info">{{ $hojaRuta->estado }}</span>
                    </div>

                    <div class="col-md-4">
                        <strong>Urgente:</strong><br>
                        <span class="badge {{ $hojaRuta->urgente ? 'bg-danger' : 'bg-secondary' }}">
                            {{ $hojaRuta->urgente ? 'SÍ' : 'NO' }}
                        </span>
                    </div>

                    <div class="col-md-4">
                        <strong>Fecha Creación:</strong><br>
                        {{ $hojaRuta->fecha_creacion?->format('d/m/Y') }}
                    </div>

                    <div class="col-md-4">
                        <strong>Total Fojas:</strong><br>
                        <span class="badge bg-primary fs-6">
                            {{ $hojaRuta->derivaciones->sum('fojas') }}
                        </span>
                    </div>

                    <div class="col-md-4">
                        <strong>Creado por:</strong><br>
                        <span class="badge bg-success fs-6">
                            {{ $hojaRuta->creador?->email ?? 'Sistema' }}
                        </span>
                    </div>

                    <div class="col-md-12">
                        <strong>Asunto:</strong><br>
                        {{ $hojaRuta->asunto }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- HISTORIAL DE DERIVACIONES --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Historial de Derivaciones</h4>
            </div>

            <div class="card-body">

                {{-- ================= DESKTOP / TABLET ================= --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-centered w-100 mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Unidad Origen</th>
                                <th>Destino / Funcionario</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Fechas</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hojaRuta->derivaciones as $index => $derivacion)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $derivacion->unidadOrigen->nombre ?? '—' }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $derivacion->unidadDestino->nombre ?? '—' }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $derivacion->funcionario?->nombre ?? 'Sin funcionario' }}
                                    </small>
                                </td>

                                <td class="text-wrap" style="max-width: 320px">
                                    {{ $derivacion->descripcion ?? '—' }}
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        {{ $derivacion->estado }}
                                    </span>
                                </td>

                                <td>
                                    <small>
                                        <strong>Derivación:</strong><br>
                                        {{ $derivacion->created_at?->format('d/m/Y H:i') }}
                                    </small>
                                    <br>
                                    <small>
                                        <strong>Recepción:</strong><br>
                                        {{ $derivacion->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}
                                    </small>
                                </td>

                                <td>
                                    @if(!empty($derivacion->pdf))
                                    <a href="{{$derivacion->pdf}}" target="_blank" class="btn btn-sm btn-outline-danger"
                                        title="Ver PDF">
                                        <i data-feather="file-text"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No existen derivaciones registradas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ================= MOBILE ================= --}}
                <div class="d-md-none">
                    @forelse ($hojaRuta->derivaciones as $index => $derivacion)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>#{{ $index + 1 }}</strong>
                                <span class="badge bg-primary">
                                    {{ $derivacion->estado }}
                                </span>
                            </div>

                            <p class="mb-2">
                                <strong>Unidad Origen:</strong><br>
                                {{ $derivacion->unidadOrigen->nombre ?? '—' }}
                            </p>

                            <p class="mb-2">
                                <strong>Unidad Destino:</strong><br>
                                {{ $derivacion->unidadDestino->nombre ?? '—' }}<br>
                                <small class="text-muted">
                                    {{ $derivacion->funcionario?->nombre ?? 'Sin funcionario' }}
                                </small>
                            </p>

                            <p class="mb-2">
                                <strong>Descripción:</strong><br>
                                {{ $derivacion->descripcion ?? '—' }}
                            </p>

                            <p class="mb-2">
                                <strong>Fecha Derivación:</strong><br>
                                {{ $derivacion->created_at?->format('d/m/Y H:i') }}
                            </p>

                            <p class="mb-3">
                                <strong>Fecha Recepción:</strong><br>
                                {{ $derivacion->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}
                            </p>

                            @if(!empty($derivacion->pdf))
                            <a href="{{ $derivacion->pdf}}" target="_blank" class="btn btn-outline-danger btn-sm w-100">
                                <i data-feather="file-text"></i> Ver PDF
                            </a>
                            @endif

                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">
                        No existen derivaciones registradas.
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ asset('js/anulaciones/anulaciones.js') }}"></script>
@endsection