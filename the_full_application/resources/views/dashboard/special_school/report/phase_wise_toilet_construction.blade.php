@section('title') 
Special School || Toilet Construction Status
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
#example23 {
    border-collapse: collapse !important;
    width: 100% !important;
}

#example23 th,
#example23 td {
    border: 1px solid #dee2e6 !important;
    vertical-align: middle !important;
    text-align: center;
    white-space: nowrap;
}

#example23 th.wrap-text,
#example23 td.wrap-text {
    white-space: normal !important;
    word-break: break-word;
}

#example23 thead th,
#example23 tfoot th {
    background: #f8f9fa;
    font-weight: 600;
}
</style>
@endsection 
@php
use Illuminate\Support\Str;
@endphp
@section('content')
<div class="container-fluid">
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
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
        <button onclick="history.back()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info"><i class="fas fa-arrow-alt-circle-left"></i> Go Back</button>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<!-- row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"></h4>
                @include('dashboard.component.message')
                <div class="table-responsive m-t-40">
                    <table id="example23" class="table table-hover table-striped table-bordered w-100">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="2">Sl. No.</th>
                                <th rowspan="2">District Name</th>
                                <th rowspan="2">Type</th>
                                <th rowspan="2" class="wrap-text" style="min-width:180px;">Management Name</th>
                                <th rowspan="2" class="wrap-text" style="min-width:220px;">Special School Name</th>
                                <th rowspan="2">New / Existing</th>

                                <th colspan="5">Phase 1</th>
                                <th colspan="5">Phase 2</th>
                                <th colspan="5">Phase 3</th>
                                <th colspan="5">Phase 4</th>
                                <th colspan="5">Phase 5</th>
                            </tr>

                            <tr>
                                <!-- Phase 1 -->
                                <th>Phase-1 Upload Status</th>
                                <th>Phase-1 Uploaded On</th>
                                <th>Phase-1 Approved On</th>
                                <th>Phase-1 Approval Status</th>
                                <th>Phase-1 Approver Remarks</th>

                                <!-- Phase 2 -->
                                <th>Phase-2 Upload Status</th>
                                <th>Phase-2 Uploaded On</th>
                                <th>Phase-2 Approved On</th>
                                <th>Phase-2 Approval Status</th>
                                <th>Phase-2 Approver Remarks</th>

                                <!-- Phase 3 -->
                                <th>Phase-3 Upload Status</th>
                                <th>Phase-3 Uploaded On</th>
                                <th>Phase-3 Approved On</th>
                                <th>Phase-3 Approval Status</th>
                                <th>Phase-3 Approver Remarks</th>

                                <!-- Phase 4 -->
                                <th>Phase-4 Upload Status</th>
                                <th>Phase-4 Uploaded On</th>
                                <th>Phase-4 Approved On</th>
                                <th>Phase-4 Approval Status</th>
                                <th>Phase-4 Approver Remarks</th>

                                <!-- Phase 5 -->
                                <th>Phase-5 Upload Status</th>
                                <th>Phase-5 Uploaded On</th>
                                <th>Phase-5 Approved On</th>
                                <th>Phase-5 Approval Status</th>
                                <th>Phase-5 Approver Remarks</th>
                            </tr>
                        </thead>
                        <tfoot class="text-center">
                            <tr>
                                <th rowspan="2">Sl. No.</th>
                                <th rowspan="2">District Name</th>
                                <th rowspan="2">Type</th>
                                <th rowspan="2" class="wrap-text" style="min-width:180px;">Management Name</th>
                                <th rowspan="2" style="white-space: normal; min-width: 220px;">Special School Name</th>
                                <th rowspan="2">New / Existing</th>

                                <th colspan="5">Phase 1</th>
                                <th colspan="5">Phase 2</th>
                                <th colspan="5">Phase 3</th>
                                <th colspan="5">Phase 4</th>
                                <th colspan="5">Phase 5</th>
                            </tr>

                            <tr>
                                <!-- Phase 1 -->
                                <th>Phase-1 Upload Status</th>
                                <th>Phase-1 Uploaded On</th>
                                <th>Phase-1 Approved On</th>
                                <th>Phase-1 Approval Status</th>
                                <th>Phase-1 Approver Remarks</th>

                                <!-- Phase 2 -->
                                <th>Phase-2 Upload Status</th>
                                <th>Phase-2 Uploaded On</th>
                                <th>Phase-2 Approved On</th>
                                <th>Phase-2 Approval Status</th>
                                <th>Phase-2 Approver Remarks</th>

                                <!-- Phase 3 -->
                                <th>Phase-3 Upload Status</th>
                                <th>Phase-3 Uploaded On</th>
                                <th>Phase-3 Approved On</th>
                                <th>Phase-3 Approval Status</th>
                                <th>Phase-3 Approver Remarks</th>

                                <!-- Phase 4 -->
                                <th>Phase-4 Upload Status</th>
                                <th>Phase-4 Uploaded On</th>
                                <th>Phase-4 Approved On</th>
                                <th>Phase-4 Approval Status</th>
                                <th>Phase-4 Approver Remarks</th>

                                <!-- Phase 5 -->
                                <th>Phase-5 Upload Status</th>
                                <th>Phase-5 Uploaded On</th>
                                <th>Phase-5 Approved On</th>
                                <th>Phase-5 Approval Status</th>
                                <th>Phase-5 Approver Remarks</th>
                            </tr>
                        </tfoot>

                        <tbody>
                            @foreach($reports as $key => $row)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $row->District_Name }}</td>
                                <td>{{ $row->Type }}</td>
                                <td class="wrap-text">{{ $row->Management_Name }}</td>
                                <td class="wrap-text">{{ $row->School_Name }}</td>
                                <td>{{ $row->New_Existing }}</td>

                                <!-- Phase 1 -->
                                <td>{{ $row->phase_1_upload_status }}</td>
                                <td>{{ $row->phase_1_uploaded_on }}</td>
                                <td>{{ $row->phase_1_approved_on }}</td>
                                <td>{{ $row->phase_1_approval_status }}</td>
                                <td>{{ $row->phase_1_approver_remarks }}</td>

                                <!-- Phase 2 -->
                                <td>{{ $row->phase_2_upload_status }}</td>
                                <td>{{ $row->phase_2_uploaded_on }}</td>
                                <td>{{ $row->phase_2_approved_on }}</td>
                                <td>{{ $row->phase_2_approval_status }}</td>
                                <td>{{ $row->phase_2_approver_remarks }}</td>

                                <!-- Phase 3 -->
                                <td>{{ $row->phase_3_upload_status }}</td>
                                <td>{{ $row->phase_3_uploaded_on }}</td>
                                <td>{{ $row->phase_3_approved_on }}</td>
                                <td>{{ $row->phase_3_approval_status }}</td>
                                <td>{{ $row->phase_3_approver_remarks }}</td>

                                <!-- Phase 4 -->
                                <td>{{ $row->phase_4_upload_status }}</td>
                                <td>{{ $row->phase_4_uploaded_on }}</td>
                                <td>{{ $row->phase_4_approved_on }}</td>
                                <td>{{ $row->phase_4_approval_status }}</td>
                                <td>{{ $row->phase_4_approver_remarks }}</td>

                                <!-- Phase 5 -->
                                <td>{{ $row->phase_5_upload_status }}</td>
                                <td>{{ $row->phase_5_uploaded_on }}</td>
                                <td>{{ $row->phase_5_approved_on }}</td>
                                <td>{{ $row->phase_5_approval_status }}</td>
                                <td>{{ $row->phase_5_approver_remarks }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- row -->
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
</div>
@endsection 
@section('script')
<script>
    $(function () {
        $('#example23').DataTable({
            processing: true,
            responsive: false,
            ordering: true,
            scrollX: true,
            lengthMenu: [[10, 500, 1000, -1], [10, 500, 1000, "All"]],
            dom: 'Blfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
    });   
</script>
@endsection