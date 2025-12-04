@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0">Roles</h3>

        <div class="d-flex gap-2">
            <!-- Botón Crear Rol -->
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary d-flex align-items-center"
                title="Crear Nuevo Rol">
                <i data-feather="plus" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Crear Rol</span>
                <i data-feather="plus" class="d-inline d-md-none"></i>
            </a>

            <!-- Botón Actualizar -->
            <button type="button" id="refresh-table" class="btn btn-secondary d-flex align-items-center"
                title="Actualizar Tabla">
                <i id="refresh-icon" data-feather="refresh-cw" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Actualizar</span>
                <i id="refresh-icon-mobile" data-feather="refresh-cw" class="d-inline d-md-none"></i>
            </button>
        </div>
    </div>

    <!-- row -->
    <div class="row">
        <div class="col-xl-12 col-12 mb-5">
            <!-- card -->
            <div class="card">
                <!-- card header -->
                <div class="card-header">
                    <h4 class="mb-0">Lista de Roles</h4>
                </div>

                <!-- table -->
                <div class="card-body">
                    <div class="table-card">
                        <table class="table text-nowrap mb-0 table-centered table-hover w-100" id="roles-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Guard</th>
                                    <th>Permisos</th>
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
    {{-- Scripts específicos para Roles (igual que usuarios pero separados) --}}
    <script src="{{ asset('js/roles/index.js') }}"></script>
    <script src="{{ asset('js/roles/delete.js') }}"></script>
@endsection