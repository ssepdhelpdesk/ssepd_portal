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
      <div class="card-body p-0">
         <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;">#</th>
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
                     <td>{{ $idx + 1 }}</td>
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
            <small class="text-muted">Showing {{ $institutes->firstItem() ?? 0 }} to {{ $institutes->lastItem() ?? 0 }} of {{ $institutes->total() }} institutions</small>
         </div>

         <!-- Filter Tabs: All / Visited / Unvisited -->
         <div class="btn-group btn-group-sm">
            @php $currentFilter = request('coverage_filter', 'all'); @endphp
            <a href="{{ request()->fullUrlWithQuery(['coverage_filter' => 'all']) }}" class="btn {{ $currentFilter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
               All ({{ number_format($total_institutes) }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['coverage_filter' => 'visited']) }}" class="btn {{ $currentFilter === 'visited' ? 'btn-success text-white' : 'btn-outline-success' }}">
               Visited ({{ number_format($visited_count) }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['coverage_filter' => 'unvisited']) }}" class="btn {{ $currentFilter === 'unvisited' ? 'btn-danger text-white' : 'btn-outline-danger' }}">
               Unvisited ({{ number_format($unvisited_count) }})
            </a>
         </div>
      </div>
      <div class="card-body p-0">
         <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;">#</th>
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
               <tbody>
                  @forelse($institutes as $idx => $inst)
                  @php
                     $isVisited = ($inst->visit_count ?? 0) > 0;
                     $blockOrUlb = $inst->block ? $inst->block->block_name : ($inst->municipality ? $inst->municipality->municipality_name : $inst->block_name);
                  @endphp
                  <tr>
                     <td>{{ $institutes->firstItem() + $idx }}</td>
                     <td class="font-monospace small fw-bold">{{ $inst->institute_code }}</td>
                     <td class="fw-bold text-dark">{{ $inst->institute_name }}</td>
                     <td><span class="badge bg-light text-dark border">{{ $inst->category_name }}</span></td>
                     <td>
                        <div class="small fw-semibold">{{ $inst->district ? $inst->district->district_name : ($inst->district_name ?: 'N/A') }}</div>
                        <div class="small text-muted">{{ $blockOrUlb ?: '—' }}</div>
                     </td>
                     <td class="text-center">
                        @if($isVisited)
                           <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Visited</span>
                        @else
                           <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Not Visited</span>
                        @endif
                     </td>
                     <td class="text-center fw-bold">{{ number_format($inst->visit_count ?? 0) }}</td>
                     <td>
                        @if(!empty($inst->last_visit_date))
                           <span class="fw-semibold text-dark">{{ date('d-M-Y', strtotime($inst->last_visit_date)) }}</span>
                        @else
                           <span class="text-muted small">Never</span>
                        @endif
                     </td>
                     <td class="text-center">
                        @if($isVisited)
                           <a href="{{ route('admin.field_visit_reports.detailed', ['institution_id' => $inst->id]) }}" class="btn btn-xs btn-outline-info" title="View visits to this institution">
                              <i class="fas fa-eye me-1"></i> Visits
                           </a>
                        @else
                           <span class="text-muted small">—</span>
                        @endif
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="9" class="text-center py-5 text-muted">
                        <i class="fas fa-university fa-2x mb-2 text-secondary d-block"></i>
                        No institutions found matching the filter criteria.
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
      @if($institutes->hasPages())
      <div class="card-footer bg-white py-2">
         {{ $institutes->links() }}
      </div>
      @endif
   </div>
</div>
@endsection

