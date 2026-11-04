@extends('dashboard.layouts.main')

@section('title', 'Field Visit Reports || Field Visit Summary')

@section('style')
<style>
   .kpi-card {
      border: 0;
      border-radius: 8px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
   }
   .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
   }
   .kpi-icon {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
   }
   .chart-card {
      border: 0;
      border-radius: 8px;
      min-height: 340px;
   }
   .chart-container {
      position: relative;
      height: 260px;
      width: 100%;
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
            <li class="breadcrumb-item active">Field Visit Summary</li>
         </ol>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <span class="badge bg-info text-dark px-3 py-2 fw-semibold">
            <i class="fas fa-shield-alt me-1"></i> Access Level: {{ ucfirst($filterOptions['jurisdiction']['level'] ?? 'State') }}
         </span>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   <!-- Common Filters Partial -->
   @include('dashboard.field_visit.reports._filters', ['exportRoute' => 'admin.field_visit_reports.summary.export'])

   <!-- KPI Cards Row -->
   <div class="row g-3 mb-4">
      <!-- Total Visits -->
      <div class="col-lg-2 col-md-4 col-sm-6">
         <div class="card kpi-card shadow-sm border-start border-primary border-4">
            <div class="card-body p-3">
               <div class="d-flex align-items-center justify-content-between">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Total Visits</p>
                     <h3 class="mb-0 fw-bold text-primary">{{ number_format($kpis['total_visits']) }}</h3>
                  </div>
                  <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                     <i class="fas fa-clipboard-check"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Rural Visits -->
      <div class="col-lg-2 col-md-4 col-sm-6">
         <div class="card kpi-card shadow-sm border-start border-success border-4">
            <div class="card-body p-3">
               <div class="d-flex align-items-center justify-content-between">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Rural Visits</p>
                     <h3 class="mb-0 fw-bold text-success">{{ number_format($kpis['rural_visits']) }}</h3>
                  </div>
                  <div class="kpi-icon bg-success bg-opacity-10 text-success">
                     <i class="fas fa-tree"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Urban Visits -->
      <div class="col-lg-2 col-md-4 col-sm-6">
         <div class="card kpi-card shadow-sm border-start border-info border-4">
            <div class="card-body p-3">
               <div class="d-flex align-items-center justify-content-between">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Urban Visits</p>
                     <h3 class="mb-0 fw-bold text-info">{{ number_format($kpis['urban_visits']) }}</h3>
                  </div>
                  <div class="kpi-icon bg-info bg-opacity-10 text-info">
                     <i class="fas fa-city"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Unique Institutions -->
      <div class="col-lg-2 col-md-4 col-sm-6">
         <div class="card kpi-card shadow-sm border-start border-warning border-4">
            <div class="card-body p-3">
               <div class="d-flex align-items-center justify-content-between">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Institutions</p>
                     <h3 class="mb-0 fw-bold text-warning">{{ number_format($kpis['unique_institutions']) }}</h3>
                  </div>
                  <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                     <i class="fas fa-building"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Unique Officers -->
      <div class="col-lg-2 col-md-4 col-sm-6">
         <div class="card kpi-card shadow-sm border-start border-purple border-4" style="border-left-color: #6f42c1 !important;">
            <div class="card-body p-3">
               <div class="d-flex align-items-center justify-content-between">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Visiting Officers</p>
                     <h3 class="mb-0 fw-bold" style="color: #6f42c1;">{{ number_format($kpis['unique_officers']) }}</h3>
                  </div>
                  <div class="kpi-icon text-white" style="background-color: #6f42c1;">
                     <i class="fas fa-user-tie"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Uploaded Evidence Files -->
      <div class="col-lg-2 col-md-4 col-sm-6">
         <div class="card kpi-card shadow-sm border-start border-danger border-4">
            <div class="card-body p-3">
               <div class="d-flex align-items-center justify-content-between">
                  <div>
                     <p class="text-muted small mb-1 text-uppercase fw-semibold">Evidence Files</p>
                     <h3 class="mb-0 fw-bold text-danger">{{ number_format($kpis['total_evidences']) }}</h3>
                  </div>
                  <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                     <i class="fas fa-camera"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Charts Row 1 -->
   <div class="row g-3 mb-4">
      <!-- Chart 1: Rural vs Urban -->
      <div class="col-lg-4 col-md-6">
         <div class="card chart-card shadow-sm">
            <div class="card-header bg-white py-2 border-0">
               <h6 class="card-title mb-0 fw-bold"><i class="fas fa-chart-pie text-success me-2"></i>Rural vs Urban Split</h6>
            </div>
            <div class="card-body p-3">
               <div class="chart-container">
                  <canvas id="chartRuralUrban"></canvas>
               </div>
            </div>
         </div>
      </div>

      <!-- Chart 2: Visit Types -->
      <div class="col-lg-4 col-md-6">
         <div class="card chart-card shadow-sm">
            <div class="card-header bg-white py-2 border-0">
               <h6 class="card-title mb-0 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>Visit Types Distribution</h6>
            </div>
            <div class="card-body p-3">
               <div class="chart-container">
                  <canvas id="chartVisitTypes"></canvas>
               </div>
            </div>
         </div>
      </div>

      <!-- Chart 3: Monthly Trend -->
      <div class="col-lg-4 col-md-12">
         <div class="card chart-card shadow-sm">
            <div class="card-header bg-white py-2 border-0">
               <h6 class="card-title mb-0 fw-bold"><i class="fas fa-chart-line text-info me-2"></i>Monthly Visit Trend</h6>
            </div>
            <div class="card-body p-3">
               <div class="chart-container">
                  <canvas id="chartMonthlyTrend"></canvas>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Charts Row 2 -->
   <div class="row g-3 mb-4">
      <!-- Chart 4: Categories -->
      <div class="col-lg-6 col-md-12">
         <div class="card chart-card shadow-sm">
            <div class="card-header bg-white py-2 border-0">
               <h6 class="card-title mb-0 fw-bold"><i class="fas fa-th-large text-warning me-2"></i>Institution Category Distribution</h6>
            </div>
            <div class="card-body p-3">
               <div class="chart-container">
                  <canvas id="chartCategories"></canvas>
               </div>
            </div>
         </div>
      </div>

      <!-- Chart 5: Geographical Distribution -->
      <div class="col-lg-6 col-md-12">
         <div class="card chart-card shadow-sm">
            <div class="card-header bg-white py-2 border-0">
               <h6 class="card-title mb-0 fw-bold"><i class="fas fa-map-marked-alt text-danger me-2"></i>Geographical Distribution</h6>
            </div>
            <div class="card-body p-3">
               <div class="chart-container">
                  <canvas id="chartGeographical"></canvas>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Jurisdiction Breakdown Summary Table -->
   <div class="row mb-4">
      <div class="col-12">
         <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
               <h5 class="card-title mb-0 fw-bold">
                  <i class="fas fa-table text-primary me-2"></i>
                  @if($filterOptions['jurisdiction']['level'] === 'state')
                     District-wise Visit Breakdown
                  @elseif($filterOptions['jurisdiction']['level'] === 'district')
                     Subdivision-wise Visit Breakdown
                  @elseif($filterOptions['jurisdiction']['level'] === 'subdivision')
                     Block & Municipality-wise Visit Breakdown
                  @elseif($filterOptions['jurisdiction']['level'] === 'block')
                     Gram Panchayat-wise Visit Breakdown
                  @elseif($filterOptions['jurisdiction']['level'] === 'ulb')
                     Ward-wise Visit Breakdown
                  @else
                     Jurisdiction Breakdown
                  @endif
               </h5>
               <a href="{{ route('admin.field_visit_reports.summary.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                  <i class="fas fa-download me-1"></i> Export Table
               </a>
            </div>
            <div class="card-body p-0">
               <div class="table-responsive">
                  <table class="table table-hover table-striped mb-0 align-middle">
                     <thead class="table-dark">
                        <tr>
                           <th style="width: 50px;">#</th>
                           <th>Administrative Unit</th>
                           <th>Type</th>
                           <th class="text-center">Total Visits</th>
                           <th class="text-center">Rural Visits</th>
                           <th class="text-center">Urban Visits</th>
                           <th class="text-center">Unique Institutions</th>
                           <th class="text-center">Unique Officers</th>
                           <th class="text-center" style="width: 140px;">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($breakdownRows as $idx => $row)
                        <tr>
                           <td>{{ $idx + 1 }}</td>
                           <td class="fw-bold text-dark">{{ $row['name'] }}</td>
                           <td><span class="badge bg-secondary">{{ $row['type'] }}</span></td>
                           <td class="text-center fw-bold">{{ number_format($row['total_visits']) }}</td>
                           <td class="text-center text-success fw-semibold">{{ number_format($row['rural_visits']) }}</td>
                           <td class="text-center text-info fw-semibold">{{ number_format($row['urban_visits']) }}</td>
                           <td class="text-center">{{ number_format($row['unique_institutes']) }}</td>
                           <td class="text-center">{{ number_format($row['unique_officers']) }}</td>
                           <td class="text-center">
                              @if($row['total_visits'] > 0)
                                 <a href="{{ route('admin.field_visit_reports.geographical', $row['drilldown_param']) }}" class="btn btn-xs btn-primary text-white" title="Explore in Geographical Analysis">
                                    <i class="fas fa-sitemap me-1"></i> Drill Down
                                 </a>
                              @else
                                 <span class="text-muted small">—</span>
                              @endif
                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="9" class="text-center py-4 text-muted">
                              <i class="fas fa-info-circle me-1"></i> No records found for the selected jurisdiction and filter criteria.
                           </td>
                        </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('scripts')
<!-- Load Chart.js with CDN fallback -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
   if (typeof Chart === 'undefined') {
      console.error('Chart.js could not be loaded.');
      return;
   }

   const chartColors = ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6c757d'];

   // 1. Rural vs Urban Chart (Donut)
   const ruralUrbanCtx = document.getElementById('chartRuralUrban');
   if (ruralUrbanCtx) {
      new Chart(ruralUrbanCtx, {
         type: 'doughnut',
         data: {
            labels: {!! json_encode($charts['rural_vs_urban']['labels']) !!},
            datasets: [{
               data: {!! json_encode($charts['rural_vs_urban']['data']) !!},
               backgroundColor: {!! json_encode($charts['rural_vs_urban']['colors']) !!},
               borderWidth: 2,
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
               legend: { position: 'bottom' }
            }
         }
      });
   }

   // 2. Visit Types Chart (Bar)
   const visitTypesCtx = document.getElementById('chartVisitTypes');
   if (visitTypesCtx) {
      new Chart(visitTypesCtx, {
         type: 'bar',
         data: {
            labels: {!! json_encode($charts['visit_types']['labels']) !!},
            datasets: [{
               label: 'Visits',
               data: {!! json_encode($charts['visit_types']['data']) !!},
               backgroundColor: '#007bff',
               borderRadius: 4,
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
               y: { beginAtZero: true, ticks: { precision: 0 } }
            },
            plugins: {
               legend: { display: false }
            }
         }
      });
   }

   // 3. Monthly Trend Chart (Line)
   const monthlyCtx = document.getElementById('chartMonthlyTrend');
   if (monthlyCtx) {
      new Chart(monthlyCtx, {
         type: 'line',
         data: {
            labels: {!! json_encode($charts['monthly_trend']['labels']) !!},
            datasets: [{
               label: 'Visits Conducted',
               data: {!! json_encode($charts['monthly_trend']['data']) !!},
               borderColor: '#17a2b8',
               backgroundColor: 'rgba(23, 162, 184, 0.15)',
               fill: true,
               tension: 0.3,
               pointRadius: 4,
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
               y: { beginAtZero: true, ticks: { precision: 0 } }
            }
         }
      });
   }

   // 4. Categories Chart (Horizontal Bar)
   const categoriesCtx = document.getElementById('chartCategories');
   if (categoriesCtx) {
      new Chart(categoriesCtx, {
         type: 'bar',
         data: {
            labels: {!! json_encode($charts['categories']['labels']) !!},
            datasets: [{
               label: 'Visits',
               data: {!! json_encode($charts['categories']['data']) !!},
               backgroundColor: '#ffc107',
               borderRadius: 4,
            }]
         },
         options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
               x: { beginAtZero: true, ticks: { precision: 0 } }
            },
            plugins: {
               legend: { display: false }
            }
         }
      });
   }

   // 5. Geographical Chart (Bar)
   const geoCtx = document.getElementById('chartGeographical');
   if (geoCtx) {
      new Chart(geoCtx, {
         type: 'bar',
         data: {
            labels: {!! json_encode($charts['geographical']['labels']) !!},
            datasets: [{
               label: 'Visits Count',
               data: {!! json_encode($charts['geographical']['data']) !!},
               backgroundColor: '#20c997',
               borderRadius: 4,
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
               y: { beginAtZero: true, ticks: { precision: 0 } }
            },
            plugins: {
               legend: { display: false }
            }
         }
      });
   }
});
</script>
@endsection

