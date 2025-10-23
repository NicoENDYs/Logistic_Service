<?php

namespace App\Exports;

use App\Models\Delivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DeliveriesExport implements FromCollection,WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $status;

    public function __construct($status = null){
        $this->status= $status;
    }
    
    public function collection()
    {
        $query =Delivery::Query();

        if ($this->status){
            $query->where('status',$this->status);

        }

        return $query->latest()->get();
    }

   public function headings(): array
{
    return [
        'ID',
        'ID del Viaje',
        'Nombre del Cliente',
        'Dirección del Envio',
        'Estado',
        'Ultima Actualización',
        'Ciudad actual'
    ];
}

private function getCity($lat, $lng)
    {
        if (!$lat || !$lng) return 'Sin coordenadas';

        $cacheKey = "geo_city_{$lat}_{$lng}";

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($lat, $lng) {
            try {
                $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lng}&format=json";

                $response = Http::withHeaders([
                    'User-Agent' => 'MiAppLaravel/1.0'
                ])->timeout(8)->get($url);

                if (!$response->successful()) {
                    return 'Desconocido';
                }

                $data = $response->json();

                return $data['address']['city']
                    ?? $data['address']['town']
                    ?? $data['address']['village']
                    ?? 'Desconocido';
            } catch (\Exception $e) {
                return 'Desconocido';
            }
        });
    }

public function map($delivery): array
{
    $city = $this->getCity($delivery->latitude, $delivery->longitude);

    return [
        $delivery->id,
        $delivery->trip_id,
        $delivery->customer_name,
        $delivery->delivery_address,
        ucfirst($delivery->status),
        $delivery->updated_at ? $delivery->updated_at->format('Y-m-d H:i') : null,
        $city

    ];
}

      public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 22,
            'D' => 36,
            'E' => 12,
            'F' => 36,
            'G' => 20
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // === Encabezados ===
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'], // Verde agrícola
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // === Bordes generales ===
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:G{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_HAIR],
            ],
        ]);

        // === Ajustar altura de filas y alineación ===
        $sheet->getDefaultRowDimension()->setRowHeight(18);
        $sheet->getStyle("A2:G{$lastRow}")
            ->getAlignment()->setVertical('center');

        // === Estilo condicional para columna de estado (H) ===
        for ($row = 2; $row <= $lastRow; $row++) {
            $cell = $sheet->getCell("H{$row}");
            $status = strtolower(trim($cell->getValue()));

            $color = match ($status) {
                'entregada' => 'C8E6C9', // verde claro
                'pendiente' => 'FFF9C4', // amarillo claro
                'fallida' => 'FFCDD2',   // rojo claro
                default => 'FFFFFF',
            };

            $sheet->getStyle("H{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'font' => ['bold' => true],
            ]);
        }

        // Centrar las columnas clave
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B:B')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('E:I')->getAlignment()->setHorizontal('center');

        // Ajustar ancho de texto automáticamente para columnas grandes
        $sheet->getColumnDimension('D')->setAutoSize(true);

        return [];
    }
}
