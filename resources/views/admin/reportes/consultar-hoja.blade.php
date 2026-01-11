@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- ENCABEZADO --}}
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-1">Consulta de Hoja de Ruta</h4>
                <span class="text-muted">
                    Detalle general y seguimiento de derivaciones
                </span>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form id="consultaHojaForm">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label class="form-label">Número General <small class="text-danger">*</small> </label>
                            <input type="text" name="numero" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Unidad <small class="text-danger">*</small></label>
                            <select name="unidad_id" class="form-select select2">
                                <option value="">— Seleccione —</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>

                            {{-- feedback específico para select2 --}}
                            <div class="invalid-feedback d-block select2-feedback"></div>
                        </div>


                        <div class="col-md-4">
                            <label class="form-label">Fecha <small class="text-danger">*</small></label>
                            <input type="date" name="fecha" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" id="buscarHojaBtn" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm d-none"></span>
                                <span class="btn-text">Buscar Hoja</span>
                            </button>
                            <button type="button" id="limpiarHojaBtn" class="btn btn-secondary">
                                Limpiar
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- RESULTADO AJAX --}}
        <div id="resultadoHoja"></div>

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            /* ================= SELECT2 ================= */
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: "Seleccione una unidad",
                allowClear: true,
                width: '100%'
            });

            /* ================= UTILIDADES ================= */
            function limpiarError(input) {
                input.removeClass('is-invalid');
                input.next('.invalid-feedback').text('');

                if (input.hasClass('select2-hidden-accessible')) {
                    input.next('.select2-container')
                        .find('.select2-selection')
                        .removeClass('is-invalid');

                    input.closest('.col-md-4')
                        .find('.select2-feedback')
                        .text('');
                }
            }

            $('#consultaHojaForm').on('input change', 'input, select', function () {
                limpiarError($(this));
            });

            $('#limpiarHojaBtn').on('click', function () {
                const form = $('#consultaHojaForm');

                form[0].reset();

                // Reset Select2
                form.find('.select2').val(null).trigger('change');

                form.find('.invalid-feedback').text('');
                form.find('.is-invalid').removeClass('is-invalid');

                $('#resultadoHoja').html('');
            });

            $('#consultaHojaForm').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(this);
                const resultado = $('#resultadoHoja');

                const btn = $('#buscarHojaBtn');
                const spinner = btn.find('.spinner-border');
                const btnText = btn.find('.btn-text');

                /* DEBUG */
                const formObj = {};
                formData.forEach((value, key) => { formObj[key] = value });
                console.log("Datos enviados al backend:", formObj);

                /* Limpiar errores */
                form.find('.invalid-feedback').text('');
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.select2-selection').removeClass('is-invalid');

                /* Spinner */
                btn.prop('disabled', true);
                spinner.removeClass('d-none');
                btnText.addClass('opacity-50');

                resultado.html(`
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="spinner-border text-primary mb-3"></div>
                                                <div class="fw-semibold">Buscando hoja de ruta...</div>
                                            </div>
                                        </div>
                                    `);

                /* AJAX */
                $.ajax({
                    url: "",
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

                                // INPUT NORMAL
                                if (!input.hasClass('select2-hidden-accessible')) {
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(messages[0]);
                                }

                                // SELECT2
                                if (input.hasClass('select2-hidden-accessible')) {
                                    input.next('.select2-container')
                                        .find('.select2-selection')
                                        .addClass('is-invalid');

                                    input.closest('.col-md-4')
                                        .find('.select2-feedback')
                                        .text(messages[0]);
                                }
                            });

                            resultado.html('');
                        }
                        else {
                            resultado.html(`
                                                    <div class="alert alert-danger">
                                                        Ocurrió un error al buscar la hoja de ruta.
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