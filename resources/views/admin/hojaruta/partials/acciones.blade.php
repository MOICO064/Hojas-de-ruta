<div class="d-flex gap-2">

    <!-- Ver Hoja de Ruta -->
    <a href="{{ route('admin.hojaruta.show', $row->id) }}" class="btn btn-sm btn-info" title="Ver Hoja de Ruta">
        <i data-feather="eye" class="icon-xxs"></i>
    </a>

    <!-- Imprimir Hoja de Ruta -->

    <a href="" target="_blank" class="btn btn-sm btn-secondary" title="Imprimir Hoja de Ruta">
        <i data-feather="printer" class="icon-xxs"></i>
    </a>



    @if ($row->estado != 'Concluido' && $row->estado != 'Anulado' && $row->creado_por === auth()->user()->id)
        <!-- Ver Derivaciones -->
        <a href="{{ route('admin.derivaciones.index', $row->id) }}" class="btn btn-sm btn-primary" title="Ver Derivaciones">
            <i data-feather="shuffle" class="icon-xxs"></i>
        </a>
        <!-- Editar Hoja de Ruta -->
        <a href="{{ route('admin.hojaruta.edit', $row->id) }}" class="btn btn-sm btn-warning" title="Editar Hoja de Ruta">
            <i data-feather="edit" class="icon-xxs"></i>
        </a>

        <!-- Eliminar Hoja de Ruta -->
        <button type="button" class="btn btn-sm btn-danger" title="Eliminar Hoja de Ruta"
            onclick="eliminarHojaRuta({{ $row->id }})">
            <i data-feather="trash-2" class="icon-xxs"></i>
        </button>
    @endif

</div>