<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Hoja de Ruta {{ $hojaRuta->idgral }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ public_path('assets/images/recurces/escudo.png') }}" />


    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        /* letra más pequeña */
        margin: 15px;
        line-height: 1.2;
    }

    /* HEADER */
    .header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .header img.logo {
        max-height: 50px;
        margin-right: 10px;
    }

    .header img.icon {
        max-height: 35px;
        margin-right: 10px;
    }

    /* TÍTULO */
    .title {
        border: 2px solid #000;
        width: 280px;
        margin: 0 auto 15px auto;
        padding: 6px 0;
        /* menos padding */
        font-size: 16px;
        /* más pequeño */
        font-weight: bold;
        text-align: center;
    }

    /* DETALLES */
    .details {
        border: 1px solid #000;
        padding: 6px;
        /* menos padding */
        margin-bottom: 15px;
        font-size: 10px;
    }

    .details .col {
        display: inline-block;
        width: 48%;
        box-sizing: border-box;
        margin-bottom: 4px;
        /* menos margen */
        vertical-align: top;
    }

    .details .full-width {
        display: block;
        width: 100%;
        margin-bottom: 4px;
    }

    .details strong {
        display: inline-block;
        width: 120px;
        /* menos ancho para labels */
    }

    /* TABLA DERIVACIONES */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        table-layout: fixed;
        font-size: 10px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 3px 4px;
        /* menos padding */
        vertical-align: middle;
    }

    th {
        background-color: #004080;
        color: #fff;
        text-align: center;
        font-weight: bold;
    }

    td {
        text-align: center;
    }

    td.descripcion {
        width: 40%;
        text-align: left;
    }

    td.destino-col {
        font-weight: bold;
        padding: 4px 6px;
    }

    tbody tr:nth-child(even) {
        background-color: #e6f0ff;
    }

    tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .unidad-funcionario {
        display: block;
        font-size: 9px;
        color: #333;
    }

    /* Anchos columnas */
    th.numero,
    td.numero {
        width: 5%;
    }

    th.origen,
    td.origen {
        width: 15%;
        font-weight: bold;
    }

    th.destino-col,
    td.destino-col {
        width: 15%;
        font-weight: bold;
    }

    th.fecha,
    td.fecha {
        width: 12%;
        min-height: 20px;
    }

    th.recepcion,
    td.recepcion {
        width: 12%;
        min-height: 20px;
    }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">

        <img src="{{ public_path('assets/images/recurces/gamc-320x128-23.png') }}" class="logo" alt="Logo">
    </div>

    {{-- TÍTULO --}}
    <div class="title">HOJA DE RUTA</div>

    {{-- DETALLES DE LA HOJA --}}
    <div class="details">
        <div class="col"><strong>Número General:</strong> {{ $hojaRuta->idgral }}</div>
        <div class="col"><strong>Número {{ $hojaRuta->unidadOrigen?->codigo ?? '-' }}:</strong>
            {{ $hojaRuta->numero_unidad }}</div>
        <div class="col"><strong>Gestión:</strong> {{ $hojaRuta->gestion }}</div>
        <div class="full-width"><strong>Solicitante:</strong>
            {{ $hojaRuta->solicitante?->nombre ?? $hojaRuta->nombre_solicitante }}</div>
        <div class="col"><strong>Unidad Origen:</strong> {{ $hojaRuta->unidadOrigen?->nombre ?? '-' }}</div>
        <div class="col"><strong>CITE:</strong> {{ $hojaRuta->cite ?? '-' }}</div>
        <div class="col"><strong>Estado:</strong> {{ $hojaRuta->estado }}</div>
        <div class="col"><strong>Urgente:</strong> {{ $hojaRuta->urgente ? 'SÍ' : 'NO' }}</div>
        <div class="col"><strong>Fecha:</strong> {{ $hojaRuta->fecha_creacion?->format('d/m/Y') }}</div>
        <div class="col"><strong>Creado por:</strong> {{ $hojaRuta->creador?->email ?? '-' }}</div>
        <div class="col"><strong>Fojas:</strong> {{ $hojaRuta->derivaciones->sum('fojas') ?? 0 }}</div>
        <div class="col"><strong>Fecha impre:</strong> {{ $hojaRuta->fecha_impresion?->format('d/m/Y H:i') ?? '-' }}
        </div>
        <div class="full-width"><strong>Asunto:</strong> {{ $hojaRuta->asunto }}</div>
    </div>

    {{-- TABLA DERIVACIONES --}}
    <table>
        <thead>
            <tr>
                <th class="numero">#</th>
                <th class="origen">Unidad Origen</th>
                <th class="destino-col">Unidad Destino / Funcionario</th>
                <th class="fecha">Fecha Derivación</th>
                <th class="recepcion">Fecha Recepción</th>
                <th class="descripcion">Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hojaRuta->derivaciones as $index => $derivacion)
            <tr>
                <td class="numero">{{ $index + 1 }}</td>
                <td class="origen">{{ $derivacion->unidadOrigen?->nombre ?? '-' }}</td>
                <td class="destino-col">
                    {{ $derivacion->unidadDestino?->nombre ?? '-' }}
                    <span class="unidad-funcionario">{{ $derivacion->funcionario?->nombre ?? '-' }}</span>
                </td>
                <td class="fecha">{{ $derivacion->fecha_derivacion?->format('d/m/Y H:i') ?? '-' }}</td>
                <td class="recepcion">{{ $derivacion->fecha_recepcion?->format('d/m/Y H:i') ?? '-' }}</td>
                <td class="descripcion">{{ $derivacion->descripcion ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>