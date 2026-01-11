function concluirHojaRuta(hojaId, btn) {
    const btnText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = `<i class="spinner-border spinner-border-sm"></i>`;

    fetch(`/admin/hojaruta/${hojaId}/concluir`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id: hojaId })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = btnText;

        if (data.success) {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: 'Hoja de Ruta concluida',
                showConfirmButton: false,
                timer: 2000,
                customClass: { popup: 'swal2-toast-width' },
                ...swalStyles()
            }).then(() => {
                $("#buzon-table").DataTable().ajax.reload(null, false);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo concluir la hoja de ruta',
                ...swalStyles()
            });
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = btnText;

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al conectarse con el servidor',
            ...swalStyles()
        });
    });
}
