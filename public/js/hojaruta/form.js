$(document).ready(function () {

    /* ===============================
       UTIL: LIMPIAR ERRORES
    =============================== */
    function limpiarError(input) {
        input.removeClass('is-invalid');

        if (input.hasClass('select2-hidden-accessible')) {
            input.parent().find('.invalid-feedback').text('');
        } else {
            input.next('.invalid-feedback').text('');
        }
    }

    /* ===============================
       LIMPIAR ERRORES AL ESCRIBIR
    =============================== */
    $('#hojarutaForm').on('input change', 'input, select', function () {
        limpiarError($(this));
    });

    /* ===============================
       EXTERNO / INTERNO TOGGLE
    =============================== */
    function toggleExterno() {
        if ($('#externo').is(':checked')) {
            $('#externoBox').removeClass('d-none');
            $('#unidadBox, #funcionarioBox').addClass('d-none');
            $('#unidad_origen_id, #solicitante_id').val(null).trigger('change');
        } else {
            $('#externoBox').addClass('d-none');
            $('#unidadBox').removeClass('d-none');
            $('#nombre_solicitante').val('');
        }
    }

    $('#externo').on('change', toggleExterno);
    toggleExterno(); // al cargar (editar)

    /* ===============================
       CARGAR FUNCIONARIOS POR UNIDAD
    =============================== */
    $('#unidad_origen_id').on('change', function () {
        const unidadId = $(this).val();

        $('#funcionarioBox').addClass('d-none');
        $('#solicitante_id').empty().append('<option value="">-- Seleccione Funcionario --</option>');

        if (!unidadId) return;

        $.ajax({
            url: `/admin/hojaruta/${unidadId}/funcionarios`,
            type: 'GET',
            success: function (data) {
                data.forEach(f => {
                    $('#solicitante_id').append(
                        `<option value="${f.id}">${f.nombre}</option>`
                    );
                });
                $('#funcionarioBox').removeClass('d-none');
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los funcionarios',
                    toast: true,
                    position: 'top',
                    timer: 4000,
                    showConfirmButton: false,
                    ...swalStyles()
                });
            }
        });
    });

    /* ===============================
       SUBMIT FORM
    =============================== */
    $('#hojarutaForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const hojaId = $('#hoja_id').val();

        const url = hojaId
            ? `/admin/hojaruta/${hojaId}`
            : `/admin/hojaruta`;

        if (hojaId) {
            formData.append('_method', 'PUT');
        }

        const btn = form.find('button[type="submit"]');
        const spinner = btn.find('.spinner-border');
        const btnText = btn.find('.btn-text');

        // limpiar errores previos
        form.find('.invalid-feedback').text('');
        form.find('.is-invalid').removeClass('is-invalid');

        // UI loading
        btn.prop('disabled', true);
        spinner?.removeClass('d-none');
        btnText?.addClass('opacity-50');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

            success: function (data) {
                btn.prop('disabled', false);
                spinner?.addClass('d-none');
                btnText?.removeClass('opacity-50');

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message || (hojaId ? 'Hoja de Ruta actualizada' : 'Hoja de Ruta creada'),
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'center',
                    ...swalStyles()
                }).then(() => {
                    window.location.href = hojaId
                        ? '/admin/buzon/salida'
                        : `/admin/hojaruta/${data.hoja.id}/derivaciones/crear`;

                });
            },

            error: function (xhr) {
                btn.prop('disabled', false);
                spinner?.addClass('d-none');
                btnText?.removeClass('opacity-50');

                let message = 'Error desconocido';

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    $.each(errors, function (field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');

                        if (input.hasClass('select2-hidden-accessible')) {
                            input.parent().find('.invalid-feedback').text(messages[0]);
                        } else {
                            input.next('.invalid-feedback').text(messages[0]);
                        }
                    });

                    message = 'Corrige los errores del formulario.';
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: message,
                    toast: true,
                    position: 'top',
                    timer: 5000,
                    showConfirmButton: false,
                    ...swalStyles()
                });

                console.error('Error servidor:', xhr);
            }
        });
    });

});
