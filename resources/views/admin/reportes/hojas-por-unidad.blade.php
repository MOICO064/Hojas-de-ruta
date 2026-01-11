@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Reportes: Hojas por Unidad</h4>
            <span class="text-muted">
                Filtra las hojas de ruta según unidad, gestión y fecha
            </span>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <div class="card mb-4">
        <div class="card-body">
            <form id="buscarHojasUnidadForm">
                <div class="row g-3 align-items-end">

                    {{-- UNIDAD --}}
                    <div class="col-md-4">
                        <label class="form-label">Unidad <small class="text-danger">*</small> </label>
                        <select name="unidad_id" class="form-select select2">
                            <option value="">— Seleccione —</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" style="display:none"></div>
                    </div>

                    {{-- GESTIÓN --}}
                    <div class="col-md-4">
                        <label class="form-label">Gestión <small class="text-danger">*</small></label>
                        <select name="gestion" class="form-select select2">
                            <option value="">— Seleccione —</option>
                            @for($y = date('Y'); $y >= 2000; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                        <div class="invalid-feedback" style="display:none"></div>
                    </div>

                    {{-- FECHA --}}
                    <div class="col-md-4">
                        <label class="form-label">Fecha <small class="text-muted">(opcional)</small> </label>
                        <input type="date" name="fecha" class="form-control">
                        <div class="invalid-feedback" style="display:none"></div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-12 mt-2 d-flex gap-2">
                        <button type="submit" id="buscarHojasBtn" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm d-none"></span>
                            <span class="btn-text">Buscar</span>
                        </button>
                        <button type="button" id="limpiarHojasBtn" class="btn btn-secondary">
                            Limpiar
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- RESULTADOS --}}
    <div id="resultadoHojasUnidad"></div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    /* ================= SELECT2 ================= */
    $('.select2').select2({
         theme: 'bootstrap-5',
        placeholder: "Seleccione una opción",
        allowClear: true,
        width: '100%'
    });

    /* ================= UTILIDADES ================= */
    function limpiarError(input) {
        const group = input.closest('.col-md-4');

        input.removeClass('is-invalid');
        group.find('.invalid-feedback').text('').hide();

        if (input.hasClass('select2-hidden-accessible')) {
            input.next('.select2-container')
                .find('.select2-selection')
                .removeClass('is-invalid');
        }
    }

    function marcarError(input, mensaje) {
        const group = input.closest('.col-md-4');

        group.find('.invalid-feedback')
            .text(mensaje)
            .show();

        input.addClass('is-invalid');

        if (input.hasClass('select2-hidden-accessible')) {
            input.next('.select2-container')
                .find('.select2-selection')
                .addClass('is-invalid');
        }
    }

    /* ================= LIMPIAR ERRORES AL CAMBIAR ================= */
    $('#buscarHojasUnidadForm').on('change input', 'input, select', function () {
        limpiarError($(this));
    });

    /* ================= BOTÓN LIMPIAR ================= */
    $('#limpiarHojasBtn').on('click', function () {
        const form = $('#buscarHojasUnidadForm');

        form[0].reset();
        form.find('.select2').val(null).trigger('change');

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('').hide();
        $('.select2-selection').removeClass('is-invalid');

        $('#resultadoHojasUnidad').html('');
    });

    /* ================= SUBMIT ================= */
    $('#buscarHojasUnidadForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const resultado = $('#resultadoHojasUnidad');

        const btn = $('#buscarHojasBtn');
        const spinner = btn.find('.spinner-border');
        const btnText = btn.find('.btn-text');

        // limpiar errores previos
        form.find('.invalid-feedback').text('').hide();
        form.find('.is-invalid').removeClass('is-invalid');
        $('.select2-selection').removeClass('is-invalid');

        // spinner
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.addClass('opacity-50');

        resultado.html(`
            <div class="card">
                <div class="card-body text-center">
                    <div class="spinner-border text-primary mb-3"></div>
                    <div class="fw-semibold">Buscando hojas...</div>
                </div>
            </div>
        `);

        $.ajax({
            url: "{{ route('admin.reportes.hojas-por-unidad-ajax') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                resultado.hide().html(response.html).fadeIn(300);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    $.each(errors, function (field, messages) {
                        const input = form.find('[name="' + field + '"]');
                        marcarError(input, messages[0]);
                    });

                    resultado.html('');
                } else {
                    resultado.html(`
                        <div class="alert alert-danger">
                            Ocurrió un error al buscar las hojas.
                        </div>
                    `);
                    console.error(xhr);
                }
            }
        });
    });

});
</script>
@endsection
