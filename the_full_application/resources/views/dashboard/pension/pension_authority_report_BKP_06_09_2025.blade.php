@section('title') 
Pension || Block/ULB wise Pension Disbursing Office Details || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection 
@extends('dashboard.layouts.main')
@section('style')
@endsection 
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
            <h4 class="card-title">Block/ULB wise Pension Disbursing Office Details</h4>
            @include('dashboard.component.message')
            <div class="table-responsive m-t-40">
               <table id="pensionTable" class="display nowrap table table-hover table-striped border" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>District</th>
                                    <th>Block/ULB</th>
                                    <th>Gp/Ward</th>
                                    <th>Provided/Not Provided</th>
                                    <th>Officer Name</th>
                                    <th>Mobile No</th>
                                    <th>Designation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
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
    $('#pensionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.pension.pension_authority_report') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'district_name', name: 'district_name' },
            { data: 'block_ulb', name: 'block_ulb' },
            { data: 'gp_ward', name: 'gp_ward' },
            { data: 'provided_status', name: 'provided_status', orderable: false, searchable: false },
            { data: 'authority_name', name: 'authority_name' },
            { data: 'authority_mobile_no', name: 'authority_mobile_no' },
            { data: 'designation', name: 'designation' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        scrollX: true,
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
});
</script>
@endsection