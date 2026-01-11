$(document).ready(function () {
     const tipoBuzon = $('#tipoBuzon').text().trim().toLowerCase();
console.log(tipoBuzon);
    let table = $('#buzon-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: `/admin/buzon/${tipoBuzon}/data`, 
        columns: [
            { data: 'numero_general', name: 'numero_general' },
            { data: 'numero_unidad', name: 'numero_unidad', defaultContent: '' },
            { data: 'asunto', name: 'asunto', defaultContent: '' },
            { data: 'unidad_origen', name: 'unidad_origen' },
            { data: 'unidad_destino', name: 'unidad_destino' },
            { data: 'estado', name: 'estado' },
            { data: 'urgente', name: 'urgente' },
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
        $('#refresh-icon-buzon').addClass('spin');

        table.ajax.reload(null, false);

        table.one('xhr', function () {
            $('#refresh-icon-buzon').removeClass('spin');

            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: 'Buzón actualizado',
                showConfirmButton: false,
                timer: 2000,
                customClass: { popup: 'swal2-toast-width' },
                ...swalStyles()
            });
        });
    });

    table.on('draw.dt responsive-display.dt', function () {
        if (typeof feather !== 'undefined') feather.replace();
    });
});
