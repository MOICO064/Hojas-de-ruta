<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Hojas de Ruta</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ public_path('assets/images/recurces/escudo.png') }}" />

    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        margin: 15px;
        line-height: 1.2;
    }

    /* =========================
           CONTROL DE PÁGINAS
        ========================= */
    .page {
        page-break-after: always;
    }

    .page:last-child {
        page-break-after: auto;
    }

    /* =========================
           HEADER
        ========================= */
    .header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .header img.logo {
        max-height: 50px;
    }

    .title {
        border: 2px solid #000;
        width: 280px;
        margin: 0 auto 15px auto;
        padding: 6px 0;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    /* =========================
           DETALLES
        ========================= */
    .details {
        border: 1px solid #000;
        padding: 6px;
        margin-bottom: 15px;
        font-size: 10px;
    }

    .details .col {
        display: inline-block;
        width: 48%;
        margin-bottom: 4px;
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
    }

    /* =========================
           TABLA
        ========================= */
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 10px;
        text-align: center;
        /* 🔥 TODO CENTRADO */
    }

    th,
    td {
        border: 1px solid #000;
        padding: 3px 4px;
        vertical-align: middle;
        text-align: center;
    }

    th {
        background-color: #004080;
        color: #fff;
        font-weight: bold;
    }

    /* EXCEPCIÓN: DESCRIPCIÓN A LA IZQUIERDA */
    td.descripcion {
        width: 40%;
        text-align: left;
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
    }

    th.recepcion,
    td.recepcion {
        width: 12%;
    }

    /* COLUMNA FIRMA */
    th:last-child,
    td:last-child {
        width: 10%;
        min-height: 40px;
        padding: 2px;
    }

    .unidad-funcionario {
        display: block;
        font-size: 9px;
        color: #333;
        text-align: center;
    }
    </style>
</head>

<body>

    @foreach ($derivaciones as $derivacion)
    <div class="page">

        {{-- HEADER --}}
        <div class="header">
            <img src="{{ public_path('assets/images/recurces/gamc-320x128-23.png') }}" class="logo" alt="Logo">
        </div>

        {{-- TÍTULO --}}
        <div class="title">HOJA DE RUTA</div>

        {{-- DETALLES --}}
        <div class="details">
            <div class="col"><strong>Número General:</strong> {{ $derivacion->hojaRuta->idgral }}</div>
            <div class="col">
                <strong>Número {{ $derivacion->hojaRuta->unidadOrigen?->codigo ?? '-' }}:</strong>
                {{ $derivacion->hojaRuta->numero_unidad }}
            </div>

            <div class="col"><strong>Gestión:</strong> {{ $derivacion->hojaRuta->gestion }}</div>

            <div class="full-width">
                <strong>Solicitante:</strong>
                {{ $derivacion->hojaRuta->solicitante?->nombre ?? $derivacion->hojaRuta->nombre_solicitante }}
            </div>

            <div class="col"><strong>Unidad Origen:</strong> {{ $derivacion->hojaRuta->unidadOrigen?->nombre ?? '-' }}
            </div>
            <div class="col"><strong>CITE:</strong> {{ $derivacion->hojaRuta->cite ?? '-' }}</div>

            <div class="col"><strong>Estado:</strong> {{ $derivacion->hojaRuta->estado }}</div>
            <div class="col"><strong>Urgente:</strong> {{ $derivacion->hojaRuta->urgente ? 'SÍ' : 'NO' }}</div>

            <div class="col"><strong>Fecha:</strong> {{ $derivacion->hojaRuta->fecha_creacion?->format('d/m/Y') }}</div>
            <div class="col"><strong>Creado por:</strong> {{ $derivacion->hojaRuta->creador?->email ?? '-' }}</div>

            <div class="col"><strong>Fojas:</strong> {{ $derivacion->fojas ?? 0 }}</div>
            <div class="col">
                <strong>Fecha impresión:</strong>
                {{ $derivacion->hojaRuta->fecha_impresion?->format('d/m/Y H:i') ?? '-' }}
            </div>

            <div class="full-width"><strong>Asunto:</strong> {{ $derivacion->hojaRuta->asunto }}</div>
        </div>

        {{-- TABLA --}}
        <table>
            <thead>
                <tr>
                    <th class="origen">Unidad Origen</th>
                    <th class="destino-col">Unidad Destino / Funcionario</th>
                    <th class="fecha">Fecha Derivación</th>
                    <th class="recepcion">Fecha Recepción</th>
                    <th class="descripcion">Descripción</th>
                    <th>Firma</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="origen">{{ $derivacion->unidadOrigen?->nombre ?? '-' }}</td>

                    <td class="destino-col">
                        {{ $derivacion->unidadDestino?->nombre ?? '-' }}
                        <span class="unidad-funcionario">
                            {{ $derivacion->funcionario?->nombre ?? '-' }}
                        </span>
                    </td>

                    <td class="fecha">{{ $derivacion->fecha_derivacion?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="recepcion">{{ $derivacion->fecha_recepcion?->format('d/m/Y H:i') ?? '-' }}</td>

                    <td class="descripcion">{{ $derivacion->descripcion ?? '-' }}</td>

                    <td></td>
                </tr>
            </tbody>
        </table>

    </div>
    @endforeach

</body>

</html>