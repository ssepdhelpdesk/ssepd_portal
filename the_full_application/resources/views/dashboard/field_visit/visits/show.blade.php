@section('title') 
Field Visit Details || {{ $visit->visit_reference_no }}
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .detail-label {
      font-weight: 600;
      color: #495057;
      width: 220px;
   }
   .detail-value {
      color: #212529;
   }
   .evidence-img-container {
      position: relative;
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid #dee2e6;
      background: #000;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
   }
   .evidence-img-container img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      transition: transform 0.3s;
   }
   .evidence-img-container img:hover {
      transform: scale(1.03);
   }
   .evidence-overlay-bar {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(0, 0, 0, 0.75);
      color: #fff;
      padding: 6px 10px;
      font-size: 0.75rem;
      font-family: monospace;
   }
   .evidence-overlay-bar a {
      color: #00ff88;
      text-decoration: none;
   }
   .evidence-overlay-bar a:hover {
      text-decoration: underline;
   }
   .checksum-badge {
      font-family: monospace;
      font-size: 0.7rem;
      word-break: break-all;
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      padding: 3px 6px;
      border-radius: 4px;
      display: inline-block;
      max-width: 100%;
   }
   .map-box {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      overflow: hidden;
      height: 260px;
   }
   .map-box iframe {
      width: 100%;
      height: 100%;
      border: 0;
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
               <li class="breadcrumb-item"><a href="{{ route('admin.field_visits.index') }}">Field Visit</a></li>
               <li class="breadcrumb-item active">Visit Details</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.field_visits.index') }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-info">
               <i class="fas fa-arrow-alt-circle-left me-1"></i> Back to Visits
            </a>
            @can('field-visit-edit')
            <a href="{{ route('admin.field_visits.edit', $visit->id) }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-warning">
               <i class="fas fa-edit me-1"></i> Edit Visit
            </a>
            @endcan
            <button type="button" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-primary" onclick="window.print()">
               <i class="fas fa-print me-1"></i> Print
            </button>
         </div>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   <!-- HEADER CARD -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
         <div class="d-flex align-items-center gap-2">
            <i class="fas fa-map-marked-alt fa-2x text-warning"></i>
            <div>
               <h4 class="mb-0 text-white font-monospace">{{ $visit->visit_reference_no }}</h4>
               <span class="small text-light">{{ $visit->visitType ? $visit->visitType->name : 'Field Visit' }} &bull; {{ $visit->visit_date ? $visit->visit_date->format('d-M-Y') : 'N/A' }}</span>
            </div>
         </div>
         <div class="d-flex align-items-center gap-2">
            {!! $visit->status_badge !!}
            @if($visit->is_follow_up_required)
            <span class="badge bg-warning text-dark"><i class="fas fa-bell me-1"></i> Follow-up Due: {{ $visit->follow_up_date ? $visit->follow_up_date->format('d-M-Y') : 'Required' }}</span>
            @endif
         </div>
      </div>
      <div class="card-body">
         <div class="row g-3">
            <div class="col-md-6">
               <h6 class="text-muted small mb-1">OFFICE / INSTITUTION</h6>
               <h5 class="fw-bold mb-1">{{ $visit->institution_name }}</h5>
               <span class="badge bg-light text-dark border">{{ $visit->institution_type }}</span>
               @if($visit->organization_code)
               <span class="font-monospace text-muted small ms-1">[{{ $visit->organization_code }}]</span>
               @endif
               <div class="small text-muted mt-2">
                  <i class="fas fa-map-pin text-danger me-1"></i>
                  {{ $visit->district ? $visit->district->district_name : 'No District' }}
                  @if($visit->block) &bull; {{ $visit->block->block_name }} @elseif($visit->municipality) &bull; {{ $visit->municipality->municipality_name }} @endif
                  @if($visit->institution_address) &bull; {{ $visit->institution_address }} @endif
               </div>
            </div>
            <div class="col-md-6 border-start ps-md-4">
               <h6 class="text-muted small mb-1">VISITING OFFICER DETAILS</h6>
               <h5 class="fw-bold mb-1">
                  <i class="fas fa-user-tie text-primary me-1"></i>
                  {{ $visit->visiting_officer_name ?: ($visit->visitingOfficer ? $visit->visitingOfficer->name : 'N/A') }}
               </h5>
               @php
                  $desigDisplay = $visit->visiting_officer_designation === 'Other' && !empty($visit->other_designation)
                     ? $visit->other_designation
                     : $visit->visiting_officer_designation;
               @endphp
               @if($desigDisplay)
               <div class="mb-1">
                  <span class="badge bg-secondary font-monospace">{{ $desigDisplay }}</span>
               </div>
               @endif
               <div class="small text-muted mt-2">
                  <strong>Loggedin Officer:</strong> {{ $visit->visitingOfficer ? $visit->visitingOfficer->name : ('Officer #' . $visit->visiting_officer_id) }}
                  @if($visit->visitingOfficer && $visit->visitingOfficer->email) ({{ $visit->visitingOfficer->email }}) @endif
               </div>
               <div class="small text-muted">
                  Attribution: <span class="badge bg-light text-dark border font-monospace">user_table_id: #{{ $visit->visiting_officer_id }}</span>
               </div>
               <div class="small text-muted mt-2">
                  <strong>Timing:</strong> 
                  {{ $visit->start_time ? date('h:i A', strtotime($visit->start_time)) : 'N/A' }} 
                  to 
                  {{ $visit->end_time ? date('h:i A', strtotime($visit->end_time)) : 'N/A' }}
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- OBSERVATIONS, FINDINGS & RECOMMENDATIONS -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-light py-2">
         <h5 class="mb-0 fs-6 fw-bold text-dark"><i class="fas fa-clipboard-check text-primary me-2"></i> Purpose & Observations</h5>
      </div>
      <div class="card-body">
         <table class="table table-bordered mb-0">
            <tbody>
               <tr>
                  <td class="detail-label">Visit Purpose / Objective</td>
                  <td class="detail-value fw-semibold text-primary">{{ $visit->visit_purpose }}</td>
               </tr>
               <tr>
                  <td class="detail-label">
                     Key Observations, Recommendations & Suggestions
                     @if(!empty($visit->key_observations))
                     <br><span class="badge bg-light text-muted border font-monospace mt-1">{{ strlen($visit->key_observations) }} / 500 characters</span>
                     @endif
                  </td>
                  <td class="detail-value text-wrap">{!! nl2br(e($visit->key_observations)) ?: '<span class="text-muted">None recorded</span>' !!}</td>
               </tr>
               @if(!empty($visit->areas_inspected))
               <tr>
                  <td class="detail-label">Areas / Activities Inspected</td>
                  <td class="detail-value text-wrap">{{ $visit->areas_inspected }}</td>
               </tr>
               @endif
               @if(!empty($visit->findings))
               <tr>
                  <td class="detail-label">Detailed Findings</td>
                  <td class="detail-value text-wrap">{!! nl2br(e($visit->findings)) !!}</td>
               </tr>
               @endif
               @if(!empty($visit->issues_identified))
               <tr>
                  <td class="detail-label">Deficiencies / Issues Identified</td>
                  <td class="detail-value text-wrap text-danger">{!! nl2br(e($visit->issues_identified)) !!}</td>
               </tr>
               @endif
               @if(!empty($visit->recommendations))
               <tr>
                  <td class="detail-label">Recommendations</td>
                  <td class="detail-value text-wrap">{!! nl2br(e($visit->recommendations)) !!}</td>
               </tr>
               @endif
               @if(!empty($visit->corrective_actions))
               <tr>
                  <td class="detail-label">Corrective Actions Required</td>
                  <td class="detail-value text-wrap">{!! nl2br(e($visit->corrective_actions)) !!}</td>
               </tr>
               @endif
               <tr>
                  <td class="detail-label">Follow-up Required</td>
                  <td class="detail-value">
                     @if($visit->is_follow_up_required)
                     <span class="badge bg-danger"><i class="fas fa-check-circle me-1"></i> YES</span>
                     <span class="ms-2">Due Date: <strong>{{ $visit->follow_up_date ? $visit->follow_up_date->format('d-M-Y') : 'Not set' }}</strong></span>
                     @else
                     <span class="badge bg-secondary text-dark">NO</span>
                     @endif
                  </td>
               </tr>
               @if(!empty($visit->overall_remarks))
               <tr>
                  <td class="detail-label">Overall Remarks</td>
                  <td class="detail-value text-wrap">{!! nl2br(e($visit->overall_remarks)) !!}</td>
               </tr>
               @endif
            </tbody>
         </table>
      </div>
   </div>

   <!-- GEOLOCATION & MAP CARD -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
         <h5 class="mb-0 fs-6 fw-bold text-dark"><i class="fas fa-satellite text-info me-2"></i> Verified Site Geolocation</h5>
         @if($visit->map_url)
         <a href="{{ $visit->map_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-xs">
            <i class="fas fa-external-link-alt me-1"></i> View in Google Maps
         </a>
         @endif
      </div>
      <div class="card-body">
         <div class="row g-3">
            <div class="col-md-6">
               <table class="table table-bordered mb-0">
                  <tbody>
                     <tr>
                        <td class="detail-label">Latitude</td>
                        <td class="detail-value font-monospace text-primary fw-bold">{{ $visit->latitude ? $visit->latitude . '° N' : 'Not recorded' }}</td>
                     </tr>
                     <tr>
                        <td class="detail-label">Longitude</td>
                        <td class="detail-value font-monospace text-primary fw-bold">{{ $visit->longitude ? $visit->longitude . '° E' : 'Not recorded' }}</td>
                     </tr>
                     <tr>
                        <td class="detail-label">GPS Accuracy</td>
                        <td class="detail-value">
                           @if($visit->gps_accuracy)
                           <span class="badge bg-light text-dark border">± {{ $visit->gps_accuracy }} metres</span>
                           @else
                           <span class="text-muted">—</span>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Acquired Timestamp</td>
                        <td class="detail-value">{{ $visit->location_captured_at ? $visit->location_captured_at->format('d-M-Y h:i:s A') : '—' }}</td>
                     </tr>
                     <tr>
                        <td class="detail-label">Site Description</td>
                        <td class="detail-value">{{ $visit->location_address ?: '—' }}</td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="col-md-6">
               @if($visit->osm_embed_url)
               <div class="map-box">
                  <iframe src="{{ $visit->osm_embed_url }}" title="Site Map Location" loading="lazy"></iframe>
               </div>
               <div class="text-muted text-end small mt-1">Map preview generated via OpenStreetMap</div>
               @else
               <div class="alert alert-light border text-center py-5 text-muted">
                  <i class="fas fa-map-marker-slash fa-2x mb-2 d-block"></i>
                  No verified GPS coordinates were recorded for this visit.
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>

   <!-- GEO-TAGGED PHOTOGRAPHIC EVIDENCE GALLERY (CRITICAL REQUIREMENT) -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
         <h5 class="mb-0 fs-6 fw-bold text-dark">
            <i class="fas fa-camera-retro text-danger me-2"></i> Geo-Tagged Photographic Evidence ({{ $visit->evidences->count() }})
         </h5>
         @can('field-visit-edit')
         <a href="{{ route('admin.field_visits.edit', $visit->id) }}#sectionPhotos" class="btn btn-outline-danger btn-xs">
            <i class="fas fa-plus me-1"></i> Add More Photos
         </a>
         @endcan
      </div>
      <div class="card-body">
         @if($visit->evidences->count() > 0)
         <div class="row g-3">
            @foreach($visit->evidences as $photo)
            <div class="col-lg-4 col-md-6">
               <div class="card h-100 shadow-sm border">
                  <div class="evidence-img-container">
                     <a href="{{ $photo->url }}" target="_blank" rel="noopener noreferrer" title="Click to view full photograph">
                        <img src="{{ $photo->url }}" alt="{{ $photo->caption ?: 'Field Visit Photo' }}" loading="lazy">
                     </a>
                     <div class="evidence-overlay-bar d-flex justify-content-between align-items-center">
                        <span>
                           <i class="fas fa-map-marker-alt text-danger"></i> 
                           <a href="{{ $photo->map_url }}" target="_blank" rel="noopener noreferrer">
                              {{ $photo->latitude }}°N, {{ $photo->longitude }}°E
                           </a>
                        </span>
                        <span>±{{ $photo->gps_accuracy ?: 'N/A' }}m</span>
                     </div>
                  </div>
                  <div class="card-body p-3">
                     <h6 class="fw-bold mb-1">{{ $photo->caption ?: 'Photograph #' . $loop->iteration }}</h6>
                     <div class="small text-muted mb-2">
                        <div><strong>Capture Time:</strong> {{ $photo->client_capture_time ? $photo->client_capture_time->format('d-M-Y h:i:s A') : '—' }}</div>
                        <div><strong>Capture Source:</strong> <span class="badge {{ $photo->capture_source === 'DEVICE_CAMERA' ? 'bg-success' : 'bg-secondary' }}">{{ $photo->capture_source }}</span></div>
                        <div><strong>Size / Format:</strong> {{ $photo->formatted_size }} ({{ $photo->mime_type }})</div>
                        <div><strong>Attribution:</strong> {{ $photo->creator ? $photo->creator->name : ('Officer #' . $photo->created_by) }}</div>
                     </div>
                     <div class="mt-2 pt-2 border-top">
                        <span class="small text-muted d-block mb-1"><strong>SHA-256 Checksum:</strong></span>
                        <div class="checksum-badge text-truncate" title="{{ $photo->file_checksum }}">{{ $photo->file_checksum }}</div>
                     </div>
                  </div>
               </div>
            </div>
            @endforeach
         </div>
         @else
         <div class="alert alert-light border text-center py-5 text-muted">
            <i class="fas fa-camera fa-2x mb-2 text-secondary d-block"></i>
            No photographs were recorded for this field visit.
         </div>
         @endif
      </div>
   </div>

   <!-- REFERENCE DOCUMENTS -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-light py-2">
         <h5 class="mb-0 fs-6 fw-bold text-dark"><i class="fas fa-paperclip text-purple me-2"></i> Reference Documents & Attachments ({{ $visit->documents->count() }})</h5>
      </div>
      <div class="card-body">
         @if($visit->documents->count() > 0)
         <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
               <thead class="table-light">
                  <tr>
                     <th style="width: 40px;" class="text-center">#</th>
                     <th>Document Title</th>
                     <th>Original Filename</th>
                     <th style="width: 100px;">File Size</th>
                     <th>SHA-256 Checksum</th>
                     <th style="width: 130px;" class="text-center">Action</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($visit->documents as $doc)
                  <tr>
                     <td class="text-center text-muted">{{ $loop->iteration }}</td>
                     <td>
                        <i class="{{ $doc->file_icon }} me-1 fs-5 align-middle"></i>
                        <span class="fw-bold">{{ $doc->document_name }}</span>
                     </td>
                     <td class="small text-muted font-monospace">{{ $doc->original_filename }}</td>
                     <td class="small">{{ $doc->formatted_size }}</td>
                     <td>
                        <span class="checksum-badge" title="{{ $doc->file_checksum }}">{{ Str::limit($doc->file_checksum, 24) }}</span>
                     </td>
                     <td class="text-center">
                        <a href="{{ $doc->download_url }}" class="btn btn-outline-primary btn-xs">
                           <i class="fas fa-download me-1"></i> Download
                        </a>
                     </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         @else
         <div class="alert alert-light border text-center py-4 text-muted mb-0">
            <i class="fas fa-folder-open fa-2x mb-2 text-secondary d-block"></i>
            No reference documents attached to this field visit.
         </div>
         @endif
      </div>
   </div>

   <!-- AUDIT TRAIL FOOTER -->
   <div class="card shadow-sm bg-light border">
      <div class="card-body py-3">
         <div class="row small text-muted">
            <div class="col-md-6">
               <div><strong>Created On:</strong> {{ $visit->created_at ? $visit->created_at->format('d-M-Y h:i:s A') : 'N/A' }}</div>
               <div><strong>Created By:</strong> {{ $visit->creator ? $visit->creator->name . ' (user_table_id: #' . $visit->created_by . ')' : ('user_table_id: #' . $visit->created_by) }}</div>
            </div>
            <div class="col-md-6 text-md-end">
               <div><strong>Last Updated:</strong> {{ $visit->updated_at ? $visit->updated_at->format('d-M-Y h:i:s A') : 'N/A' }}</div>
               <div><strong>Updated By:</strong> {{ $visit->updater ? $visit->updater->name . ' (user_table_id: #' . $visit->updated_by . ')' : ($visit->updated_by ? 'user_table_id: #' . $visit->updated_by : '—') }}</div>
            </div>
         </div>
      </div>
   </div>

</div>
@endsection
