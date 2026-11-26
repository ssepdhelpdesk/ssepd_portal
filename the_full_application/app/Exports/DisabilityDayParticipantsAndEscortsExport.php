<?php

namespace App\Exports;

use App\Models\DisabilityDayParticipantRegistration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DisabilityDayParticipantsAndEscortsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle,
    WithEvents
{
    protected Builder $query;
    protected int $serialNo = 0;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function title(): string
    {
        return 'Participants & Escorts';
    }

    public function query(): Builder
    {
        return $this->query
            ->with(['escorts', 'district', 'state'])
            ->orderBy('disability_day_participant_registrations.id', 'asc');
    }

    public function headings(): array
    {
        return [
            'Sl.No',
            'Participant Registration No.',
            'Participant Name',
            'Institute / School Name',
            'Gender',
            'T-Shirt Size',
            'Participation Category',
            'Participation Other Details',
            'Dates of Participation',
            'Mode of Transportation',
            'Transportation Other Details',
            'Expected Arrival Date',
            'Expected Arrival Time',
            'District',
            'Block',
            'Grampanchayat (GP)',
            'Village',
            'Municipality / ULB',
            'PIN Code',
            'Declared No. of Escorts',
            'Escort Details',
        ];
    }

    public function map($row): array
    {
        $this->serialNo++;

        $districtName = $row->getDistrictDisplayName();
        $districtName = $districtName === '—' ? '' : $districtName;

        $isRural = (int) $row->address_type === 1;
        $isUrban = (int) $row->address_type === 2;

        $blockName = $isRural ? trim((string) ($row->block_name ?? '')) : '';
        $gpName = $isRural ? trim((string) ($row->gp_name ?? '')) : '';
        $villageName = $isRural ? trim((string) ($row->village_name ?? '')) : '';
        $municipalityName = $isUrban ? trim((string) ($row->municipality_name ?? '')) : '';

        $attendingDates = '';
        if (!empty($row->attending_dates)) {
            $datesArr = is_array($row->attending_dates)
                ? $row->attending_dates
                : json_decode((string) $row->attending_dates, true);
            $attendingDates = DisabilityDayParticipantRegistration::formatAttendingDatesForDisplay(
                is_array($datesArr) ? $datesArr : null
            );
        }

        $arrivalDate = $row->expected_arrival_date
            ? Carbon::parse($row->expected_arrival_date)->format('d-M-Y')
            : '';

        $arrivalTime = '';
        if (!empty($row->expected_arrival_time)) {
            try {
                $arrivalTime = Carbon::parse($row->expected_arrival_time)->format('h:i A');
            } catch (\Throwable $e) {
                $arrivalTime = (string) $row->expected_arrival_time;
            }
        }

        $escortDetailsText = $this->formatEscortDetails($row);

        return [
            $this->serialNo,
            $row->registration_no ?? '',
            $row->participant_name ?? '',
            $row->school_name ?? '',
            $row->gender ?? '',
            $row->tshirt_size ?? '',
            $row->participation_in ?? '',
            $row->participation_other ?? '',
            $attendingDates,
            $row->transport_mode ?? '',
            $row->transport_mode_other ?? '',
            $arrivalDate,
            $arrivalTime,
            $districtName,
            $blockName,
            $gpName,
            $villageName,
            $municipalityName,
            $row->pin ?: ($row->postal_pin ?? ''),
            (int) $row->number_of_escorts,
            $escortDetailsText,
        ];
    }

    /**
     * Format all associated escorts for a participant into a single readable multiline cell.
     */
    protected function formatEscortDetails(DisabilityDayParticipantRegistration $participant): string
    {
        $escorts = $participant->escorts;
        if (!$escorts || $escorts->isEmpty()) {
            return 'No Escort';
        }

        $blocks = [];
        foreach ($escorts->values() as $idx => $escort) {
            $lines = ['Escort ' . ($idx + 1)];

            if (!empty(trim((string) $escort->name))) {
                $lines[] = 'Name: ' . trim((string) $escort->name);
            }
            if (!empty(trim((string) $escort->relationship))) {
                $lines[] = 'Relationship: ' . trim((string) $escort->relationship);
            }
            if (!empty(trim((string) $escort->mobile))) {
                $lines[] = 'Mobile: ' . trim((string) $escort->mobile);
            }
            if (!empty(trim((string) $escort->email))) {
                $lines[] = 'Email: ' . trim((string) $escort->email);
            }

            $blocks[] = implode("\n", $lines);
        }

        return implode("\n---\n", $blocks);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B5E20'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Freeze header row
                $sheet->freezePane('A2');

                // Set readable width for Escort Details column (U) and enable Wrap Text
                $sheet->getColumnDimension('U')->setAutoSize(false);
                $sheet->getColumnDimension('U')->setWidth(45);

                if ($highestRow >= 2) {
                    $sheet->getStyle("A2:U{$highestRow}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP);

                    $sheet->getStyle("U2:U{$highestRow}")
                        ->getAlignment()
                        ->setWrapText(true);

                    // Auto-adjust row heights for multiline Escort Details
                    for ($r = 2; $r <= $highestRow; $r++) {
                        $val = (string) $sheet->getCell("U{$r}")->getValue();
                        $lineCount = substr_count($val, "\n") + 1;
                        if ($lineCount > 1) {
                            $sheet->getRowDimension($r)->setRowHeight(max(20, $lineCount * 15 + 6));
                        }
                    }
                }
            },
        ];
    }
}
