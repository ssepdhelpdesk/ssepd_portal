@extends('dashboard.layouts.main')

@section('title', 'Field Visit Reports || Geographical Analysis')

@section('content')
<div class="container-fluid">
   <!-- Page Title & Breadcrumb -->
   <div class="row page-titles">
      <div class="col-md-7 align-self-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item"><a href="{{ route('admin.field_visit_reports.geographical') }}">Geographical Analysis</a></li>
            @foreach($breadcrumbs as $bc)
               @if($bc['url'])
                  <li class="breadcrumb-item"><a href="{{ $bc['url'] }}">{{ $bc['title'] }}</a></li>
               @else
                  <li class="breadcrumb-item active">{{ $bc['title'] }}</li>
               @endif
            @endforeach
         </ol>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <a href="{{ route('admin.field_visit_reports.geographical') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-home me-1"></i> Root Level
         </a>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   <!-- Common Filters Partial -->
   @include('dashboard.field_visit.reports._filters', ['exportRoute' => 'admin.field_visit_reports.geographical.export'])

   <!-- Breadcrumb / Drill Level Banner -->
   <div class="card shadow-sm border-0 mb-3 bg-white">
      <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
         <div class="d-flex align-items-center">
            <span class="badge bg-primary px-3 py-2 me-2 fs-6">
               <i class="fas fa-layer-group me-1"></i> Current Level: {{ ucfirst(str_replace('_', ' ', $current_level)) }}
            </span>
            @if(count($breadcrumbs) > 0)
               <span class="text-muted fw-semibold">
                  Hierarchy Path: <span class="text-dark">{{ implode(' › ', array_column($breadcrumbs, 'title')) }}</span>
               </span>
            @endif
         </div>
         <div>
            <a href="{{ route('admin.field_visit_reports.geographical.export', request()->query()) }}" class="btn btn-sm btn-success text-white">
               <i class="fas fa-file-excel me-1"></i> Export This Level
            </a>
         </div>
      </div>
   </div>

   <!-- Main Content: Visits Listing if at Leaf Level, else Units Breakdown Table -->
   @if(!empty($visits))
      <!-- Leaf Level: Detailed Visits under GP / Ward -->
      <div class="card shadow-sm border-0">
         <div class="card-header bg-light py-2 px-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
               <i class="fas fa-list-alt text-primary me-2"></i>Field Visits in Selected Jurisdiction
            </h5>
         </div>
         <div class="card-body p-3">
            <div class="table-responsive">
               <table id="geoVisitsTable" class="table table-striped table-bordered display responsive nowrap align-middle" style="width:100%">
                  <thead class="table-dark">
                     <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Visit Ref No</th>
                        <th>Visit Date</th>
                        <th>Visit Type</th>
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
                        <th>Visit Type</th>
                        <th>Institution Name</th>
                        <th>Location</th>
                        <th>Visiting Officer</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @forelse($visits as $idx => $v)
                     <tr>
                        <td class="text-center text-muted">{{ $visits->firstItem() + $idx }}</td>
                        <td>
                           <a href="{{ route('admin.field_visits.show', $v->id) }}" class="fw-bold text-primary font-monospace">
                              {{ $v->visit_reference_no }}
                           </a>
                        </td>
                        <td>{{ $v->visit_date ? $v->visit_date->format('d-M-Y') : 'N/A' }}</td>
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
                           <a href="{{ route('admin.field_visits.show', $v->id) }}" class="btn btn-xs btn-info text-white">
                              <i class="fas fa-eye me-1"></i> View Details
                           </a>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                           <i class="fas fa-info-circle me-1"></i> No visits recorded in this jurisdiction.
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
   @else
      <!-- Hierarchy Drill Table -->
      <div class="card shadow-sm border-0">
         <div class="card-header bg-light py-2 px-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
               <i class="fas fa-sitemap text-primary me-2"></i>Geographical Hierarchy Breakdown
            </h5>
         </div>
         <div class="card-body p-3">
            <div class="table-responsive">
               <table id="geoHierarchyTable" class="table table-striped table-bordered display responsive nowrap align-middle" style="width:100%">
                  <thead class="table-dark">
                     <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Geographical Unit Name</th>
                        <th>Classification</th>
                        <th class="text-center">Total Visits</th>
                        <th class="text-center">Rural Visits</th>
                        <th class="text-center">Urban Visits</th>
                        <th class="text-center" style="width: 160px;">Drill Down</th>
                     </tr>
                  </thead>
                  <tfoot>
                     <tr>
                        <th class="text-center">#</th>
                        <th>Geographical Unit Name</th>
                        <th>Classification</th>
                        <th class="text-center">Total Visits</th>
                        <th class="text-center">Rural Visits</th>
                        <th class="text-center">Urban Visits</th>
                        <th class="text-center">Drill Down</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @forelse($items as $idx => $item)
                     <tr>
                        <td class="text-center text-muted">{{ $idx + 1 }}</td>
                        <td class="fw-bold text-dark fs-6">{{ $item['name'] }}</td>
                        <td><span class="badge bg-secondary">{{ $item['type'] }}</span></td>
                        <td class="text-center fw-bold">{{ number_format($item['total_visits']) }}</td>
                        <td class="text-center text-success fw-semibold">{{ number_format($item['rural_visits']) }}</td>
                        <td class="text-center text-info fw-semibold">{{ number_format($item['urban_visits']) }}</td>
                        <td class="text-center">
                           @php
                              $drillParams = array_merge(request()->query(), $item['next_param']);
                           @endphp
                           <a href="{{ route('admin.field_visit_reports.geographical', $drillParams) }}" class="btn btn-xs btn-primary text-white">
                              <i class="fas fa-arrow-circle-right me-1"></i> Explore
                           </a>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                           <i class="fas fa-info-circle me-1"></i> No child geographical units found for this level.
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   @endif
</div>
@endsection

@section('script')
<script>
$(function () {
   if ($('#geoVisitsTable').length) {
      $('#geoVisitsTable').DataTable({
         responsive: true,
         scrollX: true,
         dom: 'Blfrtip',
         buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
         pageLength: 20,
         lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "All"]],
         order: [[2, 'desc']]
      });
   }

   if ($('#geoHierarchyTable').length) {
      $('#geoHierarchyTable').DataTable({
         responsive: true,
         scrollX: true,
         dom: 'Blfrtip',
         buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
         pageLength: 10,
         lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
         order: [[3, 'desc']]
      });
   }

   $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
      .addClass('btn btn-primary me-1');
});
</script>
@endsection

