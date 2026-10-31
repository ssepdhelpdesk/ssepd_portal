@section('title') 
Visit Type Master || Create
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
               <li class="breadcrumb-item active">Add Visit Type</li>
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
               <div class="mb-3">
                  <h4 class="card-title mb-1">Create New Visit Type</h4>
                  <p class="text-muted small mb-0">Register a new standardized category of field visit or departmental inspection.</p>
               </div>

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

               <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.field_visit_types.store') }}">
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
                                  value="{{ old('name') }}" 
                                  class="form-control @error('name') is-invalid @enderror" 
                                  placeholder="e.g., Surprise Inspection, Annual Audit, Verification" 
                                  required 
                                  maxlength="150" 
                                  autofocus>
                           <div class="form-hint mt-1">Descriptive title of the visit type as displayed across reports.</div>
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
                                  value="{{ old('code') }}" 
                                  class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                  placeholder="e.g., SURPRISE_INSPECTION" 
                                  required 
                                  maxlength="50"
                                  pattern="^[A-Za-z0-9_]+$">
                           <div class="form-hint mt-1">Unique uppercase code (letters, numbers, underscores only). Auto-generated if left blank.</div>
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
                           <label for="sort_order" class="form-label">Display Sort Order</label>
                           <input type="number" 
                                  name="sort_order" 
                                  id="sort_order" 
                                  value="{{ old('sort_order', $nextSortOrder ?? 1) }}" 
                                  class="form-control @error('sort_order') is-invalid @enderror" 
                                  min="0" 
                                  max="9999">
                           <div class="form-hint mt-1">Lower numbers appear first in dropdown selection lists.</div>
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
                              <input class="form-check-input" type="radio" name="is_active" id="status_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}>
                              <label class="form-check-label text-success fw-bold" for="status_active">
                                 <i class="fas fa-check-circle me-1"></i> Active
                              </label>
                           </div>
                           <div class="form-check form-check-inline mt-2">
                              <input class="form-check-input" type="radio" name="is_active" id="status_inactive" value="0" {{ old('is_active') === '0' ? 'checked' : '' }}>
                              <label class="form-check-label text-danger fw-bold" for="status_inactive">
                                 <i class="fas fa-times-circle me-1"></i> Inactive
                              </label>
                           </div>
                           <div class="form-hint mt-1">Active visit types can be selected immediately by field officers.</div>
                        </div>
                     </div>
                  </div>

                  <!-- Description -->
                  <div class="mb-4">
                     <div class="form-group">
                        <label for="description" class="form-label">Description / Scope</label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Provide guidance on when and how this visit type should be used by departmental officers..." 
                                  maxlength="1000">{{ old('description') }}</textarea>
                        <div class="form-hint mt-1">Optional. Maximum 1000 characters.</div>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="d-flex gap-2 border-top pt-3">
                     <button type="submit" class="btn btn-success text-white fw-bold from-prevent-multiple-submits">
                        <i class="fas fa-save me-1"></i> Save Visit Type
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
      const nameInput = document.getElementById('name');
      const codeInput = document.getElementById('code');
      let codeManuallyEdited = false;

      if (codeInput) {
         codeInput.addEventListener('input', function () {
            codeManuallyEdited = true;
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '_');
         });
      }

      if (nameInput && codeInput) {
         nameInput.addEventListener('input', function () {
            if (!codeManuallyEdited && (!codeInput.value || codeInput.value === nameToCode(this.value.slice(0, -1)))) {
               codeInput.value = nameToCode(this.value);
            }
         });
      }

      function nameToCode(str) {
         return str.trim()
            .toUpperCase()
            .replace(/[\s\-]+/g, '_')
            .replace(/[^A-Z0-9_]/g, '')
            .substring(0, 50);
      }
   });
</script>
@endsection
