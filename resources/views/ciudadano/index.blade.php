<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="MPH">

    <title>Consulta de Hoja de Ruta - Ciudadano</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/recurces/escudo.png') }}" type="image/x-icon">

    <!-- Color modes -->
    <script src="{{ asset('assets/js/vendors/color-modes.js') }}"></script>

    <!-- Libs CSS -->
    <link href="{{ asset('assets/libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/@mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet">
 <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }
    </style>
</head>

<body>
    <main class="container d-flex flex-column min-vh-100 justify-content-center py-5">
<div class="position-absolute end-0 top-0 p-8">
                    <div class="dropdown">
                        <button class="btn btn-ghost btn-icon rounded-circle" type="button" aria-expanded="false"
                            data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
                            <i class="bi theme-icon-active"></i>
                            <span class="visually-hidden bs-theme-text">Toggle theme</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bs-theme-text">
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="light" aria-pressed="false">
                                    <i class="bi theme-icon bi-sun-fill"></i>
                                    <span class="ms-2">Light</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="dark" aria-pressed="false">
                                    <i class="bi theme-icon bi-moon-stars-fill"></i>
                                    <span class="ms-2">Dark</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center active"
                                    data-bs-theme-value="auto" aria-pressed="true">
                                    <i class="bi theme-icon bi-circle-half"></i>
                                    <span class="ms-2">Auto</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
        <div class="text-center mb-5">
            <a href="{{ route('login') }}">
                <img src="{{ asset('assets/images/recurces/icono_sistema.png') }}" style="width:200px;" alt="Logo" class="mb-2 text-inverse">
            </a>
            <h3 class="mt-3">Consulta de Hoja de Ruta</h3>
            <p class="text-muted">Detalle general y seguimiento de derivaciones</p>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form id="consultaHojaForm">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label class="form-label">Número General <small class="text-danger">*</small></label>
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
                            <div class="invalid-feedback d-block select2-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha <small class="text-danger">*</small></label>
                            <input type="date" name="fecha" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" id="buscarHojaBtn" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm d-none"></span>
                                <span class="btn-text">Buscar Hoja</span>
                            </button>
                            <button type="button" id="limpiarHojaBtn" class="btn btn-secondary">Limpiar</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- Resultado AJAX -->
        <div id="resultadoHoja" class="mt-4"></div>

    </main>

    <!-- Libs JS -->
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/dist/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/theme.min.js') }}"></script>
 <script>
        $(document).ready(function () {

            function aplicarTemaSelect2() {
                const isDark = $('html').attr('data-bs-theme') === 'dark';

                $('#select2-theme-styles').remove();

                if (isDark) {
                    const darkCss = `
                /* Fondo del select */
                .select2-container--bootstrap-5 .select2-selection,
                .select2-container--bootstrap-5.select2-container--open {
                    background-color: #0F172A !important;
                    color: #64748B !important;
                    border: 1px solid #0F172A !important;
                }

                /* Texto del valor seleccionado */
                .select2-container--bootstrap-5 .select2-selection__rendered {
                    color: #64748B !important;
                }

                /* Placeholder */
                .select2-container--bootstrap-5 .select2-selection__placeholder {
                    color: #64748B !important;
                }

                /* Dropdown items */
                .select2-container--bootstrap-5 .select2-results__option {
                    background-color: #0F172A !important;
                    color: #64748B !important;
                }

                /* Hover en los items */
                .select2-container--bootstrap-5 .select2-results__option--highlighted {
                    background-color: #8ad7e6 !important;
                    color: #64748B !important;
                }

                /* Dropdown completo */
                .select2-container--open .select2-dropdown {
                    background-color: #0F172A !important;
                    color: #64748B !important;
                }

                /* Borde rojo si hay error */
                .select2-selection.is-invalid {
                    border-color: #dc3545 !important;
                }
            `;

                    $('head').append(`<style id="select2-theme-styles">${darkCss}</style>`);
                } else {
                    
                }
            }

     
            aplicarTemaSelect2();

            const observer = new MutationObserver(() => {
                aplicarTemaSelect2();
            });

            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });

        });
    </script>
    <script>
        $(document).ready(function () {

            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: "Seleccione una unidad",
                allowClear: true,
                width: '100%'
            });

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

                $.ajax({
                    url: "{{ route('consultar.hoja.resultado') }}",
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

                                if (!input.hasClass('select2-hidden-accessible')) {
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(messages[0]);
                                }

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
                        } else {
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
</body>

</html>
