<div class="d-flex gap-2">

    <!-- Ver Hoja de Ruta -->
    <a href="{{ route('admin.hojaruta.show', $row->id) }}" class="btn btn-sm btn-info" title="Ver Hoja de Ruta">
        <i data-feather="eye" class="icon-xxs"></i>
    </a>


    <a href="{{ route('admin.hojaruta.edit', $row->id) }}" class="btn btn-sm btn-warning" title="Editar Hoja de Ruta">
        <i data-feather="edit" class="icon-xxs"></i>
    </a>
    <!-- Editar Hoja de Ruta -->
    @can('admin.hojaruta.edit')
    @endcan


    <form method="POST" action="{{ route('admin.hojaruta.destroy', $row->id) }}" class="d-inline-block delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Eliminar Hoja de Ruta">
            <i data-feather="trash-2" class="icon-xxs"></i>
        </button>
    </form>
    <!-- Eliminar Hoja de Ruta -->
    @can('admin.hojaruta.destroy')
    @endcan

</div>