<?php

namespace App\Exports\FieldVisit;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FieldVisitDetailedExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected Collection $visits;

    public function __construct(Collection $visits)
    {
        $this->visits = $visits;
    }

    public function collection(): Collection
    {
        $data = [];
        $i = 1;
        foreach ($this->visits as $v) {
            $desig = $v->visiting_officer_designation === 'Other' && !empty($v->other_designation)
                ? $v->other_designation
                : $v->visiting_officer_designation;

            $blockOrUlb = $v->address_type === 1
                ? ($v->block ? $v->block->block_name : '')
                : ($v->municipality ? $v->municipality->municipality_name : '');

            $gpOrWard = $v->address_type === 1
                ? ($v->grampanchayat ? $v->grampanchayat->gp_name : '')
                : ($v->ward ? ($v->ward->ward_name ?: 'Ward ' . $v->ward_id) : '');

            $data[] = [
                'sl_no'                 => $i++,
                'visit_ref_no'          => $v->visit_reference_no,
                'visit_date'            => $v->visit_date ? $v->visit_date->format('d-M-Y') : 'N/A',
                'visit_type'            => $v->visitType ? $v->visitType->name : 'N/A',
                'category_name'         => $v->institute ? $v->institute->category_name : ($v->institution_type ?: 'N/A'),
                'institution_name'      => $v->institution_name,
                'area_type'             => $v->address_type === 1 ? 'Rural' : ($v->address_type === 2 ? 'Urban' : 'N/A'),
                'district'              => $v->district ? $v->district->district_name : 'N/A',
                'block_ulb'             => $blockOrUlb ?: 'N/A',
                'gp_ward'               => $gpOrWard ?: 'N/A',
                'loggedin_officer'      => $v->visitingOfficer ? $v->visitingOfficer->name : 'ID #' . $v->visiting_officer_id,
                'visiting_officer_name' => $v->visiting_officer_name ?: 'N/A',
                'visiting_designation'  => $desig ?: 'N/A',
                'key_observations'      => $v->key_observations ?: 'N/A',
                'recommendations'       => $v->recommendations ?: 'N/A',
                'status'                => strtoupper($v->status),
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            '#',
            'Visit Reference No',
            'Visit Date',
            'Visit Type',
            'Category',
            'Institution Name',
            'Area Type',
            'District',
            'Block / ULB',
            'GP / Ward',
            'Logged-in Officer',
            'Visiting Officer Name',
            'Visiting Officer Designation',
            'Key Observations',
            'Recommendations',
            'Status',
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

