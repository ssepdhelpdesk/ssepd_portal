@extends('main_website.layouts.app')

@section('title', 'Stall Registration — International Day of Persons With Disabilities 2026')
@section('meta_description', 'Register for an official exhibition/service stall at the International Day of Persons With Disabilities 2026, hosted by SSEPD Department, Government of Odisha at S\'O\'A University.')

@section('style')
<style>
    .event-hero-banner {
        background: linear-gradient(135deg, #0b3954 0%, #087e8b 100%);
        color: #ffffff;
        padding: 45px 0 35px;
        position: relative;
        overflow: hidden;
    }
    .event-hero-banner::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff9933 0%, #ffffff 50%, #138808 100%);
    }
    .event-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .event-info-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.12);
        padding: 8px 18px;
        border-radius: 8px;
        margin-right: 12px;
        margin-top: 10px;
        font-size: 0.92rem;
    }
    .registration-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-top: -30px;
        margin-bottom: 50px;
        position: relative;
        z-index: 10;
    }
    .form-section-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 24px;
        font-weight: 700;
        font-size: 1.05rem;
        color: #0b3954;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-section-body {
        padding: 24px;
    }
    .required-asterisk {
        color: #dc3545;
        font-weight: bold;
    }
    .success-receipt-card {
        background: #f0fdf4;
        border: 2px dashed #22c55e;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        margin-bottom: 35px;
    }
    .reg-code-highlight {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 1.8rem;
        font-weight: 800;
        color: #0b3954;
        background: #ffffff;
        padding: 12px 24px;
        border-radius: 8px;
        display: inline-block;
        border: 2px solid #22c55e;
        margin: 15px 0;
        letter-spacing: 1px;
    }
    .system-info-notice {
        background: #fefce8;
        border-left: 4px solid #eab308;
        padding: 16px 20px;
        border-radius: 0 8px 8px 0;
        font-size: 0.9rem;
        color: #713f12;
    }
    .track-link-box {
        background: #f1f5f9;
        border-radius: 8px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: gap;
        margin-top: 25px;
    }
</style>
@endsection

@section('content')
<!-- Event Header Banner -->
<header class="event-hero-banner" role="banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="event-badge">
                    <i class="fas fa-wheelchair"></i> Government of Odisha &bull; SSEPD Department
                </span>
                <h1 class="h2 fw-bold mb-2">International Day of Persons With Disabilities – 2026</h1>
                <h2 class="h4 text-warning fw-semibold mb-3">Stall Registration</h2>
                <p class="text-white-50 mb-3" style="max-width: 650px;">
                    Invitation for organizations, non-profits, institutions, self-help groups, and assistive technology exhibitors to request an exhibition/service stall for the state-level celebration.
                </p>
                <div class="d-flex flex-wrap">
                    <div class="event-info-pill">
                        <i class="fas fa-map-marker-alt text-warning"></i>
                        <span><strong>Venue:</strong> S'O'A University, Bhubaneswar</span>
                    </div>
                    <div class="event-info-pill">
                        <i class="fas fa-calendar-alt text-warning"></i>
                        <span><strong>Date:</strong> 01 December 2026 – 03 December 2026</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('disability_day.track') }}" class="btn btn-outline-light btn-lg px-4 shadow-sm">
                    <i class="fas fa-search me-1"></i> Track Your Application
                </a>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Success Confirmation Screen (When redirected after successful submission) -->
            @if(session('registration_success'))
            <div class="registration-card p-4 p-md-5 mt-4">
                <div class="success-receipt-card">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 3.5rem;"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-2">Stall Registration Submitted Successfully!</h3>
                    <p class="text-muted mb-3">
                        Your stall application for the <strong>International Day of Persons With Disabilities – 2026</strong> has been officially recorded by the department.
                    </p>

                    <div class="text-muted small text-uppercase fw-semibold">Your Official Registration Number</div>
                    <div class="reg-code-highlight" id="regNumberDisplay">{{ session('registration_no') }}</div>

                    <div class="row justify-content-center text-start my-3">
                        <div class="col-md-8">
                            <ul class="list-group list-group-flush shadow-sm rounded border">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Organization:</span>
                                    <span class="fw-bold">{{ session('organization_name') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Contact Person:</span>
                                    <span class="fw-bold">{{ session('contact_person') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Registered Email:</span>
                                    <span class="fw-bold">{{ session('email') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Submission Date & Time:</span>
                                    <span class="fw-bold">{{ session('applied_at') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Confirmation Email:</span>
                                    <span>
                                        @if(session('mail_sent'))
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Dispatched to {{ session('email') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Delivery pending (Save Reg No)</span>
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="system-info-notice text-start my-4 mx-auto" style="max-width: 680px;">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Next Steps:</strong> Stall allotment will be reviewed and assigned by the authorized departmental committee. You can check your allotment status anytime online by clicking the tracking button below.
                    </div>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print Confirmation
                        </button>
                        <a href="{{ route('disability_day.track') }}" class="btn btn-primary px-4">
                            <i class="fas fa-route me-1"></i> Track Application Status
                        </a>
                        <a href="{{ route('disability_day.registration') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-1"></i> Register Another Stall
                        </a>
                    </div>
                </div>
            </div>
            @else

            <!-- Registration Form Card -->
            <div class="registration-card">

                <!-- Alert Messages -->
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i> Please correct the highlighted errors before submitting:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form action="{{ route('disability_day.registration.store') }}" method="POST" id="stallRegistrationForm" novalidate>
                    @csrf

                    <!-- SECTION A: ORGANIZATION DETAILS -->
                    <div class="form-section-header">
                        <i class="fas fa-building text-primary"></i>
                        <span>Section A — Organization Details</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="organization_name" class="form-label fw-semibold">
                                    1. Organization Name <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('organization_name') is-invalid @enderror" 
                                       id="organization_name" 
                                       name="organization_name" 
                                       value="{{ old('organization_name') }}" 
                                       placeholder="e.g. Odisha Association for the Blind" 
                                       maxlength="255" 
                                       required>
                                @error('organization_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person" class="form-label fw-semibold">
                                    2. Contact Person <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('contact_person') is-invalid @enderror" 
                                       id="contact_person" 
                                       name="contact_person" 
                                       value="{{ old('contact_person') }}" 
                                       placeholder="Full name of authorized representative" 
                                       maxlength="255" 
                                       required>
                                @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    3. Email Address <span class="required-asterisk">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="contact@organization.org" 
                                       maxlength="150" 
                                       required>
                                <div class="form-text small text-muted">A confirmation email and tracking OTPs will be sent to this address.</div>
                                <div id="emailDuplicateFeedback" class="invalid-feedback d-none">This email ID has already been registered. One organization can submit only one registration.</div>
                                @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    4. Phone (Mobile Number) <span class="required-asterisk">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">+91</span>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="10-digit mobile number" 
                                           maxlength="10" 
                                           pattern="[6-9][0-9]{9}" 
                                           required>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">10-digit Indian mobile number starting with 6, 7, 8, or 9.</div>
                            </div>

                            <div class="col-12">
                                <label for="purpose" class="form-label fw-semibold">
                                    5. Purpose of Requirement of Stall <span class="required-asterisk">*</span>
                                </label>
                                <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                          id="purpose" 
                                          name="purpose" 
                                          rows="3" 
                                          placeholder="Describe the activities, products, services, or assistive devices you intend to display/demonstrate..." 
                                          maxlength="2000" 
                                          required>{{ old('purpose') }}</textarea>
                                @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION B: ADDRESS DETAILS -->
                    <div class="form-section-header">
                        <i class="fas fa-map-marked-alt text-primary"></i>
                        <span>Section B — Address Details</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="at" class="form-label fw-semibold">
                                    6. AT (Village / Street / Location) <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('at') is-invalid @enderror" 
                                       id="at" 
                                       name="at" 
                                       value="{{ old('at') }}" 
                                       placeholder="e.g. Plot No 12, Saheed Nagar" 
                                       maxlength="255" 
                                       required>
                                @error('at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="po" class="form-label fw-semibold">
                                    7. PO (Post Office) <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('po') is-invalid @enderror" 
                                       id="po" 
                                       name="po" 
                                       value="{{ old('po') }}" 
                                       placeholder="e.g. Saheed Nagar SO" 
                                       maxlength="255" 
                                       required>
                                @error('po')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ps" class="form-label fw-semibold">
                                    8. PS (Police Station) <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('ps') is-invalid @enderror" 
                                       id="ps" 
                                       name="ps" 
                                       value="{{ old('ps') }}" 
                                       placeholder="e.g. Saheed Nagar PS" 
                                       maxlength="255" 
                                       required>
                                @error('ps')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state_id" class="form-label fw-semibold">
                                    9. State <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('state_id') is-invalid @enderror" 
                                        id="state_id" 
                                        name="state_id" 
                                        required>
                                    <option value="">-- Select State --</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ (old('state_id', 1) == $state->id || strtolower($state->state_name) === 'odisha') ? 'selected' : '' }}>
                                        {{ $state->state_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('state_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="district_id" class="form-label fw-semibold">
                                    10. District Name <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('district_id') is-invalid @enderror" 
                                        id="district_id" 
                                        name="district_id" 
                                        required>
                                    <option value="">-- Select District --</option>
                                    @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->district_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="pin" class="form-label fw-semibold">
                                    11. PIN Code <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('pin') is-invalid @enderror" 
                                       id="pin" 
                                       name="pin" 
                                       value="{{ old('pin') }}" 
                                       placeholder="6-digit PIN" 
                                       maxlength="6" 
                                       pattern="[1-9][0-9]{5}" 
                                       required>
                                @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION C: SYSTEM GENERATED INFORMATION -->
                    <div class="form-section-header">
                        <i class="fas fa-cogs text-primary"></i>
                        <span>Section C — System-Generated Information & Allotment Policy</span>
                    </div>
                    <div class="form-section-body">
                        <div class="system-info-notice">
                            <h6 class="fw-bold mb-2"><i class="fas fa-shield-alt me-1"></i> Registration & Allotment Details</h6>
                            <ul class="mb-0 ps-3">
                                <li><strong>12. Registration Number:</strong> Generated automatically by the secure server upon successful submission in the official format (<code>SSEPD/DISABILITYDAY/DD/MM/YYYY/XXXXXX</code>).</li>
                                <li><strong>13. Applied Date & Time:</strong> Recorded automatically from the official Indian Standard Time (IST) server clock.</li>
                                <li><strong>14. Allotted Stall:</strong> Initially <em>Not Yet Allotted</em>. Stalls are reviewed and assigned by the SSEPD Department committee.</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-3">
                            <div class="text-muted small">
                                <i class="fas fa-lock me-1"></i> All submitted information is encrypted and handled under official SSEPD IT security policies.
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4" id="submitBtn">
                                <i class="fas fa-paper-plane me-1"></i> Submit Stall Registration
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tracking Link Footer Box -->
            <div class="track-link-box mb-5">
                <div>
                    <h6 class="fw-bold mb-1"><i class="fas fa-search-location text-primary me-2"></i> Already Registered for a Stall?</h6>
                    <p class="text-muted small mb-0">Use your Registration Number and Registered Email to check your stall allotment and application status.</p>
                </div>
                <a href="{{ route('disability_day.track') }}" class="btn btn-outline-primary btn-sm px-3">
                    Track Application <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

            @endif

        </div>
    </div>
</div>
@endsection

@section('script')
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // Real-time Input Sanitization (Numbers Only)
    // -------------------------------------------------------------
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    const pinInput = document.getElementById('pin');
    if (pinInput) {
        pinInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // -------------------------------------------------------------
    // Email Uniqueness & Duplicate Check (Frontend)
    // -------------------------------------------------------------
    const emailInput = document.getElementById('email');
    const emailFeedback = document.getElementById('emailDuplicateFeedback');
    let isEmailDuplicate = false;

    if (emailInput) {
        emailInput.addEventListener('blur', function () {
            const val = this.value.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!val || !emailRegex.test(val)) return;

            fetch("{{ route('disability_day.check_email') }}?email=" + encodeURIComponent(val))
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        isEmailDuplicate = true;
                        emailInput.classList.add('is-invalid');
                        if (emailFeedback) {
                            emailFeedback.textContent = data.message;
                            emailFeedback.classList.remove('d-none');
                            emailFeedback.classList.add('d-block');
                        }
                    } else {
                        isEmailDuplicate = false;
                        emailInput.classList.remove('is-invalid');
                        if (emailFeedback) {
                            emailFeedback.classList.add('d-none');
                            emailFeedback.classList.remove('d-block');
                        }
                    }
                })
                .catch(() => {
                    // Authoritative backend validation will catch it
                });
        });

        emailInput.addEventListener('input', function () {
            if (isEmailDuplicate) {
                isEmailDuplicate = false;
                this.classList.remove('is-invalid');
                if (emailFeedback) {
                    emailFeedback.classList.add('d-none');
                    emailFeedback.classList.remove('d-block');
                }
            }
        });
    }

    // -------------------------------------------------------------
    // Client-side Form Validation with SweetAlert2
    // -------------------------------------------------------------
    const form = document.getElementById('stallRegistrationForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            // Helper for SweetAlert error display
            function showValidationError(title, text, fieldEl) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: text,
                    confirmButtonColor: '#087e8b'
                }).then(() => {
                    if (fieldEl) {
                        fieldEl.focus();
                        fieldEl.classList.add('is-invalid');
                    }
                });
            }

            // 1. Organization Name
            const org = document.getElementById('organization_name');
            if (!org || !org.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please enter the organization name.', org);
                return false;
            }

            // 2. Contact Person
            const contact = document.getElementById('contact_person');
            if (!contact || !contact.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please enter the contact person name.', contact);
                return false;
            }

            // 3. Email Address
            const email = document.getElementById('email');
            const emailVal = email ? email.value.trim() : '';
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailVal || !emailRegex.test(emailVal)) {
                showValidationError('Invalid Email Address', 'Please enter a valid email address.', email);
                return false;
            }

            if (isEmailDuplicate) {
                showValidationError(
                    'Duplicate Registration',
                    'This email ID has already been registered. One organization can submit only one registration.',
                    email
                );
                return false;
            }

            // 4. Mobile Number (Strict 10-digit Indian mobile starting with 6, 7, 8, or 9)
            const phoneVal = phoneInput ? phoneInput.value.trim() : '';
            if (!phoneVal || !/^[6-9][0-9]{9}$/.test(phoneVal)) {
                showValidationError(
                    'Invalid Mobile Number',
                    'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.',
                    phoneInput
                );
                return false;
            }

            // 5. Purpose of Requirement
            const purpose = document.getElementById('purpose');
            if (!purpose || !purpose.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please describe the purpose of requirement of stall.', purpose);
                return false;
            }

            // 6. AT (Village / Street / Location)
            const at = document.getElementById('at');
            if (!at || !at.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please enter AT (Village / Street / Location).', at);
                return false;
            }

            // 7. PO (Post Office)
            const po = document.getElementById('po');
            if (!po || !po.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please enter Post Office (PO).', po);
                return false;
            }

            // 8. PS (Police Station)
            const ps = document.getElementById('ps');
            if (!ps || !ps.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please enter Police Station (PS).', ps);
                return false;
            }

            // 9. State
            const state = document.getElementById('state_id');
            if (!state || !state.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please select a State.', state);
                return false;
            }

            // 10. District
            const district = document.getElementById('district_id');
            if (!district || !district.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please select a District.', district);
                return false;
            }

            // 11. PIN Code (6 digits)
            const pinVal = pinInput ? pinInput.value.trim() : '';
            if (!pinVal || !/^[1-9][0-9]{5}$/.test(pinVal)) {
                showValidationError('Invalid PIN Code', 'Please enter a valid 6-digit Indian PIN code.', pinInput);
                return false;
            }

            // All validations passed — disable button to prevent double-submit
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting Registration...';
            }
        });
    }

    // -------------------------------------------------------------
    // Display Server-side Validation Errors via SweetAlert2
    // -------------------------------------------------------------
    @if(isset($errors) && $errors->any())
        let errorMessages = '<ul style="text-align: left; margin: 10px 0 0; padding-left: 20px;">';
        @foreach($errors->all() as $err)
            errorMessages += '<li style="margin-bottom: 5px;">{{ e($err) }}</li>';
        @endforeach
        errorMessages += '</ul>';

        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            html: errorMessages,
            confirmButtonColor: '#087e8b'
        });
    @endif

    // -------------------------------------------------------------
    // Display Server-side General Error via SweetAlert2
    // -------------------------------------------------------------
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Submission Error',
            text: "{{ session('error') }}",
            confirmButtonColor: '#087e8b'
        });
    @endif

    // -------------------------------------------------------------
    // Display Server-side Success Dialog via SweetAlert2
    // -------------------------------------------------------------
    @if(session('registration_success'))
        Swal.fire({
            icon: 'success',
            title: 'Registration Submitted!',
            html: `
                <p style="margin-bottom: 15px;">Your stall registration has been successfully recorded.</p>
                <div style="background: #f0fdf4; border: 2px dashed #22c55e; border-radius: 8px; padding: 15px; margin: 15px 0;">
                    <div style="font-size: 11px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 1px;">Registration Number</div>
                    <div style="font-family: monospace; font-size: 1.4rem; font-weight: 800; color: #0b3954; margin-top: 5px;">{{ session('registration_no') }}</div>
                </div>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 0;">Please save your Registration Number for application tracking.</p>
            `,
            confirmButtonColor: '#087e8b',
            confirmButtonText: '<i class="fas fa-check me-1"></i> View Receipt'
        });
    @endif
});
</script>
@endsection
