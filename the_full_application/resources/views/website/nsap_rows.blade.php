@forelse ($nsapDump as $index => $row)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $row->applicant_name ?? '-' }}</td>
    <td>{{ $row->father_husband_name ?? '-' }}</td>
    <td>{{ $row->scheme ?? '-' }}</td>

    <td>
        @php
            $value = $row->sanction_date;
            if (is_numeric($value)) {
                echo \Carbon\Carbon::create(1899,12,30)->addDays((int)$value)->diffForHumans();
            } elseif ($value) {
                echo \Carbon\Carbon::parse($value)->diffForHumans();
            } else {
                echo '-';
            }
        @endphp
    </td>

    <td>{{ $row->sanction_order_no ?? '-' }}</td>
    <td>{{ $row->disbursement_mode ?? '-' }}</td>

    <td>
        @php
            $value = $row->disbursement_upto;
            if (is_numeric($value)) {
                echo \Carbon\Carbon::create(1899,12,30)->addDays((int)$value)->format('d M Y');
            } elseif ($value) {
                echo \Carbon\Carbon::parse($value)->format('d M Y');
            } else {
                echo '-';
            }
        @endphp
    </td>

    <td>{{ $row->district }}</td>
    <td>{{ $row->area === 'R' ? 'Rural' : 'Urban' }}</td>
    <td>{{ $row->sub_district_municipality }}</td>
    <td>{{ $row->gram_panchayat_ward }}</td>
    <td>{{ $row->status }}</td>
</tr>
@empty
<tr>
    <td colspan="14" class="text-center text-muted">Select Your Respective Distict, Address Type, Block/ULB & GP/Ward to view the results.</td>
</tr>
@endforelse
