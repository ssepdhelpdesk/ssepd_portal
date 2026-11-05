@extends('dashboard.layouts.main')

@section('title', 'Field Visit Reports || Institution Coverage Report')

@section('style')
<style>
   .coverage-card {
      border-radius: 8px;
   }
   .progress-coverage {
      height: 12px;
      border-radius: 6px;
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
            <li class="breadcrumb-item active">Institution Coverage Report</li>
         </ol>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <span class="badge bg-primary px-3 py-2">
            Coverage: {{ $coverage_percent }}%
         </span>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   <!-- Common Filters Partial -->
   @include('dashboard.field_visit.reports._filters', ['exportRoute' => 'admin.field_visit_reports.coverage.export'])

   <!-- Coverage KPI Cards -->
   <div class="row g-3 mb-4">
      <!-- Total Eligible Institutes -->
      <div class="col-lg-3 col-md-6">
         <div class="card shadow-sm border-0 border-start border-primary border-4 coverage-card">
            <div class="card-body p-3">
               <div class="d-flex justify-content-between align-items-center">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Total Master Institutes</p>
                     <h3 class="mb-0 fw-bold text-primary">{{ number_format($total_institutes) }}</h3>
                     <small class="text-muted">In your assigned jurisdiction</small>
                  </div>
                  <div class="p-3 bg-primary bg-opacity-10 rounded-circle text-primary">
                     <i class="fas fa-university fa-lg"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Covered Institutes -->
      <div class="col-lg-3 col-md-6">
         <div class="card shadow-sm border-0 border-start border-success border-4 coverage-card">
            <div class="card-body p-3">
               <div class="d-flex justify-content-between align-items-center">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Institutes Visited</p>
                     <h3 class="mb-0 fw-bold text-success">{{ number_format($visited_count) }}</h3>
                     <small class="text-success fw-semibold">{{ $coverage_percent }}% of total covered</small>
                  </div>
                  <div class="p-3 bg-success bg-opacity-10 rounded-circle text-success">
                     <i class="fas fa-check-circle fa-lg"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Unvisited Institutes -->
      <div class="col-lg-3 col-md-6">
         <div class="card shadow-sm border-0 border-start border-danger border-4 coverage-card">
            <div class="card-body p-3">
               <div class="d-flex justify-content-between align-items-center">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Pending / Unvisited</p>
                     <h3 class="mb-0 fw-bold text-danger">{{ number_format($unvisited_count) }}</h3>
                     <small class="text-muted">{{ $total_institutes > 0 ? round(($unvisited_count / $total_institutes) * 100, 1) : 0 }}% remaining</small>
                  </div>
                  <div class="p-3 bg-danger bg-opacity-10 rounded-circle text-danger">
                     <i class="fas fa-hourglass-half fa-lg"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Ad-hoc / Unlinked Visits -->
      <div class="col-lg-3 col-md-6">
         <div class="card shadow-sm border-0 border-start border-warning border-4 coverage-card">
            <div class="card-body p-3">
               <div class="d-flex justify-content-between align-items-center">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Unlinked / Ad-Hoc Visits</p>
                     <h3 class="mb-0 fw-bold text-warning">{{ number_format($unlinked_visits_count) }}</h3>
                     <small class="text-muted">Without master institute link</small>
                  </div>
                  <div class="p-3 bg-warning bg-opacity-10 rounded-circle text-warning">
                     <i class="fas fa-question-circle fa-lg"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Overall Progress Bar Card -->
   <div class="card shadow-sm border-0 mb-4">
      <div class="card-body p-3">
         <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-bold text-dark"><i class="fas fa-chart-line text-primary me-2"></i>Overall Coverage Progress</span>
            <span class="badge bg-success fs-6">{{ $coverage_percent }}%</span>
         </div>
         <div class="progress progress-coverage bg-light">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $coverage_percent }}%;" aria-valuenow="{{ $coverage_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
         </div>
         <div class="d-flex justify-content-between mt-2 small text-muted">
            <span>{{ number_format($visited_count) }} covered</span>
            <span>{{ number_format($total_institutes) }} total eligible institutions</span>
         </div>
      </div>
   </div>

   <!-- Category-wise Coverage Table -->
   <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-light py-2 px-3">
         <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="fas fa-tags text-primary me-2"></i>Institution Category-wise Coverage Breakdown
         </h5>
      </div>
      <div class="card-body p-3">
         <div class="table-responsive">
            <table id="categoryBreakdownTable" class="table table-striped table-bordered display responsive nowrap align-middle" style="width:100%">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;" class="text-center">#</th>
                     <th>Institution Category</th>
                     <th class="text-center">Total Registered</th>
                     <th class="text-center">Visited at least Once</th>
                     <th class="text-center">Unvisited</th>
                     <th style="width: 200px;">Coverage %</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($category_breakdown as $idx => $cat)
                  <tr>
                     <td class="text-center text-muted">{{ $idx + 1 }}</td>
                     <td class="fw-bold text-dark">{{ $cat['category_name'] }}</td>
                     <td class="text-center fw-semibold">{{ number_format($cat['total']) }}</td>
                     <td class="text-center text-success fw-bold">{{ number_format($cat['visited']) }}</td>
                     <td class="text-center text-danger fw-semibold">{{ number_format($cat['unvisited']) }}</td>
                     <td>
                        <div class="d-flex align-items-center gap-2">
                           <div class="progress flex-grow-1" style="height: 8px;">
                              <div class="progress-bar {{ $cat['coverage_pct'] >= 50 ? 'bg-success' : ($cat['coverage_pct'] > 0 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $cat['coverage_pct'] }}%;"></div>
                           </div>
                           <span class="small fw-bold" style="min-width: 45px;">{{ $cat['coverage_pct'] }}%</span>
                        </div>
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="6" class="text-center py-3 text-muted">No category data available.</td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
   </div>

   <!-- Institutions Register Table with Filter (All / Visited / Unvisited) -->
   <div class="card shadow-sm border-0">
      <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
         <div>
            <h5 class="card-title mb-0 fw-bold text-dark">
               <i class="fas fa-list text-primary me-2"></i>Master Institutions Directory & Status
            </h5>
            <small class="text-muted">Master database of institutions in your assigned jurisdiction</small>
         </div>

         <!-- Filter Tabs: All / Visited / Unvisited -->
         @php $currentFilter = request('coverage_filter', 'all'); @endphp
         <div class="btn-group btn-group-sm" id="coverageFilterTabs">
            <button type="button" class="btn coverage-filter-btn {{ $currentFilter === 'all' ? 'btn-primary active' : 'btn-outline-primary' }}" data-filter="all">
               All ({{ number_format($total_institutes) }})
            </button>
            <button type="button" class="btn coverage-filter-btn {{ $currentFilter === 'visited' ? 'btn-success text-white active' : 'btn-outline-success' }}" data-filter="visited">
               Visited ({{ number_format($visited_count) }})
            </button>
            <button type="button" class="btn coverage-filter-btn {{ $currentFilter === 'unvisited' ? 'btn-danger text-white active' : 'btn-outline-danger' }}" data-filter="unvisited">
               Unvisited ({{ number_format($unvisited_count) }})
            </button>
         </div>
      </div>
      <div class="card-body p-3">
         <div class="table-responsive">
            <table id="coverageInstitutesTable" class="table table-striped table-bordered display responsive nowrap align-middle" style="width:100%">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;" class="text-center">#</th>
                     <th>Code</th>
                     <th>Institution Name</th>
                     <th>Category</th>
                     <th>Location</th>
                     <th class="text-center">Coverage Status</th>
                     <th class="text-center">Total Visits</th>
                     <th>Last Visit Date</th>
                     <th class="text-center" style="width: 100px;">Action</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th class="text-center">#</th>
                     <th>Code</th>
                     <th>Institution Name</th>
                     <th>Category</th>
                     <th>Location</th>
                     <th class="text-center">Coverage Status</th>
                     <th class="text-center">Total Visits</th>
                     <th>Last Visit Date</th>
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
    // 1. Initialize Category Breakdown Table (Client-side DataTable)
    var catTable = $('#categoryBreakdownTable').DataTable({
        responsive: true,
        scrollX: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[2, 'desc']]
    });

    // 2. Initialize Master Institutions Directory Table (Server-side Yajra DataTable)
    var currentCoverageFilter = "{{ request('coverage_filter', 'all') }}";

    var instTable = $('#coverageInstitutesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.field_visit_reports.coverage') }}",
            data: function (d) {
                var filterData = $('#reportFilterForm').serializeArray();
                $.each(filterData, function (i, field) {
                    if (field.value) {
                        d[field.name] = field.value;
                    }
                });
                d.coverage_filter = currentCoverageFilter;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
            { data: 'institute_code', name: 'institute_code' },
            { data: 'institute_name', name: 'institute_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'location', name: 'district.district_name' },
            { data: 'coverage_status', name: 'coverage_status', className: 'text-center' },
            { data: 'visit_count', name: 'visit_count', className: 'text-center' },
            { data: 'last_visit_date', name: 'last_visit_date' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[2, 'asc']],
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
    });

    // Apply primary button styling to export buttons
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
        .addClass('btn btn-primary me-1');

    // Filter Tabs Handler
    $('.coverage-filter-btn').on('click', function () {
        var selected = $(this).data('filter');
        currentCoverageFilter = selected;

        $('.coverage-filter-btn').removeClass('active btn-primary btn-success btn-danger text-white')
            .each(function () {
                var f = $(this).data('filter');
                if (f === 'all') $(this).addClass('btn-outline-primary');
                else if (f === 'visited') $(this).addClass('btn-outline-success');
                else if (f === 'unvisited') $(this).addClass('btn-outline-danger');
            });

        $(this).addClass('active');
        if (selected === 'all') {
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        } else if (selected === 'visited') {
            $(this).removeClass('btn-outline-success').addClass('btn-success text-white');
        } else if (selected === 'unvisited') {
            $(this).removeClass('btn-outline-danger').addClass('btn-danger text-white');
        }

        instTable.ajax.reload();
    });
});
</script>
@endsection

