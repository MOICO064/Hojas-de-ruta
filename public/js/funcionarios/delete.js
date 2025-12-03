function eliminarFuncionario(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Este funcionario será eliminado o anulado según sus relaciones',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        ...swalStyles()
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/funcionarios/${id}`,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    $('#funcionarios-table').DataTable().ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || 'Acción realizada correctamente',
                        toast: true,
                        timer: 3000,
                        position: 'top',
                        showConfirmButton: false,
                        ...swalStyles()
                    });
                },
                error: function (xhr) {
                    let message = 'Error desconocido';
                    if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        toast: true,
                        timer: 4000,
                        position: 'top',
                        showConfirmButton: false,
                        ...swalStyles()
                    });
                }
            });
        }
    });
}
