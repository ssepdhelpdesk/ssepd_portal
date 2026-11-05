@extends('dashboard.layouts.main')

@section('title', 'Field Visit Reports || Date-wise Visit Report')

@section('style')
<style>
   /* Standardized Pagination UI */
   .dataTables_wrapper .dataTables_paginate {
      padding-top: 0.5rem;
      text-align: right;
   }
   .dataTables_wrapper .dataTables_paginate ul.pagination {
      margin: 2px 0;
      white-space: nowrap;
      justify-content: flex-end;
   }
   .dataTables_wrapper .dataTables_paginate .page-item .page-link {
      color: #007bff;
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
      line-height: 1.5;
      border: 1px solid #dee2e6;
   }
   .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
      color: #fff;
      background-color: #007bff;
      border-color: #007bff;
      font-weight: 600;
   }
   .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
      color: #6c757d;
      pointer-events: none;
      cursor: not-allowed;
      background-color: #fff;
      border-color: #dee2e6;
      opacity: 0.65;
   }
   .dataTables_wrapper .dataTables_paginate .page-item:not(.active):not(.disabled) .page-link:hover {
      color: #0056b3;
      background-color: #e9ecef;
      border-color: #dee2e6;
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
            <li class="breadcrumb-item active">Date-wise Visit Report</li>
         </ol>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <span class="badge bg-primary px-3 py-2">
            <i class="fas fa-calendar-alt me-1"></i> Recorded Dates: {{ number_format($daily_totals->count()) }}
         </span>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   <!-- Common Filters Partial -->
   @include('dashboard.field_visit.reports._filters', ['exportRoute' => 'admin.field_visit_reports.datewise.export'])

   <!-- Monthly Totals Cards Row -->
   @if($monthly_totals->isNotEmpty())
   <div class="row g-2 mb-4">
      <div class="col-12">
         <h6 class="fw-bold text-muted text-uppercase mb-2 small"><i class="fas fa-calendar me-1"></i> Monthly Aggregations</h6>
      </div>
      @foreach($monthly_totals->take(6) as $m)
      <div class="col-lg-2 col-md-4 col-sm-6">
         <div class="card shadow-sm border-0 border-top border-primary border-3 bg-white">
            <div class="card-body p-3">
               <div class="small fw-bold text-muted text-uppercase">{{ $m->month_name }}</div>
               <h4 class="fw-bold text-primary mb-1 mt-1">{{ number_format($m->total_visits) }} <small class="fs-6 text-muted font-normal">visits</small></h4>
               <div class="d-flex justify-content-between small text-muted">
                  <span class="text-success"><i class="fas fa-tree me-1"></i>{{ $m->rural_visits }}</span>
                  <span class="text-info"><i class="fas fa-city me-1"></i>{{ $m->urban_visits }}</span>
                  <span><i class="fas fa-building me-1"></i>{{ $m->unique_institutions }}</span>
               </div>
            </div>
         </div>
      </div>
      @endforeach
   </div>
   @endif

   <!-- Daily Summary Table -->
   <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
         <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="fas fa-calendar-day text-primary me-2"></i>Daily Visit Summary
         </h5>
         <a href="{{ route('admin.field_visit_reports.datewise.export', request()->query()) }}" class="btn btn-sm btn-success text-white">
            <i class="fas fa-file-excel me-1"></i> Export Daily Totals
         </a>
      </div>
      <div class="card-body p-3">
         <div class="table-responsive">
            <table id="dailySummaryTable" class="table table-striped table-bordered display responsive nowrap align-middle" style="width:100%">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;" class="text-center">#</th>
                     <th>Visit Date</th>
                     <th>Day</th>
                     <th class="text-center">Total Visits</th>
                     <th class="text-center">Rural Visits</th>
                     <th class="text-center">Urban Visits</th>
                     <th class="text-center">Unique Institutions</th>
                     <th class="text-center">Unique Officers</th>
                     <th class="text-center" style="width: 120px;">Action</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th class="text-center">#</th>
                     <th>Visit Date</th>
                     <th>Day</th>
                     <th class="text-center">Total Visits</th>
                     <th class="text-center">Rural Visits</th>
                     <th class="text-center">Urban Visits</th>
                     <th class="text-center">Unique Institutions</th>
                     <th class="text-center">Unique Officers</th>
                     <th class="text-center">Action</th>
                  </tr>
               </tfoot>
               <tbody>
                  @forelse($daily_totals as $idx => $d)
                  @php
                     $dateObj = \Carbon\Carbon::parse($d->date_val);
                  @endphp
                  <tr>
                     <td class="text-center text-muted">{{ $idx + 1 }}</td>
                     <td class="fw-bold text-dark font-monospace">{{ $dateObj->format('d-M-Y') }}</td>
                     <td><span class="badge bg-light text-dark border">{{ $dateObj->format('l') }}</span></td>
                     <td class="text-center fw-bold">{{ number_format($d->total_visits) }}</td>
                     <td class="text-center text-success fw-semibold">{{ number_format($d->rural_visits) }}</td>
                     <td class="text-center text-info fw-semibold">{{ number_format($d->urban_visits) }}</td>
                     <td class="text-center">{{ number_format($d->unique_institutions) }}</td>
                     <td class="text-center">{{ number_format($d->unique_officers) }}</td>
                     <td class="text-center">
                        <a href="{{ route('admin.field_visit_reports.detailed', array_merge(request()->query(), ['date_from' => $d->date_val, 'date_to' => $d->date_val])) }}" class="btn btn-xs btn-outline-primary">
                           <i class="fas fa-list me-1"></i> View Visits
                        </a>
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="9" class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle me-1"></i> No visits recorded in this period.
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
   </div>

   <!-- Detailed Visits for the filtered period -->
   <div class="card shadow-sm border-0">
      <div class="card-header bg-light py-2 px-3">
         <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="fas fa-stream text-primary me-2"></i>Date-wise Field Visit Listings
         </h5>
      </div>
      <div class="card-body p-3">
         <div class="table-responsive">
            <table id="datewiseListingsTable" class="table table-striped table-bordered display responsive nowrap align-middle" style="width:100%">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;" class="text-center">#</th>
                     <th>Visit Ref No</th>
                     <th>Visit Date</th>
                     <th>Type</th>
                     <th>Institution Name</th>
                     <th>Location</th>
                     <th>Visiting Officer</th>
                     <th>Status</th>
                     <th class="text-center" style="width: 100px;">Action</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th class="text-center">#</th>
                     <th>Visit Ref No</th>
                     <th>Visit Date</th>
                     <th>Type</th>
                     <th>Institution Name</th>
                     <th>Location</th>
                     <th>Visiting Officer</th>
                     <th>Status</th>
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
   // 1. Daily Visit Summary (Client-side aggregated DataTable)
   $('#dailySummaryTable').DataTable({
      responsive: true,
      scrollX: true,
      dom: 'Blfrtip',
      buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
      pageLength: 10,
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
      order: [[1, 'desc']]
   });

   // 2. Date-wise Field Visit Listings (Server-Side Yajra DataTable)
   var listingsTable = $('#datewiseListingsTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: {
         url: "{{ route('admin.field_visit_reports.datewise') }}",
         data: function (d) {
            var filterData = $('#reportFilterForm').serializeArray();
            $.each(filterData, function (i, field) {
               if (field.value) {
                  d[field.name] = field.value;
               }
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
         { data: 'status', name: 'status', className: 'text-center' },
         { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
      ],
      order: [[2, 'desc']],
      dom: 'Blfrtip',
      buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
      lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, "All"]],
   });

   $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
      .addClass('btn btn-primary me-1');
});
</script>
@endsection

