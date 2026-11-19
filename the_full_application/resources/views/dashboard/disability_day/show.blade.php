@section('title') 
Disability Day 2026 || View Registration: {{ $registration->registration_no }}
@endsection 
@extends('dashboard.layouts.main')

@section('style')
<style>
    .font-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    .detail-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.88rem;
        margin-bottom: 2px;
    }
    .detail-val {
        font-weight: 500;
        color: #1e293b;
        font-size: 0.95rem;
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
                    <li class="breadcrumb-item active">{{ $registration->registration_no }}</li>
                </ol>
            </div>
        </div>
        <div class="col-md-5 align-self-center text-end">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.disability_day.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Registrations
                </a>
                @can('disability-day-edit')
                <a href="{{ route('admin.disability_day.edit', $registration->id) }}" class="btn btn-warning btn-sm text-dark">
                    <i class="fas fa-edit me-1"></i> Edit Details
                </a>
                @endcan
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @include('dashboard.component.message')

    <div class="row">
        <!-- Main Details Column -->
        <div class="col-lg-8">

            <!-- Card 1: Registration Header & Stall Allotment -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Application Reference</span>
                        <h4 class="mb-0 text-primary font-monospace fw-bold">{{ $registration->registration_no }}</h4>
                    </div>
                    <div>
                        @if($registration->isAllotted())
                            <div class="d-flex flex-wrap gap-1 justify-content-end">
                                @foreach($registration->getAllottedStallNumbers() as $sNum)
                                    <span class="badge bg-success fs-6 px-3 py-2 font-monospace">
                                        <i class="fas fa-store me-1"></i> Stall #{{ $sNum }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                <i class="fas fa-hourglass-half me-1"></i> Not Yet Allotted
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="detail-label">Event</div>
                            <div class="detail-val fw-bold">International Day of Persons With Disabilities – 2026</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Venue</div>
                            <div class="detail-val">S'O'A University</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">Event Dates</div>
                            <div class="detail-val">01 Dec – 03 Dec 2026</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">Financial Year</div>
                            <div class="detail-val font-monospace fw-bold text-primary">{{ $registration->financial_year ?? '2026-27' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Organization Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-building text-primary me-2"></i> Section A — Organization & Contact Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Organization Name</div>
                            <div class="detail-val fs-6 fw-bold text-dark">{{ $registration->organization_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Contact Person</div>
                            <div class="detail-val fs-6 text-dark">{{ $registration->contact_person }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Registered Email</div>
                            <div class="detail-val">
                                <a href="mailto:{{ $registration->email }}"><i class="fas fa-envelope me-1 text-muted"></i>{{ $registration->email }}</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Phone Number</div>
                            <div class="detail-val font-monospace">
                                <i class="fas fa-phone-alt me-1 text-muted"></i>+91 {{ $registration->phone }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Purpose of Requirement of Stall</div>
                            <div class="p-3 bg-light rounded text-dark mt-1" style="white-space: pre-wrap;">{{ $registration->purpose }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Address Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i> Section B — Address Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">AT (Village / Street)</div>
                            <div class="detail-val">{{ $registration->at }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Post Office (PO)</div>
                            <div class="detail-val">{{ $registration->po }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Police Station (PS)</div>
                            <div class="detail-val">{{ $registration->ps }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">District</div>
                            <div class="detail-val fw-semibold">{{ $registration->district ? $registration->district->district_name : ($registration->district_name ?? '—') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">State</div>
                            <div class="detail-val fw-semibold">{{ $registration->state ? $registration->state->state_name : ($registration->state_name ?? 'Odisha') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">PIN Code</div>
                            <div class="detail-val font-monospace fw-semibold">{{ $registration->pin }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar / Action Column -->
        <div class="col-lg-4">

            <!-- Stall Allotment Control Card -->
            @can('disability-day-allot-stall')
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-store-alt me-2"></i> Manage Stall Allotment
                    </h5>
                    <span class="badge bg-light text-primary">{{ count($availableStalls) }} Available</span>
                </div>
                <div class="card-body">
                    @php
                        $currentStallNumbers = $registration->getAllottedStallNumbers();
                    @endphp

                    @if(!empty($currentStallNumbers))
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted mb-1">Currently Allotted Stalls ({{ count($currentStallNumbers) }}):</label>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @foreach($currentStallNumbers as $sNo)
                                    <form action="{{ route('admin.disability_day.allot_stall', $registration->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove Stall #{{ $sNo }} from this registration?')">
                                        @csrf
                                        <input type="hidden" name="remove_stall" value="{{ $sNo }}">
                                        <span class="badge bg-success font-monospace fs-7 p-2 d-inline-flex align-items-center">
                                            <i class="fas fa-store me-1"></i> Stall #{{ $sNo }}
                                            <button type="submit" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" title="Remove this stall"></button>
                                        </span>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary py-2 px-3 small mb-3">
                            <i class="fas fa-info-circle me-1"></i> <strong>Status:</strong> Not yet allotted any stall.
                        </div>
                    @endif

                    @if(empty($availableStalls) && empty($currentStallNumbers))
                        <div class="alert alert-danger py-2 px-3 small mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i> <strong>Capacity Reached:</strong> All 100 stalls are assigned. No stalls available.
                        </div>
                    @endif

                    <form action="{{ route('admin.disability_day.allot_stall', $registration->id) }}" method="POST" id="showAllotStallForm">
                        @csrf
                        <div class="mb-3">
                            <label for="allotted_stalls" class="form-label fw-semibold">
                                Assign Stalls (1 to 100) — Multi-select
                            </label>
                            <select class="form-select" 
                                    id="allotted_stalls" 
                                    name="stalls[]" 
                                    multiple="multiple" 
                                    style="width: 100%;" 
                                    {{ empty($availableStalls) && empty($currentStallNumbers) ? 'disabled' : '' }}>
                                @php
                                    $combinedStalls = array_values(array_unique(array_merge($currentStallNumbers, $availableStalls)));
                                    sort($combinedStalls);
                                @endphp
                                @foreach($combinedStalls as $stallNum)
                                    @php $isAssigned = in_array($stallNum, $currentStallNumbers, true); @endphp
                                    <option value="{{ $stallNum }}" {{ $isAssigned ? 'selected' : '' }}>
                                        Stall #{{ $stallNum }}{{ $isAssigned ? ' (Currently Assigned)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small mt-1">
                                <i class="fas fa-info-circle me-1"></i> Select one or more available stalls. Existing assignments remain checked.
                            </div>
                            @error('stalls')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success" {{ empty($availableStalls) && empty($currentStallNumbers) ? 'disabled' : '' }}>
                                <i class="fas fa-save me-1"></i> Save Stall Allotments
                            </button>
                        </div>
                    </form>

                    @if(!empty($currentStallNumbers))
                    <div class="mt-2 text-center">
                        <form action="{{ route('admin.disability_day.allot_stall', $registration->id) }}" method="POST" onsubmit="return confirm('Clear ALL stall allotments for this registration?')">
                            @csrf
                            <input type="hidden" name="clear_all" value="1">
                            <button type="submit" class="btn btn-link text-danger btn-sm text-decoration-none p-0">
                                <i class="fas fa-times-circle me-1"></i> Clear All Allotted Stalls
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endcan

            <!-- Email & Communications Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-paper-plane text-primary me-2"></i> Communications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="fw-semibold small text-dark d-block mb-1">Registration Confirmation Email:</label>
                        <p class="small text-muted mb-2">
                            Resend the initial registration receipt email containing the Registration Number to <strong>{{ $registration->email }}</strong>.
                        </p>
                        <form action="{{ route('admin.disability_day.resend_email', $registration->id) }}" method="POST">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Send registration confirmation email to {{ $registration->email }}?')">
                                    <i class="fas fa-envelope me-1"></i> Resend Registration Email
                                </button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <label class="fw-semibold small text-dark d-block mb-1">Stall Allotment Confirmation Email:</label>
                        <p class="small text-muted mb-2">
                            Send the official stall allotment notification email with assigned stall details to <strong>{{ $registration->email }}</strong>.
                        </p>
                        <form action="{{ route('admin.disability_day.send_allotment_email', $registration->id) }}" method="POST" onsubmit="return confirm('Send official Stall Allotment Confirmation email to {{ $registration->email }} for allotted stalls?')">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-sm" {{ !$registration->isAllotted() ? 'disabled' : '' }}>
                                    <i class="fas fa-paper-plane me-1"></i> Send Allotment Email
                                </button>
                            </div>
                            @if(!$registration->isAllotted())
                                <div class="text-muted small text-center mt-1"><i class="fas fa-exclamation-circle me-1"></i> Allot at least one stall to enable sending allotment confirmation.</div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Audit Trail Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-history text-muted me-2"></i> Audit Information
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <span class="text-muted d-block">Applied Date & Time:</span>
                            <strong>{{ optional($registration->applied_at)->format('d-M-Y h:i:s A') ?? 'N/A' }}</strong>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted d-block">Record Created:</span>
                            <span>{{ $registration->created_at ? $registration->created_at->format('d-M-Y h:i:s A') : 'N/A' }}</span>
                            @if($registration->creator)
                            <span class="d-block text-muted">By: {{ $registration->creator->name }}</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <span class="text-muted d-block">Last Updated:</span>
                            <span>{{ $registration->updated_at ? $registration->updated_at->format('d-M-Y h:i:s A') : 'N/A' }}</span>
                            @if($registration->updater)
                            <span class="d-block text-muted">By: {{ $registration->updater->name }}</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Danger Zone -->
            @can('disability-day-delete')
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h6 class="text-danger fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Delete Application</h6>
                    <p class="small text-muted mb-2">Soft-delete this registration record from active listings.</p>
                    <form action="{{ route('admin.disability_day.destroy', $registration->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete registration {{ $registration->registration_no }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="fas fa-trash-alt me-1"></i> Delete Registration
                        </button>
                    </form>
                </div>
            </div>
            @endcan

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    $('#allotted_stalls').select2({
        placeholder: '-- Select One or More Stalls (1 - 100) --',
        allowClear: true,
        closeOnSelect: false,
        width: '100%'
    });

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Allotment Updated',
        text: {!! json_encode(session('success')) !!},
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Allotment Error',
        text: {!! json_encode(session('error')) !!}
    });
    @endif
});
</script>
@endsection

