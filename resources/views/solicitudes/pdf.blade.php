<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Solicitud - PDVSA GAS</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #ed1c24; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-text { color: #ed1c24; font-weight: bold; font-size: 24px; }
        .title { text-align: center; text-transform: uppercase; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; text-align: left; padding: 8px; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        
        .section-title { background-color: #ed1c24; color: white; padding: 5px 10px; font-weight: bold; margin-top: 20px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 5px; }
        .status-badge { font-weight: bold; color: #ed1c24; }
    </style>
</head>
<body>

    <div class="header">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 50%;">
                    <span class="logo-text">PDVSA GAS</span><br>
                    <small>Gerencia de Tecnología y Procesos</small>
                </td>
                <td style="border: none; width: 50%; text-align: right;">
                    <strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i:s') }}<br>
                    <strong>Nro. Solicitud:</strong> #{{ $solicitud->id_solicitud }}
                </td>
            </tr>
        </table>
    </div>

    <div class="title">Comprobante de Registro de Solicitud</div>

    <div class="section-title">Datos del Solicitante</div>
    <table>
        <tr>
            <th>Usuario/Indicador:</th>
            <td>{{ Auth::user()->usuario }}</td>
            <th>ID Empleado:</th>
            <td>{{ Auth::user()->id_empleado }}</td>
        </tr>
    </table>

    <div class="section-title">Detalles del Beneficio</div>
    <table>
        <tr>
            <th style="width: 30%;">Tipo de Beneficio:</th>
            <td>{{ $solicitud->beneficio->nombre_beneficio ?? 'No especificado' }}</td>
        </tr>
        <tr>
            <th>Fecha de Solicitud:</th>
            <td>{{ date('d/m/Y', strtotime($solicitud->fecha_solicitud)) }}</td>
        </tr>
        <tr>
            <th>Estatus Actual:</th>
            <td class="status-badge">{{ $solicitud->estatus->descripcion ?? 'Pendiente' }}</td>
        </tr>
    </table>

    <div class="section-title">Descripción de la Solicitud</div>
    <div style="padding: 10px; border: 1px solid #ddd; min-height: 100px;">
        {{ $solicitud->descripcion }}
    </div>

    <div style="margin-top: 50px;">
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: center; width: 50%;">
                    <br><br>
                    ___________________________<br>
                    Firma del Solicitante
                </td>
                <td style="border: none; text-align: center; width: 50%;">
                    <br><br>
                    ___________________________<br>
                    Sello / Firma Autorizada
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Este documento es un comprobante digital emitido por el Sistema de Gestión de Solicitudes de PDVSA GAS.
    </div>

</body>
</html>