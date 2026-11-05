@section('title') 
SSEPD Institute Master || List
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
   .stat-card {
      border-left: 4px solid #009efb;
      transition: all 0.2s ease-in-out;
   }
   .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.08);
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
               <li class="breadcrumb-item active">Institute Master</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         @can('field-visit-institute-create')
         <a href="{{ route('admin.field_visit_institutes.create') }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success">
            <i class="fa fa-plus-circle"></i> Add Institute
         </a>
         @endcan
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <!-- Statistics Overview Cards -->
   <div class="row g-3 mb-3">
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle btn-info text-white"><i class="fas fa-university"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Total Master Institutes</h6>
                     <h4 class="mb-0 fw-bold">{{ $institutes->total() }}</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0" style="border-left-color: #26c6da;">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle btn-success text-white"><i class="fas fa-landmark"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Govt. of Odisha Funded</h6>
                     <h4 class="mb-0 fw-bold">294</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0" style="border-left-color: #745af2;">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle text-white" style="background:#745af2;"><i class="fas fa-flag"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Govt. of India Funded</h6>
                     <h4 class="mb-0 fw-bold">143</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0" style="border-left-color: #55ce63;">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle btn-success text-white"><i class="fas fa-check-circle"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Active Facilities</h6>
                     <h4 class="mb-0 fw-bold">433</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Start Page Content -->
   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <div>
                     <h4 class="card-title mb-1">SSEPD Institute Master</h4>
                     <p class="text-muted small mb-0">Centralized departmental registry of special schools, rehabilitation centers, old age homes, and social welfare institutions.</p>
                  </div>
                  <div>
                     <span class="badge bg-primary fs-6 px-3 py-2">
                        Displaying {{ $institutes->count() }} of {{ $institutes->total() }} Institutes
                     </span>
                  </div>
               </div>

               @include('dashboard.component.message')

               <!-- Filter & Search Toolbar -->
               <form id="filterForm" method="GET" action="{{ route('admin.field_visit_institutes.index') }}" class="row g-2 mb-4 align-items-end p-3 bg-light rounded border">
                  <div class="col-lg-3 col-md-6 col-sm-12">
                     <label for="search" class="form-label small fw-semibold text-muted mb-1">Search Keyword</label>
                     <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}" 
                               class="form-control" 
                               placeholder="Code, name, or NGO...">
                     </div>
                  </div>

                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <label for="district_id" class="form-label small fw-semibold text-muted mb-1">District</label>
                     <select name="district_id" id="district_id" class="form-select form-select-sm">
                        <option value="">-- All Districts --</option>
                        @foreach($districts as $dist)
                           <option value="{{ $dist->district_id }}" {{ request('district_id') == $dist->district_id ? 'selected' : '' }}>
                              {{ $dist->district_name }}
                           </option>
                        @endforeach
                     </select>
                  </div>

                  <div class="col-lg-3 col-md-4 col-sm-6">
                     <label for="category_id" class="form-label small fw-semibold text-muted mb-1">Institute Category</label>
                     <select name="category_id" id="category_id" class="form-select form-select-sm">
                        <option value="">-- All Categories --</option>
                        @foreach($categories as $catId => $catName)
                           <option value="{{ $catId }}" {{ request('category_id') == $catId ? 'selected' : '' }}>
                              {{ $catName }}
                           </option>
                        @endforeach
                     </select>
                  </div>

                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <label for="which_govt" class="form-label small fw-semibold text-muted mb-1">Govt. Scope</label>
                     <select name="which_govt" id="which_govt" class="form-select form-select-sm">
                        <option value="">-- All Schemes --</option>
                        <option value="1" {{ request('which_govt') == '1' ? 'selected' : '' }}>Govt. of Odisha</option>
                        <option value="2" {{ request('which_govt') == '2' ? 'selected' : '' }}>Govt. of India</option>
                     </select>
                  </div>

                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <label for="status" class="form-label small fw-semibold text-muted mb-1">Status</label>
                     <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">-- All Statuses --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                     </select>
                  </div>

                  <div class="col-12 text-end mt-2">
                     <button type="submit" class="btn btn-sm btn-primary px-3">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                     </button>
                     <button type="button" id="btnResetFilter" class="btn btn-sm btn-outline-secondary px-3 ms-1">
                        <i class="fas fa-undo me-1"></i> Reset
                     </button>
                  </div>
               </form>

               <!-- Institutes Table (Yajra DataTables Server-Side) -->
               <div class="table-responsive m-t-20">
                  <table id="institutesTable" class="display nowrap table table-hover table-striped border align-middle" cellspacing="0" width="100%">
                     <thead class="table-dark">
                        <tr>
                           <th style="width: 50px;" class="text-center">#</th>
                           <th style="width: 120px;">Code</th>
                           <th>Institute Name & Managing Body</th>
                           <th>Category</th>
                           <th>District / Sub-District</th>
                           <th style="width: 130px;" class="text-center">Govt. Scope</th>
                           <th style="width: 90px;" class="text-center">Status</th>
                           <th style="width: 90px;" class="text-center">Action</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th class="text-center">#</th>
                           <th>Code</th>
                           <th>Institute Name & Managing Body</th>
                           <th>Category</th>
                           <th>District / Sub-District</th>
                           <th class="text-center">Govt. Scope</th>
                           <th class="text-center">Status</th>
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
    var table = $('#institutesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.field_visit_institutes.index') }}",
            data: function (d) {
                d.search_keyword = $('#search').val();
                d.district_id = $('#district_id').val();
                d.category_id = $('#category_id').val();
                d.which_govt = $('#which_govt').val();
                d.status = $('#status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted fw-bold' },
            { data: 'institute_code', name: 'institute_code' },
            { data: 'institute_name', name: 'institute_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'district_name', name: 'district_name' },
            { data: 'which_govt', name: 'which_govt', className: 'text-center' },
            { data: 'is_active', name: 'is_active', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'asc']],
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
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
