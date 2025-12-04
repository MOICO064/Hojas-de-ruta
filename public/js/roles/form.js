$(document).ready(function () {
    feather.replace();

    $('#roleForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const roleId = $('#role_id').val();
        const url = roleId ? `/admin/roles-permisos/roles/${roleId}` : `/admin/roles-permisos/roles`;
        const method = roleId ? 'POST' : 'POST';
        if (roleId) form.find('input[name="_method"]').val('PUT');

        const btn = $('#saveRoleBtn');
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

                if (!roleId) {

                    Swal.fire({
                        icon: 'success',
                        title: '¡Rol creado!',
                        text: data.message || 'El rol se creó correctamente.',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, gestionar permisos',
                        cancelButtonText: 'No, ir al listado',
                        allowOutsideClick: false,
                        ...swalStyles()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `/admin/roles-permisos/roles/${data.role_id}/permisos`;
                        } else {
                            window.location.href = '/admin/roles-permisos/roles';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Rol actualizado!',
                        text: data.message || 'El rol se actualizó correctamente.',
                        timer: 2000,                
                        timerProgressBar: true,     
                        showConfirmButton: false,   
                        allowOutsideClick: false,   
                        allowEscapeKey: false,      
                        allowEnterKey: false,       
                        ...swalStyles()
                    }).then(() => {
                        window.location.href = '/admin/roles-permisos/roles';
                    });


                }
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
                        showConfirmButton: false
                    });
                }
            }
        });
    });
});
