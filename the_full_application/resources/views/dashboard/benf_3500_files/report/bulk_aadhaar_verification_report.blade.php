@section('title') 
Special School || List
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .wrap-text {
      white-space: normal !important;
      word-break: break-word;
      max-width: 200px;
   }
</style>
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
               <h4 class="card-title"></h4>
               @include('dashboard.component.message')
               <div class="table-responsive m-t-40">
                  <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                     <thead>
                        <tr>
                           <th>Sl No</th>
                           <th>Scheme</th>
                           <th>Verification Remarks</th>
                           <th>Total</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>Scheme</th>
                           <th>Verification Remarks</th>
                           <th>Total</th>
                        </tr>
                     </tfoot>
                     <tbody>
                        @foreach($schemeWise as $row)
                        <tr>
                           <td class="text-center">{{ $loop->iteration }}</td>
                           <td>{{ $row['scheme'] }}</td>
                           <td>{{ $row['verified_aadhar_remarks'] }}</td>
                           <td>{{ $row['total'] }}</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
                  <table id="example231" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                     <thead>
                        <tr>
                           <th>Sl No</th>
                           <th>Verification Remarks</th>
                           <th>Total</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>Scheme</th>
                           <th>Verification Remarks</th>
                           <th>Total</th>
                        </tr>
                     </tfoot>
                     <tbody>
                        @foreach($combined as $remark => $count)
                        <tr>
                           <td class="text-center">{{ $loop->iteration }}</td>
                           <td>{{ $remark }}</td>
                           <td>{{ $count }}</td>
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
      processing: false,
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

   $(function () {
     $('#example231').DataTable({
       processing: false,
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