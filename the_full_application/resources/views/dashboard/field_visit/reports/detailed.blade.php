@extends('dashboard.layouts.main')

@section('title', 'Field Visit Reports || Detailed Visit Report')

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
   <!-- Page Title & Breadcrumb -->
   <div class="row page-titles">
      <div class="col-md-7 align-self-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item active">Detailed Visit Report</li>
         </ol>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <span class="badge bg-primary px-3 py-2">
            <i class="fas fa-list-ul me-1"></i> Total Matches: {{ number_format($visits->total()) }}
         </span>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   <!-- Common Filters Partial -->
   @include('dashboard.field_visit.reports._filters', ['exportRoute' => 'admin.field_visit_reports.detailed.export'])

   <!-- Detailed Visits Table Card -->
   <div class="card shadow-sm border-0">
      <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
         <div>
            <h5 class="card-title mb-0 fw-bold text-dark">
               <i class="fas fa-file-invoice text-primary me-2"></i>Field Visits Register
            </h5>
            <small class="text-muted">Showing {{ $visits->firstItem() ?? 0 }} to {{ $visits->lastItem() ?? 0 }} of {{ $visits->total() }} recorded field visits</small>
         </div>
         <div>
            <a href="{{ route('admin.field_visit_reports.detailed.export', request()->query()) }}" class="btn btn-sm btn-success text-white">
               <i class="fas fa-file-excel me-1"></i> Export Filtered to Excel
            </a>
         </div>
      </div>
      <div class="card-body p-3">
         <div class="table-responsive">
            <table id="detailedVisitsTable" class="display nowrap table table-hover table-striped border align-middle" cellspacing="0" width="100%">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;" class="text-center">#</th>
                     <th>Visit Ref No</th>
                     <th>Visit Date</th>
                     <th>Type</th>
                     <th>Institution Details</th>
                     <th>Geographical Location</th>
                     <th>Visiting Officer</th>
                     <th>Observations & Suggestions</th>
                     <th class="text-center">Status</th>
                     <th class="text-center" style="width: 100px;">Action</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th class="text-center">#</th>
                     <th>Visit Ref No</th>
                     <th>Visit Date</th>
                     <th>Type</th>
                     <th>Institution Details</th>
                     <th>Geographical Location</th>
                     <th>Visiting Officer</th>
                     <th>Observations & Suggestions</th>
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
@endsection

@section('script')
<script>
$(function () {
    var table = $('#detailedVisitsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.field_visit_reports.detailed') }}",
            data: function (d) {
                var filterData = $('#reportFilterForm').serializeArray();
                $.each(filterData, function (i, field) {
                    d[field.name] = field.value;
                });
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
            { data: 'visit_reference_no', name: 'visit_reference_no' },
            { data: 'visit_date', name: 'visit_date' },
            { data: 'visit_type', name: 'visitType.name' },
            { data: 'institution_name', name: 'institution_name' },
            { data: 'location', name: 'district.district_name' },
            { data: 'visiting_officer', name: 'visiting_officer_name' },
            { data: 'observations', name: 'key_observations', orderable: false },
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

    $('#reportFilterForm').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#reportFilterForm a[href*="reset"]').on('click', function (e) {
        e.preventDefault();
        $('#reportFilterForm')[0].reset();
        table.ajax.reload();
    });
});
</script>
@endsection

