<?php

namespace App\Exports;

use App\Models\Solicitud;
use Maatwebsite\Excel\Concerns\FromQuery; // Cambiado para mayor eficiencia con filtros
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Nueva: Autoajusta el ancho de columnas
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SolicitudesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $fecha_inicio;
    protected $fecha_fin;

    /**
     * Constructor para recibir las fechas del filtro
     */
    public function __construct($inicio = null, $fin = null)
    {
        $this->fecha_inicio = $inicio;
        $this->fecha_fin = $fin;
    }

    /**
     * Consulta filtrada para el Excel
     */
    public function query()
    {
        $query = Solicitud::query()->with(['beneficio', 'estatus']);

        // Si se pasaron fechas, filtramos. Si no, trae todo el mes actual.
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('fecha_solicitud', [$this->fecha_inicio, $this->fecha_fin]);
        } else {
            $query->whereMonth('fecha_solicitud', now()->month);
        }

        return $query->orderBy('fecha_solicitud', 'asc');
    }

    /**
     * Encabezados con formato profesional
     */
    public function headings(): array
    {
        return [
            'N° SOLICITUD',
            'INDICADOR',
            'FECHA DE REGISTRO',
            'TIPO DE BENEFICIO',
            'DESCRIPCIÓN DEL REQUERIMIENTO',
            'ESTATUS ACTUAL',
            'MONTO (Bs.)'
        ];
    }

    /**
     * Mapeo de datos para legibilidad técnica
     */
    public function map($solicitud): array
    {
        return [
            $solicitud->id_solicitud,
            $solicitud->id_usuario, // Añadido para que RRHH sepa de quién es
            \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y'),
            $solicitud->beneficio->nombre_beneficio ?? 'N/A', // Corregido según tu modelo
            $solicitud->descripcion,
            $solicitud->estatus->descripcion ?? 'Pendiente',
            number_format($solicitud->monto, 2, ',', '.')
        ];
    }

    /**
     * Estilos corporativos (Rojo PDVSA y texto blanco)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila 1 (Cabeceras)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF']
                ], 
                'fill' => [
                    'fillType' => 'solid', 
                    'startColor' => ['rgb' => 'ED1C24'] // Rojo Corporativo
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ],
            // Bordes para toda la tabla (opcional)
            'A1:G100' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }
}