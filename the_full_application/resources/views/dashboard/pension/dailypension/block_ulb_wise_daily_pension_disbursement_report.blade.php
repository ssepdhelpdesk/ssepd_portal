@section('title') 
Pension || Block/ULB Wise Daily Pension Disbursement - {{$selectedDate}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
            <h4 class="card-title">Block / ULB Wise Daily Pension Disbursement Report - {{$selectedDate}}</h4>
            @include('dashboard.component.message')
            <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label><strong>Select Disbursement Start Date:</strong></label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}">
            </div>

            <div class="col-md-3 mt-4">
                <button type="submit" class="btn btn-primary mt-2">Search</button>
            </div>
        </div>
    </form>
            
            <div class="table-responsive m-t-40">
             <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
               <thead>
                  <tr>
                     <th>Sl.No</th>
                    <th>District Name</th>
                    <th>Address Type (Block/ULB)</th>
                    <th>Block / ULB Name</th>
                    <th>Status</th>
                    <th>No. of Normal Pensioners</th>
                    <th>No. of EP Pensioners</th>
                    <th>Funds for Normal Pensioners</th>
                    <th>Funds for EP Pensioners</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th>Sl.No</th>
                    <th>District Name</th>
                    <th>Address Type (Block/ULB)</th>
                    <th>Block / ULB Name</th>
                    <th>Status</th>
                    <th>No. of Normal Pensioners</th>
                    <th>No. of EP Pensioners</th>
                    <th>Funds for Normal Pensioners</th>
                    <th>Funds for EP Pensioners</th>
                  </tr>
               </tfoot>
               <tbody>
                @foreach ($data as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row['district_name'] }}</td>
                        <td>{{ $row['staff_address_type'] }}</td>
                        <td>{{ $row['block_ulb_name'] }}</td>
                        <td>{{ $row['status'] }}</td>
                        <td>{{ number_format($row['no_of_normal_pensioners']) }}</td>
                        <td>{{ number_format($row['no_of_ep_pensioners']) }}</td>
                        <td>{{ number_format($row['funds_no_of_normal_pensioners']) }}</td>
                        <td>{{ number_format($row['funds_no_of_ep_pensioners']) }}</td>
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