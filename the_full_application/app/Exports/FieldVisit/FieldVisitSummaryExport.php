<?php

namespace App\Exports\FieldVisit;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FieldVisitSummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected array $rows;
    protected string $levelTitle;

    public function __construct(array $rows, string $levelTitle = 'Jurisdiction')
    {
        $this->rows = $rows;
        $this->levelTitle = $levelTitle;
    }

    public function collection(): Collection
    {
        $data = [];
        $i = 1;
        foreach ($this->rows as $row) {
            $data[] = [
                'sl_no'              => $i++,
                'name'               => $row['name'] ?? '',
                'type'               => $row['type'] ?? '',
                'total_visits'       => $row['total_visits'] ?? 0,
                'rural_visits'       => $row['rural_visits'] ?? 0,
                'urban_visits'       => $row['urban_visits'] ?? 0,
                'unique_institutes'  => $row['unique_institutes'] ?? 0,
                'unique_officers'    => $row['unique_officers'] ?? 0,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            '#',
            $this->levelTitle . ' Name',
            'Administrative Level',
            'Total Visits',
            'Rural Visits',
            'Urban Visits',
            'Unique Institutions',
            'Unique Visiting Officers',
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

