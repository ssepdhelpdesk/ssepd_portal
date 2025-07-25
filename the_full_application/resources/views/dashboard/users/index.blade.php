@section('title') 
Users || View
@endsection 
@extends('dashboard.layouts.main')
@section('style')
@endsection 
@section('content')
<div class="container-fluid">
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
         <button onclick="history.back()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info">
            <i class="fas fa-arrow-alt-circle-left"></i> Go Back
         </button>
         @can('user-create')
         <a href="{{ route('admin.users.create') }}">
            <button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success">
               <i class="fas fa-plus-square"></i> Add New
            </button>
         </a>
         @endcan
      </div>
   </div>
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
                           <th>Name</th>
                           <th>User ID</th>
                           <th>Email</th>
                           <th>Mobile No</th>
                           <th>Roles</th>
                           <th>Created At</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>Name</th>
                           <th>User ID</th>
                           <th>Email</th>
                           <th>Mobile No</th>
                           <th>Roles</th>
                           <th>Created At</th>
                           <th>Action</th>
                        </tr>
                     </tfoot>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection 
@section('script')
<script>
   $(function () {
     $('#example23').DataTable({
       processing: true,
       serverSide: true,
       ajax: "{{ route('admin.users.index') }}",
       columns: [
         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
         { data: 'name', name: 'name' },
         { data: 'user_id', name: 'user_id' },
         { data: 'email', name: 'email' },
         { data: 'mobile_no', name: 'mobile_no' },
         { data: 'roles', name: 'roles'},
         { data: 'created_at', name: 'created_at' },
         { data: 'action', name: 'action', orderable: false, searchable: false }
       ],
       dom: 'Blfrtip',
       buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
       lengthMenu: [[10, 50, 100, 500], [10, 50, 100, 500]],
     });

     $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
       .addClass('btn btn-primary me-1');
   });
</script>
@endsection
