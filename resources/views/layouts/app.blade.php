<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="author" content="Codescandy" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-M8S4MT3EYG"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-M8S4MT3EYG');
    </script>


    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/recurces/escudo.png') }}" />

    <!-- Color modes -->
    <script src="{{ asset('assets/js/vendors/color-modes.js') }}"></script>

    <!-- Libs CSS -->
    <link href="{{ asset('assets/libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/@mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">

    <!-- CDN DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <!-- SweetAlert2 CSS (opcional, ya viene integrado en el JS) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap5-theme@1.5.2/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <!-- Filepond-->
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js">
    </script>
    <style>
        .sniper-loading {
            opacity: 0.8;
            transform: scale(0.98);
            transition: all 0.3s ease;
        }
    </style>
    <title>GAMC - Hojas de Ruta</title>
</head>

<body>
    <main id="main-wrapper" class="main-wrapper">
        <div class="header">
            @include('components.navbar')
        </div>

        @include('components.sidebar')
        <!-- page content -->
        <div id="app-content">

            <div class="app-content-area">

                @yield('content')
            </div>
        </div>

    </main>




    <!-- Libs JS -->

    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/dist/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>


    <!-- Theme JS -->
    <script src="{{ asset('assets/js/theme.min.js') }}"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script src="{{ asset('js/swalAlert/theme.js') }}"></script>

    <script>
        $(document).ready(function () {

            function aplicarTemaSelect2() {
                const isDark = $('html').attr('data-bs-theme') === 'dark';

                // Elimina estilos previos para evitar duplicados
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
                    // Light: eliminas dark y Select2 usa tema bootstrap-light normal
                    // Si quieres, aquí puedes agregar overrides para light
                }
            }

            // Aplicar tema al cargar la página
            aplicarTemaSelect2();

            // Detectar cambios en data-bs-theme (toggle Light/Dark/Auto)
            const observer = new MutationObserver(() => {
                aplicarTemaSelect2();
            });

            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });

        });
    </script>

    @yield('scripts')
    <script src="{{ asset('js/notificaciones/notificaciones.js')}}"></script>
    <script>
        function sniperBackup(btn) {

            Swal.fire({
                title: 'Sacando backup ',
                text: 'Por favor espere, no cierre esta ventana...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                ...swalStyles()
            });

            fetch("{{ route('admin.backup.full') }}", {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(async response => {

                    if (!response.ok) {

                        let errorMessage = 'Error desconocido al generar el backup';

                        const contentType = response.headers.get('content-type');

                        if (contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            errorMessage = data.message ?? JSON.stringify(data);
                        } else {
                            const text = await response.text();
                            if (text) {
                                errorMessage = text.substring(0, 800);
                            }
                        }

                        throw new Error(errorMessage);
                    }

                    return response.blob();
                })
                .then(blob => {
                    Swal.close();

                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;

                    const now = new Date().toISOString().replace(/[:T]/g, '-').split('.')[0];
                    a.download = `backup-${now}.sql`;

                    document.body.appendChild(a);
                    a.click();
                    a.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Backup generado',
                        text: 'La base de datos fue respaldada correctamente',
                        timer: 2500,
                        showConfirmButton: false,
                        ...swalStyles()
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al sacar backup',
                        html: `<pre style="text-align:left; white-space:pre-wrap;">${escapeHtml(error.message)}</pre>`,
                        ...swalStyles()
                    });
                });
        }

        // Evita que HTML roto rompa el Swal
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;");
        }
    </script>



</body>

</html>