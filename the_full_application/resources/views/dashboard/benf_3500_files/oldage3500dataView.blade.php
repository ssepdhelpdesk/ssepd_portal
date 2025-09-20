@extends('layouts.app')
@section('title', 'Old Age Pensioner Data (80 and Above)')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Old Age Pensioner Beneficiaries (80 Years & Above)</h4>

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
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#oldAgeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.oldage3500data.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'scheme_name', name: 'scheme_name'},
            {data: 'updated_scheme_name', name: 'updated_scheme_name'},
            {data: 'name_of_the_beneficiary', name: 'name_of_the_beneficiary'},
            {data: 'father_or_husband_name', name: 'father_or_husband_name'},
            {data: 'date_of_birth', name: 'date_of_birth'},
            {data: 'age', name: 'age'},
            {data: 'gender', name: 'gender'},
            {data: 'district', name: 'district'},
            {data: 'block_or_ulb', name: 'block_or_ulb'},
            {data: 'gp_or_ward', name: 'gp_or_ward'},
            {data: 'village', name: 'village'},
            {data: 'aadhaar_no', name: 'aadhaar_no'},
            {data: 'nsap_sanction_order_no', name: 'nsap_sanction_order_no'},
            {data: 'sub_collector_sanction_order_no', name: 'sub_collector_sanction_order_no'},
            {data: 'pension_month', name: 'pension_month'},
            {data: 'status', name: 'status'},
            {data: 'created_by', name: 'created_by'},
            {data: 'created_by_date', name: 'created_by_date'},
            {data: 'discontinued_date', name: 'discontinued_date'},
            {data: 'discontinued_reason', name: 'discontinued_reason'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[8, 'asc']]
    });
});
</script>
@endpush
