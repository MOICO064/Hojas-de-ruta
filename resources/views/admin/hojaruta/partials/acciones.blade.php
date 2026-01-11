<div class="d-flex gap-2">

    <a href="{{ route('admin.hojaruta.show', $row->id) }}" class="btn btn-sm btn-info" title="Ver Hoja de Ruta">
        <i data-feather="eye" class="icon-xxs"></i>
    </a>

    <a href="{{ route('admin.reportes.hoja-ruta', $row->id) }}" target="_blank" class="btn btn-sm btn-secondary"
        title="Imprimir Hoja de Ruta">
        <i data-feather="printer" class="icon-xxs"></i>
    </a>

</div>