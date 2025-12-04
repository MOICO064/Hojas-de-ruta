$(document).ready(function () {
    let table = $('#permisos-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "/admin/roles-permisos/permisos/data", 
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'guard_name', name: 'guard_name' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
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

    // Reemplazar feather icons en cada draw
    table.on('draw.dt responsive-display.dt', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
});
