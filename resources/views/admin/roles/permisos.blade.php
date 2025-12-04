@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between flex-wrap align-items-center mb-5">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="shield"></i>
            Permisos del Rol: 
            <span id="role-name" data-rol-id="{{ $role->id }}">{{ $role->name ?? '' }}</span> 
        </h3>

        <div class="d-flex gap-2 justify-content-end">
            <!-- Botón Asignar todos -->
            <button id="btn-asignar-todos"  title="Asignar todos los permisos"
                    class="btn btn-success d-flex align-items-center gap-2">
                <i data-feather="check-square"></i>
                <span class="d-none d-md-inline">Asignar Todos</span>
            </button>

            <!-- Botón Quitar todos -->
            <button id="btn-quitar-todos" title="Quitar todos los permisos"
                    class="btn btn-danger d-flex align-items-center gap-2">
                <i data-feather="x-square"></i>
                <span class="d-none d-md-inline">Quitar Todos</span>
            </button>

            <!-- Botón Volver -->
            <a href="{{ route('admin.roles.index') }}"  title="Volver atras"
               class="btn btn-outline-primary d-flex align-items-center gap-2">
                <i data-feather="arrow-left"></i>
                <span class="d-none d-md-inline">Volver</span>
            </a>
        </div>
    </div>

    <div class="row" id="permisos-container">
        @forelse($permisos as $permiso)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="card shadow-sm border-0 p-3 d-flex align-items-center justify-content-between flex-row">
                    <span>{{ $permiso->name }}</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input permiso-switch" type="checkbox"
                               data-permiso-id="{{ $permiso->id }}"
                               {{ $permiso->asignado ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary text-center mb-0">
                    No existe ningún permiso.
                </div>
            </div>
        @endforelse
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/roles/permisos.js') }}"></script>
@endsection
