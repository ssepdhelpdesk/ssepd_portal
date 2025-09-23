@section('title') 
Special School || No of Staffs in Schools
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
                           <th>District</th>
                           <th>Management Name</th>
                           <th>School Name</th>
                           <th>Staff Strength</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>District</th>
                           <th>Management Name</th>
                           <th>School Name</th>
                           <th>Staff Strength</th>
                        </tr>
                     </tfoot>
                     <tbody>
                       @forelse ($specialSchoolMapping as $schoolDetails)
                       <tr>
                         <td class="text-center">{{ $loop->iteration }}</td>
                         <td>{{ $schoolDetails->district->district_name ?? 'N/A' }}</td>
                         <td class="wrap-text">{{ $schoolDetails->management_name }}</td>
                         <td class="wrap-text">{{ $schoolDetails->special_school_name }}</td>
                         <td class="text-center"><a href="{{ route('admin.specialschool.view_staff_details_by_state_office', $schoolDetails->special_school_id) }}" target="_blank">{{ $schoolDetails->staff_count ?? '0' }}</a></td>                       
                      </tr>
                      @empty
                      <tr>
                         <td colspan="8" class="text-center">No records found.</td>
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