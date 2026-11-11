@section('title') 
Disability Day 2026 || Edit Registration: {{ $registration->registration_no }}
@endsection 
@extends('dashboard.layouts.main')

@section('style')
<style>
    .font-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    .required-asterisk {
        color: #dc3545;
        font-weight: bold;
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
                    <li class="breadcrumb-item"><a href="{{ route('admin.disability_day.index') }}">Disability Day 2026</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.disability_day.show', $registration->id) }}">{{ $registration->registration_no }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
        <div class="col-md-5 align-self-center text-end">
            <a href="{{ route('admin.disability_day.show', $registration->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @include('dashboard.component.message')

    @if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i> Please correct the highlighted errors:</strong>
        <ul class="mb-0 mt-2 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-edit text-warning me-2"></i> Edit Stall Registration Details
                    </h5>
                </div>

                <form action="{{ route('admin.disability_day.update', $registration->id) }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="card-body">

                        <!-- Non-editable System Metadata -->
                        <div class="row g-3 p-3 bg-light rounded mb-4 border">
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Registration Number (Immutable)</label>
                                <input type="text" class="form-control font-monospace fw-bold bg-white" value="{{ $registration->registration_no }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Applied Date & Time (Immutable)</label>
                                <input type="text" class="form-control bg-white" value="{{ optional($registration->applied_at)->format('d-M-Y h:i:s A') ?? 'N/A' }}" readonly disabled>
                            </div>
                        </div>

                        <!-- Section A: Organization Details -->
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-building me-1"></i> Section A — Organization Details
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="organization_name" class="form-label fw-semibold">
                                    Organization Name <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('organization_name') is-invalid @enderror" 
                                       id="organization_name" 
                                       name="organization_name" 
                                       value="{{ old('organization_name', $registration->organization_name) }}" 
                                       maxlength="255" 
                                       required>
                                @error('organization_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person" class="form-label fw-semibold">
                                    Contact Person <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('contact_person') is-invalid @enderror" 
                                       id="contact_person" 
                                       name="contact_person" 
                                       value="{{ old('contact_person', $registration->contact_person) }}" 
                                       maxlength="255" 
                                       required>
                                @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email Address <span class="required-asterisk">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $registration->email) }}" 
                                       maxlength="150" 
                                       required>
                                <div class="form-text small">Updating email affects subsequent OTP tracking and confirmation resends.</div>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Phone (Mobile Number) <span class="required-asterisk">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">+91</span>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $registration->phone) }}" 
                                           maxlength="10" 
                                           pattern="[6-9][0-9]{9}" 
                                           required>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="purpose" class="form-label fw-semibold">
                                    Purpose of Requirement of Stall <span class="required-asterisk">*</span>
                                </label>
                                <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                          id="purpose" 
                                          name="purpose" 
                                          rows="3" 
                                          maxlength="2000" 
                                          required>{{ old('purpose', $registration->purpose) }}</textarea>
                                @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section B: Address Details -->
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i> Section B — Address Details
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="at" class="form-label fw-semibold">
                                    AT (Village / Street) <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('at') is-invalid @enderror" 
                                       id="at" 
                                       name="at" 
                                       value="{{ old('at', $registration->at) }}" 
                                       maxlength="255" 
                                       required>
                                @error('at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="po" class="form-label fw-semibold">
                                    Post Office (PO) <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('po') is-invalid @enderror" 
                                       id="po" 
                                       name="po" 
                                       value="{{ old('po', $registration->po) }}" 
                                       maxlength="255" 
                                       required>
                                @error('po')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ps" class="form-label fw-semibold">
                                    Police Station (PS) <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('ps') is-invalid @enderror" 
                                       id="ps" 
                                       name="ps" 
                                       value="{{ old('ps', $registration->ps) }}" 
                                       maxlength="255" 
                                       required>
                                @error('ps')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state_id" class="form-label fw-semibold">
                                    State <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('state_id') is-invalid @enderror" id="state_id" name="state_id" required>
                                    <option value="">-- Select State --</option>
                                    @foreach($states as $s)
                                    <option value="{{ $s->id }}" {{ old('state_id', $registration->state_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->state_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('state_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="district_id" class="form-label fw-semibold">
                                    District <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('district_id') is-invalid @enderror" id="district_id" name="district_id" required>
                                    <option value="">-- Select District --</option>
                                    @foreach($districts as $d)
                                    <option value="{{ $d->id }}" {{ old('district_id', $registration->district_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->district_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="pin" class="form-label fw-semibold">
                                    PIN Code <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('pin') is-invalid @enderror" 
                                       id="pin" 
                                       name="pin" 
                                       value="{{ old('pin', $registration->pin) }}" 
                                       maxlength="6" 
                                       pattern="[1-9][0-9]{5}" 
                                       required>
                                @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-light py-3 d-flex justify-content-between">
                        <a href="{{ route('admin.disability_day.show', $registration->id) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
