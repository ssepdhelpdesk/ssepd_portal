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
               <form method="GET" action="{{ route('admin.field_visits.index') }}" class="p-3 bg-light rounded border mb-4">
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
                        @if(request()->hasAny(['search', 'field_visit_type_id', 'status', 'district_id', 'is_follow_up_required', 'date_from', 'date_to']))
                        <a href="{{ route('admin.field_visits.index') }}" class="btn btn-secondary btn-sm px-3">
                           <i class="fas fa-undo me-1"></i> Reset
                        </a>
                        @endif
                     </div>
                  </div>
               </form>

               <!-- Table Data -->
               <div class="table-responsive">
                  <table class="table table-bordered table-hover align-middle">
                     <thead class="table-dark">
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
                     <tbody>
                        @forelse($visits as $index => $visit)
                        <tr>
                           <td class="text-center text-muted">
                              {{ $visits->firstItem() + $index }}
                           </td>
                           <td>
                              <a href="{{ route('admin.field_visits.show', $visit->id) }}" class="ref-badge text-primary text-decoration-none">
                                 {{ $visit->visit_reference_no }}
                              </a>
                              @if($visit->is_follow_up_required)
                              <span class="badge bg-warning text-dark d-block mt-1" style="font-size: 0.7rem;">
                                 <i class="fas fa-exclamation-circle me-1"></i> Follow-up Req
                              </span>
                              @endif
                           </td>
                           <td class="small">
                              <div>{{ $visit->visit_date ? $visit->visit_date->format('d-M-Y') : '—' }}</div>
                              @if($visit->start_time)
                              <span class="text-muted" style="font-size: 0.75rem;">{{ date('h:i A', strtotime($visit->start_time)) }}</span>
                              @endif
                           </td>
                           <td>
                              <span class="fw-semibold">{{ $visit->visitType ? $visit->visitType->name : 'N/A' }}</span>
                           </td>
                           <td>
                              <span class="badge bg-light text-dark border me-1">{{ $visit->institution_type }}</span>
                              <div class="fw-bold mt-1 text-dark">{{ $visit->institution_name }}</div>
                              @if($visit->organization_code)
                              <span class="text-muted font-monospace" style="font-size: 0.75rem;">[{{ $visit->organization_code }}]</span>
                              @endif
                           </td>
                           <td class="small">
                              {{ $visit->district ? $visit->district->district_name : '—' }}
                              @if($visit->block)
                              <div class="text-muted" style="font-size: 0.75rem;">{{ $visit->block->block_name }}</div>
                              @endif
                           </td>
                           <td class="small">
                              <i class="fas fa-user-check text-success me-1"></i>
                              <strong>{{ $visit->visitingOfficer ? $visit->visitingOfficer->name : ('Officer #' . $visit->visiting_officer_id) }}</strong>
                           </td>
                           <td class="text-center">
                              @php $photoCount = $visit->evidences->count(); @endphp
                              @if($photoCount > 0)
                              <span class="badge bg-info text-dark" title="{{ $photoCount }} geotagged photo(s)">
                                 <i class="fas fa-camera me-1"></i> {{ $photoCount }}
                              </span>
                              @else
                              <span class="text-muted" style="font-size: 0.8rem;">—</span>
                              @endif
                           </td>
                           <td class="text-center">
                              {!! $visit->status_badge !!}
                           </td>
                           <td class="text-center">
                              <div class="btn-group">
                                 <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                 </button>
                                 <div class="dropdown-menu dropdown-menu-end">
                                    @can('field-visit-show')
                                    <a class="dropdown-item" href="{{ route('admin.field_visits.show', $visit->id) }}">
                                       <i class="fas fa-eye text-info me-2"></i> View Details
                                    </a>
                                    @endcan
                                    @can('field-visit-edit')
                                    <a class="dropdown-item" href="{{ route('admin.field_visits.edit', $visit->id) }}">
                                       <i class="fas fa-edit text-warning me-2"></i> Edit Visit
                                    </a>
                                    @endcan
                                    @can('field-visit-delete')
                                    <a class="dropdown-item text-danger" href="{{ route('admin.field_visits.destroy', $visit->id) }}" id="delete">
                                       <i class="fas fa-trash-alt text-danger me-2"></i> Delete
                                    </a>
                                    @endcan
                                 </div>
                              </div>
                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="10" class="text-center py-5 text-muted">
                              <i class="fas fa-map-marked-alt fa-3x mb-3 text-secondary d-block"></i>
                              <h5>No Field Visits Found</h5>
                              <p class="small mb-3">No field visit inspection records match the current filter criteria.</p>
                              @can('field-visit-create')
                              <a href="{{ route('admin.field_visits.create') }}" class="btn btn-success btn-sm text-white">
                                 <i class="fa fa-plus-circle me-1"></i> Record First Field Visit
                              </a>
                              @endcan
                           </td>
                        </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>

               <!-- Pagination -->
               <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                  <div class="small text-muted mb-2">
                     Showing {{ $visits->firstItem() ?? 0 }} to {{ $visits->lastItem() ?? 0 }} of {{ $visits->total() }} visits
                  </div>
                  <div>
                     {{ $visits->links('pagination::bootstrap-5') }}
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</div>
@endsection
