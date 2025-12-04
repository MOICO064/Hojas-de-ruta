@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="key"></i>
            {{ isset($permiso) ? 'Editar Permiso' : 'Crear Nuevo Permiso' }}
        </h3>

        <a href="{{ route('admin.permisos.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <i data-feather="arrow-left"></i>
            <span class="d-none d-md-inline">Volver</span>
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center gap-2">
                    <i data-feather="edit-3"></i>
                    <h4 class="mb-0">{{ isset($permiso) ? 'Editar Permiso' : 'Crear Nuevo Permiso' }}</h4>
                </div>

                <div class="card-body py-4">

                    <form id="permisoForm">
                        @csrf
                        @if(isset($permiso))
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="permiso_id" value="{{ $permiso->id }}">
                        @endif

                        {{-- Nombre del Permiso --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="tag"></i> Nombre del Permiso
                            </label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $permiso->name ?? '' }}"
                                placeholder="Ingrese el nombre del permiso" >

                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Botón Guardar --}}
                        <button type="submit" id="savePermisoBtn"
                            class="btn btn-primary d-flex justify-content-center align-items-center gap-2 py-2">

                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>

                            <i data-feather="check-circle"></i>
                            <span class="btn-text">{{ isset($permiso) ? 'Actualizar Permiso' : 'Crear Permiso' }}</span>
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/permisos/form.js') }}"></script>
@endsection