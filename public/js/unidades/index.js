$(document).ready(function () {

    let table = $('#unidades-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: {
            details: {
                type: 'inline',
                display: $.fn.dataTable.Responsive.display.childRow,
                renderer: function (api, rowIdx, columns) {
                    let data = $.map(columns, function (col) {
                        return col.hidden
                            ? `<tr>
                        <td><strong>${col.title}</strong></td>
                        <td>${col.data}</td>
                       </tr>`
                            : '';
                    }).join('');

                    return data
                        ? $('<table class="table table-bordered w-100"/>').append(data)
                        : false;
                }
            }
        },
        ajax: "/admin/unidades/data",

        dom: `
            <"dt-top-container d-flex flex-wrap justify-content-between  gap-2"
                <"dt-left d-flex " l>
                <"dt-right d-flex " f>
            >
            rt
            <"dt-bottom-container d-flex flex-wrap justify-content-center justify-content-md-between"
                <"dt-info text-center" i>
                <"dt-paginate text-center" p>
            >
        `,

        columns: [
            { data: 'id', name: 'id' },
            { data: 'unidad_padre', name: 'unidad_padre' },
            { data: 'nombre', name: 'nombre' },
            { data: 'jefe', name: 'jefe' },
            { data: 'codigo', name: 'codigo' },
            { data: 'telefono', name: 'telefono' },
            { data: 'interno', name: 'interno' },
            { data: 'nivel', name: 'nivel' },
            { data: 'sub_unidades_count', name: 'sub_unidades_count' },
            { data: 'estado', name: 'estado' },
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

    table.on('draw.dt', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
    table.on('responsive-display.dt', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

});
