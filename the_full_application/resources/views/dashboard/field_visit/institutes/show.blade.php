@section('title') 
{{ $institute->institute_name }} || SSEPD Institute Master
@endsection 
@extends('dashboard.layouts.main')
@section('content')
<div class="container-fluid">
   <!-- ============================================================== -->
   <!-- Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->
   <div class="row page-titles">
      <div class="col-md-7 align-self-center">
         <div class="d-flex align-items-center">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
               <li class="breadcrumb-item"><a href="javascript:void(0)">Field Visit</a></li>
               <li class="breadcrumb-item"><a href="{{ route('admin.field_visit_institutes.index') }}">Institute Master</a></li>
               <li class="breadcrumb-item active">{{ $institute->institute_code }}</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <a href="{{ route('admin.field_visit_institutes.index') }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-info me-1">
            <i class="fa fa-arrow-left me-1"></i> Back to Listing
         </a>
         @can('field-visit-institute-edit')
         <a href="{{ route('admin.field_visit_institutes.edit', $institute->id) }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-warning">
            <i class="fa fa-edit me-1"></i> Edit Institute
         </a>
         @endcan
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <div class="row">
      <!-- Main Details Column -->
      <div class="col-lg-8 col-md-12">
         <!-- Card 1: Header / Overview -->
         <div class="card shadow-sm border-top border-primary border-3 mb-4">
            <div class="card-body p-4">
               <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                  <div>
                     <span class="badge bg-primary font-monospace px-3 py-1 fs-6 mb-2">
                        {{ $institute->institute_code }}
                     </span>
                     <h3 class="card-title mb-1 fw-bold text-dark">{{ $institute->institute_name }}</h3>
                     @if(!empty($institute->management_name))
                        <p class="text-muted mb-0"><i class="fas fa-building text-info me-1"></i> Managed by: <strong>{{ $institute->management_name }}</strong></p>
                     @endif
                  </div>
                  <div class="text-end">
                     @if($institute->is_active)
                        <span class="badge bg-success fs-6 px-3 py-2">
                           <i class="fas fa-check-circle me-1"></i> Active Facility
                        </span>
                     @else
                        <span class="badge bg-danger fs-6 px-3 py-2">
                           <i class="fas fa-times-circle me-1"></i> Inactive Facility
                        </span>
                     @endif
                     <div class="mt-2">
                        @if($institute->which_govt == 2)
                           <span class="badge bg-secondary px-2 py-1">Govt. of India Funded</span>
                        @else
                           <span class="badge bg-success px-2 py-1">Govt. of Odisha Funded</span>
                        @endif
                     </div>
                  </div>
               </div>

               <hr>

               <!-- Details Grid -->
               <div class="row g-3">
                  <div class="col-md-6">
                     <div class="p-3 bg-light rounded border h-100">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2"><i class="fas fa-tag text-info me-1"></i> Category Classification</h6>
                        <div class="fs-5 fw-bold text-dark">{{ $institute->category_name }}</div>
                        <small class="text-muted">Category ID: {{ $institute->category_id }}</small>
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="p-3 bg-light rounded border h-100">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i> District & Sub-District</h6>
                        <div class="fs-5 fw-bold text-dark">{{ $institute->district_name ?? 'N/A' }}</div>
                        <small class="text-muted">
                           @if(!empty($institute->block_name))
                              Block: {{ $institute->block_name }}
                           @elseif(!empty($institute->municipality_name))
                              Municipality: {{ $institute->municipality_name }}
                           @else
                              District Code: {{ $institute->district_id }}
                           @endif
                        </small>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Card 2: Physical Location & Address -->
         <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
               <h5 class="fw-bold text-dark mb-3"><i class="fas fa-compass text-success me-2"></i> Location & Postal Address</h5>
               <table class="table table-bordered mb-0">
                  <tbody>
                     <tr>
                        <th style="width: 25%;" class="bg-light text-muted">State</th>
                        <td>{{ $institute->state->state_name ?? 'Odisha' }} <small class="text-muted">(State ID: {{ $institute->state_id ?? 228 }})</small></td>
                     </tr>
                     <tr>
                        <th class="bg-light text-muted">Address Type</th>
                        <td>
                           @if($institute->address_type == 1)
                              <span class="badge bg-success"><i class="fas fa-tree me-1"></i> Rural (Block)</span>
                           @elseif($institute->address_type == 2)
                              <span class="badge bg-primary"><i class="fas fa-city me-1"></i> Urban (ULB)</span>
                           @else
                              <span class="text-muted">Not specified</span>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <th class="bg-light text-muted">District</th>
                        <td>{{ $institute->district_name ?? 'N/A' }} (Domain ID: {{ $institute->district_id }})</td>
                     </tr>
                     <tr>
                        <th class="bg-light text-muted">Block / Municipality</th>
                        <td>
                           @if(!empty($institute->block_name))
                              {{ $institute->block_name }} <span class="badge bg-light text-dark border">Block</span>
                           @elseif(!empty($institute->municipality_name))
                              {{ $institute->municipality_name }} <span class="badge bg-light text-dark border">ULB</span>
                           @else
                              <span class="text-muted">Not specified</span>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <th class="bg-light text-muted">Gram Panchayat / Ward</th>
                        <td>
                           @if(!empty($institute->gp_name))
                              GP: {{ $institute->gp_name }}
                           @elseif(!empty($institute->ward_name))
                              Ward: {{ $institute->ward_name }}
                           @else
                              <span class="text-muted">Not specified</span>
                           @endif
                        </td>
                     </tr>
                     @if(!empty($institute->village_name) || !empty($institute->village_id))
                     <tr>
                        <th class="bg-light text-muted">Village</th>
                        <td>{{ $institute->village_name ?? 'N/A' }} <small class="text-muted">(Village ID: {{ $institute->village_id ?? 'N/A' }})</small></td>
                     </tr>
                     @endif
                     <tr>
                        <th class="bg-light text-muted">Physical Address</th>
                        <td>{{ $institute->address ?: 'Address not on legacy file' }}</td>
                     </tr>
                     <tr>
                        <th class="bg-light text-muted">PIN Code</th>
                        <td>{{ $institute->pin_code ?: 'N/A' }}</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>

         <!-- Card 3: Associated Field Visits -->
         <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
               <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="fw-bold text-dark mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i> Field Visit & Inspection History</h5>
                  <span class="badge bg-info text-dark">{{ $institute->fieldVisits->count() }} Recorded Visits</span>
               </div>

               @if($institute->fieldVisits->count() > 0)
                  <div class="table-responsive">
                     <table class="table table-hover table-striped align-middle border">
                        <thead class="table-light">
                           <tr>
                              <th>Visit Ref</th>
                              <th>Date</th>
                              <th>Purpose</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($institute->fieldVisits as $visit)
                           <tr>
                              <td class="font-monospace fw-bold">{{ $visit->visit_reference_no }}</td>
                              <td>{{ $visit->visit_date ? $visit->visit_date->format('d-M-Y') : 'N/A' }}</td>
                              <td>{{ Str::limit($visit->visit_purpose, 40) }}</td>
                              <td><span class="badge bg-info">{{ ucfirst($visit->status) }}</span></td>
                              <td>
                                 <a href="{{ route('admin.field_visits.show', $visit->id) }}" class="btn btn-xs btn-outline-info">
                                    <i class="fas fa-eye"></i> View
                                 </a>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               @else
                  <div class="alert alert-light border text-center text-muted py-4 mb-0">
                     <i class="fas fa-clipboard-list fa-2x mb-2 d-block opacity-50"></i>
                     <p class="mb-0">No field visits have been recorded for this institute yet.</p>
                     @can('field-visit-create')
                     <a href="{{ route('admin.field_visits.create') }}?institution_id={{ $institute->id }}" class="btn btn-xs btn-primary mt-2">
                        <i class="fas fa-plus me-1"></i> Record New Field Visit
                     </a>
                     @endcan
                  </div>
               @endif
            </div>
         </div>
      </div>

      <!-- Right Column: Contact & Audit Details -->
      <div class="col-lg-4 col-md-12">
         <!-- Card: Contact & Liaison Details -->
         <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
               <h5 class="fw-bold text-dark mb-3"><i class="fas fa-address-book text-warning me-2"></i> Site Liaison</h5>
               <ul class="list-group list-group-flush">
                  <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                     <span class="text-muted">Contact Person:</span>
                     <span class="fw-bold text-dark">{{ $institute->contact_person ?: 'N/A' }}</span>
                  </li>
                  <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                     <span class="text-muted">Phone Number:</span>
                     <span class="fw-bold text-dark">
                        @if($institute->contact_number)
                           <a href="tel:{{ $institute->contact_number }}">{{ $institute->contact_number }}</a>
                        @else
                           N/A
                        @endif
                     </span>
                  </li>
                  <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                     <span class="text-muted">Email Address:</span>
                     <span class="fw-bold text-dark">
                        @if($institute->email)
                           <a href="mailto:{{ $institute->email }}">{{ $institute->email }}</a>
                        @else
                           N/A
                        @endif
                     </span>
                  </li>
               </ul>

               @if(!empty($institute->remarks))
                  <div class="mt-3 p-2 bg-light rounded border small">
                     <strong>Remarks:</strong>
                     <p class="mb-0 text-muted mt-1">{{ $institute->remarks }}</p>
                  </div>
               @endif
            </div>
         </div>

         <!-- Card: System Audit & Provenance -->
         <div class="card shadow-sm border-top border-secondary border-3 mb-4">
            <div class="card-body p-4">
               <h5 class="fw-bold text-dark mb-3"><i class="fas fa-shield-alt text-info me-2"></i> System Audit & Metadata</h5>
               <table class="table table-sm table-borderless mb-0 small">
                  <tbody>
                     <tr>
                        <td class="text-muted">Internal DB ID:</td>
                        <td class="fw-bold font-monospace">{{ $institute->id }}</td>
                     </tr>
                     <tr>
                        <td class="text-muted">Excel Source Row ID:</td>
                        <td class="fw-bold font-monospace">{{ $institute->source_institute_id ?? 'N/A' }}</td>
                     </tr>
                     <tr>
                        <td class="text-muted">Legacy Sub-ID:</td>
                        <td class="fw-bold font-monospace">{{ $institute->legacy_sub_id ?? 'N/A' }}</td>
                     </tr>
                     <tr>
                        <td class="text-muted">Management ID:</td>
                        <td class="fw-bold font-monospace">{{ $institute->management_id ?? 'N/A' }}</td>
                     </tr>
                     <tr class="border-top">
                        <td class="text-muted pt-2">Created By:</td>
                        <td class="pt-2">
                           <span class="badge bg-secondary font-monospace">user_table_id: {{ $institute->created_by ?? 'N/A' }}</span>
                           @if($institute->creator)
                              <br><small class="text-muted">{{ $institute->creator->name }}</small>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <td class="text-muted">Created At:</td>
                        <td>{{ $institute->created_at ? $institute->created_at->format('d-M-Y h:i A') : 'N/A' }}</td>
                     </tr>
                     <tr class="border-top">
                        <td class="text-muted pt-2">Last Updated By:</td>
                        <td class="pt-2">
                           <span class="badge bg-secondary font-monospace">user_table_id: {{ $institute->updated_by ?? 'N/A' }}</span>
                           @if($institute->updater)
                              <br><small class="text-muted">{{ $institute->updater->name }}</small>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <td class="text-muted">Last Updated At:</td>
                        <td>{{ $institute->updated_at ? $institute->updated_at->format('d-M-Y h:i A') : 'N/A' }}</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
