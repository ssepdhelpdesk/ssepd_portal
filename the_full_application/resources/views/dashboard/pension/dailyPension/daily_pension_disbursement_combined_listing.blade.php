@section('title') 
Pension || GP/Ward wise Daily Basis Pension Disbursement for the month - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
            <h4 class="card-title">GP/Ward wise Daily Basis Pension Disbursement for the month - {{$forTheMonth}}</h4>
            @include('dashboard.component.message')
            <div class="table-responsive m-t-40">
             <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
               <thead>
                  <tr>
                            <th>Sl. No</th>
                            <th>District</th>
                            <th>Block / ULB Name</th>
                            <th>GP / Ward Name</th>
                            <th>Disbursement Dates</th>
                            <th>Status</th>
                            @foreach($numericColumns as $col)
                            <th>{{ str_replace('_',' ',ucfirst($col)) }}</th>
                            @endforeach
                        </tr>
               </thead>
               <tfoot>
                  <tr>
                            <th>Sl. No</th>
                            <th>District</th>
                            <th>Block / ULB Name</th>
                            <th>GP / Ward Name</th>
                            <th>Disbursement Dates</th>
                            <th>Status</th>
                            @foreach($numericColumns as $col)
                            <th>{{ str_replace('_',' ',ucfirst($col)) }}</th>
                            @endforeach
                        </tr>
               </tfoot>
               
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
   $(function() {
     $('#example23').DataTable({
       processing: true,
       serverSide: true,
       ajax: "{{ route('admin.dailypensiondisbursement.combined_report') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'district_name', name: 'district_name' },
            { data: 'block_ulb_name', name: 'block_ulb_name' },
            { data: 'gp_ward_name', name: 'gp_ward_name' },
            { data: 'disbursement_dates', name: 'disbursement_dates' },
            { data: 'status', name: 'status',
              render: function(data, type, row) {
                  if(data === 'Submitted') {
                      return '<span class="badge bg-success">Submitted</span>';
                  } else {
                      return '<span class="badge bg-danger">Not Submitted</span>';
                  }
              }
            },
            @foreach($numericColumns as $col)
            { data: 'totals.{{ "total_".$col }}', name: '{{ "total_".$col }}', defaultContent: 0 },
            @endforeach
        ],
      scrollX: true,
      lengthMenu: [[10, 50, 100, -1],[10, 50, 100, "All"]],
      dom: 'Blfrtip',
      buttons: ['copy','csv','excel','pdf','print']
   });
  });
</script>
@endsection