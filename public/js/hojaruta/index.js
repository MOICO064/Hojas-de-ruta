$(document).ready(function () {
    let gestion = $('#hojaruta-table').data('gestion') || '';

    let table = $('#hojaruta-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: `/admin/hojaruta/data/${gestion}`,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'idgral', name: 'idgral' }, // Número general
            { data: 'asunto', name: 'asunto' },
            { data: 'unidad_origen', name: 'unidad_origen' },
            { data: 'estado', name: 'estado' },
            { data: 'gestion', name: 'gestion' },
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

    $('#refresh-table').on('click', function () {
        $('#refresh-icon, #refresh-icon-mobile').addClass('spin');

        table.ajax.reload(null, false);

        table.on('xhr', function () {
            $('#refresh-icon, #refresh-icon-mobile').removeClass('spin');

            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: 'Tabla actualizada',
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
