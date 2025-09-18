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
         @can('user-create')
         <a href="{{ route('admin.users.create') }}"><button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success"><i class="fas fa-plus-square"></i> Add New</button></a>
         @endcan
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
                           <th>Staff Info Status</th>
                           <th>Toilet Construction</th>
                           <th>Status</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>District</th>
                           <th>Management Name</th>
                           <th>School Name</th>
                           <th>Staff Info Status</th>
                           <th>Toilet Construction</th>
                           <th>Status</th>
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
                       <td class="text-center">
                        @php
                        $phaseMap = [
                        1 => 'First',
                        2 => 'Second',
                        3 => 'Third',
                        4 => 'Fourth',
                        5 => 'Fifth',
                        6 => 'Sixth',
                        7 => 'Seventh',
                        8 => 'Eighth',
                        9 => 'Ninth',
                        10 => 'Tenth'
                        ];
                        $phase = $schoolDetails->construction_max_phase_no ?? 0;
                        @endphp

                        @if($phase)
                        <a href="{{ route('admin.specialschoolconstructions.index', $schoolDetails->special_school_id) }}" target="_blank">{{ $phaseMap[$phase] ?? 'Phase ' . $phase }} Phase uploaded</a>
                        @else
                        Pending to upload
                        @endif
                     </td>
                     <td class="text-center">
                       @php
                       $construction = DB::table('special_school_constructions')
                       ->where('special_school_id', $schoolDetails->special_school_id)
                       ->where('status', 1)
                       ->select('approve_status', 'approved_date')
                       ->first();
                       @endphp

                       @if($construction)
                       @if($construction->approve_status == 0)
                       Pending for Approval                       
                       @elseif($construction->approve_status == 1)
                       Approved on<br><small>{{ $construction->approved_date }}</small>
                       @elseif($construction->approve_status == 2)
                       Rejected on<br><small>{{ $construction->approved_date }}</small>
                       @endif
                       @else
                       <span class="text-muted">Not Submitted</span>
                       @endif
                    </td>
                 </tr>
                 @empty
                 <tr>
                    <td colspan="6" class="text-center">No records found.</td>
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