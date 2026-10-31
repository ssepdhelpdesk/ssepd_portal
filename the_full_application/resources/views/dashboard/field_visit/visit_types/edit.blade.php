@section('title') 
Visit Type Master || Edit
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
</style>
@endsection 
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
               <li class="breadcrumb-item"><a href="{{ route('admin.field_visit_types.index') }}">Field Visit</a></li>
               <li class="breadcrumb-item active">Edit Visit Type</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <a href="{{ route('admin.field_visit_types.index') }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info">
            <i class="fas fa-arrow-alt-circle-left"></i> Back to List
         </a>
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <!-- Start Page Content -->
   <div class="row">
      <div class="col-lg-10 col-md-12 mx-auto">
         <div class="card shadow-sm">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-center mb-1">
                  <h4 class="card-title mb-0">Edit Visit Type: {{ $visitType->name }}</h4>
                  <span class="badge bg-secondary font-monospace">{{ $visitType->code }}</span>
               </div>
               <p class="text-muted small mb-4">Modify the field visit category definition, sort order, or active status.</p>

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

               <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.field_visit_types.update', $visitType->id) }}">
                  @csrf

                  <div class="row">
                     <!-- Visit Type Name -->
                     <div class="col-md-6 mb-3">
                        <div class="form-group">
                           <label for="name" class="form-label">
                              Visit Type Name <span class="required-star">*</span>
                           </label>
                           <input type="text" 
                                  name="name" 
                                  id="name" 
                                  value="{{ old('name', $visitType->name) }}" 
                                  class="form-control @error('name') is-invalid @enderror" 
                                  placeholder="e.g., Monitoring Visit, Inquiry, Inspection" 
                                  required 
                                  maxlength="150" 
                                  autofocus>
                           <div class="form-hint mt-1">Unique descriptive name for the visit type.</div>
                           @error('name')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>

                     <!-- Code -->
                     <div class="col-md-6 mb-3">
                        <div class="form-group">
                           <label for="code" class="form-label">
                              System Code <span class="required-star">*</span>
                           </label>
                           <input type="text" 
                                  name="code" 
                                  id="code" 
                                  value="{{ old('code', $visitType->code) }}" 
                                  class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                  placeholder="e.g., MONITORING, INQUIRY, INSPECTION" 
                                  required 
                                  maxlength="50"
                                  pattern="^[A-Za-z0-9_]+$">
                           <div class="form-hint mt-1">Unique uppercase identifier (letters, numbers, underscore only).</div>
                           @error('code')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <!-- Sort Order -->
                     <div class="col-md-6 mb-3">
                        <div class="form-group">
                           <label for="sort_order" class="form-label">Sort Order</label>
                           <input type="number" 
                                  name="sort_order" 
                                  id="sort_order" 
                                  value="{{ old('sort_order', $visitType->sort_order) }}" 
                                  class="form-control @error('sort_order') is-invalid @enderror" 
                                  min="0" 
                                  max="9999">
                           <div class="form-hint mt-1">Defines display sequence in forms and dropdowns (lower number appears first).</div>
                           @error('sort_order')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>

                     <!-- Active Status -->
                     <div class="col-md-6 mb-3">
                        <div class="form-group">
                           <label class="form-label d-block">Status</label>
                           <div class="form-check form-check-inline mt-2">
                              <input class="form-check-input" type="radio" name="is_active" id="status_active" value="1" {{ old('is_active', (string)(int)$visitType->is_active) === '1' ? 'checked' : '' }}>
                              <label class="form-check-label text-success fw-bold" for="status_active">
                                 <i class="fas fa-check-circle me-1"></i> Active
                              </label>
                           </div>
                           <div class="form-check form-check-inline mt-2">
                              <input class="form-check-input" type="radio" name="is_active" id="status_inactive" value="0" {{ old('is_active', (string)(int)$visitType->is_active) === '0' ? 'checked' : '' }}>
                              <label class="form-check-label text-danger fw-bold" for="status_inactive">
                                 <i class="fas fa-times-circle me-1"></i> Inactive
                              </label>
                           </div>
                           <div class="form-hint mt-1">Inactive types cannot be selected in new field visit surveys.</div>
                        </div>
                     </div>
                  </div>

                  <!-- Description -->
                  <div class="mb-4">
                     <div class="form-group">
                        <label for="description" class="form-label">Description / Purpose</label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Brief description explaining when this visit type should be utilized by field officers..." 
                                  maxlength="1000">{{ old('description', $visitType->description) }}</textarea>
                        <div class="form-hint mt-1">Optional. Maximum 1000 characters.</div>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <!-- Metadata Audit Info -->
                  <div class="bg-light p-3 rounded mb-4 border">
                     <div class="row small text-muted">
                        <div class="col-md-6">
                           <div><strong>Created:</strong> {{ $visitType->created_at ? $visitType->created_at->format('d-M-Y h:i A') : 'N/A' }}</div>
                           <div><strong>Created By:</strong> {{ $visitType->creator ? $visitType->creator->name . ' (user_table_id: #' . $visitType->created_by . ')' : ($visitType->created_by ? 'user_table_id: #' . $visitType->created_by : 'System') }}</div>
                        </div>
                        <div class="col-md-6">
                           <div><strong>Last Updated:</strong> {{ $visitType->updated_at ? $visitType->updated_at->format('d-M-Y h:i A') : 'N/A' }}</div>
                           <div><strong>Updated By:</strong> {{ $visitType->updater ? $visitType->updater->name . ' (user_table_id: #' . $visitType->updated_by . ')' : ($visitType->updated_by ? 'user_table_id: #' . $visitType->updated_by : 'N/A') }}</div>
                        </div>
                     </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="d-flex gap-2 border-top pt-3">
                     <button type="submit" class="btn btn-warning text-dark fw-bold from-prevent-multiple-submits">
                        <i class="fas fa-check-circle me-1"></i> Update Visit Type
                     </button>
                     <a href="{{ route('admin.field_visit_types.index') }}" class="btn btn-secondary">
                        Cancel
                     </a>
                  </div>

               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection 

@section('script')
<script>
   document.addEventListener('DOMContentLoaded', function () {
      const codeInput = document.getElementById('code');
      if (codeInput) {
         codeInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '_');
         });
      }
   });
</script>
@endsection
