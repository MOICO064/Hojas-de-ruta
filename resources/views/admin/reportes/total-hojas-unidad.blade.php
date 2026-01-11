@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Reporte: Total de Hojas por Gestión</h4>
            <span class="text-muted">Filtra por gestión y mes</span>
        </div>
    </div>

    {{-- FORMULARIO DE BÚSQUEDA --}}
    <div class="card mb-4">
        <div class="card-body">
            <form id="reporteHojasForm">
                <div class="row g-3 align-items-end">

                    {{-- Gestión --}}
                    <div class="col-md-6">
                        <label class="form-label">Gestión <small class="text-danger">*</small></label>
                        <select name="gestion" class="form-select select2">
                            <option value="">— Seleccione —</option>
                            @for($y = date('Y'); $y >= 2000; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                        <div class="invalid-feedback d-block select2-feedback"></div>
                    </div>

                    {{-- Mes (opcional) --}}
                    <div class="col-md-6">
                        <label class="form-label">Mes <small class="text-muted">(opcional)</small> </label>
                        <select name="mes" class="form-select select2">
                            <option value="">— Todos —</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">
                                    {{ \Carbon\Carbon::create()->locale('es')->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block select2-feedback"></div>
                    </div>

                    {{-- Botones --}}
                    <div class="col-12 mt-2 d-flex gap-2">
                        <button type="submit" id="buscarReporteBtn" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm d-none"></span>
                            <span class="btn-text">Buscar</span>
                        </button>
                        <button type="button" id="limpiarReporteBtn" class="btn btn-secondary">Limpiar</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- RESULTADOS --}}
    <div id="resultadoReporte"></div>

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
    function limpiarError(input){
        input.removeClass('is-invalid');

        if(input.hasClass('select2-hidden-accessible')){
            input.next('.select2-container')
                .find('.select2-selection')
                .removeClass('is-invalid');

            input.closest('.col-md-6')
                .find('.select2-feedback')
                .text('');
        } else {
            input.next('.invalid-feedback').text('');
        }
    }

    function marcarError(input, mensaje){
        if(input.hasClass('select2-hidden-accessible')){
            input.next('.select2-container')
                .find('.select2-selection')
                .addClass('is-invalid');

            input.closest('.col-md-6')
                .find('.select2-feedback')
                .text(mensaje);
        } else {
            input.addClass('is-invalid');
            input.next('.invalid-feedback').text(mensaje);
        }
    }

    /* ================= LIMPIAR BOTÓN ================= */
    $('#limpiarReporteBtn').click(function(){
        const form = $('#reporteHojasForm');
        form[0].reset();
        form.find('.select2').val(null).trigger('change');

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        $('.select2-selection').removeClass('is-invalid');
        $('.select2-feedback').text('');

        $('#resultadoReporte').html('');
    });

    /* ================= SUBMIT ================= */
    $('#reporteHojasForm').submit(function(e){
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const resultado = $('#resultadoReporte');
        const btn = $('#buscarReporteBtn');
        const spinner = btn.find('.spinner-border');
        const btnText = btn.find('.btn-text');

        // Limpiar errores previos
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        $('.select2-selection').removeClass('is-invalid');
        $('.select2-feedback').text('');

        // Spinner
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.addClass('opacity-50');

        resultado.html(`<div class="card text-center"><div class="card-body"><div class="spinner-border text-primary mb-3"></div>Buscando...</div></div>`);

        $.ajax({
            url: "{{ route('admin.reportes.hojas-total-unidades-ajax') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                resultado.hide().html(response.html).fadeIn(300);
            },
            error: function(xhr){
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                if(xhr.status === 422){
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, msgs){
                        const input = form.find('[name="'+field+'"]');
                        marcarError(input, msgs[0]);
                    });
                    resultado.html('');
                } else {
                    resultado.html('<div class="alert alert-danger">Error al generar el reporte.</div>');
                    console.error(xhr);
                }
            }
        });
    });

});
</script>
@endsection
