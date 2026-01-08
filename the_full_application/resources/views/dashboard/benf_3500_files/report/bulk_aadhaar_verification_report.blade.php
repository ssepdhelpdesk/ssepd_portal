<h4>Scheme-wise Aadhaar Verification Report</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Scheme</th>
            <th>Verification Remarks</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($schemeWise as $row)
        <tr>
            <td>{{ $row->scheme }}</td>
            <td>{{ $row->verified_aadhar_remarks }}</td>
            <td>{{ $row->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4>Combined Summary</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Verification Remarks</th>
            <th>Total Count</th>
        </tr>
    </thead>
    <tbody>
        @foreach($combined as $remark => $count)
        <tr>
            <td>{{ $remark }}</td>
            <td>{{ $count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
