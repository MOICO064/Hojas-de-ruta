function eliminarUnidad(id) {
    const isDark = $('body').hasClass('theme-dark'); 
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción no se puede deshacer!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        focusCancel: true,
        background: isDark ? '#1E1E2D' : '#fff',
        color: isDark ? '#fff' : '#000'
    }).then((result) => {
        if (result.isConfirmed) {
            const swalBtn = Swal.getConfirmButton();
            swalBtn.disabled = true;
            swalBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...';

            $.ajax({
                url: `/admin/unidades/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    Swal.close(); // Cierra confirmación

                    Swal.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.success ? '¡Éxito!' : 'Error',
                        text: response.message,
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false,
                        background: isDark ? '#1E1E2D' : '#fff',
                        color: isDark ? '#fff' : '#000'
                    });

                    if (response.success) {
                        $('#unidades-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    let message = xhr.responseJSON?.message || 'Error desconocido. Revisa la consola.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del servidor',
                        html: message.replace(/\n/g, '<br>'),
                        toast: true,
                        position: 'top-end',
                        timer: 5000,
                        showConfirmButton: false,
                        background: isDark ? '#1E1E2D' : '#fff',
                        color: isDark ? '#fff' : '#000'
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
