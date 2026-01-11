@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="user-check"></i>
            {{ isset($user) ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
        </h3>

        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <i data-feather="arrow-left"></i>
            <span class="d-none d-md-inline">Volver</span>
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center gap-2">
                    <i data-feather="edit-3"></i>
                    <h4 class="mb-0">
                        {{ isset($user) ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
                    </h4>
                </div>

                <div class="card-body py-4">

                    <form id="userForm">
                        @csrf

                        @if(isset($user))
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="user_id" value="{{ $user->id }}">
                        @endif

                        {{-- Funcionario --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="layers"></i> Funcionario
                            </label>
                            <select name="funcionario_id" id="funcionario_id" class="form-select">
                                <option value="">-- Seleccione un Funcionario --</option>
                                @foreach($funcionarios as $f)
                                    <option value="{{ $f->id }}" {{ isset($user) && $user->funcionario_id == $f->id ? 'selected' : '' }}>
                                        {{ $f->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>


                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="mail"></i> Email
                            </label>
                            <input type="text" name="email" id="email" class="form-control"
                                value="{{ $user->email ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Contraseña (solo crear o si quiere cambiar) --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="lock"></i> Contraseña
                            </label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="{{ isset($user) ? 'Dejar en blanco para no cambiar' : '' }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Rol --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="award"></i> Rol
                            </label>
                            <select name="role" id="role" class="form-select">
                                <option value="">-- Seleccione un Rol --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Estado (solo editar) --}}
                        @if(isset($user))
                            <div class="mb-3">
                                <label class="form-label d-flex align-items-center gap-2">
                                    <i data-feather="flag"></i> Estado
                                </label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="ACTIVO" {{ $user->estado == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                                    <option value="INACTIVO" {{ $user->estado == 'INACTIVO' ? 'selected' : '' }}>INACTIVO</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        @endif

                        {{-- Guardar --}}
                        <button type="submit" id="saveUserBtn"
                            class="btn btn-primary d-flex justify-content-center align-items-center gap-2 py-2">

                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>

                            <i data-feather="check-circle"></i>
                            <span class="btn-text">
                                {{ isset($user) ? 'Actualizar Usuario' : 'Crear Usuario' }}
                            </span>
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/usuarios/form.js') }}"></script>

    <script>
        
        $(document).ready(function () {
            $('#funcionario_id').select2({
                 theme:  'bootstrap-5',
             placeholder: "-- Seleccione un Funcionario --",
                allowClear: true,
                width: '100%'
            });



            feather.replace();
        });
    </script>
@endsection
