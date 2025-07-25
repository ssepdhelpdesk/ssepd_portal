@section('title') 
VIEW || PERMISSIONS
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
         <a href="{{ route('admin.permissions.create') }}"><button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success"><i class="fas fa-plus-square"></i> Add New</button></a>
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
                           <th>Permission Name</th>
                           <th>Created At</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>Permission Name</th>
                           <th>Created At</th>
                           <th>Action</th>
                        </tr>
                     </tfoot>
                     <tbody>
                        @php($i=1)
                        @if ($permissions->isNotEmpty())
                        @foreach($permissions as $permission)
                        <tr>
                           <td>{{ $i++ }}</td>
                           <td>{{ $permission->name }}</td>
                           <td>{{ \Carbon\Carbon::parse($permission->created_at)->format('d-M-Y') }}</td>
                           <td>
                              @can('permission-show')
                              <a href="{{route('admin.permissions.show', $permission->id)}}"><span class="label label-warning">View</span></a>
                              @endcan
                              @can('permission-edit')
                              <a href="{{route('admin.permissions.edit', $permission->id)}}"><span class="label label-table label-success">Edit</span></a>
                              @endcan
                              @can('permission-delete')
                              <a href="{{route('admin.permissions.destroy', $permission->id)}}" id="delete"><span class="label label-table label-danger">Delete</span></a>
                              @endcan
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