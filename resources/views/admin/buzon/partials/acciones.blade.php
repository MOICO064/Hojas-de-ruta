<div class="d-flex gap-2">


    <a href="{{ route('admin.hojaruta.show', $row->id) }}" class="btn btn-sm btn-info" title="Ver Hoja de Ruta">
        <i data-feather="eye" class="icon-xxs"></i>
    </a>


    @can('admin.hojaruta.edit')
        <a href="{{ route('admin.hojaruta.edit', $row->id) }}" class="btn btn-sm btn-warning" title="Editar Hoja de Ruta">
            <i data-feather="edit" class="icon-xxs"></i>
        </a>
    @endcan


    @can('admin.hojaruta.destroy')
        <button type="button" class="btn btn-sm btn-danger btn-delete"
            data-url="{{ route('admin.hojaruta.destroy', $row->id) }}" title="Eliminar Hoja de Ruta">
            <i data-feather="trash-2" class="icon-xxs"></i>
        </button>
    @endcan

</div>