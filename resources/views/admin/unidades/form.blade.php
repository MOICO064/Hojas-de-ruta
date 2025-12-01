@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="layers"></i>
            {{ isset($unidad) ? 'Editar Unidad' : 'Crear Nueva Unidad' }}
        </h3>

        <a href="{{ route('admin.unidades.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <i data-feather="arrow-left"></i>
            <span class="d-none d-md-inline">Volver</span>
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header  d-flex align-items-center gap-2">
                    <i data-feather="edit-3"></i>
                    <h4 class="mb-0">{{ isset($unidad) ? 'Editar Unidad' : 'Crear Nueva Unidad' }}</h4>
                </div>

                <div class="card-body py-4">

                    <form id="unidadForm">
                        @csrf
                        @if(isset($unidad))
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="unidad_id" value="{{ $unidad->id }}">
                        @endif

                        {{-- Unidad Padre --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="git-branch"></i> Unidad Padre
                            </label>
                            <select name="unidad_padre_id" id="unidad_padre_id" class="form-select">
                                <option value="">-- Ninguna --</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" {{ isset($unidad) && $unidad->unidad_padre_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>


                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="bookmark"></i> Nombre de la Unidad
                            </label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="{{ $unidad->nombre ?? '' }}"
                                oninput="this.value=this.value.toUpperCase();generateCodigo(this.value);onlyLetters(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Jefe --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="user"></i> Jefe / Responsable
                            </label>
                            <input type="text" name="jefe" id="jefe" class="form-control" value="{{ $unidad->jefe ?? '' }}"
                                oninput="this.value=this.value.toUpperCase();onlyLetters(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Código + Teléfono + Interno --}}
                        <div class="row g-3 mb-3">

                            <div class="col-md-4">
                                <label class="form-label d-flex align-items-center gap-2">
                                    <i data-feather="key"></i> Código
                                </label>
                                <input type="text" name="codigo" id="codigo" class="form-control" readonly
                                    value="{{ $unidad->codigo ?? '' }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label d-flex align-items-center gap-2">
                                    <i data-feather="phone"></i> Teléfono
                                </label>
                                <input type="text" name="telefono" id="telefono" class="form-control"
                                    value="{{ $unidad->telefono ?? '' }}" oninput="onlyNumbers(this);">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label d-flex align-items-center gap-2">
                                    <i data-feather="hash"></i> Interno
                                </label>
                                <input type="text" name="interno" id="interno" class="form-control"
                                    value="{{ $unidad->interno ?? '' }}" oninput="onlyNumbers(this);">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        {{-- Nivel --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="align-left"></i> Nivel Jerárquico
                            </label>
                            <input type="text" name="nivel" id="nivel" class="form-control"
                                value="{{ $unidad->nivel ?? 1 }}" oninput="onlyNumbers(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Estado --}}
                        @if(isset($unidad))
                            <div class="mb-3">
                                <label class="form-label d-flex align-items-center gap-2">
                                    <i data-feather="flag"></i> Estado
                                </label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="ACTIVO" {{ $unidad->estado == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                                    <option value="ANULADO" {{ $unidad->estado == 'ANULADO' ? 'selected' : '' }}>ANULADO</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        @endif

                        {{-- Botón Guardar --}}
                        <button type="submit" id="saveUnidadBtn"
                            class="btn btn-primary  d-flex justify-content-center align-items-center gap-2 py-2">

                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>

                            <i data-feather="check-circle"></i>
                            <span class="btn-text">
                                {{ isset($unidad) ? 'Actualizar Unidad' : 'Crear Unidad' }}
                            </span>
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/unidades/form.js') }}"></script>
    <script src="{{ asset('js/unidades/form-code.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#unidad_padre_id').select2({
                placeholder: "-- Ninguna --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <script>

        feather.replace();
    </script>
@endsection