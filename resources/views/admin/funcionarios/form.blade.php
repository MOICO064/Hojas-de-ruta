@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="user-check"></i>
            {{ isset($funcionario) ? 'Editar Funcionario' : 'Crear Nuevo Funcionario' }}
        </h3>

        <a href="{{ route('admin.funcionarios.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
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
                        {{ isset($funcionario) ? 'Editar Funcionario' : 'Crear Nuevo Funcionario' }}
                    </h4>
                </div>

                <div class="card-body py-4">

                    <form id="funcionarioForm">
                        @csrf

                        @if(isset($funcionario))
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="funcionario_id" value="{{ $funcionario->id }}">
                        @endif

                        {{-- Unidad --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="layers"></i> Unidad
                            </label>
                            <select name="unidad_id" id="unidad_id" class="form-select">
                                <option value="">-- Seleccione una Unidad --</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" {{ isset($funcionario) && $funcionario->unidad_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="user"></i> Nombre Completo
                            </label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="{{ $funcionario->nombre ?? '' }}" oninput="this.value=this.value.toUpperCase();">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- CI --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="credit-card"></i> Cédula de Identidad
                            </label>
                            <input type="text" name="ci" id="ci" class="form-control" value="{{ $funcionario->ci ?? '' }}"
                                oninput="onlyNumbers(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Cargo --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="briefcase"></i> Cargo
                            </label>
                            <input type="text" name="cargo" id="cargo" class="form-control"
                                value="{{ $funcionario->cargo ?? '' }}" oninput="this.value=this.value.toUpperCase();">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Número de Ítem --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="hash"></i> Nº Ítem
                            </label>
                            <input type="text" name="nro_item" id="nro_item" class="form-control"
                                value="{{ $funcionario->nro_item ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Celular --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-2">
                                <i data-feather="phone"></i> Celular
                            </label>
                            <input type="text" name="celular" id="celular" class="form-control"
                                value="{{ $funcionario->celular ?? '' }}" oninput="onlyNumbers(this);">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Estado (solo editar) --}}
                        @if(isset($funcionario))
                            <div class="mb-3">
                                <label class="form-label d-flex align-items-center gap-2">
                                    <i data-feather="flag"></i> Estado
                                </label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="ACTIVO" {{ $funcionario->estado == 'ACTIVO' ? 'selected' : '' }}>ACTIVO
                                    </option>
                                    <option value="ANULADO" {{ $funcionario->estado == 'ANULADO' ? 'selected' : '' }}>ANULADO
                                    </option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        @endif

                        {{-- Guardar --}}
                        <button type="submit" id="saveFuncionarioBtn"
                            class="btn btn-primary d-flex justify-content-center align-items-center gap-2 py-2">

                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>

                            <i data-feather="check-circle"></i>
                            <span class="btn-text">
                                {{ isset($funcionario) ? 'Actualizar Funcionario' : 'Crear Funcionario' }}
                            </span>
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/funcionarios/form.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#unidad_id').select2({
                placeholder: "-- Seleccione una Unidad --",
                allowClear: true,
                width: '100%'
            });
        });

        feather.replace();
    </script>
@endsection