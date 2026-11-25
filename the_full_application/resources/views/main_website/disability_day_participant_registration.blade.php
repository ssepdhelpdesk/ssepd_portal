@extends('main_website.layouts.app')

@section('title', 'Participant Registration — International Day of Persons With Disabilities 2026')
@section('meta_description', 'Official Participant Registration Form for Students, Escorts, and Institutions participating in Cultural, Sports, and Other activities at the International Day of Persons With Disabilities 2026, hosted by SSEPD Department, Government of Odisha.')

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
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 25px;
    }
    .escort-card {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-left: 4px solid #087e8b;
        border-radius: 8px;
        padding: 18px 20px;
        margin-bottom: 18px;
        transition: all 0.2s ease-in-out;
    }
    .escort-card:hover {
        box-shadow: 0 4px 12px rgba(8, 126, 139, 0.1);
        border-color: #087e8b;
    }
    .address-type-pill {
        border: 2px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px 20px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .address-type-pill:hover {
        border-color: #087e8b;
        background: #f0fdfa;
    }
    .form-check-input:checked + .form-check-label {
        font-weight: 600;
        color: #0b3954;
    }
    .address-dynamic-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
    }
    .box-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0b3954;
        margin-top: 15px;
        margin-bottom: 5px;
    }

    /* Select2 Custom Theme Alignment */
    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 14px;
        background-color: #fff;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e293b;
        padding-left: 0;
        font-size: 0.95rem;
        line-height: normal;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
        right: 12px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #087e8b;
        box-shadow: 0 0 0 0.25rem rgba(8, 126, 139, 0.25);
        outline: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #64748b;
        font-size: 0.95rem;
    }
    .select2-dropdown {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        z-index: 1055;
    }
    .select2-search--dropdown {
        padding: 10px;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 12px;
        outline: none;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #087e8b;
    }
    .select2-results__option {
        padding: 8px 14px;
        font-size: 0.95rem;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #087e8b;
        color: white;
    }
    .select2-container--default .select2-results__option--selected {
        background-color: #f1f5f9;
        color: #0b3954;
        font-weight: 600;
    }
    .select2-container--default.is-invalid .select2-selection--single,
    .is-invalid + .select2-container--default .select2-selection--single {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                <h1 class="h2 fw-bold mb-2">Participant Registration – Disability Day 2026</h1>
                <h2 class="h5 text-warning fw-semibold mb-3">Registration Form for Participants and their Escorts.</h2>
                <p class="text-white-50 mb-3" style="max-width: 650px;">
                    State-level registration portal for students, special school candidates, escorts, and participants in Cultural, Sports, and allied events celebrating International Day of Persons with Disabilities.
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
                <a href="{{ route('disability_day.registration') }}" class="btn btn-outline-light btn-lg px-4 shadow-sm">
                    <i class="fas fa-store me-1"></i> Register for Exhibition Stall
                </a>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Success Confirmation Screen -->
            @if(session('registration_success'))
            <div class="registration-card p-4 p-md-5 mt-4">
                <div class="success-receipt-card">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 3.5rem;"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-2">Participant Registration Submitted Successfully!</h3>
                    <p class="text-muted mb-3">
                        Your participant registration for the <strong>International Day of Persons With Disabilities – 2026</strong> has been officially recorded by the department.
                    </p>

                    <div class="text-muted small text-uppercase fw-semibold">Your Official Registration Number</div>
                    <div class="reg-code-highlight" id="regNumberDisplay">{{ session('registration_no') }}</div>

                    <div class="row justify-content-center text-start my-3">
                        <div class="col-md-9">
                            <ul class="list-group list-group-flush shadow-sm rounded border">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Participant Name:</span>
                                    <span class="fw-bold">{{ session('participant_name') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">School / Institute:</span>
                                    <span class="fw-bold">{{ session('school_name') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Gender & T-Shirt Size:</span>
                                    <span class="fw-bold">{{ session('gender') }} &bull; Size: {{ session('tshirt_size') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Accompanying Escorts:</span>
                                    <span class="fw-bold">{{ session('number_of_escorts') }} Escort(s)</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Participation Category:</span>
                                    <span class="badge bg-primary fs-6">{{ session('participation_in') }}</span>
                                </li>
                                @if(session('attending_dates'))
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Dates of Participation:</span>
                                    <span class="fw-bold">{{ session('attending_dates') }}</span>
                                </li>
                                @endif
                                @if(session('transport_mode'))
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Mode of Transportation:</span>
                                    <span class="fw-bold">{{ session('transport_mode') }}</span>
                                </li>
                                @endif
                                @if(session('expected_arrival_date'))
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Expected Venue Arrival:</span>
                                    <span class="fw-bold">{{ session('expected_arrival_date') }} at {{ session('expected_arrival_time') }}</span>
                                </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Submitted At:</span>
                                    <span class="fw-bold">{{ session('applied_at') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="system-info-notice text-start my-4 mx-auto" style="max-width: 680px;">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Important Instructions:</strong> Please save or print this official Registration Number. Participants and their escorts must present this registration acknowledgment at the event registration desk upon arrival at S'O'A University.
                    </div>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print Receipt
                        </button>
                        <a href="{{ route('disability_day.participant_registration') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus me-1"></i> Register Another Participant
                        </a>
                        <a href="{{ route('disability_day.registration') }}" class="btn btn-outline-primary">
                            <i class="fas fa-store me-1"></i> Register a Stall
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

                <form action="{{ route('disability_day.participant_registration.store') }}" method="POST" id="participantRegistrationForm" novalidate>
                    @csrf

                    <!-- SECTION A: PARTICIPANT DETAILS -->
                    <div class="form-section-header">
                        <i class="fas fa-user text-primary"></i>
                        <span>Section A — Participant Details</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row g-3">
                            <!-- 1. Participant Name -->
                            <div class="col-md-6">
                                <label for="participant_name" class="form-label fw-semibold">
                                    1. Participant Name <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('participant_name') is-invalid @enderror" 
                                       id="participant_name" 
                                       name="participant_name" 
                                       value="{{ old('participant_name') }}" 
                                       placeholder="Enter full name of participant" 
                                       minlength="2" 
                                       maxlength="150" 
                                       required>
                                @error('participant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 2. School Name -->
                            <div class="col-md-6">
                                <label for="institute_id" class="form-label fw-semibold">
                                    2. School Name <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select select2-institute @error('institute_id') is-invalid @enderror" 
                                        id="institute_id" 
                                        name="institute_id" 
                                        data-placeholder="Search and Select School Name"
                                        required>
                                    <option value="">Search and Select School Name</option>
                                    @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}" {{ old('institute_id') == $institute->id ? 'selected' : '' }}>
                                        {{ $institute->institute_name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text small text-muted">Search and select from registered SSEPD Special Schools and Recognized Institutes.</div>
                                @error('institute_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 3. Gender -->
                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-semibold">
                                    3. Gender <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('gender') is-invalid @enderror" 
                                        id="gender" 
                                        name="gender" 
                                        required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 4. T-Shirt Size -->
                            <div class="col-md-6">
                                <label for="tshirt_size" class="form-label fw-semibold">
                                    4. T-Shirt Size <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('tshirt_size') is-invalid @enderror" 
                                        id="tshirt_size" 
                                        name="tshirt_size" 
                                        required>
                                    <option value="">-- Select T-Shirt Size --</option>
                                    @foreach(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)
                                    <option value="{{ $size }}" {{ old('tshirt_size') === $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text small text-muted">Official event kit merchandise size for participant.</div>
                                @error('tshirt_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION B: COMPLETE ADDRESS OF THE PARTICIPANT -->
                    <div class="form-section-header">
                        <i class="fas fa-map-marked-alt text-primary"></i>
                        <span>Section B — Complete Address of the Participant</span>
                    </div>
                    <div class="form-section-body">
                        <!-- Address Type Selection (Village vs Municipality) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Select Address Type <span class="required-asterisk">*</span>
                            </label>
                            <div class="d-flex flex-wrap gap-4 mt-1">
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="address_type" 
                                           id="address_type_village" 
                                           value="1" 
                                           {{ old('address_type', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="address_type_village">
                                        <i class="fas fa-tree text-success me-1"></i> Village (Block / Rural Area)
                                    </label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="address_type" 
                                           id="address_type_municipality" 
                                           value="2" 
                                           {{ old('address_type') == '2' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="address_type_municipality">
                                        <i class="fas fa-city text-primary me-1"></i> Municipality (ULB / Urban Area)
                                    </label>
                                </div>
                            </div>
                            @error('address_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamic Address Content Container (Injected via frontend/get-address-type-content-data/{type}) -->
                        <div id="dynamicAddressContainer" class="address-dynamic-box">
                            <div class="text-center py-4 text-muted" id="addressLoadingIndicator">
                                <i class="fas fa-spinner fa-spin me-2"></i> Loading address fields...
                            </div>
                            <div id="addressFieldsTarget" class="row g-3"></div>
                        </div>
                    </div>

                    <!-- SECTION C: ESCORT DETAILS -->
                    <div class="form-section-header">
                        <i class="fas fa-user-friends text-primary"></i>
                        <span>Section C — Escort Details</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row g-3 align-items-center mb-4">
                            <div class="col-md-5">
                                <label for="number_of_escorts" class="form-label fw-semibold">
                                    5. Number of Escorts <span class="required-asterisk">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('number_of_escorts') is-invalid @enderror" 
                                       id="number_of_escorts" 
                                       name="number_of_escorts" 
                                       min="0" 
                                       max="5" 
                                       value="{{ old('number_of_escorts', 0) }}" 
                                       required>
                                <div class="form-text small text-muted">Enter 0 if the participant travels unaccompanied, or up to 5 escorts.</div>
                                @error('number_of_escorts')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-7">
                                <div class="alert alert-light border small text-muted mb-0 py-2">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    Dynamic escort detail cards will be generated automatically below based on the number specified.
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Escort Detail Cards Container -->
                        <div id="escortsContainer">
                            <!-- Populated via JavaScript -->
                        </div>
                    </div>

                    <!-- SECTION D: PARTICIPATION DETAILS -->
                    <div class="form-section-header">
                        <i class="fas fa-trophy text-primary"></i>
                        <span>Section D — Participation Details</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row g-3">
                            <!-- 7. Participation In -->
                            <div class="col-md-6">
                                <label for="participation_in" class="form-label fw-semibold">
                                    7. Participation In <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('participation_in') is-invalid @enderror" 
                                        id="participation_in" 
                                        name="participation_in" 
                                        required>
                                    <option value="">-- Select Participation Category --</option>
                                    <option value="Cultural" {{ old('participation_in') === 'Cultural' ? 'selected' : '' }}>Cultural (Dance, Music, Drama, Art)</option>
                                    <option value="Sports" {{ old('participation_in') === 'Sports' ? 'selected' : '' }}>Sports (Athletics, Indoor Games, Para-Sports)</option>
                                    <option value="Others" {{ old('participation_in') === 'Others' ? 'selected' : '' }}>Others (Exhibition, Volunteering, Others)</option>
                                </select>
                                @error('participation_in')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Conditional Field: Please Specify Other Participation -->
                            <div class="col-md-6 {{ old('participation_in') === 'Others' ? '' : 'd-none' }}" id="otherParticipationWrapper">
                                <label for="participation_other" class="form-label fw-semibold">
                                    Please Specify Other Participation <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('participation_other') is-invalid @enderror" 
                                       id="participation_other" 
                                       name="participation_other" 
                                       value="{{ old('participation_other') }}" 
                                       placeholder="Describe other participation details" 
                                       maxlength="255">
                                @error('participation_other')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dates of Participation -->
                            <div class="col-12 mt-3" id="attendingDatesGroup">
                                <label class="form-label fw-semibold d-block">
                                    Dates of Participation <span class="required-asterisk">*</span>
                                </label>
                                <div class="form-text small text-muted mb-2">
                                    Please select the date(s) on which you will attend the event.
                                </div>
                                @php
                                    $oldAttendingDates = old('attending_dates', []);
                                    if (!is_array($oldAttendingDates)) $oldAttendingDates = [];
                                @endphp
                                <div class="row g-2">
                                    @foreach(\App\Models\DisabilityDayParticipantRegistration::EVENT_DATES as $isoDate => $dateLabel)
                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-check custom-radio p-2 border rounded bg-light">
                                            <input class="form-check-input ms-1 attending-date-checkbox attending-date-individual"
                                                   type="checkbox"
                                                   name="attending_dates[]"
                                                   id="attending_date_{{ $isoDate }}"
                                                   value="{{ $isoDate }}"
                                                   {{ in_array($isoDate, $oldAttendingDates, true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium ms-1" for="attending_date_{{ $isoDate }}">
                                                {{ $dateLabel }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-check custom-radio p-2 border rounded bg-light border-primary">
                                            <input class="form-check-input ms-1 attending-date-checkbox"
                                                   type="checkbox"
                                                   name="attending_dates[]"
                                                   id="attending_date_all"
                                                   value="{{ \App\Models\DisabilityDayParticipantRegistration::ALL_DAYS_VALUE }}"
                                                   {{ in_array(\App\Models\DisabilityDayParticipantRegistration::ALL_DAYS_VALUE, $oldAttendingDates, true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold text-primary ms-1" for="attending_date_all">
                                                All Days (December 1–3, 2026)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('attending_dates')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION E: TRANSPORTATION & VENUE ARRIVAL DETAILS -->
                    <div class="form-section-header">
                        <i class="fas fa-bus text-primary"></i>
                        <span>Section E — Transportation &amp; Venue Arrival Details</span>
                    </div>
                    <div class="form-section-body">
                        <div class="row g-3">
                            <!-- 8. Mode of Transportation -->
                            <div class="col-md-6">
                                <label for="transport_mode" class="form-label fw-semibold">
                                    8. Mode of Transportation <span class="required-asterisk">*</span>
                                </label>
                                <select class="form-select @error('transport_mode') is-invalid @enderror" 
                                        id="transport_mode" 
                                        name="transport_mode" 
                                        required>
                                    <option value="">-- Select Mode of Transportation --</option>
                                    @foreach(\App\Models\DisabilityDayParticipantRegistration::TRANSPORT_MODES as $mode)
                                    <option value="{{ $mode }}" {{ old('transport_mode') === $mode ? 'selected' : '' }}>
                                        {{ $mode }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('transport_mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Conditional Field: Specify Other Mode of Transportation -->
                            <div class="col-md-6 {{ old('transport_mode') === 'Other' ? '' : 'd-none' }}" id="otherTransportWrapper">
                                <label for="transport_mode_other" class="form-label fw-semibold">
                                    Specify Other Mode of Transportation <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('transport_mode_other') is-invalid @enderror" 
                                       id="transport_mode_other" 
                                       name="transport_mode_other" 
                                       value="{{ old('transport_mode_other') }}" 
                                       placeholder="Enter mode of transportation details" 
                                       maxlength="255">
                                @error('transport_mode_other')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 9. Expected Date of Arrival at Venue -->
                            <div class="col-md-6">
                                <label for="expected_arrival_date" class="form-label fw-semibold">
                                    9. Expected Date of Arrival at Venue <span class="required-asterisk">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('expected_arrival_date') is-invalid @enderror" 
                                       id="expected_arrival_date" 
                                       name="expected_arrival_date" 
                                       value="{{ old('expected_arrival_date') }}" 
                                       required>
                                @error('expected_arrival_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 10. Expected Time of Arrival at Venue -->
                            <div class="col-md-6">
                                <label for="expected_arrival_time" class="form-label fw-semibold">
                                    10. Expected Time of Arrival at Venue <span class="required-asterisk">*</span>
                                </label>
                                <input type="time" 
                                       class="form-control @error('expected_arrival_time') is-invalid @enderror" 
                                       id="expected_arrival_time" 
                                       name="expected_arrival_time" 
                                       value="{{ old('expected_arrival_time') }}" 
                                       required>
                                @error('expected_arrival_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- System-generated Information Notice -->
                        <!-- <div class="system-info-notice my-4">
                            <h6 class="fw-bold mb-2"><i class="fas fa-shield-alt me-1"></i> Registration & Event Identity Policy</h6>
                            <ul class="mb-0 ps-3">
                                <li><strong>Registration Number:</strong> Generated automatically by the secure server upon successful submission (<code>SSEPD/DD2026/PART/DD/MM/YYYY/XXXXXX</code>).</li>
                                <li><strong>Applied Date & Time:</strong> Timestamped from the official Indian Standard Time (IST) departmental clock.</li>
                                <li><strong>Identity & T-Shirt Allocation:</strong> Official badge and T-Shirt kit will be handed over to registered participants and escorts at the venue reception counter.</li>
                            </ul>
                        </div> -->

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-3">
                            <div class="text-muted small">
                                <i class="fas fa-lock me-1"></i> All submitted information is encrypted and handled under official SSEPD IT security policies.
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4" id="submitBtn">
                                <i class="fas fa-paper-plane me-1"></i> Submit Participant Registration
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer Quick Links -->
            <div class="track-link-box mb-5">
                <div>
                    <h6 class="fw-bold mb-1"><i class="fas fa-store text-primary me-2"></i> Registering an Organization Stall?</h6>
                    <p class="text-muted small mb-0">Institutions, SHGs, and assistive technology exhibitors can request an official exhibition/service stall.</p>
                </div>
                <a href="{{ route('disability_day.registration') }}" class="btn btn-outline-primary btn-sm px-3">
                    Stall Registration <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

            @endif

        </div>
    </div>
</div>
@endsection

@section('script')
<!-- jQuery (Required for existing dependent dropdown AJAX routes) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- Select2 Library -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // Select2 Initialization for School / Institute Name
    // -------------------------------------------------------------
    if (typeof $.fn.select2 !== 'undefined') {
        $('#institute_id').select2({
            placeholder: "Search and Select School Name",
            allowClear: true,
            width: '100%'
        });

        $('#institute_id').on('change', function () {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
                $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }
        });
    }

    // -------------------------------------------------------------
    // CSRF Token Setup for jQuery AJAX
    // -------------------------------------------------------------
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // -------------------------------------------------------------
    // 1. Dynamic Address Loading (Reusing existing shared route)
    // -------------------------------------------------------------
    const addressRadios = document.querySelectorAll('input[name="address_type"]');
    const addressTarget = document.getElementById('addressFieldsTarget');
    const addressLoader = document.getElementById('addressLoadingIndicator');

    // Values preserved from old input on validation failure
    const oldAddressType = "{{ old('address_type', '1') }}";
    const oldState = "{{ old('state', '') }}";
    const oldDistrict = "{{ old('district', '') }}";
    const oldBlock = "{{ old('block', '') }}";
    const oldGP = "{{ old('grampanchayat', '') }}";
    const oldVillage = "{{ old('village', '') }}";
    const oldMunicipality = "{{ old('municipality', '') }}";
    const oldPin = "{{ old('pin', '') }}";

    function loadAddressContent(type, isInitialLoad = false) {
        if (!addressTarget) return;

        addressLoader.style.display = 'block';
        addressTarget.innerHTML = '';

        const url = "{{ url('frontend/get-address-type-content-data') }}/" + type;

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Failed to load address content');
                return res.json();
            })
            .then(data => {
                addressLoader.style.display = 'none';
                let cleanContent = (data.content || '').replace(/<h3 class="box-title">Postal Address<\/h3>[\s\S]*$/i, '');
                addressTarget.innerHTML = cleanContent;

                // Initialize dependent dropdown event listeners
                initializeLocationDropdowns(type);

                // Restore old values if re-rendering after validation error
                if (isInitialLoad) {
                    restoreOldAddressValues(type);
                }
            })
            .catch(err => {
                addressLoader.style.display = 'none';
                addressTarget.innerHTML = '<div class="col-12 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Unable to load address fields. Please refresh the page.</div>';
                console.error('Error loading address content:', err);
            });
    }

    function initializeLocationDropdowns(type) {
        // State -> District
        $('#state-dropdown').off('change').on('change', function () {
            const idState = $(this).val();
            $('#district-dropdown').html('<option value="">-- Select District --</option>');
            $('#block-dropdown').html('<option value="">-- Select Block --</option>');
            $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
            $('#village-dropdown').html('<option value="">-- Select Village --</option>');
            $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');

            if (!idState) return;

            $.ajax({
                url: "{{ url('frontend/locations/fetch-districts') }}",
                type: "POST",
                data: { state_id: idState, all_districts: 1 },
                dataType: "json",
                success: function (res) {
                    $('#district-dropdown').html('<option value="">-- Select District --</option>');
                    if (res && res.districts) {
                        $.each(res.districts, function (k, val) {
                            $('#district-dropdown').append('<option value="' + val.district_id + '">' + val.district_name + '</option>');
                        });
                    }
                }
            });
        });

        // District -> Block or Municipality
        $('#district-dropdown').off('change').on('change', function () {
            const idDistrict = $(this).val();
            $('#block-dropdown').html('<option value="">-- Select Block --</option>');
            $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
            $('#village-dropdown').html('<option value="">-- Select Village --</option>');
            $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');

            if (!idDistrict) return;

            if (type === '1') {
                // Fetch Blocks
                $.ajax({
                    url: "{{ url('frontend/locations/fetch-block') }}",
                    type: "POST",
                    data: { district_id: idDistrict, all_blocks: 1 },
                    dataType: "json",
                    success: function (res) {
                        $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                        if (res && res.blocks) {
                            $.each(res.blocks, function (k, val) {
                                $('#block-dropdown').append('<option value="' + val.block_id + '">' + val.block_name + '</option>');
                            });
                        }
                    }
                });
            } else if (type === '2') {
                // Fetch Municipalities
                $.ajax({
                    url: "{{ url('frontend/locations/fetch-municipality') }}",
                    type: "POST",
                    data: { district_id: idDistrict, all_municipalities: 1 },
                    dataType: "json",
                    success: function (res) {
                        $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
                        if (res && res.municipalities) {
                            $.each(res.municipalities, function (k, val) {
                                $('#municipality-dropdown').append('<option value="' + val.municipality_id + '">' + val.municipality_name + '</option>');
                            });
                        }
                    }
                });
            }
        });

        // Block -> Grampanchayat (For Village type)
        $('#block-dropdown').off('change').on('change', function () {
            const idBlock = $(this).val();
            $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
            $('#village-dropdown').html('<option value="">-- Select Village --</option>');

            if (!idBlock) return;

            $.ajax({
                url: "{{ url('frontend/locations/fetch-grampanchayat') }}",
                type: "POST",
                data: { block_id: idBlock },
                dataType: "json",
                success: function (res) {
                    $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                    if (res && res.grampanchayats) {
                        $.each(res.grampanchayats, function (k, val) {
                            $('#grampanchayat-dropdown').append('<option value="' + val.gp_id + '">' + val.gp_name + '</option>');
                        });
                    }
                }
            });
        });

        // Grampanchayat -> Village (For Village type)
        $('#grampanchayat-dropdown').off('change').on('change', function () {
            const idGp = $(this).val();
            $('#village-dropdown').html('<option value="">-- Select Village --</option>');

            if (!idGp) return;

            $.ajax({
                url: "{{ url('frontend/locations/fetch-village') }}",
                type: "POST",
                data: { gp_id: idGp },
                dataType: "json",
                success: function (res) {
                    $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                    if (res && res.villages) {
                        $.each(res.villages, function (k, val) {
                            $('#village-dropdown').append('<option value="' + val.village_id + '">' + val.village_name + '</option>');
                        });
                    }
                }
            });
        });

        // PIN code sanitization
        $(document).on('input', '#pin', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    function restoreOldAddressValues(type) {
        if (oldPin) $('#pin').val(oldPin);

        if (oldState) {
            $('#state-dropdown').val(oldState);

            $.ajax({
                url: "{{ url('frontend/locations/fetch-districts') }}",
                type: "POST",
                data: { state_id: oldState, all_districts: 1 },
                dataType: "json",
                success: function (res) {
                    $('#district-dropdown').html('<option value="">-- Select District --</option>');
                    if (res && res.districts) {
                        $.each(res.districts, function (k, val) {
                            const sel = (val.district_id == oldDistrict) ? 'selected' : '';
                            $('#district-dropdown').append('<option value="' + val.district_id + '" ' + sel + '>' + val.district_name + '</option>');
                        });
                    }

                    if (oldDistrict) {
                        if (type === '1') {
                            $.ajax({
                                url: "{{ url('frontend/locations/fetch-block') }}",
                                type: "POST",
                                data: { district_id: oldDistrict, all_blocks: 1 },
                                dataType: "json",
                                success: function (res) {
                                    $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                                    if (res && res.blocks) {
                                        $.each(res.blocks, function (k, val) {
                                            const sel = (val.block_id == oldBlock) ? 'selected' : '';
                                            $('#block-dropdown').append('<option value="' + val.block_id + '" ' + sel + '>' + val.block_name + '</option>');
                                        });
                                    }

                                    if (oldBlock) {
                                        $.ajax({
                                            url: "{{ url('frontend/locations/fetch-grampanchayat') }}",
                                            type: "POST",
                                            data: { block_id: oldBlock },
                                            dataType: "json",
                                            success: function (res) {
                                                $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                                                if (res && res.grampanchayats) {
                                                    $.each(res.grampanchayats, function (k, val) {
                                                        const sel = (val.gp_id == oldGP) ? 'selected' : '';
                                                        $('#grampanchayat-dropdown').append('<option value="' + val.gp_id + '" ' + sel + '>' + val.gp_name + '</option>');
                                                    });
                                                }

                                                if (oldGP) {
                                                    $.ajax({
                                                        url: "{{ url('frontend/locations/fetch-village') }}",
                                                        type: "POST",
                                                        data: { gp_id: oldGP },
                                                        dataType: "json",
                                                        success: function (res) {
                                                            $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                                                            if (res && res.villages) {
                                                                $.each(res.villages, function (k, val) {
                                                                    const sel = (val.village_id == oldVillage) ? 'selected' : '';
                                                                    $('#village-dropdown').append('<option value="' + val.village_id + '" ' + sel + '>' + val.village_name + '</option>');
                                                                });
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        } else if (type === '2') {
                            $.ajax({
                                url: "{{ url('frontend/locations/fetch-municipality') }}",
                                type: "POST",
                                data: { district_id: oldDistrict, all_municipalities: 1 },
                                dataType: "json",
                                success: function (res) {
                                    $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
                                    if (res && res.municipalities) {
                                        $.each(res.municipalities, function (k, val) {
                                            const sel = (val.municipality_id == oldMunicipality) ? 'selected' : '';
                                            $('#municipality-dropdown').append('<option value="' + val.municipality_id + '" ' + sel + '>' + val.municipality_name + '</option>');
                                        });
                                    }
                                }
                            });
                        }
                    }
                }
            });
        }
    }

    // Attach listener to Address Type Radios
    addressRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            loadAddressContent(this.value, false);
        });
    });

    // Initial Address Load
    const activeAddressType = document.querySelector('input[name="address_type"]:checked')
        ? document.querySelector('input[name="address_type"]:checked').value
        : '1';
    loadAddressContent(activeAddressType, true);


    // -------------------------------------------------------------
    // 2. Dynamic Escort Sections Generation
    // -------------------------------------------------------------
    const escortsCountInput = document.getElementById('number_of_escorts');
    const escortsContainer = document.getElementById('escortsContainer');

    // Repopulate escorts if available in old session
    const oldEscorts = @json(old('escorts', []));

    function renderEscortCards(count) {
        if (!escortsContainer) return;
        escortsContainer.innerHTML = '';

        count = parseInt(count, 10);
        if (isNaN(count) || count <= 0) {
            escortsContainer.innerHTML = `
                <div class="alert alert-light border text-center py-4 text-muted">
                    <i class="fas fa-walking me-1"></i> No escorts declared. If the participant will be accompanied by an escort/guardian, please enter 1 or more above.
                </div>
            `;
            return;
        }

        if (count > 5) {
            count = 5;
            escortsCountInput.value = 5;
        }

        for (let i = 1; i <= count; i++) {
            const arrIndex = i - 1;
            const escortOld = oldEscorts && oldEscorts[arrIndex] ? oldEscorts[arrIndex] : {};
            const oldName = escortOld.name || '';
            const oldMobile = escortOld.mobile || '';
            const oldEmail = escortOld.email || '';
            const oldRel = escortOld.relationship || '';

            const card = document.createElement('div');
            card.className = 'escort-card';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-id-card text-primary me-2"></i> Escort #${i} Details
                    </h6>
                    <span class="badge bg-light text-dark border">Escort ${i} of ${count}</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            a. Escort Name <span class="required-asterisk">*</span>
                        </label>
                        <input type="text" 
                               name="escorts[${arrIndex}][name]" 
                               id="escort_name_${i}" 
                               class="form-control" 
                               placeholder="Full name of escort" 
                               value="${oldName}" 
                               maxlength="150" 
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            b. Mobile Number <span class="required-asterisk">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">+91</span>
                            <input type="tel" 
                                   name="escorts[${arrIndex}][mobile]" 
                                   id="escort_mobile_${i}" 
                                   class="form-control escort-mobile-input" 
                                   placeholder="10-digit mobile number" 
                                   value="${oldMobile}" 
                                   maxlength="10" 
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            c. Email ID <span class="text-muted small">(Optional)</span>
                        </label>
                        <input type="email" 
                               name="escorts[${arrIndex}][email]" 
                               id="escort_email_${i}" 
                               class="form-control" 
                               placeholder="escort@example.com" 
                               value="${oldEmail}" 
                               maxlength="150">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            d. Relationship with Participant <span class="required-asterisk">*</span>
                        </label>
                        <input type="text" 
                               name="escorts[${arrIndex}][relationship]" 
                               id="escort_rel_${i}" 
                               class="form-control" 
                               placeholder="e.g. Father, Mother, Teacher, Guardian, Attendant" 
                               value="${oldRel}" 
                               maxlength="100" 
                               required>
                    </div>
                </div>
            `;
            escortsContainer.appendChild(card);
        }

        // Attach numbers-only sanitize to newly created mobile inputs
        document.querySelectorAll('.escort-mobile-input').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    }

    if (escortsCountInput) {
        escortsCountInput.addEventListener('input', function () {
            renderEscortCards(this.value);
        });
        // Initial render
        renderEscortCards(escortsCountInput.value);
    }


    // -------------------------------------------------------------
    // 3. Conditional Participation Details
    // -------------------------------------------------------------
    const participationSelect = document.getElementById('participation_in');
    const otherWrapper = document.getElementById('otherParticipationWrapper');
    const otherInput = document.getElementById('participation_other');

    if (participationSelect && otherWrapper) {
        participationSelect.addEventListener('change', function () {
            if (this.value === 'Others') {
                otherWrapper.classList.remove('d-none');
                if (otherInput) otherInput.setAttribute('required', 'required');
            } else {
                otherWrapper.classList.add('d-none');
                if (otherInput) {
                    otherInput.removeAttribute('required');
                    otherInput.value = '';
                }
            }
        });
    }


    // -------------------------------------------------------------
    // 3a. Mutually Exclusive Attendance Dates Checkboxes
    // -------------------------------------------------------------
    const allDaysCheckbox = document.getElementById('attending_date_all');
    const individualDateCheckboxes = document.querySelectorAll('.attending-date-individual');

    if (allDaysCheckbox && individualDateCheckboxes.length > 0) {
        allDaysCheckbox.addEventListener('change', function () {
            if (this.checked) {
                individualDateCheckboxes.forEach(cb => {
                    cb.checked = false;
                });
            }
        });

        individualDateCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked) {
                    allDaysCheckbox.checked = false;
                }
            });
        });
    }

    // -------------------------------------------------------------
    // 3b. Conditional Transportation Details & Name Normalization
    // -------------------------------------------------------------
    const transportSelect = document.getElementById('transport_mode');
    const otherTransportWrapper = document.getElementById('otherTransportWrapper');
    const otherTransportInput = document.getElementById('transport_mode_other');

    if (transportSelect && otherTransportWrapper) {
        transportSelect.addEventListener('change', function () {
            if (this.value === 'Other') {
                otherTransportWrapper.classList.remove('d-none');
                if (otherTransportInput) otherTransportInput.setAttribute('required', 'required');
            } else {
                otherTransportWrapper.classList.add('d-none');
                if (otherTransportInput) {
                    otherTransportInput.removeAttribute('required');
                    otherTransportInput.value = '';
                }
            }
        });
    }

    const pNameInput = document.getElementById('participant_name');
    if (pNameInput) {
        pNameInput.addEventListener('blur', function () {
            this.value = this.value.trim().replace(/\s+/g, ' ');
        });
    }


    // -------------------------------------------------------------
    // 4. Client-side Form Validation with SweetAlert2
    // -------------------------------------------------------------
    const form = document.getElementById('participantRegistrationForm');
    if (form) {
        form.addEventListener('submit', function (e) {
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

            // 1. Participant Name (Single Participant Validation)
            const pName = document.getElementById('participant_name');
            if (!pName) {
                showValidationError('Mandatory Field Missing', 'Participant name field is missing.', pName);
                return false;
            }
            const rawPName = pName.value;
            if (/[,;\r\n\t|\/&]/.test(rawPName)) {
                showValidationError('Invalid Participant Name', 'Please enter only one participant\'s full name. Multiple names separated by commas, semicolons, or new lines are not allowed.', pName);
                return false;
            }
            const normalizedPName = rawPName.trim().replace(/\s+/g, ' ');
            pName.value = normalizedPName;
            if (!normalizedPName || normalizedPName.length < 2) {
                showValidationError('Mandatory Field Missing', 'Please enter a valid participant name (at least 2 characters).', pName);
                return false;
            }
            if (normalizedPName.split(' ').filter(Boolean).length > 6) {
                showValidationError('Multiple Participants Detected', 'Please enter only one participant\'s full name. Multiple participants\' names cannot be entered in a single registration.', pName);
                return false;
            }

            // 2. School Name
            const inst = document.getElementById('institute_id');
            if (!inst || !inst.value.trim()) {
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#institute_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                    $('#institute_id').select2('open');
                }
                showValidationError('Mandatory Field Missing', 'Please search and select a School / Institute from the list.', inst);
                return false;
            }

            // 3. Gender
            const gender = document.getElementById('gender');
            if (!gender || !gender.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please select the participant gender.', gender);
                return false;
            }

            // 4. T-Shirt Size
            const tshirt = document.getElementById('tshirt_size');
            if (!tshirt || !tshirt.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please select a T-Shirt size for the participant.', tshirt);
                return false;
            }

            // 5. Address Validation
            const state = document.getElementById('state-dropdown');
            if (!state || !state.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please select a State in Section B.', state);
                return false;
            }

            const district = document.getElementById('district-dropdown');
            if (!district || !district.value.trim()) {
                showValidationError('Mandatory Field Missing', 'Please select a District in Section B.', district);
                return false;
            }

            const selectedAddrType = document.querySelector('input[name="address_type"]:checked')
                ? document.querySelector('input[name="address_type"]:checked').value
                : '1';

            if (selectedAddrType === '1') {
                const block = document.getElementById('block-dropdown');
                if (!block || !block.value.trim()) {
                    showValidationError('Mandatory Field Missing', 'Please select a Block in Section B.', block);
                    return false;
                }
                const gp = document.getElementById('grampanchayat-dropdown');
                if (!gp || !gp.value.trim()) {
                    showValidationError('Mandatory Field Missing', 'Please select a Grampanchayat in Section B.', gp);
                    return false;
                }
                const village = document.getElementById('village-dropdown');
                if (!village || !village.value.trim()) {
                    showValidationError('Mandatory Field Missing', 'Please select a Village in Section B.', village);
                    return false;
                }
            } else if (selectedAddrType === '2') {
                const muni = document.getElementById('municipality-dropdown');
                if (!muni || !muni.value.trim()) {
                    showValidationError('Mandatory Field Missing', 'Please select a Municipality / ULB in Section B.', muni);
                    return false;
                }
            }

            const pin = document.getElementById('pin');
            if (!pin || !/^[1-9][0-9]{5}$/.test(pin.value.trim())) {
                showValidationError('Invalid PIN Code', 'Please enter a valid 6-digit PIN code in Section B.', pin);
                return false;
            }

            // 6. Escort Validation
            const escCount = parseInt(escortsCountInput ? escortsCountInput.value : 0, 10);
            if (isNaN(escCount) || escCount < 0) {
                showValidationError('Invalid Escort Count', 'Please enter a valid non-negative number of escorts.', escortsCountInput);
                return false;
            }

            if (escCount > 0) {
                for (let i = 1; i <= escCount; i++) {
                    const eName = document.getElementById(`escort_name_${i}`);
                    if (!eName || !eName.value.trim() || eName.value.trim().length < 2) {
                        showValidationError('Escort Name Required', `Please enter the full name for Escort #${i}.`, eName);
                        return false;
                    }

                    const eMobile = document.getElementById(`escort_mobile_${i}`);
                    if (!eMobile || !/^[6-9][0-9]{9}$/.test(eMobile.value.trim())) {
                        showValidationError('Invalid Escort Mobile', `Please enter a valid 10-digit mobile number for Escort #${i}.`, eMobile);
                        return false;
                    }

                    const eEmail = document.getElementById(`escort_email_${i}`);
                    if (eEmail && eEmail.value.trim() !== '') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(eEmail.value.trim())) {
                            showValidationError('Invalid Escort Email', `Please enter a valid email address for Escort #${i}, or leave it blank.`, eEmail);
                            return false;
                        }
                    }

                    const eRel = document.getElementById(`escort_rel_${i}`);
                    if (!eRel || !eRel.value.trim()) {
                        showValidationError('Escort Relationship Required', `Please specify the relationship for Escort #${i} with the participant.`, eRel);
                        return false;
                    }
                }
            }

            // 7. Participation Details
            const partIn = document.getElementById('participation_in');
            if (!partIn || !partIn.value.trim()) {
                showValidationError('Participation Category Missing', 'Please select a participation category (Cultural, Sports, or Others).', partIn);
                return false;
            }

            if (partIn.value === 'Others') {
                if (!otherInput || !otherInput.value.trim()) {
                    showValidationError('Please Specify Other Participation', 'Please describe the other participation activity in the field provided.', otherInput);
                    return false;
                }
            }

            // 7b. Dates of Participation
            const checkedAttendance = document.querySelectorAll('.attending-date-checkbox:checked');
            if (!checkedAttendance || checkedAttendance.length === 0) {
                showValidationError('Dates of Participation Missing', 'Please select at least one date on which you will attend the event.', document.getElementById('attending_date_2026-12-01'));
                return false;
            }

            // 8. Transportation & Venue Arrival Details
            const transMode = document.getElementById('transport_mode');
            if (!transMode || !transMode.value.trim()) {
                showValidationError('Transportation Mode Missing', 'Please select a Mode of Transportation.', transMode);
                return false;
            }

            if (transMode.value === 'Other') {
                if (!otherTransportInput || !otherTransportInput.value.trim()) {
                    showValidationError('Please Specify Other Transportation', 'Please specify the other mode of transportation.', otherTransportInput);
                    return false;
                }
            }

            const arrDate = document.getElementById('expected_arrival_date');
            if (!arrDate || !arrDate.value.trim()) {
                showValidationError('Arrival Date Missing', 'Please select the Expected Date of Arrival at Venue.', arrDate);
                return false;
            }

            const arrTime = document.getElementById('expected_arrival_time');
            if (!arrTime || !arrTime.value.trim()) {
                showValidationError('Arrival Time Missing', 'Please enter the Expected Time of Arrival at Venue.', arrTime);
                return false;
            }

            // Prevent multiple submissions
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting Registration...';
            }
        });
    }

    // -------------------------------------------------------------
    // 5. Server-side Error & Success SweetAlert2 Triggers
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

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Submission Error',
            text: "{{ session('error') }}",
            confirmButtonColor: '#087e8b'
        });
    @endif

    @if(session('registration_success'))
        Swal.fire({
            icon: 'success',
            title: 'Participant Registered!',
            html: `
                <p style="margin-bottom: 15px;">Your participant registration has been recorded successfully.</p>
                <div style="background: #f0fdf4; border: 2px dashed #22c55e; border-radius: 8px; padding: 15px; margin: 15px 0;">
                    <div style="font-size: 11px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 1px;">Official Registration Number</div>
                    <div style="font-family: monospace; font-size: 1.4rem; font-weight: 800; color: #0b3954; margin-top: 5px;">{{ session('registration_no') }}</div>
                </div>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 0;">Please keep this registration number for event check-in and certificate issuance.</p>
            `,
            confirmButtonColor: '#087e8b',
            confirmButtonText: '<i class="fas fa-check me-1"></i> View Receipt'
        });
    @endif
});
</script>
@endsection
