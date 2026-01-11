<div class="d-flex gap-2">

    @if($row->estado === 'PENDIENTE' && $buzon === "Entrada")
    <button type="button" class="btn btn-sm btn-success" title="Recepcionar Derivación"
        onclick="recepcionarDerivacion({{ $row->id }}, this)">
        <i data-feather="check-circle" class="icon-xxs"></i>
    </button>

    @elseif($row->estado === 'ANULADO')
    <button type="button" class="btn btn-sm btn-danger" title="Ver Justificación"
        onclick="abrirModalVerAnulacion('derivacion', {{ $row->id }})">
        <i data-feather="alert-triangle" class="icon-xxs"></i>
    </button>

    {{-- Otros estados (RECEPCIONADO o CONCLUIDO) --}}
    @else
    {{-- Ver Hoja de Ruta --}}
    <a href="{{ route('admin.hojaruta.show', $row->hoja_id) }}" class="btn btn-sm btn-info" title="Ver Hoja de Ruta">
        <i data-feather="eye" class="icon-xxs"></i>
    </a>

    {{-- Imprimir: solo el usuario que derivó --}}
    @if(auth()->user()->funcionario_id === $row->derivado_por)
    <a href="{{ route('admin.reportes.derivaciones', ['derivaciones[]' => $row->id]) }}" target="_blank"
        class="btn btn-sm btn-secondary" title="Imprimir Hoja de Ruta">
        <i data-feather="printer" class="icon-xxs"></i>
    </a>
    @endif

    {{-- Solo si la hoja NO está concluida --}}
    @if($row->hojaRuta->estado !== 'CONCLUIDO')

    {{-- Derivar ruta: solo si estamos en el buzon Entrada --}}
    @if($buzon === "Entrada" && $row->estado !="CONCLUIDO")
    <a href="{{ route('admin.derivaciones.create', $row->hoja_id) }}" class="btn btn-sm btn-success"
        title="Derivar Hoja de Ruta">
        <i data-feather="corner-up-right" class="icon-xxs"></i>
    </a>
    @endif

    @php
    // Determinar la última derivación válida
    $ultimaDerivacionValida = $row->hojaRuta
    ->derivaciones
    ->where('estado', '!=', 'ANULADO')
    ->last();
    $esUltimaDerivacion = $ultimaDerivacionValida && $ultimaDerivacionValida->id === $row->id;
    @endphp

    {{-- Editar y Anular derivación: solo el que derivó y si es la última válida --}}
    @if(auth()->user()->funcionario_id === $row->derivado_por && $row->estado !== 'ANULADO' && $row->estado !== 'CONCLUIDO' && $esUltimaDerivacion)
    <a href="{{ route('admin.derivaciones.edit', [$row->hoja_id, $row->id]) }}" class="btn btn-sm btn-warning"
        title="Editar Derivación">
        <i data-feather="edit" class="icon-xxs"></i>
    </a>

    <button type="button" class="btn btn-sm btn-danger" title="Anular Derivación"
        onclick="abrirModalAnulacion('derivacion', {{ $row->id }})">
        <i data-feather="trash-2" class="icon-xxs"></i>
    </button>
    @endif

  
    @if($buzon === "Entrada" && $esUltimaDerivacion && auth()->user()->funcionario_id === $row->funcionario_id && $row->hojaRuta->estado!=="Concluido")
    <button type="button" class="btn btn-sm btn-primary" title="Concluir Hoja de Ruta"
        onclick="concluirHojaRuta({{ $row->hoja_id }}, this)">
        <i data-feather="check-square" class="icon-xxs me-1"></i>
        <span class="d-none d-md-inline">Concluir</span>
    </button>
    @endif

    @endif
    @endif

</div>