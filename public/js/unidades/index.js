$(document).ready(function () {
    let table = $('#unidades-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "/admin/unidades/data",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'unidad_padre', name: 'unidad_padre' },
            { data: 'nombre', name: 'nombre' },
            { data: 'jefe', name: 'jefe' },
            { data: 'codigo', name: 'codigo' },
            { data: 'telefono', name: 'telefono' },
            { data: 'celular', name: 'celular' },
            { data: 'nivel', name: 'nivel' },
            { data: 'sub_unidades_count', name: 'sub_unidades_count' },
            { data: 'estado', name: 'estado' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });

    $('#refresh-table').on('click', function () {
        // Agregar clase spin a los iconos
        $('#refresh-icon, #refresh-icon-mobile').addClass('spin');

        table.ajax.reload(null, false); // recarga solo la tabla

        // Quitar la clase spin cuando termine
        table.on('xhr', function () {
            $('#refresh-icon, #refresh-icon-mobile').removeClass('spin');
        });
    });

    // Inicializar feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
