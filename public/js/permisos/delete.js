function eliminarPermiso(id) {
    const primaryColor = '#5e72e4'; // color primario
    const secondaryColor = '#6c757d'; // color secundario

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción no se puede deshacer!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: primaryColor,
        cancelButtonColor: secondaryColor,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        focusCancel: true,
        ...swalStyles()
    }).then((result) => {
        if (result.isConfirmed) {
            const swalBtn = Swal.getConfirmButton();
            swalBtn.disabled = true;
            swalBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...';

            $.ajax({
                url: `/admin/roles-permisos/permisos/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    Swal.close();

                    Swal.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.success ? '¡Éxito!' : 'Error',
                        text: response.message,
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        ...swalStyles(),
                    });

                    if (response.success) {
                        $('#permisos-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    let message = xhr.responseJSON?.message || 'Error desconocido. Revisa la consola.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: message.replace(/\n/g, '<br>'),
                        toast: true,
                        position: 'top-end',
                        timer: 5000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        ...swalStyles()
                    });
                },
                complete: function () {
                    swalBtn.disabled = false;
                    swalBtn.innerHTML = 'Sí, eliminar';
                }
            });
        }
    });
}
