@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0">{{ isset($unidad) ? 'Editar Unidad' : 'Crear Nueva Unidad' }}</h3>

        <div class="d-flex gap-2">
            <!-- Botón Volver a Lista de Unidades -->
            <a href="{{ route('admin.unidades.index') }}" class="btn btn-primary d-flex align-items-center"
                title="Volver a Lista de Unidades">
                <i data-feather="arrow-left" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Lista de Unidades</span>
                <i data-feather="arrow-left" class="d-inline d-md-none"></i>
            </a>


        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ isset($unidad) ? 'Editar Unidad' : 'Crear Nueva Unidad' }}</h4>
                </div>
                <div class="card-body">
                    <form id="unidadForm">
                        @csrf
                        @if(isset($unidad))
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="unidad_id" value="{{ $unidad->id }}">
                        @endif

                        <div class="mb-3">
                            <label for="unidad_padre_id" class="form-label">Unidad Padre</label>
                            <select name="unidad_padre_id" id="unidad_padre_id" class="form-select">
                                <option value="">-- Ninguna --</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" {{ (isset($unidad) && $unidad->unidad_padre_id == $u->id) ? 'selected' : '' }}>
                                        {{ $u->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="{{ $unidad->nombre ?? '' }}"
                                oninput="this.value = this.value.toUpperCase(); generateCodigo(this.value); onlyLetters(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="jefe" class="form-label">Jefe</label>
                            <input type="text" name="jefe" id="jefe" class="form-control" value="{{ $unidad->jefe ?? '' }}"
                                oninput="this.value = this.value.toUpperCase(); onlyLetters(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" name="codigo" id="codigo" class="form-control" readonly
                                    value="{{ $unidad->codigo ?? '' }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control"
                                    value="{{ $unidad->telefono ?? '' }}" oninput="onlyNumbers(this);">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="interno" class="form-label">Interno</label>
                                <input type="text" name="interno" id="interno" class="form-control"
                                    value="{{ $unidad->interno ?? '' }}" oninput="onlyNumbers(this);">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nivel" class="form-label">Nivel</label>
                            <input type="text" name="nivel" id="nivel" class="form-control"
                                value="{{ $unidad->nivel ?? 1 }}" oninput="onlyNumbers(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        @if(isset($unidad))
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="ACTIVO" {{ $unidad->estado == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                                    <option value="ANULADO" {{ $unidad->estado == 'ANULADO' ? 'selected' : '' }}>ANULADO</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        @endif

                        <button type="submit" id="saveUnidadBtn" class="btn btn-primary d-flex align-items-center">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                aria-hidden="true"></span>
                            <span class="btn-text">{{ isset($unidad) ? 'Actualizar Unidad' : 'Crear Unidad' }}</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('js/unidades/form.js') }}"></script>
@endsection