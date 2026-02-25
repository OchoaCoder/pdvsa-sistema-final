<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; }
        .header { border-bottom: 3px solid #ed1c24; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-text { font-size: 28px; font-weight: bold; color: #ed1c24; }
        .sub-header { font-size: 12px; color: #555; }
        .title { text-align: center; background: #f4f4f4; padding: 10px; margin: 20px 0; font-size: 18px; text-transform: uppercase; border: 1px solid #ddd; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { text-align: left; padding: 8px; background: #fafafa; border: 1px solid #ddd; width: 30%; }
        .data-table td { padding: 8px; border: 1px solid #ddd; }
        .desc-box { border: 1px solid #ddd; padding: 15px; min-height: 100px; font-style: italic; background: #fff; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <span class="logo-text">PDVSA GAS</span><br>
        <span class="sub-header">Gerencia de Recursos Humanos - Carabobo</span>
    </div>

    <div class="title">Comprobante de Solicitud de Beneficio</div>

    <table class="data-table">
        <tr>
            <th>Número de Control:</th>
            <td><strong>#{{ str_pad($solicitud->id_solicitud, 6, '0', STR_PAD_LEFT) }}</strong></td>
        </tr>
        <tr>
            <th>Fecha y Hora:</th>
            <td>{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <th>Indicador / Usuario:</th>
            <td>{{ auth()->user()->usuario }}</td>
        </tr>
        <tr>
            <th>Tipo de Beneficio:</th>
            <td>{{ $solicitud->beneficio->descripcion ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Estado de Solicitud:</th>
            <td>{{ $solicitud->estatus->descripcion ?? 'Pendiente' }}</td>
        </tr>
    </table>

    <h4>Descripción del Requerimiento:</h4>
    <div class="desc-box">
        {{ $solicitud->descripcion }}
    </div>

    <div class="footer">
        Este documento es una constancia digital emitida por el Sistema de Autogestión PDVSA GAS. <br>
        Generado el: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>