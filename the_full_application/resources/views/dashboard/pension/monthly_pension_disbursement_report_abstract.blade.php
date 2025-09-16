@section('title') 
Pension || Block/ULB wise Pension Disbursement Details for the month - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
            <h4 class="card-title">Block/ULB wise Pension Disbursement Details for the month - {{$forTheMonth}}</h4>
            @include('dashboard.component.message')
            <div class="table-responsive m-t-40">
             <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
               <thead>
                  <tr>
                   <th>Sl. No.</th>
                   <th>District</th>
                   <th>Address Type</th>
                   <th>Block / ULB Name</th>
                   <th>GPs Submitted</th>
                   <th>Wards Submitted</th>
                   <th>GPs Pending</th>
                   <th>Wards Pending</th>
                </tr>
             </thead>
             <tfoot>
               <tr>
                <th>Sl. No.</th>
                <th>District</th>
                <th>Address Type</th>
                <th>Block / ULB Name</th>
                <th>GPs Submitted</th>
                <th>Wards Submitted</th>
                <th>GPs Pending</th>
                <th>Wards Pending</th>
             </tr>
          </tfoot>
          <tbody>
           @php
           $totalProvidedGP   = 0;
           $totalProvidedWard = 0;
           $totalPendingGP    = 0;
           $totalPendingWard  = 0;
           @endphp

           @forelse($summaryReport as $index => $row)
           @php
           $totalProvidedGP   += $row['Data_provided_by_GP'];
           $totalProvidedWard += $row['Data_provided_by_ward'];
           $totalPendingGP    += $row['Data_not_provided_by_GP'];
           $totalPendingWard  += $row['Data_not_provided_by_ward'];
           @endphp
           <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $row['district'] }}</td>
            <td>{{ $row['type'] }}</td>
            <td>{{ $row['block_or_ulb'] }}</td>
            <td>{{ $row['Data_provided_by_GP'] }}</td>
            <td>{{ $row['Data_provided_by_ward'] }}</td>
            <td>{{ $row['Data_not_provided_by_GP'] }}</td>
            <td>{{ $row['Data_not_provided_by_ward'] }}</td>
         </tr>
         @empty
         <tr>
            <td colspan="8" class="text-center">No records found</td>
         </tr>
         @endforelse                
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