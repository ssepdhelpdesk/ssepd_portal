@section('title') 
Field Visit Management || Visit Records
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .ref-badge {
      font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-weight: 600;
   }
   .table td {
      vertical-align: middle;
   }
</style>
@endsection 
@section('content')
<div class="container-fluid">
   <!-- Breadcrumb -->
   <div class="row page-titles">
      <div class="col-md-7 align-self-center">
         <div class="d-flex align-items-center">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
               <li class="breadcrumb-item"><a href="javascript:void(0)">Field Visit</a></li>
               <li class="breadcrumb-item active">Field Visits</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <div class="d-flex justify-content-end gap-2">
            @can('field-visit-export')
            <a href="{{ route('admin.field_visits.export', request()->query()) }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-info" title="Export filtered records to CSV">
               <i class="fas fa-file-excel me-1"></i> Export CSV
            </a>
            @endcan
            @can('field-visit-create')
            <a href="{{ route('admin.field_visits.create') }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-success">
               <i class="fa fa-plus-circle me-1"></i> Record Field Visit
            </a>
            @endcan
         </div>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   <!-- Main Table Card -->
   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm">
            <div class="card-body">
               
               <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <div>
                     <h4 class="card-title mb-1">Field Visit Records</h4>
                     <p class="text-muted small mb-0">Auditable register of on-ground monitoring, inspections, and inquiries conducted by departmental officers.</p>
                  </div>
                  <div>
                     <span class="badge bg-primary fs-6 px-3 py-2">
                        Total Visits: {{ $visits->total() }}
                     </span>
                  </div>
               </div>

               <!-- Advanced Search & Filter Toolbar -->
               <form id="filterForm" method="GET" action="{{ route('admin.field_visits.index') }}" class="p-3 bg-light rounded border mb-4">
                  <div class="row g-2 align-items-end">
                     <!-- Keyword Search -->
                     <div class="col-md-4 col-sm-12">
                        <label for="search" class="form-label small text-muted mb-1">Search Keywords</label>
                        <div class="input-group input-group-sm">
                           <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                           <input type="text" 
                                  name="search" 
                                  id="search" 
                                  value="{{ request('search') }}" 
                                  class="form-control" 
                                  placeholder="Ref No, Institution, Code, or Purpose...">
                        </div>
                     </div>

                     <!-- Visit Type -->
                     <div class="col-md-2 col-sm-6">
                        <label for="field_visit_type_id" class="form-label small text-muted mb-1">Visit Type</label>
                        <select name="field_visit_type_id" id="field_visit_type_id" class="form-select form-select-sm">
                           <option value="">-- All Types --</option>
                           @foreach($visitTypes as $vt)
                           <option value="{{ $vt->id }}" {{ request('field_visit_type_id') == $vt->id ? 'selected' : '' }}>
                              {{ $vt->name }}
                           </option>
                           @endforeach
                        </select>
                     </div>

                     <!-- Status -->
                     <div class="col-md-2 col-sm-6">
                        <label for="status" class="form-label small text-muted mb-1">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                           <option value="">-- All Statuses --</option>
                           <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                           <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                           <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                           <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                     </div>

                     <!-- District -->
                     <div class="col-md-2 col-sm-6">
                        <label for="district_id" class="form-label small text-muted mb-1">District</label>
                        <select name="district_id" id="district_id" class="form-select form-select-sm">
                           <option value="">-- All Districts --</option>
                           @foreach($districts as $d)
                           <option value="{{ $d->district_id }}" {{ request('district_id') == $d->district_id ? 'selected' : '' }}>
                              {{ $d->district_name }}
                           </option>
                           @endforeach
                        </select>
                     </div>

                     <!-- Follow-up Required -->
                     <div class="col-md-2 col-sm-6">
                        <label for="is_follow_up_required" class="form-label small text-muted mb-1">Follow-up</label>
                        <select name="is_follow_up_required" id="is_follow_up_required" class="form-select form-select-sm">
                           <option value="">-- All --</option>
                           <option value="1" {{ request('is_follow_up_required') === '1' ? 'selected' : '' }}>Required Only</option>
                           <option value="0" {{ request('is_follow_up_required') === '0' ? 'selected' : '' }}>Not Required</option>
                        </select>
                     </div>
                  </div>

                  <div class="row g-2 align-items-end mt-1">
                     <div class="col-md-3 col-sm-6">
                        <label for="date_from" class="form-label small text-muted mb-1">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                     </div>
                     <div class="col-md-3 col-sm-6">
                        <label for="date_to" class="form-label small text-muted mb-1">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                     </div>
                     <div class="col-md-6 col-sm-12 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-info btn-sm text-white px-3">
                           <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <button type="button" id="btnResetFilter" class="btn btn-secondary btn-sm px-3">
                           <i class="fas fa-undo me-1"></i> Reset
                        </button>
                     </div>
                  </div>
               </form>

               <!-- Table Data (Yajra DataTables Server-Side) -->
               <div class="table-responsive m-t-20">
                  <table id="fieldVisitsTable" class="display nowrap table table-hover table-striped border align-middle" cellspacing="0" width="100%">
                     <thead>
                        <tr>
                           <th style="width: 45px;" class="text-center">#</th>
                           <th style="width: 170px;">Reference No</th>
                           <th style="width: 110px;">Visit Date</th>
                           <th>Visit Type</th>
                           <th>Institution / Facility</th>
                           <th style="width: 130px;">District</th>
                           <th style="width: 150px;">Visiting Officer</th>
                           <th style="width: 80px;" class="text-center">Evidence</th>
                           <th style="width: 100px;" class="text-center">Status</th>
                           <th style="width: 85px;" class="text-center">Action</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th class="text-center">#</th>
                           <th>Reference No</th>
                           <th>Visit Date</th>
                           <th>Visit Type</th>
                           <th>Institution / Facility</th>
                           <th>District</th>
                           <th>Visiting Officer</th>
                           <th class="text-center">Evidence</th>
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
    var table = $('#fieldVisitsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.field_visits.index') }}",
            data: function (d) {
                d.search_keyword = $('#search').val();
                d.field_visit_type_id = $('#field_visit_type_id').val();
                d.status = $('#status').val();
                d.district_id = $('#district_id').val();
                d.is_follow_up_required = $('#is_follow_up_required').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
            { data: 'visit_reference_no', name: 'visit_reference_no' },
            { data: 'visit_date', name: 'visit_date' },
            { data: 'visit_type', name: 'visitType.name' },
            { data: 'institution_name', name: 'institution_name' },
            { data: 'district', name: 'district.district_name' },
            { data: 'visiting_officer', name: 'visiting_officer_name' },
            { data: 'photos_count', name: 'photos_count', orderable: false, searchable: false, className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[2, 'desc']],
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
