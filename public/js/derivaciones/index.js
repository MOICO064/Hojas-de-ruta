$(document).ready(function () {

    // ID de la hoja de ruta (contexto obligatorio)
    let hojaId = $('#derivaciones-table').data('hoja-id');

    let table = $('#derivaciones-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: `/admin/hojaruta/${hojaId}/derivaciones/data`,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'unidad_origen', name: 'unidad_origen' },
            { data: 'unidad_destino', name: 'unidad_destino' },
            { data: 'funcionario', name: 'funcionario' },
            { data: 'estado', name: 'estado' },
            { data: 'fecha_derivacion', name: 'fecha_derivacion' },
            { data: 'fecha_recepcion', name: 'fecha_recepcion' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        dom: `
            <"dt-top-container d-flex flex-wrap justify-content-between gap-2"
                <"dt-left d-flex" l>
                <"dt-right d-flex" f>
            >
            rt
            <"dt-bottom-container d-flex flex-wrap justify-content-center justify-content-md-between"
                <"dt-info text-center" i>
                <"dt-paginate text-center" p>
            >
        `
    });

    // 🔄 Botón refrescar tabla
    $('#refresh-table').on('click', function () {

        table.ajax.reload(null, false);

        table.on('xhr', function () {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: 'Derivaciones actualizadas',
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'swal2-toast-width'
                },
                ...swalStyles()
            });
        });
    });

    table.on('draw.dt responsive-display.dt', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

});

