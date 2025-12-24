@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="mb-0">Derivaciones</h3>
            <span class="text-muted fs-6">
                Hoja de Ruta N° {{ $hoja->idgral }}
            </span>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('admin.derivaciones.create', $hoja->id) }}" class="btn btn-primary d-flex align-items-center"
                title="Registrar Derivación">
                <i data-feather="plus" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Nueva Derivación</span>
                <i data-feather="plus" class="d-inline d-md-none"></i>
            </a>
            <button type="button" id="refresh-table" class="btn btn-secondary d-flex align-items-center"
                title="Actualizar Tabla">
                <i data-feather="refresh-cw" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Actualizar</span>
                <i data-feather="refresh-cw" class="d-inline d-md-none"></i>
            </button>
            <a href="{{ route('admin.buzon.salida') }}" class="btn btn-outline-secondary d-flex align-items-center"
                title="Volver a Hojas de Ruta">
                <i data-feather="arrow-left" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Volver</span>
                <i data-feather="arrow-left" class="d-inline d-md-none"></i>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-12 mb-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Historial de Derivaciones</h4>
                </div>

                <div class="card-body">
                    <div class="table-card">
                        <table id="derivaciones-table" class="table text-nowrap mb-0 table-centered table-hover w-100"
                            data-hoja-id="{{ $hoja->id }}">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Unidad Origen</th>
                                    <th>Unidad Destino</th>
                                    <th>Funcionario</th>
                                    <th>Estado</th>
                                    <th>Fecha Derivación</th>
                                    <th>Fecha Recepción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/derivaciones/index.js') }}"></script>
    <script src="{{ asset('js/derivaciones/delete.js') }}"></script>
@endsection