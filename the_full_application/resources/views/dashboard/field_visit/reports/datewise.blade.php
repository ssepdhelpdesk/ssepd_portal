@extends('dashboard.layouts.main')

@section('title', 'Field Visit Reports || Date-wise Visit Report')

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
      <div class="card-body p-0">
         <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;">#</th>
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
               <tbody>
                  @forelse($daily_totals as $idx => $d)
                  @php
                     $dateObj = \Carbon\Carbon::parse($d->date_val);
                  @endphp
                  <tr>
                     <td>{{ $idx + 1 }}</td>
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
      <div class="card-body p-0">
         <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;">#</th>
                     <th>Visit Ref No</th>
                     <th>Visit Date</th>
                     <th>Type</th>
                     <th>Institution Name</th>
                     <th>Location</th>
                     <th>Visiting Officer</th>
                     <th>Status</th>
                     <th class="text-center">Action</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($detailed_visits as $idx => $v)
                  <tr>
                     <td>{{ $detailed_visits->firstItem() + $idx }}</td>
                     <td>
                        <a href="{{ route('admin.field_visits.show', $v->id) }}" class="fw-bold text-primary font-monospace">
                           {{ $v->visit_reference_no }}
                        </a>
                     </td>
                     <td class="fw-semibold">{{ $v->visit_date ? $v->visit_date->format('d-M-Y') : 'N/A' }}</td>
                     <td>{{ $v->visitType ? $v->visitType->name : 'N/A' }}</td>
                     <td>
                        <span class="fw-semibold">{{ $v->institution_name }}</span>
                        @if($v->institution_type)
                           <br><span class="badge bg-light text-dark border">{{ $v->institution_type }}</span>
                        @endif
                     </td>
                     <td>
                        @if($v->address_type === 1)
                           <span class="badge bg-success-subtle text-success border border-success"><i class="fas fa-tree me-1"></i>Rural</span>
                           <div class="small text-muted">{{ $v->district ? $v->district->district_name : '' }} &bull; {{ $v->block ? $v->block->block_name : '' }}</div>
                        @else
                           <span class="badge bg-info-subtle text-info border border-info"><i class="fas fa-city me-1"></i>Urban</span>
                           <div class="small text-muted">{{ $v->district ? $v->district->district_name : '' }} &bull; {{ $v->municipality ? $v->municipality->municipality_name : '' }}</div>
                        @endif
                     </td>
                     <td>
                        <span class="fw-semibold">{{ $v->visiting_officer_name ?: ($v->visitingOfficer ? $v->visitingOfficer->name : 'Officer #' . $v->visiting_officer_id) }}</span>
                        @if($v->visiting_officer_designation)
                           <br><span class="badge bg-secondary font-monospace" style="font-size: 0.65rem;">{{ $v->visiting_officer_designation }}</span>
                        @endif
                     </td>
                     <td>{!! $v->status_badge !!}</td>
                     <td class="text-center">
                        <a href="{{ route('admin.field_visits.show', $v->id) }}" class="btn btn-xs btn-outline-info">
                           <i class="fas fa-eye me-1"></i> View
                        </a>
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="9" class="text-center py-4 text-muted">
                        No field visits recorded for the specified criteria.
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
      @if($detailed_visits->hasPages())
      <div class="card-footer bg-white py-2">
         {{ $detailed_visits->links() }}
      </div>
      @endif
   </div>
</div>
@endsection

