<?php

namespace App\Exports;

use App\Models\DisabilityDayParticipantRegistration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DisabilityDayParticipantsAndEscortsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected Builder $query;

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
            ->leftJoin(
                'disability_day_escorts as escorts',
                'escorts.participant_registration_id',
                '=',
                'disability_day_participant_registrations.id'
            )
            ->leftJoin(
                'districts',
                'districts.district_id',
                '=',
                'disability_day_participant_registrations.district_id'
            )
            ->leftJoin(
                'states',
                'states.state_id',
                '=',
                'disability_day_participant_registrations.state_id'
            )
            ->whereNull('disability_day_participant_registrations.deleted_at')
            ->select([
                'disability_day_participant_registrations.*',
                'districts.district_name as master_district_name',
                'states.state_name as master_state_name',
                'escorts.id as escort_id',
                'escorts.escort_index as escort_index',
                'escorts.name as escort_name',
                'escorts.relationship as escort_relationship',
                'escorts.mobile as escort_mobile',
                'escorts.email as escort_email',
                'escorts.created_at as escort_created_at',
            ])
            ->orderBy('disability_day_participant_registrations.id', 'asc')
            ->orderBy('escorts.escort_index', 'asc')
            ->orderBy('escorts.id', 'asc');
    }

    public function headings(): array
    {
        return [
            'Participant ID',
            'Participant Registration No.',
            'Participant Name',
            'Institute ID',
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
            'Address Type',
            'State',
            'District',
            'Block',
            'Grampanchayat (GP)',
            'Village',
            'Municipality / ULB',
            'PIN Code',
            'Complete Address',
            'Declared No. of Escorts',
            'Financial Year',
            'Applied At',
            'Registration Status',
            'Escort ID',
            'Escort Sl. No.',
            'Escort Name',
            'Relationship with Participant',
            'Escort Mobile Number',
            'Escort Email',
            'Escort Created At',
        ];
    }

    public function map($row): array
    {
        $districtName = $row->master_district_name ?: ($row->district_name ?: '');
        $stateName = $row->master_state_name ?: ($row->state_name ?: '');

        // Ensure complete_address uses resolved master state name if relation isn't loaded
        if (!empty($stateName) && empty($row->state_name)) {
            $row->state_name = $stateName;
        }

        $addressTypeLabel = (int) $row->address_type === 1
            ? 'Rural (Block / GP / Village)'
            : ((int) $row->address_type === 2 ? 'Urban (Municipality / ULB)' : '');

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

        $appliedAt = $row->applied_at
            ? Carbon::parse($row->applied_at)->format('d-M-Y h:i A')
            : '';

        $escortCreatedAt = !empty($row->escort_created_at)
            ? Carbon::parse($row->escort_created_at)->format('d-M-Y h:i A')
            : '';

        return [
            $row->id,
            $row->registration_no,
            $row->participant_name,
            $row->institute_id ?? '',
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
            $addressTypeLabel,
            $stateName,
            $districtName,
            $row->block_name ?? '',
            $row->gp_name ?? '',
            $row->village_name ?? '',
            $row->municipality_name ?? '',
            $row->pin ?: ($row->postal_pin ?? ''),
            $row->complete_address === '—' ? '' : $row->complete_address,
            (int) $row->number_of_escorts,
            $row->financial_year ?? '',
            $appliedAt,
            (int) $row->status === 1 ? 'Registered' : 'Inactive',
            $row->escort_id ?? '',
            $row->escort_index ?? '',
            $row->escort_name ?? '',
            $row->escort_relationship ?? '',
            $row->escort_mobile ?? '',
            $row->escort_email ?? '',
            $escortCreatedAt,
        ];
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
            ],
        ];
    }
}
