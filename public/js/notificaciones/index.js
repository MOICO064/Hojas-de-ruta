
$(document).ready(function () {

    var table = $('#tablaNotificaciones').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/notificaciones/todas',
            type: 'GET'
        },
        columns: [
            {
                data: 'mensaje',
                name: 'mensaje'
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function (data) {
                    return new Date(data).toLocaleString();
                }
            },
            {
                data: 'leida',
                name: 'leida',
                render: function (data, type, row) {
                    return data ? 'Visto' : 'No Visto';
                }
            }
        ],
        lengthMenu: [5, 10, 25],
        pageLength: 10,
        searching: false,
        ordering: false,
        info: true,
        paging: true,
        rowCallback: function (row, data) {


            $(row).css('cursor', 'pointer').off('click').on('click', function () {
                window.location.href = '/notificaciones/ver/' + data.id;
            });
        },
        language: {
            processing: "Cargando notificaciones...",
            lengthMenu: "Mostrar _MENU_ notificaciones",
            info: "Mostrando _START_ a _END_ de _TOTAL_ notificaciones",
            infoEmpty: "No hay notificaciones",
            infoFiltered: "(filtrado de _MAX_ total)",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            zeroRecords: "No se encontraron notificaciones"
        }
    });

    // Marcar todas como leídas
    $('#marcarTodoLeido').click(function () {
        $.ajax({
            url: '/notificaciones/marcar-todas-leidas',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                table.ajax.reload();
            },
            error: function (err) {
                console.error('Error al marcar todas como leídas:', err);
            }
        });
    });

    // Refrescar manual
    $('#refresh-table').click(function () {
        table.ajax.reload();
    });

});
