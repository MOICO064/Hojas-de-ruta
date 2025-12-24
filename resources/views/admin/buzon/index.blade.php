@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="mb-0">Buzon</h3>
            @if(isset($buzon))
                <span class="badge bg-info text-dark fs-6 mb-0"> {{ $buzon }}</span>
            @endif
        </div>

        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('admin.hojaruta.create') }}" class="btn btn-primary d-flex align-items-center"
                title="Crear Nueva Hoja de Ruta">
                <i data-feather="plus" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Crear Hoja de Ruta</span>
                <i data-feather="plus" class="d-inline d-md-none"></i>
            </a>

            <button type="button" id="refresh-table" class="btn btn-secondary d-flex align-items-center"
                title="Actualizar Tabla">
                <i data-feather="refresh-cw" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Actualizar</span>
                <i data-feather="refresh-cw" class="d-inline d-md-none"></i>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-12 mb-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Lista de Hojas de Ruta</h4>
                </div>
                <div class="card-body">
                    <div class="table-card">
                        <table id="hojaruta-table" class="table text-nowrap mb-0 table-centered table-hover w-100"
                            data-gestion="{{ $gestion ?? '' }}">
                            <thead class="table-light">
                                <tr>
                                    <th>Número General</th>
                                    <th>Número Unidad</th>
                                    <th>Asunto</th>
                                    <th>Unidad Origen</th>
                                    <th>Estado</th>
                                    <th>Urgente</th>
                                    <th>Gestión</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/hojaruta/index.js') }}"></script>
    <script src="{{ asset('js/hojaruta/delete.js') }}"></script>
@endsection