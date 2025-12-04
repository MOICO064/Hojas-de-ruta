@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="award"></i>
            {{ isset($role) ? 'Editar Rol' : 'Crear Nuevo Rol' }}
        </h3>

        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <i data-feather="arrow-left"></i>
            <span class="d-none d-md-inline">Volver</span>
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center gap-2">
                    <i data-feather="edit-3"></i>
                    <h4 class="mb-0">{{ isset($role) ? 'Editar Rol' : 'Crear Nuevo Rol' }}</h4>
                </div>

                <div class="card-body py-4">

                    <form id="roleForm">
                        @csrf
                        @if(isset($role))
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="role_id" value="{{ $role->id }}">
                        @endif

                        {{-- Nombre del Rol --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="tag"></i> Nombre del Rol
                            </label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $role->name ?? '' }}"
                                placeholder="Ingrese el nombre del rol" onInput="this.value = this.value.toUpperCase()">

                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Botón Guardar --}}
                        <button type="submit" id="saveRoleBtn"
                            class="btn btn-primary d-flex justify-content-center align-items-center gap-2 py-2">

                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>

                            <i data-feather="check-circle"></i>
                            <span class="btn-text">{{ isset($role) ? 'Actualizar Rol' : 'Crear Rol' }}</span>
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="{{ asset('js/roles/form.js') }}"></script>
@endsection