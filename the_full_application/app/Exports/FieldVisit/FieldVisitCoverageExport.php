<?php

namespace App\Exports\FieldVisit;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FieldVisitCoverageExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected Collection $institutes;

    public function __construct(Collection $institutes)
    {
        $this->institutes = $institutes;
    }

    public function collection(): Collection
    {
        $data = [];
        $i = 1;
        foreach ($this->institutes as $inst) {
            $isVisited = ($inst->visit_count ?? 0) > 0;
            $blockOrUlb = $inst->block ? $inst->block->block_name : ($inst->municipality ? $inst->municipality->municipality_name : $inst->block_name);

            $data[] = [
                'sl_no'           => $i++,
                'institute_code'  => $inst->institute_code,
                'institute_name'  => $inst->institute_name,
                'category_name'   => $inst->category_name,
                'district'        => $inst->district ? $inst->district->district_name : ($inst->district_name ?: 'N/A'),
                'block_ulb'       => $blockOrUlb ?: 'N/A',
                'coverage_status' => $isVisited ? 'VISITED' : 'NOT VISITED',
                'visit_count'     => $inst->visit_count ?? 0,
                'last_visit_date' => !empty($inst->last_visit_date) ? date('d-M-Y', strtotime($inst->last_visit_date)) : 'N/A',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            '#',
            'Institute Code',
            'Institute Name',
            'Category',
            'District',
            'Block / ULB',
            'Coverage Status',
            'Total Visits Count',
            'Last Visit Date',
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

