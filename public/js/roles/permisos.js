document.addEventListener('DOMContentLoaded', function () {

    // Evento al cambiar cualquier switch de permiso
    $('.permiso-switch').on('change', function () {
        const switchInput = $(this);
        const permisoId = switchInput.data('permiso-id');
        const rolId = $('#role-name').data('rol-id');
        const estado = switchInput.is(':checked') ? 1 : 0;

        $.ajax({
            url: `/admin/roles-permisos/roles/${rolId}/permisos/${permisoId}`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { estado },
            success: function (res) {

                // ICONO DEPENDIENDO DE LA ACCIÓN
                const icono = estado === 1 ? 'success' : 'warning';
                const titulo = estado === 1 ? 'Permiso Activado' : 'Permiso Desactivado';

                Swal.fire({
                    icon: icono,
                    title: titulo,
                    text: res.message,
                    toast: true,
                    position: 'top',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    ...swalStyles()
                });
            },
            error: function (xhr) {
                // Revertir switch si falla
                switchInput.prop('checked', !estado);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error inesperado.',
                    toast: true,
                    position: 'top',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    ...swalStyles()
                });
            }
        });
    });
    // ============================
    // ASIGNAR TODOS LOS PERMISOS
    // ============================
    $('#btn-asignar-todos').on('click', function () {
        const rolId = $('#role-name').data('rol-id');
        $.ajax({
            url: `/admin/roles-permisos/roles/${rolId}/asignar/permisos`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

            success: function (res) {
                if (!res.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message,
                        toast: true,
                        position: 'top',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        ...swalStyles()
                    });
                    return;
                }

                // Activar todos los switches
                $('.permiso-switch').prop('checked', true);

                Swal.fire({
                    icon: 'success',
                    title: '¡Permisos Activados!',
                    text: res.message,
                    toast: true,
                    position: 'top',
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    ...swalStyles()
                });
            },

            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error inesperado.',
                    toast: true,
                    position: 'top',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    ...swalStyles()
                });
            }
        });
    });


    // ============================
    // QUITAR TODOS LOS PERMISOS
    // ============================
    $('#btn-quitar-todos').on('click', function () {
        const rolId = $('#role-name').data('rol-id');
        $.ajax({
            url: `/admin/roles-permisos/roles/${rolId}/quitar/permisos`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

            success: function (res) {
                if (!res.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message,
                        toast: true,
                        position: 'top',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        ...swalStyles()
                    });
                    return;
                }

                // Desmarcar todos los switches
                $('.permiso-switch').prop('checked', false);

                Swal.fire({
                    icon: 'warning',
                    title: 'Permisos Eliminados',
                    text: res.message,
                    toast: true,
                    position: 'top',
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    ...swalStyles()
                });
            },

            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error inesperado.',
                    toast: true,
                    position: 'top',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    ...swalStyles()
                });
            }
        });
    });

});
