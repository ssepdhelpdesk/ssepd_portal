<?php

namespace App\Exports\FieldVisit;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FieldVisitGeographicalExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected array $items;
    protected string $title;

    public function __construct(array $items, string $title = 'Geographical Unit')
    {
        $this->items = $items;
        $this->title = $title;
    }

    public function collection(): Collection
    {
        $data = [];
        $i = 1;
        foreach ($this->items as $item) {
            $data[] = [
                'sl_no'        => $i++,
                'name'         => $item['name'] ?? '',
                'type'         => $item['type'] ?? '',
                'total_visits' => $item['total_visits'] ?? 0,
                'rural_visits' => $item['rural_visits'] ?? 0,
                'urban_visits' => $item['urban_visits'] ?? 0,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            '#',
            $this->title,
            'Unit Type',
            'Total Visits',
            'Rural Visits',
            'Urban Visits',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'],
                ],
            ],
        ];
    }
}

