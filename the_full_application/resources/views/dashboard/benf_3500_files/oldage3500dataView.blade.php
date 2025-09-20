{{-- resources/views/dashboard/benf_3500_files/oldage3500dataView.blade.php --}}
@extends('layouts.app') {{-- adjust if you use another layout --}}

@section('title', 'Old Age Pensioner Data (80 and Above)')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Old Age Pensioner Beneficiaries (80 Years & Above)</h4>

    {{-- Success / error messages --}}
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table id="oldAgeTable" class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Scheme Name</th>
                    <th>Updated Scheme</th>
                    <th>Beneficiary Name</th>
                    <th>Father/Husband Name</th>
                    <th>DOB</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>District</th>
                    <th>Block / ULB</th>
                    <th>GP / Ward</th>
                    <th>Village</th>
                    <th>Aadhaar No</th>
                    <th>NSAP Sanction Order</th>
                    <th>Sub-Collector Sanction Order</th>
                    <th>Pension Month</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Created Date</th>
                    <th>Discontinued Date</th>
                    <th>Discontinued Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($old_age_ep_data as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->scheme_name }}</td>
                        <td>{{ $row->updated_scheme_name }}</td>
                        <td>{{ $row->name_of_the_beneficiary }}</td>
                        <td>{{ $row->father_or_husband_name }}</td>
                        <td>{{ $row->date_of_birth }}</td>
                        <td>{{ $row->age }}</td>
                        <td>{{ $row->gender }}</td>
                        <td>{{ $row->district }}</td>
                        <td>{{ $row->block_or_ulb }}</td>
                        <td>{{ $row->gp_or_ward }}</td>
                        <td>{{ $row->village }}</td>
                        <td>{{ $row->aadhaar_no }}</td>
                        <td>{{ $row->nsap_sanction_order_no }}</td>
                        <td>{{ $row->sub_collector_sanction_order_no }}</td>
                        <td>{{ $row->pension_month }}</td>
                        <td>{{ $row->status }}</td>
                        <td>{{ $row->created_by }}</td>
                        <td>{{ $row->created_by_date }}</td>
                        <td>{{ $row->discontinued_date }}</td>
                        <td>{{ $row->discontinued_reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="21" class="text-center text-danger">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#oldAgeTable').DataTable({
        pageLength: 25,
        ordering: true,
        searching: true,
    });
});
</script>
@endpush
