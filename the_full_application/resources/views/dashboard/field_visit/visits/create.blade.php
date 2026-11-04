@section('title') 
Field Visit Management || Create Field Visit
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .required-star {
      color: #dc3545;
      font-weight: bold;
   }
   .form-hint {
      font-size: 0.8rem;
      color: #6c757d;
   }
   .section-card {
      border-left: 4px solid #007bff;
      margin-bottom: 1.5rem;
   }
   .section-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
   }
   .camera-preview-box {
      width: 100%;
      height: 380px;
      background: #000;
      border-radius: 8px;
      overflow: hidden;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .camera-preview-box video {
      width: 100%;
      height: 100%;
      object-fit: cover;
   }
   .camera-hud {
      position: absolute;
      top: 10px;
      left: 10px;
      right: 10px;
      background: rgba(0, 0, 0, 0.65);
      color: #00ff88;
      padding: 6px 12px;
      border-radius: 6px;
      font-family: monospace;
      font-size: 0.82rem;
      z-index: 10;
   }
   .photo-card {
      position: relative;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.06);
   }
   .photo-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
   }
   .photo-card-body {
      padding: 10px;
      font-size: 0.82rem;
   }
   .geo-badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-family: monospace;
      font-size: 0.75rem;
      background: #e9ecef;
      color: #495057;
      margin-top: 4px;
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
               <li class="breadcrumb-item active">Create Field Visit</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <a href="{{ route('admin.field_visits.index') }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info">
            <i class="fas fa-arrow-alt-circle-left"></i> Back to Visits
         </a>
      </div>
   </div>

   <!-- Flash Messages -->
   @include('dashboard.component.message')

   @if (isset($errors) && $errors->any())
   <div class="alert alert-danger alert-rounded">
      <strong><i class="fas fa-exclamation-triangle me-1"></i> Please correct the following errors:</strong>
      <ul class="mb-0 mt-2">
         @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
         @endforeach
      </ul>
   </div>
   @endif

   <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.field_visits.store') }}" enctype="multipart/form-data" id="fieldVisitForm">
      @csrf

      <!-- SECTION 1: VISIT IDENTIFICATION & SCHEDULING -->
      <div class="card shadow-sm section-card">
         <div class="card-body">
            <div class="section-title text-primary">
               <i class="fas fa-calendar-check"></i> 1. Visit Identification & Scheduling
            </div>
            
            {{-- Hidden Start & End Time (Part A) --}}
            <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
            <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time') }}">

            <div class="row">
               <div class="col-md-4 mb-3">
                  <label class="form-label">Visit Reference No</label>
                  <input type="text" class="form-control bg-light font-monospace" value="{{ $previewReferenceNo }} (System Generated)" readonly>
                  <div class="form-hint">Unique sequence assigned upon submission.</div>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="visit_date" class="form-label">Visit Date <span class="required-star">*</span></label>
                  <input type="date" name="visit_date" id="visit_date" class="form-control @error('visit_date') is-invalid @enderror" value="{{ old('visit_date', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                  @error('visit_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
               <div class="col-md-4 mb-3">
                  <label for="field_visit_type_id" class="form-label">Visit Type <span class="required-star">*</span></label>
                  <select name="field_visit_type_id" id="field_visit_type_id" class="form-select @error('field_visit_type_id') is-invalid @enderror" required>
                     <option value="">-- Select Visit Type --</option>
                     @foreach($visitTypes as $vt)
                     <option value="{{ $vt->id }}" {{ old('field_visit_type_id') == $vt->id ? 'selected' : '' }}>
                        {{ $vt->name }} ({{ $vt->code }})
                     </option>
                     @endforeach
                  </select>
                  @error('field_visit_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
            </div>

            <div class="row">
               <div class="col-md-4 mb-3">
                  <label for="status" class="form-label">Visit Status <span class="required-star">*</span></label>
                  <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                     <option value="submitted" {{ old('status', 'submitted') === 'submitted' ? 'selected' : '' }}>Submitted (Completed Visit)</option>
                     <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (In Progress)</option>
                     <option value="reviewed" {{ old('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                     <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                  </select>
                  @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
               <div class="col-md-8 mb-3">
                  <label for="visit_purpose" class="form-label">Visit Purpose / Objective <span class="required-star">*</span></label>
                  <input type="text" name="visit_purpose" id="visit_purpose" class="form-control @error('visit_purpose') is-invalid @enderror" value="{{ old('visit_purpose') }}" placeholder="e.g., Routine infrastructure and attendance inspection of special school" required maxlength="255">
                  @error('visit_purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
            </div>

            <!-- Visiting Officer & Loggedin Officer Details (Part B) -->
            <div class="row">
               <div class="col-md-4 mb-3">
                  <label class="form-label">Loggedin Officer</label>
                  <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }} ({{ Auth::user()->email }})" readonly>
                  <div class="form-hint">System attribution: User #{{ Auth::user()->user_table_id }}</div>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="visiting_officer_name" class="form-label">Visiting Officer Name <span class="required-star">*</span></label>
                  <input type="text" name="visiting_officer_name" id="visiting_officer_name" class="form-control @error('visiting_officer_name') is-invalid @enderror" value="{{ old('visiting_officer_name') }}" placeholder="Enter visiting officer full name" required maxlength="255">
                  @error('visiting_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
               <div class="col-md-4 mb-3">
                  <label for="visiting_officer_designation" class="form-label">Visiting Officer Designation <span class="required-star">*</span></label>
                  <select name="visiting_officer_designation" id="visiting_officer_designation" class="select2 form-control form-select @error('visiting_officer_designation') is-invalid @enderror" required style="width: 100%;">
                     <option value="">-- Select Designation --</option>
                     @foreach($designations as $desig)
                     <option value="{{ $desig }}" {{ old('visiting_officer_designation') === $desig ? 'selected' : '' }}>{{ $desig }}</option>
                     @endforeach
                  </select>
                  @error('visiting_officer_designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
            </div>

            <!-- Dynamic Other Designation Row -->
            <div class="row {{ old('visiting_officer_designation') === 'Other' ? '' : 'd-none' }}" id="other_designation_container">
               <div class="col-md-4 offset-md-8 mb-3">
                  <label for="other_designation" class="form-label">Other Designation <span class="required-star">*</span></label>
                  <input type="text" name="other_designation" id="other_designation" class="form-control @error('other_designation') is-invalid @enderror" value="{{ old('other_designation') }}" placeholder="Enter designation title" maxlength="255" {{ old('visiting_officer_designation') === 'Other' ? 'required' : '' }}>
                  @error('other_designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
            </div>
         </div>
      </div>

      <!-- SECTION 2: OFFICE / INSTITUTION / LOCATION SELECTION -->
      <div class="card shadow-sm section-card" style="border-left-color: #28a745;">
         <div class="card-body">
            <div class="section-title text-success">
               <i class="fas fa-building"></i> 2. Office / Institution & Location
            </div>

            <div class="row">
               <!-- Field 1: Office/Institution * (Single Category Dropdown) -->
               <div class="col-md-4 mb-3">
                  <label for="category_id" class="form-label">Office/Institution <span class="required-star">*</span></label>
                  <select name="category_id" id="category_id" class="select2 form-control form-select @error('category_id') is-invalid @enderror" required style="width: 100%;">
                     <option value="">-- Select Office/Institution --</option>
                     @foreach($categories as $cat)
                     <option value="{{ $cat->category_id }}" 
                             data-category-name="{{ $cat->category_name }}" 
                             {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                     </option>
                     @endforeach
                  </select>
                  @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>

               <!-- Field 2: Office / Institution Name * (Conditional Dropdown or Manual Text Input) -->
               <div class="col-md-4 mb-3" id="institution_name_container">
                  <label for="institution_name_label" class="form-label">Office / Institution Name <span class="required-star">*</span></label>
                  
                  <!-- Dropdown mode (Cases A & D) -->
                  <div id="institution_select_wrapper">
                     <select name="institution_id" id="institution_id" class="select2 form-control form-select @error('institution_id') is-invalid @enderror" style="width: 100%;">
                        <option value="">-- Select Office/Institution First --</option>
                     </select>
                     @error('institution_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <!-- Text input mode (Cases B & C) -->
                  <div id="institution_text_wrapper" class="d-none">
                     <input type="text" 
                            name="institution_name_manual" 
                            id="institution_name_manual" 
                            class="form-control @error('institution_name') is-invalid @enderror" 
                            value="{{ old('institution_name') }}" 
                            placeholder="Enter Office / Institution Name" 
                            maxlength="255">
                     @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <input type="hidden" name="institution_name" id="institution_name" value="{{ old('institution_name') }}">
               </div>

               <!-- Field 3: Contact Person / Head -->
               <div class="col-md-4 mb-3">
                  <label for="contact_person" class="form-label">Contact Person / Head</label>
                  <input type="text" name="contact_person" id="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" placeholder="Contact Person / Head">
                  @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>
            </div>

            <div class="row">
               <!-- Field 4: Contact Number * (Strict 10 digits only) -->
               <div class="col-md-4 mb-3">
                  <label for="contact_number" class="form-label">Contact Number <span class="required-star">*</span></label>
                  <input type="text" name="contact_number" id="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" placeholder="10-digit mobile number" maxlength="10" inputmode="numeric" required pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                  <div class="form-hint">Exactly 10 numeric digits required.</div>
                  <div id="contact_number_client_error" class="text-danger small mt-1 d-none">Please enter exactly 10 numeric digits.</div>
                  @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
               </div>

               <!-- Field 5: Address Type * (Rural / Urban) -->
               @php
                  $isRuralRole = in_array($userRole ?? 0, [4, 6]);
                  $isUrbanRole = ($userRole ?? 0) === 5;
                  $defaultRural = old('address_type') == '1' || ($isRuralRole && !old('address_type'));
                  $defaultUrban = old('address_type') == '2' || ($isUrbanRole && !old('address_type'));
               @endphp
               <div class="col-md-8 mb-3">
                  <label class="form-label fw-semibold">Address Type <span class="required-star">*</span></label>
                  <div class="d-flex align-items-center pt-1 gap-4">
                     <div class="form-check">
                        <input class="form-check-input" type="radio" name="address_type" id="address_type_rural" value="1" 
                               {{ $defaultRural ? 'checked' : '' }} 
                               {{ $isUrbanRole ? 'disabled' : '' }}>
                        <label class="form-check-label fw-semibold" for="address_type_rural">Rural (Block)</label>
                     </div>
                     <div class="form-check">
                        <input class="form-check-input" type="radio" name="address_type" id="address_type_urban" value="2" 
                               {{ $defaultUrban ? 'checked' : '' }} 
                               {{ $isRuralRole ? 'disabled' : '' }}>
                        <label class="form-check-label fw-semibold" for="address_type_urban">Urban (ULB / Municipality)</label>
                     </div>
                  </div>
                  @error('address_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
               </div>
            </div>

            <!-- Hidden Fields: System Code & Village ID -->
            <input type="hidden" name="organization_code" id="organization_code" value="{{ old('organization_code') }}">
            <input type="hidden" name="village_id" id="hidden_village_id" value="{{ old('village_id') }}">

            <!-- Dynamic content container injected through AJAX get-address-type-content/{type} -->
            <div class="row" id="dynamic-address-content"></div>

            <!-- Field 6: Postal / Location Address -->
            <div class="mb-2 mt-2" id="postal_address_container">
               <label for="institution_address" class="form-label">Postal / Location Address</label>
               <textarea name="institution_address" id="institution_address" rows="2" class="form-control @error('institution_address') is-invalid @enderror" placeholder="Village, GP, Ward, Landmark, PIN code">{{ old('institution_address') }}</textarea>
               @error('institution_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
         </div>
      </div>

      <!-- SECTION 3: SITE GEOLOCATION (OVERALL) -->
      <div class="card shadow-sm section-card" style="border-left-color: #17a2b8;">
         <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
               <div class="section-title text-info mb-0">
                  <i class="fas fa-map-marker-alt"></i> 3. Site Geolocation & Coordinates
               </div>
               <button type="button" class="btn btn-outline-info btn-sm" id="btnAcquireGps">
                  <i class="fas fa-crosshairs me-1"></i> <span id="gpsBtnText">Acquire Device GPS</span>
               </button>
            </div>
            <p class="text-muted small mb-3">Capture verified GPS coordinates from device hardware at the inspection site.</p>

            <div class="alert alert-light border d-flex align-items-center mb-3 py-2" id="gpsStatusBanner">
               <div class="spinner-border spinner-border-sm text-info me-2 d-none" id="gpsSpinner" role="status"></div>
               <i class="fas fa-info-circle text-info me-2" id="gpsIcon"></i>
               <span id="gpsStatusText" class="small">GPS status: Waiting for acquisition request.</span>
            </div>

            <div class="row">
               <div class="col-md-4 mb-3">
                  <label for="latitude" class="form-label">Latitude (°N)</label>
                  <input type="text" name="latitude" id="latitude" class="form-control font-monospace bg-light" value="{{ old('latitude') }}" placeholder="e.g., 20.2960587" readonly>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="longitude" class="form-label">Longitude (°E)</label>
                  <input type="text" name="longitude" id="longitude" class="form-control font-monospace bg-light" value="{{ old('longitude') }}" placeholder="e.g., 85.8245398" readonly>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="gps_accuracy" class="form-label">GPS Accuracy (Metres)</label>
                  <input type="text" name="gps_accuracy" id="gps_accuracy" class="form-control font-monospace bg-light" value="{{ old('gps_accuracy') }}" placeholder="Accuracy in ± metres" readonly>
               </div>
            </div>
            <div class="mb-1">
               <label for="location_address" class="form-label">Location Landmark / Verified Place Description</label>
               <input type="text" name="location_address" id="location_address" class="form-control" value="{{ old('location_address') }}" placeholder="e.g., Outside Main Office Gate, Block Campus">
            </div>
         </div>
      </div>

      <!-- SECTION 4: GEO-TAGGED CAMERA EVIDENCE PHOTOGRAPHS (CRITICAL REQUIREMENT) -->
      <div class="card shadow-sm section-card" style="border-left-color: #e83e8c;">
         <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
               <div>
                  <div class="section-title text-danger mb-0">
                     <i class="fas fa-camera"></i> 4. Geo-Tagged Camera Evidence Photographs
                  </div>
                  <p class="text-muted small mb-0">Capture live photographs using device camera with embedded GPS coordinates.</p>
               </div>
               <div class="d-flex gap-2">
                  <button type="button" class="btn btn-danger btn-sm" id="btnLaunchCamera">
                     <i class="fas fa-camera-retro me-1"></i> Open Live Camera
                  </button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="btnToggleUploadFallback">
                     <i class="fas fa-upload me-1"></i> Upload File
                  </button>
               </div>
            </div>

            <!-- Upload fallback block (hidden by default) -->
            <div class="p-3 bg-light rounded border mb-3 d-none" id="uploadFallbackBlock">
               <h6 class="small fw-bold text-dark"><i class="fas fa-file-image me-1"></i> File Upload Evidence (Fallback)</h6>
               <p class="small text-muted mb-2">If device camera is not accessible, select an image file (max 1 MB). Active GPS coordinates will be associated.</p>
               <div class="row align-items-end g-2">
                  <div class="col-md-5">
                     <input type="file" id="fallbackImageInput" class="form-control form-control-sm" accept="image/*">
                  </div>
                  <div class="col-md-5">
                     <input type="text" id="fallbackCaptionInput" class="form-control form-control-sm" placeholder="Photo caption or description">
                  </div>
                  <div class="col-md-2">
                     <button type="button" class="btn btn-secondary btn-sm w-100" id="btnAddFallbackPhoto">
                        <i class="fas fa-plus me-1"></i> Add Photo
                     </button>
                  </div>
               </div>
            </div>

            <!-- Staged Evidences Grid -->
            <div class="row g-3" id="stagedPhotosContainer">
               <!-- Dynamically populated photo cards -->
            </div>
            
            <div class="text-center py-4 text-muted border border-dashed rounded mt-3" id="emptyPhotosMsg">
               <i class="fas fa-camera fa-2x mb-2 text-secondary d-block"></i>
               <span class="small">No photographs captured yet. Click <strong>"Open Live Camera"</strong> to capture site evidence.</span>
            </div>
         </div>
      </div>

      <!-- SECTION 5: OBSERVATIONS, FINDINGS & RECOMMENDATIONS -->
      <div class="card shadow-sm section-card" style="border-left-color: #fd7e14;">
         <div class="card-body">
            <div class="section-title text-warning">
               <i class="fas fa-clipboard-list"></i> 5. Key Observations, Recommendations & Suggestions
            </div>

            {{-- Hidden Inspection Fields Preserved in DOM (Part C) --}}
            <div class="d-none">
               <textarea name="areas_inspected" id="areas_inspected">{{ old('areas_inspected') }}</textarea>
               <textarea name="findings" id="findings">{{ old('findings') }}</textarea>
               <textarea name="issues_identified" id="issues_identified">{{ old('issues_identified') }}</textarea>
               <textarea name="recommendations" id="recommendations">{{ old('recommendations') }}</textarea>
               <textarea name="corrective_actions" id="corrective_actions">{{ old('corrective_actions') }}</textarea>
               <textarea name="overall_remarks" id="overall_remarks">{{ old('overall_remarks') }}</textarea>
            </div>

            <div class="mb-3">
               <div class="d-flex justify-content-between align-items-center mb-1">
                  <label for="key_observations" class="form-label mb-0 fw-semibold">
                     Key Observations, Recommendations & Suggestions <span class="text-muted small">(Max 500 characters)</span>
                  </label>
                  <span class="badge bg-light text-muted border font-monospace" id="key_observations_counter">
                     0 / 500 characters
                  </span>
               </div>
               <textarea name="key_observations" id="key_observations" rows="5" class="form-control @error('key_observations') is-invalid @enderror" placeholder="Enter noteworthy factual observations, recommendations and constructive suggestions (max 500 characters)..." maxlength="500">{{ old('key_observations') }}</textarea>
               @error('key_observations') <div class="invalid-feedback">{{ $message }}</div> @enderror
               <div class="form-hint">Strict maximum 500 characters allowed.</div>
            </div>

            <div class="row align-items-center bg-light p-3 rounded mb-2 border">
               <div class="col-md-6">
                  <label class="form-label fw-bold d-block mb-1">Is Follow-Up Inspection Required?</label>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="is_follow_up_required" id="follow_up_no" value="0" {{ old('is_follow_up_required', '0') === '0' ? 'checked' : '' }}>
                     <label class="form-check-label" for="follow_up_no">No Follow-up Required</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="is_follow_up_required" id="follow_up_yes" value="1" {{ old('is_follow_up_required') === '1' ? 'checked' : '' }}>
                     <label class="form-check-label text-danger fw-bold" for="follow_up_yes">Yes, Follow-up Required</label>
                  </div>
               </div>
               <div class="col-md-6 {{ old('is_follow_up_required') === '1' ? '' : 'd-none' }}" id="followUpDateGroup">
                  <label for="follow_up_date" class="form-label fw-bold">Follow-Up Due Date <span class="required-star">*</span></label>
                  <input type="date" name="follow_up_date" id="follow_up_date" class="form-control" value="{{ old('follow_up_date') }}" min="{{ date('Y-m-d') }}">
                  <div class="form-hint">Target date for verifying compliance.</div>
               </div>
            </div>
         </div>
      </div>

      <!-- SECTION 6: REFERENCE DOCUMENTS -->
      <div class="card shadow-sm section-card" style="border-left-color: #6f42c1;">
         <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
               <div class="section-title text-purple mb-0">
                  <i class="fas fa-paperclip"></i> 6. Reference Documents & Attachments
               </div>
               <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddDocumentRow">
                  <i class="fas fa-plus me-1"></i> Add Another Document
               </button>
            </div>
            <p class="text-muted small mb-3">Attach official inspection letters, notices, or supporting PDFs (max 1 MB each).</p>

            <div id="documentsContainer">
               <div class="row g-2 align-items-center mb-2 document-row">
                  <div class="col-md-5">
                     <input type="text" name="document_names[]" class="form-control form-control-sm" placeholder="Document Description (e.g. Attendance Sheet)">
                  </div>
                  <div class="col-md-6">
                     <input type="file" name="documents[]" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                  </div>
                  <div class="col-md-1 text-center">
                     <button type="button" class="btn btn-outline-danger btn-sm btn-remove-doc" title="Remove attachment row">
                        <i class="fas fa-times"></i>
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- ACTION BUTTONS -->
      <div class="card shadow-sm border-0 mb-4">
         <div class="card-body">
            <div id="payloadSizeWarning" class="alert alert-danger d-none mb-3 py-2 px-3 small">
               <i class="fas fa-exclamation-triangle me-1"></i>
               <strong>Upload payload too large:</strong> <span id="payloadWarningText"></span>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
               <div class="d-flex align-items-center gap-3">
                  <a href="{{ route('admin.field_visits.index') }}" class="btn btn-secondary">
                     <i class="fas fa-times me-1"></i> Cancel
                  </a>
                  <div class="small text-muted" id="payloadIndicatorBox">
                     <i class="fas fa-cloud-upload-alt me-1 text-primary"></i>
                     <span>Estimated Payload: </span>
                     <strong id="payloadSizeText">0.0 MB</strong>
                     <span class="text-muted">/ 50 MB safe limit</span>
                  </div>
               </div>
               <button type="submit" id="btnSubmitForm" class="btn btn-success text-white fw-bold px-4 py-2 from-prevent-multiple-submits">
                  <i class="fas fa-check-circle me-1"></i> Save & Submit Field Visit
               </button>
            </div>
         </div>
      </div>

   </form>
</div>

<!-- ============================================================== -->
<!-- CAMERA CAPTURE MODAL -->
<!-- ============================================================== -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-dark text-white py-2">
            <h5 class="modal-title fs-6" id="cameraModalLabel">
               <i class="fas fa-camera me-1"></i> Capture Geo-Tagged Photo Evidence
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btnCloseCameraModal"></button>
         </div>
         <div class="modal-body p-3">
            <!-- Camera Viewfinder -->
            <div class="camera-preview-box mb-2" id="cameraBox">
               <video id="cameraVideo" autoplay playsinline muted></video>
               <canvas id="cameraCanvas" class="d-none"></canvas>
               <img id="capturedPhotoPreview" class="d-none w-100 h-100" style="object-fit: cover;" alt="Captured preview">
               
               <!-- HUD displaying Live GPS and Time -->
               <div class="camera-hud" id="cameraHud">
                  <div><i class="fas fa-map-marker-alt text-danger"></i> <span id="hudCoords">GPS: Acquiring...</span></div>
                  <div><i class="fas fa-clock text-info"></i> <span id="hudTime">--:--:--</span></div>
               </div>
            </div>

            <!-- Error Banner -->
            <div class="alert alert-danger d-none py-2 mb-2" id="cameraErrorBanner">
               <i class="fas fa-exclamation-triangle me-1"></i> <span id="cameraErrorText"></span>
            </div>

            <!-- Photo caption input -->
            <div class="mb-2">
               <label for="cameraCaption" class="form-label small fw-bold mb-1">Photograph Description / Caption</label>
               <input type="text" id="cameraCaption" class="form-control form-control-sm" placeholder="e.g., Front View of Facility, Dining Hall Inspection">
            </div>

            <div class="small text-muted mb-1">
               <i class="fas fa-shield-alt text-success me-1"></i> Verified GPS coordinates and capture timestamps will be securely embedded with this image.
            </div>
         </div>
         <div class="modal-footer bg-light py-2 d-flex justify-content-between">
            <div>
               <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSwitchCamera" title="Switch between front and back camera">
                  <i class="fas fa-sync-alt me-1"></i> Switch Camera
               </button>
            </div>
            <div class="d-flex gap-2">
               <button type="button" class="btn btn-warning btn-sm d-none" id="btnRetakePhoto">
                  <i class="fas fa-redo me-1"></i> Retake
               </button>
               <button type="button" class="btn btn-danger btn-sm" id="btnSnapPhoto">
                  <i class="fas fa-circle text-white me-1"></i> Capture Photo
               </button>
               <button type="button" class="btn btn-success btn-sm d-none" id="btnConfirmPhoto">
                  <i class="fas fa-check me-1"></i> Use This Photo
               </button>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
   // ==========================================
   // 1. Follow-up toggle handler
   // ==========================================
   const followUpYes = document.getElementById('follow_up_yes');
   const followUpNo = document.getElementById('follow_up_no');
   const followUpGroup = document.getElementById('followUpDateGroup');
   const followUpDateInput = document.getElementById('follow_up_date');

   function updateFollowUpVisibility() {
      if (followUpYes && followUpYes.checked) {
         followUpGroup.classList.remove('d-none');
         followUpDateInput.setAttribute('required', 'required');
      } else if (followUpGroup) {
         followUpGroup.classList.add('d-none');
         followUpDateInput.removeAttribute('required');
      }
   }
   if (followUpYes && followUpNo) {
      followUpYes.addEventListener('change', updateFollowUpVisibility);
      followUpNo.addEventListener('change', updateFollowUpVisibility);
   }

   // ==========================================
   // 2. Dynamic Institution Category, Institute & Location
   // ==========================================
   const categorySelect = document.getElementById('category_id');
   const institutionSelect = document.getElementById('institution_id');
   const institutionNameManual = document.getElementById('institution_name_manual');
   const institutionNameHidden = document.getElementById('institution_name');
   const organizationCodeInput = document.getElementById('organization_code');
   const contactPersonInput = document.getElementById('contact_person');
   const contactNumberInput = document.getElementById('contact_number');
   const institutionAddressInput = document.getElementById('institution_address');
   const dynamicAddressContent = document.getElementById('dynamic-address-content');
   const addressTypeRadios = document.querySelectorAll('input[name="address_type"]');
   const hiddenVillageId = document.getElementById('hidden_village_id');
   const fieldVisitForm = document.getElementById('fieldVisitForm');
   const contactNumberClientError = document.getElementById('contact_number_client_error');

   let fetchedInstitutesMap = {};
   let isRestoringHierarchy = false;

   // Initialize Select2 on Section 1 & Section 2 primary controls
   if (typeof $.fn.select2 !== 'undefined') {
      $('#category_id').select2({ width: '100%' });
      $('#institution_id').select2({ width: '100%' });
      $('#visiting_officer_designation').select2({ width: '100%' });
   }

   // Visiting officer designation "Other" handler (Part B)
   $('#visiting_officer_designation').on('change', function () {
      const val = $(this).val();
      const otherContainer = document.getElementById('other_designation_container');
      const otherInput = document.getElementById('other_designation');
      if (val === 'Other') {
         if (otherContainer) otherContainer.classList.remove('d-none');
         if (otherInput) otherInput.setAttribute('required', 'required');
      } else {
         if (otherContainer) otherContainer.classList.add('d-none');
         if (otherInput) {
            otherInput.removeAttribute('required');
            otherInput.value = '';
         }
      }
   });

   // Key Observations live character counter (Part C)
   const keyObs = document.getElementById('key_observations');
   const keyObsCounter = document.getElementById('key_observations_counter');
   if (keyObs && keyObsCounter) {
      function updateObsCounter() {
         const len = keyObs.value.length;
         keyObsCounter.textContent = `${len} / 500 characters`;
         if (len >= 500) {
            keyObsCounter.classList.add('bg-danger', 'text-white');
            keyObsCounter.classList.remove('bg-light', 'text-muted');
         } else {
            keyObsCounter.classList.remove('bg-danger', 'text-white');
            keyObsCounter.classList.add('bg-light', 'text-muted');
         }
      }
      keyObs.addEventListener('input', updateObsCounter);
      keyObs.addEventListener('keyup', updateObsCounter);
      updateObsCounter();
   }

   // Enforce strict 10 digits on contact number input
   if (contactNumberInput) {
      contactNumberInput.addEventListener('input', function () {
         this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
         if (this.value.length === 10) {
            this.classList.remove('is-invalid');
            if (contactNumberClientError) contactNumberClientError.classList.add('d-none');
         }
      });
   }

   // Resolve category classification
   function getCategoryInfo(catId) {
      if (!catId) return { id: '', name: '', isManual: false };
      const opt = $('#category_id option:selected');
      const catName = (opt.data('category-name') || opt.text() || '').trim();
      const idNum = parseInt(catId, 10);
      const isManual = (idNum === 2 || idNum === 3 || catName === 'SSEPD Field Office' || catName === 'Other');
      return { id: catId, name: catName, isManual: isManual };
   }

   // Switch between Dropdown and Manual Text Input
   function setInstitutionNameMode(catInfo, selectedInstId = null, manualVal = null) {
      const selectWrapper = document.getElementById('institution_select_wrapper');
      const textWrapper = document.getElementById('institution_text_wrapper');

      if (catInfo.isManual) {
         // Manual Text Input mode (Cases B & C: SSEPD Field Office / Other)
         selectWrapper.classList.add('d-none');
         textWrapper.classList.remove('d-none');

         $('#institution_id').prop('disabled', true).val('').trigger('change.select2');
         if (organizationCodeInput) organizationCodeInput.value = '';

         if (institutionNameManual) {
            institutionNameManual.disabled = false;
            institutionNameManual.setAttribute('name', 'institution_name');
            institutionNameManual.value = manualVal !== null ? manualVal : (institutionNameHidden ? institutionNameHidden.value : '');
         }
         if (institutionNameHidden) {
            institutionNameHidden.disabled = true;
         }
      } else {
         // Dropdown mode (Cases A & D: SSEPD State Office / All other categories)
         textWrapper.classList.add('d-none');
         selectWrapper.classList.remove('d-none');

         if (institutionNameManual) {
            institutionNameManual.disabled = true;
            institutionNameManual.removeAttribute('name');
            institutionNameManual.value = '';
         }
         if (institutionNameHidden) {
            institutionNameHidden.disabled = false;
         }

         $('#institution_id').prop('disabled', false);
         if (catInfo.id) {
            loadInstitutionsForCategory(catInfo.id, selectedInstId);
         } else {
            $('#institution_id').html('<option value="">-- Select Office/Institution First --</option>').trigger('change.select2');
         }
      }
   }

   // Complete address field scope: All address fields are always visible and active
   function applyAddressScope() {
      const postalContainer = document.getElementById('postal_address_container');
      if (postalContainer) postalContainer.classList.remove('d-none');
      if (institutionAddressInput) institutionAddressInput.disabled = false;

      $('#dynamic-address-content [class*="col-"]').removeClass('d-none');
      $('#dynamic-address-content select, #dynamic-address-content input').prop('disabled', false);
   }

   function initializeDropdownListeners() {
      if (typeof $.fn.select2 !== 'undefined') {
         $("#dynamic-address-content select").each(function () {
            if (!$(this).data('select2')) {
               $(this).select2({ width: '100%' });
            }
         });
      }

      $('#state-dropdown').off('change.fvLocation').on('change.fvLocation', function () {
         if (isRestoringHierarchy) return;
         const idState = this.value;
         $("#district-dropdown").html('<option value="">-- Select District --</option>').val('').trigger('change.select2');
         $("#block-dropdown").html('<option value="">-- Select Block --</option>').val('').trigger('change.select2');
         $("#grampanchayat-dropdown").html('<option value="">-- Select Grampanchayat --</option>').val('').trigger('change.select2');
         $("#village-dropdown").html('<option value="">-- Select Village --</option>').val('').trigger('change.select2');
         $("#municipality-dropdown").html('<option value="">-- Select Municipality --</option>').val('').trigger('change.select2');
         $("#ward-dropdown").html('<option value="">-- Select Ward --</option>').val('').trigger('change.select2');
         if (hiddenVillageId) hiddenVillageId.value = '';
         if (!idState) return;

         $.ajax({
            url: "{{ url('dashboard/locations/fetch-districts') }}",
            type: "POST",
            data: { state_id: idState, source: 'field_visit', skip_user_scope: 1, all_districts: 1, _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (result) {
               $('#district-dropdown').html('<option value="">-- Select District --</option>');
               $.each(result.districts || [], function (k, v) {
                  $('#district-dropdown').append(new Option(v.district_name, v.district_id));
               });
               $('#district-dropdown').val('').trigger('change.select2');
            }
         });
      });

      $('#district-dropdown').off('change.fvLocation').on('change.fvLocation', function () {
         if (isRestoringHierarchy) return;
         const idDistrict = this.value;
         const currentType = document.querySelector('input[name="address_type"]:checked')?.value;

         if (currentType === '1') {
            $("#block-dropdown").html('<option value="">-- Select Block --</option>').val('').trigger('change.select2');
            $("#grampanchayat-dropdown").html('<option value="">-- Select Grampanchayat --</option>').val('').trigger('change.select2');
            $("#village-dropdown").html('<option value="">-- Select Village --</option>').val('').trigger('change.select2');
            if (hiddenVillageId) hiddenVillageId.value = '';
            if (!idDistrict) return;

            $.ajax({
               url: "{{ url('dashboard/locations/fetch-block') }}",
               type: "POST",
               data: { district_id: idDistrict, source: 'field_visit', skip_user_scope: 1, all_blocks: 1, _token: '{{ csrf_token() }}' },
               dataType: 'json',
               success: function (res) {
                  $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                  $.each(res.blocks || [], function (k, v) {
                     $('#block-dropdown').append(new Option(v.block_name, v.block_id));
                  });
                  $('#block-dropdown').val('').trigger('change.select2');
               }
            });
         } else if (currentType === '2') {
            $("#municipality-dropdown").html('<option value="">-- Select Municipality --</option>').val('').trigger('change.select2');
            $("#ward-dropdown").html('<option value="">-- Select Ward --</option>').val('').trigger('change.select2');
            if (!idDistrict) return;

            $.ajax({
               url: "{{ url('dashboard/locations/fetch-municipality') }}",
               type: "POST",
               data: { district_id: idDistrict, source: 'field_visit', skip_user_scope: 1, all_municipalities: 1, _token: '{{ csrf_token() }}' },
               dataType: 'json',
               success: function (res) {
                  $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
                  $.each(res.municipalities || [], function (k, v) {
                     $('#municipality-dropdown').append(new Option(v.municipality_name, v.municipality_id));
                  });
                  $('#municipality-dropdown').val('').trigger('change.select2');
               }
            });
         }
      });

      $('#block-dropdown').off('change.fvLocation').on('change.fvLocation', function () {
         if (isRestoringHierarchy) return;

         const idBlock = this.value;
         $("#grampanchayat-dropdown").html('<option value="">-- Select Grampanchayat --</option>').val('').trigger('change.select2');
         $("#village-dropdown").html('<option value="">-- Select Village --</option>').val('').trigger('change.select2');
         if (hiddenVillageId) hiddenVillageId.value = '';
         if (!idBlock) return;

         $.ajax({
            url: "{{ url('dashboard/locations/fetch-grampanchayat') }}",
            type: "POST",
            data: { block_id: idBlock, _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (res) {
               $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
               $.each(res.grampanchayats || [], function (k, v) {
                  $('#grampanchayat-dropdown').append(new Option(v.gp_name, v.gp_id));
               });
               $('#grampanchayat-dropdown').val('').trigger('change.select2');
            }
         });
      });

      $('#grampanchayat-dropdown').off('change.fvLocation').on('change.fvLocation', function () {
         if (isRestoringHierarchy) return;

         const idGp = this.value;
         $("#village-dropdown").html('<option value="">-- Select Village --</option>').val('').trigger('change.select2');
         if (hiddenVillageId) hiddenVillageId.value = '';
         if (!idGp) return;

         $.ajax({
            url: "{{ url('dashboard/locations/fetch-village') }}",
            type: "POST",
            data: { gp_id: idGp, _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (res) {
               $('#village-dropdown').html('<option value="">-- Select Village --</option>');
               $.each(res.villages || [], function (k, v) {
                  $('#village-dropdown').append(new Option(v.village_name, v.village_id));
               });
               $('#village-dropdown').val('').trigger('change.select2');
            }
         });
      });

      $('#village-dropdown').off('change.fvLocation').on('change.fvLocation', function () {
         if (hiddenVillageId) hiddenVillageId.value = this.value || '';
      });

      $('#municipality-dropdown').off('change.fvLocation').on('change.fvLocation', function () {
         if (isRestoringHierarchy) return;

         const idMuni = this.value;
         $("#ward-dropdown").html('<option value="">-- Select Ward --</option>').val('').trigger('change.select2');
         if (!idMuni) return;

         $.ajax({
            url: "{{ url('dashboard/locations/fetch-ward') }}",
            type: "POST",
            data: { municipality_id: idMuni, _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (res) {
               $('#ward-dropdown').html('<option value="">-- Select Ward --</option>');
               $.each(res.wards || [], function (k, v) {
                  $('#ward-dropdown').append(new Option(v.ward_name, v.ward_code));
               });
               $('#ward-dropdown').val('').trigger('change.select2');
            }
         });
      });
   }

   async function restoreSequentialDropdowns(type, cfg) {
      if (!cfg) return;
      isRestoringHierarchy = true;

      // 1. State
      const stateId = cfg.state_id || 228;
      $('#state-dropdown').val(stateId).trigger('change.select2');

      // 2. District
      if (cfg.district_id) {
         try {
            const distRes = await $.ajax({
               url: "{{ url('dashboard/locations/fetch-districts') }}",
               type: "POST",
               data: { state_id: stateId, source: 'field_visit', skip_user_scope: 1, all_districts: 1, _token: '{{ csrf_token() }}' },
               dataType: 'json'
            });
            $('#district-dropdown').html('<option value="">-- Select District --</option>');
            $.each(distRes.districts || [], function (k, v) {
               $('#district-dropdown').append(new Option(v.district_name, v.district_id));
            });
            $('#district-dropdown').val(cfg.district_id).trigger('change.select2');

            if (type === '1' || type === 1) {
               // Rural: Block -> GP -> Village
               if (cfg.block_id) {
                  const blockRes = await $.ajax({
                     url: "{{ url('dashboard/locations/fetch-block') }}",
                     type: "POST",
                     data: { district_id: cfg.district_id, source: 'field_visit', skip_user_scope: 1, all_blocks: 1, _token: '{{ csrf_token() }}' },
                     dataType: 'json'
                  });
                  $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                  $.each(blockRes.blocks || [], function (k, v) {
                     $('#block-dropdown').append(new Option(v.block_name, v.block_id));
                  });
                  $('#block-dropdown').val(cfg.block_id).trigger('change.select2');

                  if (cfg.gp_id) {
                     const gpRes = await $.ajax({
                        url: "{{ url('dashboard/locations/fetch-grampanchayat') }}",
                        type: "POST",
                        data: { block_id: cfg.block_id, _token: '{{ csrf_token() }}' },
                        dataType: 'json'
                     });
                     $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                     $.each(gpRes.grampanchayats || [], function (k, v) {
                        $('#grampanchayat-dropdown').append(new Option(v.gp_name, v.gp_id));
                     });
                     $('#grampanchayat-dropdown').val(cfg.gp_id).trigger('change.select2');

                     if (cfg.village_id) {
                        const vRes = await $.ajax({
                           url: "{{ url('dashboard/locations/fetch-village') }}",
                           type: "POST",
                           data: { gp_id: cfg.gp_id, _token: '{{ csrf_token() }}' },
                           dataType: 'json'
                        });
                        $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                        $.each(vRes.villages || [], function (k, v) {
                           $('#village-dropdown').append(new Option(v.village_name, v.village_id));
                        });
                        $('#village-dropdown').val(cfg.village_id).trigger('change.select2');
                        if (hiddenVillageId) hiddenVillageId.value = cfg.village_id;
                     }
                  }
               }
            } else if (type === '2' || type === 2) {
               // Urban: Municipality -> Ward
               if (cfg.municipality_id) {
                  const muniRes = await $.ajax({
                     url: "{{ url('dashboard/locations/fetch-municipality') }}",
                     type: "POST",
                     data: { district_id: cfg.district_id, source: 'field_visit', skip_user_scope: 1, all_municipalities: 1, _token: '{{ csrf_token() }}' },
                     dataType: 'json'
                  });
                  $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
                  $.each(muniRes.municipalities || [], function (k, v) {
                     $('#municipality-dropdown').append(new Option(v.municipality_name, v.municipality_id));
                  });
                  $('#municipality-dropdown').val(cfg.municipality_id).trigger('change.select2');

                  if (cfg.ward_id) {
                     const wardRes = await $.ajax({
                        url: "{{ url('dashboard/locations/fetch-ward') }}",
                        type: "POST",
                        data: { municipality_id: cfg.municipality_id, _token: '{{ csrf_token() }}' },
                        dataType: 'json'
                     });
                     $('#ward-dropdown').html('<option value="">-- Select Ward --</option>');
                     $.each(wardRes.wards || [], function (k, v) {
                        $('#ward-dropdown').append(new Option(v.ward_name, v.ward_code));
                     });
                     $('#ward-dropdown').val(cfg.ward_id).trigger('change.select2');
                  }
               }
            }
         } catch (err) {
            console.error('Error restoring sequential dropdowns:', err);
         }
      }

      if (cfg.pin_code || cfg.pin) {
         $('#pin').val(cfg.pin_code || cfg.pin);
      }

      isRestoringHierarchy = false;
   }

   function loadAddressContent(value, cfgToRestore = null) {
      if (!value) return;
      if (value === '2' && hiddenVillageId) {
         hiddenVillageId.value = '';
      }

      dynamicAddressContent.innerHTML = '<div class="col-12 text-center py-4 text-success"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0 fw-semibold">Loading geographical hierarchy...</p></div>';

      fetch(`{{ url('dashboard/get-address-type-content') }}/${value}`)
         .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
         })
         .then(async data => {
            dynamicAddressContent.innerHTML = data.content;

            // Strip out duplicate postal address section
            const postalHeading = dynamicAddressContent.querySelector('#postal_address_heading');
            if (postalHeading) {
               let nextNode = postalHeading.nextElementSibling;
               while (nextNode) {
                  let toRemove = nextNode;
                  nextNode = nextNode.nextElementSibling;
                  toRemove.remove();
               }
               postalHeading.remove();
            }

            initializeDropdownListeners();

            applyAddressScope();

            if (cfgToRestore) {
               await restoreSequentialDropdowns(value, cfgToRestore);
            } else {
               const stateVal = $('#state-dropdown').val();
               if (stateVal) {
                  $('#state-dropdown').trigger('change.fvLocation');
               }
            }
         })
         .catch(err => {
            console.error('Error fetching address content:', err);
            dynamicAddressContent.innerHTML = '<div class="col-12"><div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Failed to load location hierarchy. Please try again.</div></div>';
         });
   }

   // Address Type radio changes
   addressTypeRadios.forEach(radio => {
      radio.addEventListener('change', function () {
         loadAddressContent(this.value, null);
      });
   });

   // Dropdown mode: Institute selection logic
   $('#institution_id').on('change', function () {
      const selectedId = this.value;
      const item = fetchedInstitutesMap[selectedId];
      if (item) {
         if (institutionNameHidden) institutionNameHidden.value = item.name || '';
         if (organizationCodeInput) organizationCodeInput.value = item.code || '';
         if (contactPersonInput) contactPersonInput.value = item.contact_person || '';
         if (contactNumberInput) {
            contactNumberInput.value = (item.contact || '').replace(/[^0-9]/g, '').slice(0, 10);
            if (contactNumberInput.value.length === 10) {
               contactNumberInput.classList.remove('is-invalid');
               if (contactNumberClientError) contactNumberClientError.classList.add('d-none');
            }
         }
         if (institutionAddressInput && item.address) {
            institutionAddressInput.value = item.address;
         }

         if (item.address_type) {
            const targetRadio = document.querySelector(`input[name="address_type"][value="${item.address_type}"]`);
            if (targetRadio) {
               targetRadio.checked = true;
               loadAddressContent(item.address_type, item);
            }
         }
      } else {
         if (institutionNameHidden) institutionNameHidden.value = '';
         if (organizationCodeInput) organizationCodeInput.value = '';
         if (contactPersonInput) contactPersonInput.value = '';
         if (contactNumberInput) contactNumberInput.value = '';
      }
   });

   function loadInstitutionsForCategory(categoryId, selectedInstituteId = null, cfgToRestore = null) {
      if (!categoryId) {
         $('#institution_id').html('<option value="">-- Select Office/Institution First --</option>').trigger('change.select2');
         fetchedInstitutesMap = {};
         return;
      }

      $('#institution_id').html('<option value="">Loading institutions...</option>').trigger('change.select2');

      const url = '{{ route("admin.field_visits.ajax.institutions") }}?category_id=' + encodeURIComponent(categoryId);
      fetch(url)
         .then(res => res.json())
         .then(data => {
            const list = data.data || [];
            fetchedInstitutesMap = {};
            let optionsHtml = '<option value="">-- Select Office / Institution Name (' + list.length + ' found) --</option>';

            list.forEach(inst => {
               fetchedInstitutesMap[inst.id] = inst;
               const isSel = (selectedInstituteId && selectedInstituteId == inst.id) ? 'selected' : '';
               optionsHtml += `<option value="${inst.id}" ${isSel}>${inst.name}${inst.code ? ' [' + inst.code + ']' : ''}</option>`;
            });

            $('#institution_id').html(optionsHtml).trigger('change.select2');

            if (selectedInstituteId && fetchedInstitutesMap[selectedInstituteId]) {
               const current = fetchedInstitutesMap[selectedInstituteId];
               if (institutionNameHidden && !institutionNameHidden.value) institutionNameHidden.value = current.name || '';
               if (organizationCodeInput && !organizationCodeInput.value) organizationCodeInput.value = current.code || '';
               if (contactPersonInput && !contactPersonInput.value) contactPersonInput.value = current.contact_person || '';
               if (contactNumberInput && !contactNumberInput.value) contactNumberInput.value = (current.contact || '').replace(/[^0-9]/g, '').slice(0, 10);
               if (institutionAddressInput && !institutionAddressInput.value && current.address) institutionAddressInput.value = current.address;

               if (cfgToRestore) {
                  const savedType = cfgToRestore.address_type || current.address_type;
                  if (savedType) {
                     const r = document.querySelector(`input[name="address_type"][value="${savedType}"]`);
                     if (r) r.checked = true;
                     loadAddressContent(savedType, cfgToRestore);
                  }
               }
            }
         })
         .catch(err => {
            console.error('Error loading institutions:', err);
            $('#institution_id').html('<option value="">Failed to load institutions</option>').trigger('change.select2');
         });
   }

   // Category dropdown changes
   $('#category_id').on('change', function () {
      const catId = this.value;
      const catInfo = getCategoryInfo(catId);

      // Reset dependent fields when category changes
      if (institutionNameHidden) institutionNameHidden.value = '';
      if (institutionNameManual) institutionNameManual.value = '';
      if (organizationCodeInput) organizationCodeInput.value = '';
      if (contactPersonInput) contactPersonInput.value = '';
      if (contactNumberInput) contactNumberInput.value = '';
      if (institutionAddressInput) institutionAddressInput.value = '';
      if (hiddenVillageId) hiddenVillageId.value = '';

      // Switch input mode (dropdown vs text)
      setInstitutionNameMode(catInfo);

      // Ensure address fields remain visible
      applyAddressScope();
   });

   // Intercept form submit for strict 10-digit validation
   if (fieldVisitForm) {
      fieldVisitForm.addEventListener('submit', function (e) {
         if (contactNumberInput) {
            const rawPhone = contactNumberInput.value.replace(/[^0-9]/g, '');
            if (rawPhone.length !== 10) {
               e.preventDefault();
               contactNumberInput.classList.add('is-invalid');
               if (contactNumberClientError) contactNumberClientError.classList.add('d-none');
               contactNumberInput.focus();
               return false;
            }
         }

         const vDrop = document.getElementById('village-dropdown');
         if (vDrop && vDrop.value && hiddenVillageId) {
            hiddenVillageId.value = vDrop.value;
         }
      });
   }

   // Restore previous submission on validation error redirect
   const oldCategoryId = '{{ old("category_id") }}';
   const oldInstitutionId = '{{ old("institution_id") }}';
   const oldInstitutionName = {!! json_encode(old('institution_name', '')) !!};
   const oldAddressType = '{{ old("address_type") }}';

   if (oldCategoryId) {
      const catInfo = getCategoryInfo(oldCategoryId);
      const oldCfg = {
         address_type: oldAddressType,
         state_id: '{{ old("state", 228) }}',
         district_id: '{{ old("district", old("district_id")) }}',
         block_id: '{{ old("block", old("block_id")) }}',
         gp_id: '{{ old("grampanchayat", old("gp_id")) }}',
         village_id: '{{ old("village", old("village_id")) }}',
         municipality_id: '{{ old("municipality", old("municipality_id")) }}',
         ward_id: '{{ old("ward", old("ward_id")) }}',
         pin: '{{ old("pin") }}'
      };

      if (catInfo.isManual) {
         setInstitutionNameMode(catInfo, null, oldInstitutionName);
      } else {
         setInstitutionNameMode(catInfo, oldInstitutionId, null);
      }

      if (oldAddressType) {
         loadAddressContent(oldAddressType, oldCfg);
      }
   } else if (oldAddressType) {
      loadAddressContent(oldAddressType);
   } else {
      const defaultChecked = document.querySelector('input[name="address_type"]:checked');
      if (defaultChecked) {
         loadAddressContent(defaultChecked.value);
      }
   }

   // ==========================================
   // 3. Geolocation Engine
   // ==========================================
   let activeLocation = {
      latitude: null,
      longitude: null,
      accuracy: null,
      timestamp: null
   };

   const btnAcquireGps = document.getElementById('btnAcquireGps');
   const gpsStatusBanner = document.getElementById('gpsStatusBanner');
   const gpsStatusText = document.getElementById('gpsStatusText');
   const gpsSpinner = document.getElementById('gpsSpinner');
   const gpsIcon = document.getElementById('gpsIcon');
   const latInput = document.getElementById('latitude');
   const lonInput = document.getElementById('longitude');
   const accInput = document.getElementById('gps_accuracy');
   const hudCoords = document.getElementById('hudCoords');
   const hudTime = document.getElementById('hudTime');

   // Update HUD clock every second
   setInterval(() => {
      if (hudTime) {
         hudTime.textContent = new Date().toLocaleTimeString();
      }
   }, 1000);

   function acquireGps(onSuccessCallback) {
      if (!navigator.geolocation) {
         updateGpsStatus('Geolocation API is not supported by your browser.', 'danger');
         return;
      }

      updateGpsStatus('Acquiring high-accuracy GPS fix from device satellites...', 'info', true);

      navigator.geolocation.getCurrentPosition(
         function (pos) {
            activeLocation.latitude = pos.coords.latitude.toFixed(7);
            activeLocation.longitude = pos.coords.longitude.toFixed(7);
            activeLocation.accuracy = pos.coords.accuracy ? pos.coords.accuracy.toFixed(1) : 'N/A';
            activeLocation.timestamp = new Date(pos.timestamp).toISOString();

            latInput.value = activeLocation.latitude;
            lonInput.value = activeLocation.longitude;
            accInput.value = activeLocation.accuracy;

            if (hudCoords) {
               hudCoords.textContent = activeLocation.latitude + '°N, ' + activeLocation.longitude + '°E (±' + activeLocation.accuracy + 'm)';
            }

            updateGpsStatus('GPS Acquired: ' + activeLocation.latitude + '°N, ' + activeLocation.longitude + '°E (Accuracy: ±' + activeLocation.accuracy + 'm)', 'success');

            if (typeof onSuccessCallback === 'function') {
               onSuccessCallback(activeLocation);
            }
         },
         function (err) {
            let msg = 'GPS acquisition failed: ';
            switch (err.code) {
               case err.PERMISSION_DENIED:
                  msg += 'Permission denied by user or browser.';
                  break;
               case err.POSITION_UNAVAILABLE:
                  msg += 'Position unavailable / satellite fix not found.';
                  break;
               case err.TIMEOUT:
                  msg += 'Request timed out.';
                  break;
               default:
                  msg += err.message;
            }
            updateGpsStatus(msg, 'warning');
         },
         { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
      );
   }

   function updateGpsStatus(text, type, showSpinner = false) {
      if (gpsStatusText) gpsStatusText.textContent = text;
      if (gpsStatusBanner) {
         gpsStatusBanner.className = 'alert d-flex align-items-center mb-3 py-2 alert-' + (type === 'danger' ? 'danger' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'light')));
      }
      if (gpsSpinner) {
         if (showSpinner) gpsSpinner.classList.remove('d-none');
         else gpsSpinner.classList.add('d-none');
      }
   }

   if (btnAcquireGps) {
      btnAcquireGps.addEventListener('click', function () {
         acquireGps();
      });
   }

   // Auto-trigger GPS on load
   acquireGps();

   // ==========================================
   // 4. Live Camera Evidence Capture Engine
   // ==========================================
   const cameraModalEl = document.getElementById('cameraModal');
   let cameraModal = null;
   if (cameraModalEl && typeof bootstrap !== 'undefined') {
      cameraModal = new bootstrap.Modal(cameraModalEl);
   }

   const btnLaunchCamera = document.getElementById('btnLaunchCamera');
   const cameraVideo = document.getElementById('cameraVideo');
   const cameraCanvas = document.getElementById('cameraCanvas');
   const capturedPreview = document.getElementById('capturedPhotoPreview');
   const btnSnapPhoto = document.getElementById('btnSnapPhoto');
   const btnRetakePhoto = document.getElementById('btnRetakePhoto');
   const btnConfirmPhoto = document.getElementById('btnConfirmPhoto');
   const btnSwitchCamera = document.getElementById('btnSwitchCamera');
   const cameraErrorBanner = document.getElementById('cameraErrorBanner');
   const cameraErrorText = document.getElementById('cameraErrorText');
   const cameraCaptionInput = document.getElementById('cameraCaption');
   const stagedPhotosContainer = document.getElementById('stagedPhotosContainer');
   const emptyPhotosMsg = document.getElementById('emptyPhotosMsg');

   let mediaStream = null;
   let currentFacingMode = 'environment'; // environment = rear camera, user = front camera
   let tempCapturedDataUrl = null;
   let photoIndex = 0;

   function startCamera() {
      stopCamera();
      showCameraError(null);

      // Reset viewfinder elements
      cameraVideo.classList.remove('d-none');
      capturedPreview.classList.add('d-none');
      btnSnapPhoto.classList.remove('d-none');
      btnRetakePhoto.classList.add('d-none');
      btnConfirmPhoto.classList.add('d-none');

      const constraints = {
         video: {
            facingMode: { ideal: currentFacingMode },
            width: { ideal: 1280 },
            height: { ideal: 720 }
         },
         audio: false
      };

      navigator.mediaDevices.getUserMedia(constraints)
         .then(stream => {
            mediaStream = stream;
            cameraVideo.srcObject = stream;
            cameraVideo.play();
         })
         .catch(err => {
            // Fallback to basic video without facingMode constraint
            navigator.mediaDevices.getUserMedia({ video: true, audio: false })
               .then(stream => {
                  mediaStream = stream;
                  cameraVideo.srcObject = stream;
                  cameraVideo.play();
               })
               .catch(fallbackErr => {
                  showCameraError('Camera error: ' + fallbackErr.message + '. Please ensure camera permissions are granted in browser settings.');
               });
         });
   }

   function stopCamera() {
      if (mediaStream) {
         mediaStream.getTracks().forEach(track => track.stop());
         mediaStream = null;
      }
   }

   function showCameraError(msg) {
      if (msg) {
         cameraErrorText.textContent = msg;
         cameraErrorBanner.classList.remove('d-none');
      } else {
         cameraErrorBanner.classList.add('d-none');
      }
   }

   if (btnLaunchCamera) {
      btnLaunchCamera.addEventListener('click', function () {
         acquireGps();
         cameraCaptionInput.value = '';
         if (cameraModal) cameraModal.show();
         startCamera();
      });
   }

   if (cameraModalEl) {
      cameraModalEl.addEventListener('hidden.bs.modal', function () {
         stopCamera();
      });
   }

   if (btnSwitchCamera) {
      btnSwitchCamera.addEventListener('click', function () {
         currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
         startCamera();
      });
   }

   if (btnSnapPhoto) {
      btnSnapPhoto.addEventListener('click', function () {
         if (!cameraVideo.videoWidth) {
            return;
         }

         cameraCanvas.width = cameraVideo.videoWidth;
         cameraCanvas.height = cameraVideo.videoHeight;
         const ctx = cameraCanvas.getContext('2d');
         ctx.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);

         // Render overlay onto canvas image for permanent offline watermark
         const captureDate = new Date();
         const dateString = captureDate.toLocaleString();
         const lat = activeLocation.latitude || 'N/A';
         const lon = activeLocation.longitude || 'N/A';
         const acc = activeLocation.accuracy || 'N/A';

         ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
         ctx.fillRect(10, cameraCanvas.height - 70, cameraCanvas.width - 20, 60);
         ctx.fillStyle = '#00ff88';
         ctx.font = 'bold 16px monospace';
         ctx.fillText('ODISHA SSEPD FIELD VISIT | ' + dateString, 20, cameraCanvas.height - 45);
         ctx.fillStyle = '#ffffff';
         ctx.font = '14px monospace';
         ctx.fillText('GPS: ' + lat + '°N, ' + lon + '°E | Acc: ±' + acc + 'm | Src: DEVICE_CAMERA', 20, cameraCanvas.height - 20);

         tempCapturedDataUrl = cameraCanvas.toDataURL('image/jpeg', 0.88);

         // Switch display to captured photo
         cameraVideo.classList.add('d-none');
         capturedPreview.src = tempCapturedDataUrl;
         capturedPreview.classList.remove('d-none');

         btnSnapPhoto.classList.add('d-none');
         btnRetakePhoto.classList.remove('d-none');
         btnConfirmPhoto.classList.remove('d-none');
      });
   }

   if (btnRetakePhoto) {
      btnRetakePhoto.addEventListener('click', function () {
         startCamera();
      });
   }

   const MAX_PHOTOS = 10;
   const MAX_DOCUMENTS = 5;
   const MAX_FILE_SIZE_BYTES = 1048576; // 1 MB (1,048,576 bytes)
   const MAX_DOC_SIZE_BYTES = 1048576;  // 1 MB
   const SAFE_PAYLOAD_LIMIT_BYTES = 30 * 1024 * 1024; // 30MB
   const WARN_PAYLOAD_THRESHOLD_BYTES = 25 * 1024 * 1024; // 25MB

   function showFileSizeErrorAlert(input) {
      if (input) {
         input.value = '';
      }
      if (typeof Swal !== 'undefined') {
         Swal.fire({
            icon: 'warning',
            title: 'File Size Limit Exceeded',
            text: 'File size must not exceed 1 MB. Please select a smaller file.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
         });
      } else {
         alert('File size must not exceed 1 MB. Please select a smaller file.');
      }
   }

   function updatePayloadIndicator() {
      let totalBytes = 0;
      // Staged photos
      const photoInputs = document.querySelectorAll('#stagedPhotosContainer input[name$="[image_data]"]');
      photoInputs.forEach(input => {
         if (input.value) {
            totalBytes += input.value.length; // Base64 payload in POST
         }
      });
      // Selected documents
      const docInputs = document.querySelectorAll('input[name="documents[]"]');
      docInputs.forEach(input => {
         if (input.files && input.files[0]) {
            totalBytes += input.files[0].size;
         }
      });

      const totalMB = (totalBytes / (1024 * 1024)).toFixed(1);
      const payloadSizeText = document.getElementById('payloadSizeText');
      const payloadSizeWarning = document.getElementById('payloadSizeWarning');
      const payloadWarningText = document.getElementById('payloadWarningText');
      const payloadIndicatorBox = document.getElementById('payloadIndicatorBox');

      if (payloadSizeText) {
         payloadSizeText.textContent = totalMB + ' MB';
      }

      if (totalBytes > SAFE_PAYLOAD_LIMIT_BYTES) {
         if (payloadSizeWarning && payloadWarningText) {
            payloadWarningText.textContent = `Total upload is ${totalMB} MB, exceeding the 50 MB limit. Please remove or compress some attachments before submitting.`;
            payloadSizeWarning.classList.remove('d-none');
         }
         if (payloadIndicatorBox) {
            payloadIndicatorBox.classList.remove('text-muted');
            payloadIndicatorBox.classList.add('text-danger', 'fw-bold');
         }
      } else if (totalBytes > WARN_PAYLOAD_THRESHOLD_BYTES) {
         if (payloadSizeWarning && payloadWarningText) {
            payloadWarningText.textContent = `Upload payload is large (${totalMB} MB). Ensure attachments are essential to avoid upload timeouts.`;
            payloadSizeWarning.classList.remove('d-none');
         }
         if (payloadIndicatorBox) {
            payloadIndicatorBox.classList.remove('text-muted');
            payloadIndicatorBox.classList.add('text-warning', 'fw-bold');
         }
      } else {
         if (payloadSizeWarning) {
            payloadSizeWarning.classList.add('d-none');
         }
         if (payloadIndicatorBox) {
            payloadIndicatorBox.classList.remove('text-danger', 'text-warning', 'fw-bold');
            payloadIndicatorBox.classList.add('text-muted');
         }
      }

      return totalBytes;
   }

   // Fallback photo optimizer using canvas
   function processFallbackPhoto(file, caption, callback) {
      const MAX_DIMENSION = 1920;
      const COMPRESSION_THRESHOLD = 2 * 1024 * 1024; // 2MB
      const originalSizeMB = (file.size / (1024 * 1024)).toFixed(2);

      const reader = new FileReader();
      reader.onload = function (e) {
         const img = new Image();
         img.onload = function () {
            let width = img.naturalWidth || img.width;
            let height = img.naturalHeight || img.height;
            const needsResize = (file.size > COMPRESSION_THRESHOLD) || (width > MAX_DIMENSION || height > MAX_DIMENSION);

            if (!needsResize) {
               callback({
                  imageData: e.target.result,
                  caption: caption || file.name,
                  optimizationNote: null
               });
               return;
            }

            // Downscale proportionally to maximum 1920px while preserving high resolution
            if (width > height) {
               if (width > MAX_DIMENSION) {
                  height = Math.round((height * MAX_DIMENSION) / width);
                  width = MAX_DIMENSION;
               }
            } else {
               if (height > MAX_DIMENSION) {
                  width = Math.round((width * MAX_DIMENSION) / height);
                  height = MAX_DIMENSION;
               }
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            const optimizedDataUrl = canvas.toDataURL('image/jpeg', 0.85);
            const approxBytes = Math.round((optimizedDataUrl.length - 23) * 0.75);
            const newSizeMB = (approxBytes / (1024 * 1024)).toFixed(2);
            const note = `Optimized from ${originalSizeMB} MB to ${newSizeMB} MB (${width}×${height} HD)`;

            callback({
               imageData: optimizedDataUrl,
               caption: caption || file.name,
               optimizationNote: note
            });
         };
         img.onerror = function () {
            alert('Unable to load image file. Please choose a valid image (JPEG, PNG, WebP).');
         };
         img.src = e.target.result;
      };
      reader.onerror = function () {
         alert('Failed to read the selected file.');
      };
      reader.readAsDataURL(file);
   }

   if (btnConfirmPhoto) {
      btnConfirmPhoto.addEventListener('click', function () {
         if (!tempCapturedDataUrl) return;

         const currentCount = stagedPhotosContainer.querySelectorAll('.photo-card').length;
         if (currentCount >= MAX_PHOTOS) {
            alert(`Maximum limit of ${MAX_PHOTOS} photographs reached.`);
            stopCamera();
            if (cameraModal) cameraModal.hide();
            return;
         }

         const caption = cameraCaptionInput.value.trim() || 'Site Photo #' + (photoIndex + 1);
         addStagedPhoto({
            imageData: tempCapturedDataUrl,
            caption: caption,
            latitude: activeLocation.latitude || '',
            longitude: activeLocation.longitude || '',
            accuracy: activeLocation.accuracy || '',
            captureTime: new Date().toISOString(),
            source: 'DEVICE_CAMERA',
            optimizationNote: null
         });

         stopCamera();
         if (cameraModal) cameraModal.hide();
      });
   }

   // Fallback file upload toggle
   const btnToggleUploadFallback = document.getElementById('btnToggleUploadFallback');
   const uploadFallbackBlock = document.getElementById('uploadFallbackBlock');
   if (btnToggleUploadFallback && uploadFallbackBlock) {
      btnToggleUploadFallback.addEventListener('click', function () {
         uploadFallbackBlock.classList.toggle('d-none');
      });
   }

   const btnAddFallbackPhoto = document.getElementById('btnAddFallbackPhoto');
   const fallbackImageInput = document.getElementById('fallbackImageInput');
   const fallbackCaptionInput = document.getElementById('fallbackCaptionInput');

   if (fallbackImageInput) {
      fallbackImageInput.addEventListener('change', function () {
         if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.size > MAX_FILE_SIZE_BYTES) {
               showFileSizeErrorAlert(this);
               return false;
            }
         }
      });
   }

   if (btnAddFallbackPhoto) {
      btnAddFallbackPhoto.addEventListener('click', function () {
         if (!fallbackImageInput.files || !fallbackImageInput.files[0]) {
            alert('Please select an image file first.');
            return;
         }

         const file = fallbackImageInput.files[0];
         if (file.size > MAX_FILE_SIZE_BYTES) {
            showFileSizeErrorAlert(fallbackImageInput);
            return;
         }

         const currentCount = stagedPhotosContainer.querySelectorAll('.photo-card').length;
         if (currentCount >= MAX_PHOTOS) {
            alert(`Maximum limit of ${MAX_PHOTOS} photographs reached.`);
            return;
         }
         if (!file.type.match(/^image\//)) {
            alert('Please select a valid image file (JPG, PNG, WebP).');
            return;
         }

         const caption = fallbackCaptionInput.value.trim() || file.name;
         btnAddFallbackPhoto.disabled = true;
         btnAddFallbackPhoto.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

         processFallbackPhoto(file, caption, function (result) {
            addStagedPhoto({
               imageData: result.imageData,
               caption: result.caption,
               latitude: activeLocation.latitude || '',
               longitude: activeLocation.longitude || '',
               accuracy: activeLocation.accuracy || '',
               captureTime: new Date().toISOString(),
               source: 'MANUAL_UPLOAD',
               optimizationNote: result.optimizationNote
            });
            fallbackImageInput.value = '';
            fallbackCaptionInput.value = '';
            uploadFallbackBlock.classList.add('d-none');
            btnAddFallbackPhoto.disabled = false;
            btnAddFallbackPhoto.innerHTML = '<i class="fas fa-plus me-1"></i> Add Photo';
         });
      });
   }

   function addStagedPhoto(photo) {
      photoIndex++;
      if (emptyPhotosMsg) emptyPhotosMsg.classList.add('d-none');

      const col = document.createElement('div');
      col.className = 'col-md-4 col-sm-6';
      col.id = 'photoCard_' + photoIndex;

      col.innerHTML = `
         <div class="photo-card">
            <img src="${photo.imageData}" alt="${photo.caption}">
            <div class="photo-card-body">
               <div class="d-flex justify-content-between align-items-start">
                  <strong>${photo.caption}</strong>
                  <button type="button" class="btn btn-outline-danger btn-xs btn-remove-photo" data-card-id="photoCard_${photoIndex}" title="Remove photo">
                     <i class="fas fa-trash"></i>
                  </button>
               </div>
               <div class="geo-badge">
                  <i class="fas fa-map-marker-alt text-danger me-1"></i> ${photo.latitude ? photo.latitude + '°N, ' + photo.longitude + '°E' : 'No GPS'} (±${photo.accuracy || 'N/A'}m)
               </div>
               <div class="small text-muted mt-1">
                  <span class="badge ${photo.source === 'DEVICE_CAMERA' ? 'bg-success' : 'bg-secondary'}">${photo.source}</span>
                  <span class="ms-1">${new Date().toLocaleTimeString()}</span>
               </div>
               ${photo.optimizationNote ? `<div class="small mt-1"><span class="badge bg-info text-dark text-wrap"><i class="fas fa-compress-arrows-alt me-1"></i> ${photo.optimizationNote}</span></div>` : ''}
               <!-- Hidden form inputs -->
               <input type="hidden" name="photos[${photoIndex}][image_data]" value="${photo.imageData}">
               <input type="hidden" name="photos[${photoIndex}][caption]" value="${photo.caption}">
               <input type="hidden" name="photos[${photoIndex}][latitude]" value="${photo.latitude}">
               <input type="hidden" name="photos[${photoIndex}][longitude]" value="${photo.longitude}">
               <input type="hidden" name="photos[${photoIndex}][gps_accuracy]" value="${photo.accuracy}">
               <input type="hidden" name="photos[${photoIndex}][capture_time]" value="${photo.captureTime}">
               <input type="hidden" name="photos[${photoIndex}][capture_source]" value="${photo.source}">
            </div>
         </div>
      `;

      stagedPhotosContainer.appendChild(col);
      updatePayloadIndicator();

      col.querySelector('.btn-remove-photo').addEventListener('click', function () {
         col.remove();
         if (stagedPhotosContainer.children.length === 0 && emptyPhotosMsg) {
            emptyPhotosMsg.classList.remove('d-none');
         }
         updatePayloadIndicator();
      });
   }

   // ==========================================
   // 5. Reference Documents Dynamic Rows
   // ==========================================
   const btnAddDocumentRow = document.getElementById('btnAddDocumentRow');
   const documentsContainer = document.getElementById('documentsContainer');

   function bindDocumentFileInput(input) {
      if (!input) return;
      input.addEventListener('change', function () {
         if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > MAX_FILE_SIZE_BYTES) {
               showFileSizeErrorAlert(input);
            }
         }
         updatePayloadIndicator();
      });
   }

   if (btnAddDocumentRow && documentsContainer) {
      btnAddDocumentRow.addEventListener('click', function () {
         const currentRows = documentsContainer.querySelectorAll('.document-row').length;
         if (currentRows >= MAX_DOCUMENTS) {
            alert(`Maximum limit of ${MAX_DOCUMENTS} reference documents reached.`);
            return;
         }

         const row = document.createElement('div');
         row.className = 'row g-2 align-items-center mb-2 document-row';
         row.innerHTML = `
            <div class="col-md-5">
               <input type="text" name="document_names[]" class="form-control form-control-sm" placeholder="Document Description (e.g. Audit Notice)">
            </div>
            <div class="col-md-6">
               <input type="file" name="documents[]" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            </div>
            <div class="col-md-1 text-center">
               <button type="button" class="btn btn-outline-danger btn-sm btn-remove-doc" title="Remove attachment row">
                  <i class="fas fa-times"></i>
               </button>
            </div>
         `;
         documentsContainer.appendChild(row);
         attachDocRemoveListener(row.querySelector('.btn-remove-doc'));
         bindDocumentFileInput(row.querySelector('input[type="file"]'));
         updatePayloadIndicator();
      });
   }

   function attachDocRemoveListener(btn) {
      if (btn) {
         btn.addEventListener('click', function () {
            const rows = documentsContainer.querySelectorAll('.document-row');
            if (rows.length > 1) {
               btn.closest('.document-row').remove();
            } else {
               btn.closest('.document-row').querySelector('input[type="file"]').value = '';
               btn.closest('.document-row').querySelector('input[type="text"]').value = '';
            }
            updatePayloadIndicator();
         });
      }
   }

   document.querySelectorAll('.btn-remove-doc').forEach(attachDocRemoveListener);
   document.querySelectorAll('input[name="documents[]"]').forEach(bindDocumentFileInput);

   // Prevent form submission if any file exceeds limit or total payload exceeds safe limit
   const mainForm = document.getElementById('fieldVisitForm') || document.querySelector('form');
   if (mainForm) {
      mainForm.addEventListener('submit', function (e) {
         // Final client-side check: ensure no oversized document input is submitted
         const docInputs = document.querySelectorAll('input[name="documents[]"]');
         for (let i = 0; i < docInputs.length; i++) {
            if (docInputs[i].files && docInputs[i].files[0] && docInputs[i].files[0].size > MAX_FILE_SIZE_BYTES) {
               e.preventDefault();
               showFileSizeErrorAlert(docInputs[i]);
               return false;
            }
         }

         const total = updatePayloadIndicator();
         if (total > SAFE_PAYLOAD_LIMIT_BYTES) {
            e.preventDefault();
            const totalMB = (total / (1024 * 1024)).toFixed(1);
            alert(`Cannot submit: Total upload payload (${totalMB} MB) exceeds the safe limit. Please remove or compress some photographs or documents.`);
            const warningEl = document.getElementById('payloadSizeWarning');
            if (warningEl) {
               warningEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
         }
      });
   }
});
</script>
@endsection
