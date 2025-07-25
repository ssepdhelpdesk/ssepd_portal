@section('title') 
Special School || List
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
                           <th>School Name</th>
                           <th>Management Name</th>
                           <th>Establishment Date</th>
                           <th>Category</th>
                           <th>Type</th>
                           <th>ACT No</th>
                           <th>ACT File</th>
                           <th>Address</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>School Name</th>
                           <th>Management Name</th>
                           <th>Establishment Date</th>
                           <th>Category</th>
                           <th>Type</th>
                           <th>ACT No</th>
                           <th>ACT File</th>
                           <th>Address</th>
                           <th>Action</th>
                        </tr>
                     </tfoot>
                     <tbody>
                        @php $i = 1; @endphp
                        @if ($specialSchool->isNotEmpty())
                        @foreach($specialSchool as $key => $schoolDetails)
                        <tr>
                           <td>{{ $i++ }}</td>
                           <td>{{ $schoolDetails->special_school_name }}</td>
                           <td>{{ $schoolDetails->special_school_management_name }}</td>
                           <td>{{ $schoolDetails->school_establishment_date }}</td>
                           <td>
                              @php
                              $categories = [
                              1 => 'VI',
                              2 => 'HI',
                              3 => 'MR/ID',
                              4 => 'CP',
                              5 => 'ASD'
                              ];
                              @endphp
                              {{ $categories[$schoolDetails->school_category] ?? 'N/A' }}
                           </td>
                           <td>
                              @php
                              $types = [
                              1 => 'Residentials',
                              2 => 'Non Residential'
                              ];
                              @endphp
                              {{ $types[$schoolDetails->school_type] ?? 'N/A' }}
                           </td>
                           <td>{{ $schoolDetails->act_reg_no }}</td>
                           <td><label class="badge bg-warning"><a href="{{ url('storage/' . $schoolDetails->file_act_reg) }}" target="_blank" style="cursor: pointer;" class=" text-white">View</a></label></td>
                           <td>{{ $schoolDetails->full_address ?? 'N/A' }}</td>
                           <td>
                              <div class="btn-group">
                                 <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                 </button>
                                 <!-- <div class="dropdown-menu">
                                    @can('special-school-show')
                                    <a class="dropdown-item" href="{{route('admin.specialschool.create')}}">Add Staff Details</a>
                                    @endcan
                                 </div> -->
                              </div>
                           </td>
                        </tr>
                        @endforeach
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
     responsive: true,
     ordering: true,
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