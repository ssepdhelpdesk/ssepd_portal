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
      <div class="card-body p-0">
         <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
               <thead class="table-dark">
                  <tr>
                     <th style="width: 40px;">#</th>
                     <th>Visit Ref No</th>
                     <th>Visit Date</th>
                     <th>Type</th>
                     <th>Institution Details</th>
                     <th>Geographical Location</th>
                     <th>Visiting Officer</th>
                     <th>Observations & Suggestions</th>
                     <th>Status</th>
                     <th class="text-center" style="width: 110px;">Action</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($visits as $idx => $v)
                  @php
                     $desig = $v->visiting_officer_designation === 'Other' && !empty($v->other_designation)
                        ? $v->other_designation
                        : $v->visiting_officer_designation;

                     $blockOrUlb = $v->address_type === 1
                        ? ($v->block ? $v->block->block_name : 'N/A')
                        : ($v->municipality ? $v->municipality->municipality_name : 'N/A');

                     $gpOrWard = $v->address_type === 1
                        ? ($v->grampanchayat ? $v->grampanchayat->gp_name : 'N/A')
                        : ($v->ward ? ($v->ward->ward_name ?: 'Ward ' . $v->ward_id) : 'N/A');
                  @endphp
                  <tr>
                     <td>{{ $visits->firstItem() + $idx }}</td>
                     <td>
                        <a href="{{ route('admin.field_visits.show', $v->id) }}" class="ref-badge text-primary text-decoration-none">
                           {{ $v->visit_reference_no }}
                        </a>
                     </td>
                     <td class="text-nowrap fw-semibold">
                        {{ $v->visit_date ? $v->visit_date->format('d-M-Y') : 'N/A' }}
                     </td>
                     <td>
                        <span class="badge bg-secondary-subtle text-dark border">
                           {{ $v->visitType ? $v->visitType->name : 'N/A' }}
                        </span>
                     </td>
                     <td>
                        <div class="fw-bold text-dark">{{ $v->institution_name }}</div>
                        <div class="small text-muted">
                           Category: <span class="fw-semibold">{{ $v->institute ? $v->institute->category_name : ($v->institution_type ?: 'General') }}</span>
                        </div>
                     </td>
                     <td>
                        @if($v->address_type === 1)
                           <span class="badge bg-success-subtle text-success border border-success"><i class="fas fa-tree me-1"></i>Rural</span>
                           <div class="small text-dark fw-semibold mt-1">{{ $v->district ? $v->district->district_name : 'N/A' }}</div>
                           <div class="small text-muted">{{ $blockOrUlb }} › {{ $gpOrWard }}</div>
                        @else
                           <span class="badge bg-info-subtle text-info border border-info"><i class="fas fa-city me-1"></i>Urban</span>
                           <div class="small text-dark fw-semibold mt-1">{{ $v->district ? $v->district->district_name : 'N/A' }}</div>
                           <div class="small text-muted">{{ $blockOrUlb }} › {{ $gpOrWard }}</div>
                        @endif
                     </td>
                     <td>
                        <div class="fw-bold">{{ $v->visiting_officer_name ?: ($v->visitingOfficer ? $v->visitingOfficer->name : 'Officer #' . $v->visiting_officer_id) }}</div>
                        @if(!empty($desig))
                           <span class="badge bg-secondary font-monospace" style="font-size: 0.65rem;">{{ $desig }}</span>
                        @endif
                        @if($v->visitingOfficer && $v->visiting_officer_name !== $v->visitingOfficer->name)
                           <div class="text-muted" style="font-size: 0.72rem;">User: {{ $v->visitingOfficer->name }}</div>
                        @endif
                     </td>
                     <td style="max-width: 250px;">
                        @if(!empty($v->key_observations))
                           <div class="small text-dark text-truncate" title="{{ $v->key_observations }}">
                              <strong>Obs:</strong> {{ Str::limit($v->key_observations, 80) }}
                           </div>
                        @endif
                        @if(!empty($v->recommendations))
                           <div class="small text-muted text-truncate" title="{{ $v->recommendations }}">
                              <strong>Rec:</strong> {{ Str::limit($v->recommendations, 80) }}
                           </div>
                        @endif
                        @if(empty($v->key_observations) && empty($v->recommendations))
                           <span class="text-muted small">—</span>
                        @endif
                     </td>
                     <td>
                        {!! $v->status_badge !!}
                     </td>
                     <td class="text-center">
                        <a href="{{ route('admin.field_visits.show', $v->id) }}" class="btn btn-xs btn-outline-primary" title="View Visit Details">
                           <i class="fas fa-eye me-1"></i> Details
                        </a>
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="10" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-2x mb-2 text-secondary d-block"></i>
                        No field visit records found matching your filters.
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
      @if($visits->hasPages())
      <div class="card-footer bg-white py-2">
         {{ $visits->links() }}
      </div>
      @endif
   </div>
</div>
@endsection

