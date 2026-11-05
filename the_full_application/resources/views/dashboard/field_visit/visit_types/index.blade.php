@section('title') 
Visit Type Master || List
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .badge-code {
      font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size: 0.85rem;
   }
   .table td {
      vertical-align: middle;
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
               <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
               <li class="breadcrumb-item"><a href="javascript:void(0)">Field Visit</a></li>
               <li class="breadcrumb-item active">Visit Types</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         @can('field-visit-type-create')
         <a href="{{ route('admin.field_visit_types.create') }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success">
            <i class="fa fa-plus-circle"></i> Add Visit Type
         </a>
         @endcan
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <!-- Start Page Content -->
   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <div>
                     <h4 class="card-title mb-1">Field Visit Types</h4>
                     <p class="text-muted small mb-0">Master catalog of standardized departmental visit and inspection categories.</p>
                  </div>
                  <div>
                     <span class="badge bg-primary fs-6 px-3 py-2">
                        Total Records: {{ $visitTypes->total() }}
                     </span>
                  </div>
               </div>

               @include('dashboard.component.message')

               <!-- Filter & Search Toolbar -->
               <form id="filterForm" method="GET" action="{{ route('admin.field_visit_types.index') }}" class="row g-2 mb-4 align-items-end">
                  <div class="col-md-5 col-sm-12">
                     <label for="search" class="form-label small text-muted mb-1">Search Keywords</label>
                     <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}" 
                               class="form-control" 
                               placeholder="Search by name, code, or description...">
                     </div>
                  </div>

                  <div class="col-md-3 col-sm-6">
                     <label for="status" class="form-label small text-muted mb-1">Status Filter</label>
                     <select name="status" id="status" class="form-select">
                        <option value="">-- All Statuses --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
                     </select>
                  </div>

                  <div class="col-md-4 col-sm-6 d-flex gap-2">
                     <button type="submit" class="btn btn-info text-white flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                     </button>
                     <button type="button" id="btnResetFilter" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> Reset
                     </button>
                  </div>
               </form>

               <!-- Table Data (Yajra DataTables Server-Side) -->
               <div class="table-responsive m-t-20">
                  <table id="visitTypesTable" class="display nowrap table table-hover table-striped border align-middle" cellspacing="0" width="100%">
                     <thead class="table-dark">
                        <tr>
                           <th style="width: 50px;" class="text-center">#</th>
                           <th>Visit Type Name</th>
                           <th style="width: 140px;">Code</th>
                           <th>Description</th>
                           <th style="width: 90px;" class="text-center">Sort Order</th>
                           <th style="width: 100px;" class="text-center">Status</th>
                           <th style="width: 140px;">Created At</th>
                           <th style="width: 90px;" class="text-center">Action</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th class="text-center">#</th>
                           <th>Visit Type Name</th>
                           <th>Code</th>
                           <th>Description</th>
                           <th class="text-center">Sort Order</th>
                           <th class="text-center">Status</th>
                           <th>Created At</th>
                           <th class="text-center">Action</th>
                        </tr>
                     </tfoot>
                     <tbody></tbody>
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
    var table = $('#visitTypesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.field_visit_types.index') }}",
            data: function (d) {
                d.search_keyword = $('#search').val();
                d.status = $('#status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
            { data: 'name', name: 'name', className: 'fw-bold text-dark' },
            { data: 'code', name: 'code' },
            { data: 'description', name: 'description', className: 'small text-muted' },
            { data: 'sort_order', name: 'sort_order', className: 'text-center' },
            { data: 'is_active', name: 'is_active', className: 'text-center' },
            { data: 'created_at', name: 'created_at', className: 'small text-muted' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[4, 'asc']],
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    });

    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
        .addClass('btn btn-primary me-1');

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterForm')[0].reset();
        table.ajax.reload();
    });
});
</script>
@endsection
