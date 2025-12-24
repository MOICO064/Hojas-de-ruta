<div class="d-flex gap-2">

    {{-- Ver PDF --}}
    @if($row->pdf)
        <a href="{{ asset('storage/' . $row->pdf) }}" target="_blank" class="btn btn-sm btn-info" title="Ver PDF">
            <i data-feather="file-text" class="icon-xxs"></i>
        </a>
    @endif
    {{-- Imprimir --}}
    <a href="{{ route('admin.derivaciones.print', [$row->hoja_id, $row->id]) }}" target="_blank"
        class="btn btn-sm btn-secondary" title="Imprimir Derivación">
        <i data-feather="printer" class="icon-xxs"></i>
    </a>
    @can('admin.derivacion.print')
    @endcan
    {{-- Editar Derivación --}}
    <a href="{{ route('admin.derivaciones.edit', [$row->hoja_id, $row->id]) }}" class="btn btn-sm btn-warning"
        title="Editar Derivación">
        <i data-feather="edit" class="icon-xxs"></i>
    </a>
    @can('admin.derivacion.edit')
    @endcan

    {{-- Anular Derivación --}}
    <button type="button" class="btn btn-sm btn-danger btn-anular"
        data-url="{{ route('admin.derivaciones.destroy', [$row->hoja_id, $row->id]) }}" title="Anular Derivación">
        <i data-feather="x-circle" class="icon-xxs"></i>
    </button>
    @can('admin.derivacion.destroy')
    @endcan



</div>