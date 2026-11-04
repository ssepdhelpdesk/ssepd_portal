<?php

namespace App\Exports\FieldVisit;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FieldVisitDatewiseExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected Collection $dailyTotals;

    public function __construct(Collection $dailyTotals)
    {
        $this->dailyTotals = $dailyTotals;
    }

    public function collection(): Collection
    {
        $data = [];
        $i = 1;
        foreach ($this->dailyTotals as $row) {
            $date = \Carbon\Carbon::parse($row->date_val);
            $data[] = [
                'sl_no'               => $i++,
                'visit_date'          => $date->format('d-M-Y'),
                'day'                 => $date->format('l'),
                'total_visits'        => $row->total_visits ?? 0,
                'rural_visits'        => $row->rural_visits ?? 0,
                'urban_visits'        => $row->urban_visits ?? 0,
                'unique_institutions' => $row->unique_institutions ?? 0,
                'unique_officers'     => $row->unique_officers ?? 0,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            '#',
            'Visit Date',
            'Day',
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

