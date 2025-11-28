$(document).ready(function () {

    // Limpiar errores de un input
    function limpiarError(input) {
        input.removeClass('is-invalid');
        input.next('.invalid-feedback').text('');
    }

    // Limpiar errores al modificar inputs
    $('#unidadForm').on('input change', 'input, select', function () {
        limpiarError($(this));
    });

    $('#unidadForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const unidadId = $('#unidad_id').val();
        const url = unidadId ? `/admin/unidades/${unidadId}` : `/admin/unidades`;

        if (unidadId) {
            formData.append('_method', 'PUT');
        }

        const btn = $('#saveUnidadBtn');
        const spinner = btn.find('.spinner-border');
        const btnText = btn.find('.btn-text');

        // Limpiar errores anteriores
        form.find('.invalid-feedback').text('');
        form.find('.is-invalid').removeClass('is-invalid');

        // Mostrar spinner y deshabilitar botón
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.addClass('opacity-50');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                // Detener spinner
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message || (unidadId ? 'Unidad actualizada correctamente' : 'Unidad creada con éxito'),
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                }).then(() => {
                    window.location.href = '/admin/unidades';
                });
            },
            error: function (xhr) {
                // Detener spinner
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                let message = 'Error desconocido';

                // Errores de validación
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        const input = form.find('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(messages[0]);
                    });
                    message = 'Por favor corrige los errores en el formulario.';
                } else {
                    // Otros errores del servidor
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON) {
                        message = JSON.stringify(xhr.responseJSON, null, 2);
                    } else {
                        message = xhr.status + ' - ' + xhr.statusText;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: message.replace(/\n/g, '<br>'),
                    toast: true,
                    position: 'top',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    width: '400px',
                    customClass: {
                        popup: 'swal2-toast-width'
                    },
                    ...swalStyles()
                });


                console.error('Error completo del servidor:', xhr);
            }
        });
    });
});
