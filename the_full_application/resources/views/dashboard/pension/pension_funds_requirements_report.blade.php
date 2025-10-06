@extends('dashboard.layouts.main')

@section('title') 
Pension || MBPY Fund Requirements for the month - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection 

@section('style')
<style>
    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
    }
    table.dataTable {
        white-space: nowrap;
    }
</style>
@endsection 

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-7 align-self-center">
            <div class="d-flex align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">@yield('title')</li>
                </ol>
            </div>
        </div>
        <div class="col-md-5 align-self-center text-end">
            <button onclick="history.back()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info">
                <i class="fas fa-arrow-alt-circle-left"></i> Go Back
            </button>
        </div>
    </div>

    <!-- Month Selection -->
    <div class="row mb-3">
        <div class="col-md-3">
            <form id="monthFilterForm">
                <div class="form-group">
                    <label class="form-label">Select Month <span class="itsrequired">*</span></label>
                    <select class="select2 form-control form-select" name="for_the_month" id="for_the_month">
                        <option value="">--Select--</option>
                        @foreach($dateConfig as $config)
                        <option value="{{ $config->for_the_month }}" {{ $forTheMonth == $config->for_the_month ? 'selected' : '' }}>
                            {{ $config->for_the_month }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('dashboard.component.message')
                    <div class="table-responsive m-t-40">
                        <table id="example23" class="display nowrap table table-hover table-striped border" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>District</th>
                                    <th>For the Month</th>
                                    <th>Block/ULB Name</th>
                                    <th>Status</th>
                                    <th>MBPY OAP Below 80 Years</th>
                                    <th>Fund Below 80</th>
                                    <th>MBPY OAP Above 80 Years</th>
                                    <th>Fund Above 80</th>
                                    <th>MBPY WP</th>
                                    <th>Fund WP</th>
                                    <th>MBPY DP</th>
                                    <th>Fund DP</th>
                                    <th>MBPY SDP Below 80%</th>
                                    <th>Fund SDP Below 80</th>
                                    <th>MBPY SDP Above 80%</th>
                                    <th>Fund SDP Above 80</th>
                                    <th>MBPY SDOAP</th>
                                    <th>Fund SDOAP</th>
                                    <th>MBPY CLP</th>
                                    <th>Fund CLP</th>
                                    <th>MBPY WP AIDS</th>
                                    <th>Fund WP AIDS</th>
                                    <th>MBPY DP AIDS</th>
                                    <th>Fund DP AIDS</th>
                                    <th>MBPY Unmarried Women</th>
                                    <th>Fund Unmarried Women</th>
                                    <th>MBPY Orphan Due To COVID</th>
                                    <th>Fund Orphan</th>
                                    <th>Total Fund</th>
                                    <th>A/C No</th>
                                    <th>IFSC Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Sl No</th>
                                    <th>District</th>
                                    <th>For the Month</th>
                                    <th>Block/ULB Name</th>
                                    <th>Status</th>
                                    <th>MBPY OAP Below 80 Years</th>
                                    <th>Fund Below 80</th>
                                    <th>MBPY OAP Above 80 Years</th>
                                    <th>Fund Above 80</th>
                                    <th>MBPY WP</th>
                                    <th>Fund WP</th>
                                    <th>MBPY DP</th>
                                    <th>Fund DP</th>
                                    <th>MBPY SDP Below 80%</th>
                                    <th>Fund SDP Below 80</th>
                                    <th>MBPY SDP Above 80%</th>
                                    <th>Fund SDP Above 80</th>
                                    <th>MBPY SDOAP</th>
                                    <th>Fund SDOAP</th>
                                    <th>MBPY CLP</th>
                                    <th>Fund CLP</th>
                                    <th>MBPY WP AIDS</th>
                                    <th>Fund WP AIDS</th>
                                    <th>MBPY DP AIDS</th>
                                    <th>Fund DP AIDS</th>
                                    <th>MBPY Unmarried Women</th>
                                    <th>Fund Unmarried Women</th>
                                    <th>MBPY Orphan Due To COVID</th>
                                    <th>Fund Orphan</th>
                                    <th>Total Fund</th>
                                    <th>A/C No</th>
                                    <th>IFSC Code</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 

@section('script')
<script>
 $(function () {
    let table = $('#example23').DataTable({
       processing: true,
       serverSide: true,
       responsive: false,
       ordering: true,
       scrollX: true,
       ajax: {
            url: '{{ route("admin.pension.report") }}',
            type: 'GET',
            data: function(d){
                d.for_the_month = $('#for_the_month').val();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('AJAX error! Check console for details.');
            }
       },
       columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'district', name: 'district' },
            { data: 'for_the_month', name: 'for_the_month' },
            { data: 'unit', name: 'unit' },
            { data: 'status', name: 'status' },
            { data: 'mbpy_oap_below_80_years', name: 'mbpy_oap_below_80_years' },
            { data: 'fund_below_80', name: 'fund_below_80' },
            { data: 'mbpy_oap_above_80_years', name: 'mbpy_oap_above_80_years' },
            { data: 'fund_above_80', name: 'fund_above_80' },
            { data: 'mbpy_wp', name: 'mbpy_wp' },
            { data: 'fund_wp', name: 'fund_wp' },
            { data: 'mbpy_dp', name: 'mbpy_dp' },
            { data: 'fund_dp', name: 'fund_dp' },
            { data: 'mbpy_sdp_below_80_percent', name: 'mbpy_sdp_below_80_percent' },
            { data: 'fund_sdp_below_80', name: 'fund_sdp_below_80' },
            { data: 'mbpy_sdp_above_80_percent', name: 'mbpy_sdp_above_80_percent' },
            { data: 'fund_sdp_above_80', name: 'fund_sdp_above_80' },
            { data: 'mbpy_sdoap', name: 'mbpy_sdoap' },
            { data: 'fund_sdoap', name: 'fund_sdoap' },
            { data: 'mbpy_clp', name: 'mbpy_clp' },
            { data: 'fund_clp', name: 'fund_clp' },
            { data: 'mbpy_wp_aids', name: 'mbpy_wp_aids' },
            { data: 'fund_wp_aids', name: 'fund_wp_aids' },
            { data: 'mbpy_dp_aids', name: 'mbpy_dp_aids' },
            { data: 'fund_dp_aids', name: 'fund_dp_aids' },
            { data: 'mbpy_unmarried_women', name: 'mbpy_unmarried_women' },
            { data: 'fund_unmarried_women', name: 'fund_unmarried_women' },
            { data: 'mbpy_orphan_due_to_covide', name: 'mbpy_orphan_due_to_covide' },
            { data: 'fund_mbpy_orphan_due_to_covide', name: 'fund_mbpy_orphan_due_to_covide' },
            { data: 'total_fund', name: 'total_fund', render: $.fn.dataTable.render.number(',', '.', 0) },
            { data: 'account', name: 'account', orderable: false, searchable: false },
            { data: 'ifsc', name: 'ifsc', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
       ],
       dom: 'Blfrtip',
       buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
       lengthMenu: [[10, 50, 100, -1], [10, 50, 100, 'All']],
       drawCallback: function() {
            $('[data-toggle="tooltip"]').tooltip();
       }
    });

    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
        .addClass('btn btn-primary me-1');

    $('#for_the_month').on('change', function(){
        table.ajax.reload();
    });
 });
</script>
@endsection
