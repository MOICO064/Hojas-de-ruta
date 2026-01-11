@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <h3 class="mb-0 d-flex align-items-center gap-2">
        <i data-feather="file-text"></i>
        {{ isset($hoja) ? 'Editar Hoja de Ruta' : 'Crear Nueva Hoja de Ruta' }}
    </h3>

    <a href="{{ isset($hoja) ? route('admin.hojaruta.show', $hoja->id) : route('admin.buzon.salida')  }}"
        class="btn btn-outline-primary d-flex align-items-center gap-2">
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

                    {{-- EXTERNO --}}
                    <input type="hidden" name="externo" value="0">
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="externo" name="externo" value="1"
                            {{ isset($hoja) && $hoja->externo ? 'checked' : '' }}>
                        <label class="form-check-label" for="externo">
                            Documento Externo <small class="text-muted">(opcional)</small>
                        </label>
                    </div>

                    {{-- NOMBRE SOLICITANTE EXTERNO --}}
                    <div class="mb-3 {{ isset($hoja) && $hoja->externo ? '' : 'd-none' }}" id="externoBox">
                        <label class="form-label">Nombre del Solicitante Externo <span
                                class="text-danger">*</span></label>
                        <input type="text" name="nombre_solicitante" id="nombre_solicitante" class="form-control"
                            value="{{ $hoja->nombre_solicitante ?? '' }}" oninput="this.value=this.value.toUpperCase()">
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- UNIDAD ORIGEN --}}
                    <div class="mb-3 {{ isset($hoja) && !$hoja->externo ? '' : '' }}" id="unidadBox">
                        <label class="form-label">Unidad <span class="text-danger">*</span></label>
                        <select name="unidad_origen_id" id="unidad_origen_id" class="form-select">
                            <option value="">-- Seleccione Unidad --</option>
                            @foreach($unidades as $u)
                            <option value="{{ $u->id }}"
                                {{ isset($hoja) && $hoja->unidad_origen_id == $u->id ? 'selected' : '' }}>
                                {{ $u->nombre }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- FUNCIONARIO --}}
                    <div class="mb-3 {{ isset($hoja) && !$hoja->externo ? '' : 'd-none' }}" id="funcionarioBox">
                        <label class="form-label">Solicitante (Funcionario) <span class="text-danger">*</span></label>
                        <select name="solicitante_id" id="solicitante_id" class="form-select">
                            <option value="">-- Seleccione Funcionario --</option>
                            @if(isset($hoja) && $hoja->solicitante_id)
                            <option value="{{ $hoja->solicitante_id }}" selected>
                                {{ $hoja->solicitante->nombre ?? '' }}
                            </option>
                            @endif
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- CITE --}}
                    <div class="mb-3">
                        <label class="form-label">CITE <small class="text-muted">(opcional)</small></label>
                        <input type="text" name="cite" class="form-control" value="{{ $hoja->cite ?? '' }}"
                            oninput="this.value=this.value.toUpperCase()">
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- PRIORIDAD --}}
                    <input type="hidden" name="prioridad" value="0">
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="prioridad" id="prioridad" value="1"
                            {{ isset($hoja) && $hoja->urgente ? 'checked' : '' }}>
                        <label class="form-check-label" for="prioridad">
                            Marcar como URGENTE <small class="text-muted">(opcional)</small>
                        </label>
                    </div>

                    {{-- ASUNTO --}}
                    <div class="mb-3">
                        <label class="form-label">Asunto <span class="text-danger">*</span></label>
                        <input type="text" name="asunto" class="form-control"
                            oninput="this.value=this.value.toUpperCase();" value="{{ $hoja->asunto ?? '' }}">
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- BOTÓN --}}
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i data-feather="check-circle"></i>
                        <span>{{ isset($hoja) ? 'Actualizar Hoja de Ruta' : 'Crear Hoja de Ruta' }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#unidad_origen_id, #solicitante_id').select2({
              theme:"bootstrap-5",
        placeholder: "-- Seleccione --",
        allowClear: true,
        width: '100%'
    });


    if ($('#externo').is(':checked')) {
        $('#externoBox').removeClass('d-none');
        $('#unidadBox').addClass('d-none');
    }

    feather.replace();
});
</script>
<script src="{{ asset('js/hojaruta/form.js') }}"></script>
@endsection