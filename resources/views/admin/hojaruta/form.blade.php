@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="file-text"></i>
            {{ isset($hoja) ? 'Editar Hoja de Ruta' : 'Crear Nueva Hoja de Ruta' }}
        </h3>

        <a href="{{ route('admin.hojaruta.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <i data-feather="arrow-left"></i>
            <span class="d-none d-md-inline">Volver</span>
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center gap-2">
                    <i data-feather="edit-3"></i>
                    <h4 class="mb-0">{{ isset($hoja) ? 'Editar Hoja de Ruta' : 'Crear Nueva Hoja de Ruta' }}</h4>
                </div>

                <div class="card-body py-4">

                    <form id="hojarutaForm">
                        @csrf

                        @if(isset($hoja))
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="hoja_id" value="{{ $hoja->id }}">
                        @endif

                        {{-- Unidad Origen --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="layers"></i> Unidad Origen
                            </label>
                            <select name="unidad_origen_id" id="unidad_origen_id" class="form-select">
                                <option value="">-- Seleccione Unidad --</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" {{ isset($hoja) && $hoja->unidad_origen_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Solicitante --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="user"></i> Solicitante
                            </label>
                            <select name="solicitante_id" id="solicitante_id" class="form-select">
                                <option value="">-- Seleccione Solicitante --</option>
                                @foreach($funcionarios as $f)
                                    <option value="{{ $f->id }}" {{ isset($hoja) && $hoja->solicitante_id == $f->id ? 'selected' : '' }}>
                                        {{ $f->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Asunto --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="file-text"></i> Asunto
                            </label>
                            <input type="text" name="asunto" id="asunto" class="form-control"
                                value="{{ $hoja->asunto ?? '' }}" oninput="this.value=this.value.toUpperCase();">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Prioridad --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="flag"></i> Prioridad
                            </label>
                            <select name="prioridad" id="prioridad" class="form-select">
                                <option value="normal" {{ isset($hoja) && $hoja->prioridad == 'normal' ? 'selected' : '' }}>
                                    Normal</option>
                                <option value="urgente" {{ isset($hoja) && $hoja->prioridad == 'urgente' ? 'selected' : '' }}>
                                    Urgente</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Fecha Creación --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="calendar"></i> Fecha de Creación
                            </label>
                            <input type="date" name="fecha_creacion" id="fecha_creacion" class="form-control"
                                value="{{ isset($hoja) ? $hoja->fecha_creacion->format('Y-m-d') : now()->format('Y-m-d') }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Fojas --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="file"></i> Fojas
                            </label>
                            <input type="number" name="fojas" id="fojas" class="form-control"
                                value="{{ $hoja->fojas ?? '' }}" min="1">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Botón Guardar --}}
                        <button type="submit" id="saveHojaRutaBtn"
                            class="btn btn-primary d-flex justify-content-center align-items-center gap-2 py-2">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            <i data-feather="check-circle"></i>
                            <span class="btn-text">
                                {{ isset($hoja) ? 'Actualizar Hoja de Ruta' : 'Crear Hoja de Ruta' }}
                            </span>
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#unidad_origen_id, #solicitante_id, #prioridad').select2({
                placeholder: "-- Seleccione --",
                allowClear: true,
                width: '100%'
            });

            feather.replace();
        });
    </script>
    <script src="{{ asset('js/hojaruta/form.js') }}"></script>
@endsection