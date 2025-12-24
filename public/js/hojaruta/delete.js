
function eliminarHojaRuta(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar request DELETE usando fetch
            fetch(`/admin/hojaruta/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Eliminado', 'La hoja de ruta fue eliminada.', 'success');
                        $('#hojaruta-table').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'No se pudo eliminar.', 'error');
                });
        }
    });
}

