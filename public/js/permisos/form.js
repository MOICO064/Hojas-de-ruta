$(document).ready(function () {
    feather.replace();

    $('#permisoForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const permisoId = $('#permiso_id').val(); 
        const url = permisoId ? `/admin/roles-permisos/permisos/${permisoId}` : `/admin/roles-permisos/permisos`;
        const method = permisoId ? 'PUT' : 'POST';

        const btn = $('#savePermisoBtn');
        const spinner = btn.find('.spinner-border');
        const btnText = btn.find('.btn-text');

        // Limpiar errores
        form.find('.invalid-feedback').text('');
        form.find('.is-invalid').removeClass('is-invalid');

        // Spinner
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.addClass('opacity-50');

        $.ajax({
            url: url,
            type: method,
            data: form.serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                Swal.fire({
                    icon: 'success',
                    title: permisoId ? '¡Permiso actualizado!' : '¡Permiso creado!',
                    text: data.message || (permisoId ? 'El permiso se actualizó correctamente.' : 'El permiso se creó correctamente.'),
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    ...swalStyles()
                }).then(() => {
                    window.location.href = '/admin/roles-permisos/permisos';
                });
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        const input = form.find('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    let message = xhr.responseJSON?.message || 'Ocurrió un error inesperado.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        toast: true,
                        position: 'top',
                        timer: 4000,
                        showConfirmButton: false,
                        ...swalStyles()
                    });
                }
            }
        });
    });
});
