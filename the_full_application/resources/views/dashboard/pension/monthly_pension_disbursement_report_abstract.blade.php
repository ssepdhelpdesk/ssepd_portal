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
                        <th>District</th>
                        <th>Type</th>
                        <th>Block / ULB</th>
                        <th>Data Provided by GP</th>
                        <th>Data Provided by Ward</th>
                        <th>Data Not Provided by GP</th>
                        <th>Data Not Provided by Ward</th>
                        <th>No. of Normal Pensioners</th>
                        <th>No. of EP Pensioners</th>
                        <th>Total Pensioners</th>
                     </tr>
                  </thead>
                  <tfoot>
                     <tr>
                        <th>District</th>
                        <th>Type</th>
                        <th>Block / ULB</th>
                        <th>Data Provided by GP</th>
                        <th>Data Provided by Ward</th>
                        <th>Data Not Provided by GP</th>
                        <th>Data Not Provided by Ward</th>
                        <th>No. of Normal Pensioners</th>
                        <th>No. of EP Pensioners</th>
                        <th>Total Pensioners</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @foreach($summaryReport as $row)
                     @if($row['district'] !== 'Grand Total')
                     <tr>
                        <td>{{ $row['district'] }}</td>
                        <td>{{ $row['type'] }}</td>
                        <td>{{ $row['block_or_ulb'] }}</td>
                        <td>{{ $row['Data_provided_by_GP'] }}</td>
                        <td>{{ $row['Data_provided_by_ward'] }}</td>
                        <td>{{ $row['Data_not_provided_by_GP'] }}</td>
                        <td>{{ $row['Data_not_provided_by_ward'] }}</td>
                        <td>{{ $row['no_of_normal_pensioners'] ?? 0 }}</td>
                        <td>{{ $row['no_of_ep_pensioners'] ?? 0 }}</td>
                        <td>{{ ($row['no_of_normal_pensioners'] ?? 0) + ($row['no_of_ep_pensioners'] ?? 0) }}</td>
                     </tr>
                     @endif
                     @endforeach

                     {{-- Always append Grand Total row at bottom --}}
                     @php
                     $grandTotal = collect($summaryReport)->firstWhere('district', 'Grand Total');
                     @endphp
                     @if($grandTotal)
                     <tr style="font-weight: bold; background: #f5f5f5;">
                        <td>{{ $grandTotal['district'] }}</td>
                        <td>{{ $grandTotal['type'] }}</td>
                        <td>{{ $grandTotal['block_or_ulb'] }}</td>
                        <td>{{ $grandTotal['Data_provided_by_GP'] }}</td>
                        <td>{{ $grandTotal['Data_provided_by_ward'] }}</td>
                        <td>{{ $grandTotal['Data_not_provided_by_GP'] }}</td>
                        <td>{{ $grandTotal['Data_not_provided_by_ward'] }}</td>
                        <td>{{ $grandTotal['no_of_normal_pensioners'] ?? 0 }}</td>
                        <td>{{ $grandTotal['no_of_ep_pensioners'] ?? 0 }}</td>
                        <td>{{ ($grandTotal['no_of_normal_pensioners'] ?? 0) + ($grandTotal['no_of_ep_pensioners'] ?? 0) }}</td>
                     </tr>
                     @endif
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
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        drawCallback: function(settings) {
          let api = this.api();
          let $grandTotalRow = api.rows().nodes().to$().filter(function() {
            return $(this).find('td:first').text().trim() === 'Grand Total';
         });
          if ($grandTotalRow.length) {
            $grandTotalRow.appendTo($(api.table().body())); // push to bottom
         }
      }
   });
      $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
   });   
</script>
@endsection