<?php

namespace App\Exports;

use App\Models\Solicitud;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class SolicitudesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $fecha_inicio;
    protected $fecha_fin;

    /**
     * Constructor: Recibe las fechas desde el SolicitudController
     */
    public function __construct($inicio = null, $fin = null)
    {
        $this->fecha_inicio = $inicio;
        $this->fecha_fin = $fin;
    }

    /**
     * Consulta Dinámica: Filtra el reporte según la selección de la Gerencia
     */
    public function query()
    {
        // Cargamos las relaciones para evitar lentitud (Eager Loading)
        $query = Solicitud::query()->with(['beneficio', 'estatus', 'usuario']);

        // Si la gerente seleccionó un rango de fechas, aplicamos el filtro
        if (!empty($this->fecha_inicio) && !empty($this->fecha_fin)) {
            $query->whereBetween('fecha_solicitud', [$this->fecha_inicio, $this->fecha_fin]);
        }

        // Ordenamos por fecha para que el reporte sea cronológico
        return $query->orderBy('fecha_solicitud', 'desc');
    }

    /**
     * Encabezados del Reporte (Rojo PDVSA)
     */
    public function headings(): array
    {
        return [
            'ID',
            'SOLICITANTE',
            'FECHA DE REGISTRO',
            'TIPO DE BENEFICIO',
            'DESCRIPCIÓN',
            'ESTATUS ACTUAL',
            'MONTO APROBADO (Bs.)'
        ];
    }

    /**
     * Mapeo de Columnas: Asegura que los datos salgan limpios
     */
    public function map($solicitud): array
    {
        return [
            $solicitud->id_solicitud,
            $solicitud->usuario->usuario ?? 'N/A', // Muestra el nombre/indicador del usuario
            Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y'),
            $solicitud->beneficio->descripcion ?? 'N/A',
            $solicitud->descripcion,
            $solicitud->estatus->descripcion ?? 'Pendiente',
            number_format($solicitud->monto, 2, ',', '.')
        ];
    }

    /**
     * Estilos Profesionales PDVSA
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo de la cabecera (Fila 1)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ], 
                'fill' => [
                    'fillType' => 'solid', 
                    'startColor' => ['rgb' => 'ED1C24'] // Rojo Corporativo
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
            // Alineación centrada para las columnas de ID, Fecha y Estatus
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            
            // Bordes finos para que se vea como una tabla oficial
            'A1:G500' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}