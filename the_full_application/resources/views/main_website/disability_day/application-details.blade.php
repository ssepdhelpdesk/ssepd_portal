@extends('main_website.layouts.app')

@section('title', 'Application Details — ' . $registration->registration_no)

@section('style')
<style>
    .details-hero {
        background: linear-gradient(135deg, #0b3954 0%, #087e8b 100%);
        color: #ffffff;
        padding: 35px 0 25px;
    }
    .details-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        margin-top: -25px;
        margin-bottom: 50px;
        overflow: hidden;
    }
    .details-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .stall-status-banner {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    .stall-status-banner.allotted {
        background: #ecfdf5;
        border: 2px solid #10b981;
        color: #065f46;
    }
    .stall-status-banner.pending {
        background: #fefce8;
        border: 2px solid #facc15;
        color: #854d0e;
    }
    .stall-no-badge {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 1.5rem;
        font-weight: 800;
        background: #ffffff;
        padding: 8px 18px;
        border-radius: 6px;
        display: inline-block;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .field-row {
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .field-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.9rem;
    }
    .field-value {
        color: #0f172a;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="details-hero">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge bg-white text-dark mb-1 px-3 py-1 rounded-pill">
                    <i class="fas fa-check-circle text-success me-1"></i> Verified Tracking Session
                </span>
                <h1 class="h3 fw-bold mb-0">Stall Application Details</h1>
                <p class="text-white-50 mb-0 small">International Day of Persons With Disabilities – 2026</p>
            </div>
            <div>
                <form action="{{ route('disability_day.track.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> End Session
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="details-card">

                <div class="details-header">
                    <div>
                        <span class="text-muted small text-uppercase">Registration Number</span>
                        <h4 class="fw-bold mb-0 text-primary font-monospace">{{ $registration->registration_no }}</h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print Application
                        </button>
                    </div>
                </div>

                <div class="p-4">

                    <!-- STALL ALLOTMENT STATUS BANNER -->
                    @if($registration->isAllotted())
                    <div class="stall-status-banner allotted">
                        <div>
                            <div class="small text-uppercase fw-bold"><i class="fas fa-check-circle me-1"></i> Stall Allotment Confirmed</div>
                            <div class="small">Your exhibition space has been officially assigned by the department committee.</div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <span class="text-muted small me-2">Allotted Stalls:</span>
                            @foreach($registration->getAllottedStallNumbers() as $sNo)
                                <span class="stall-no-badge text-success">Stall #{{ $sNo }}</span>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="stall-status-banner pending">
                        <div>
                            <div class="small text-uppercase fw-bold"><i class="fas fa-hourglass-half me-1"></i> Stall Allotment Status</div>
                            <div class="small">Your application is under committee review for stall space allocation.</div>
                        </div>
                        <div>
                            <span class="text-muted small me-2">Allotted Stall:</span>
                            <span class="badge bg-secondary fs-6 px-3 py-2">Not Yet Allotted</span>
                        </div>
                    </div>
                    @endif

                    <!-- EVENT REFERENCE -->
                    <div class="card mb-4 bg-light border-0">
                        <div class="card-body p-3">
                            <div class="row text-center text-md-start">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <span class="text-muted small d-block">Event</span>
                                    <strong class="small">International Day of Persons With Disabilities – 2026</strong>
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <span class="text-muted small d-block">Venue</span>
                                    <strong class="small">S'O'A University</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">Event Dates</span>
                                    <strong class="small">01 Dec 2026 – 03 Dec 2026</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- APPLICANT & ORGANIZATION DETAILS -->
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-building me-1"></i> Organization & Contact Information
                    </h6>
                    <div class="row mb-4">
                        <div class="col-md-6 field-row">
                            <div class="field-label">Organization Name</div>
                            <div class="field-value">{{ $registration->organization_name }}</div>
                        </div>
                        <div class="col-md-6 field-row">
                            <div class="field-label">Contact Person</div>
                            <div class="field-value">{{ $registration->contact_person }}</div>
                        </div>
                        <div class="col-md-6 field-row">
                            <div class="field-label">Registered Email</div>
                            <div class="field-value">{{ $registration->email }}</div>
                        </div>
                        <div class="col-md-6 field-row">
                            <div class="field-label">Mobile Number</div>
                            <div class="field-value">+91 {{ $registration->phone }}</div>
                        </div>
                        <div class="col-12 field-row">
                            <div class="field-label">Purpose of Requirement of Stall</div>
                            <div class="field-value text-break" style="white-space: pre-wrap;">{{ $registration->purpose }}</div>
                        </div>
                    </div>

                    <!-- ADDRESS DETAILS -->
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-map-marker-alt me-1"></i> Registered Address Details
                    </h6>
                    <div class="row mb-4">
                        <div class="col-md-4 field-row">
                            <div class="field-label">AT (Village / Street / Location)</div>
                            <div class="field-value">{{ $registration->at }}</div>
                        </div>
                        <div class="col-md-4 field-row">
                            <div class="field-label">Post Office (PO)</div>
                            <div class="field-value">{{ $registration->po }}</div>
                        </div>
                        <div class="col-md-4 field-row">
                            <div class="field-label">Police Station (PS)</div>
                            <div class="field-value">{{ $registration->ps }}</div>
                        </div>
                        <div class="col-md-4 field-row">
                            <div class="field-label">District</div>
                            <div class="field-value">{{ $registration->district ? $registration->district->district_name : ($registration->district_name ?? '—') }}</div>
                        </div>
                        <div class="col-md-4 field-row">
                            <div class="field-label">State</div>
                            <div class="field-value">{{ $registration->state ? $registration->state->state_name : ($registration->state_name ?? 'Odisha') }}</div>
                        </div>
                        <div class="col-md-4 field-row">
                            <div class="field-label">PIN Code</div>
                            <div class="field-value">{{ $registration->pin }}</div>
                        </div>
                    </div>

                    <!-- SUBMISSION AUDIT -->
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-history me-1"></i> Application Timeline
                    </h6>
                    <div class="row">
                        <div class="col-md-6 field-row">
                            <div class="field-label">Applied Date & Time</div>
                            <div class="field-value">{{ optional($registration->applied_at)->format('d F Y, h:i A') ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 field-row">
                            <div class="field-label">Allotment Status</div>
                            <div class="field-value">
                                @if($registration->isAllotted())
                                    <span class="badge bg-success">Allotted (Stall: {{ $registration->allotted_stall }})</span>
                                @else
                                    <span class="badge bg-secondary">Not Yet Allotted</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                        <a href="{{ route('disability_day.track') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-search me-1"></i> Check Another Registration
                        </a>
                        <form action="{{ route('disability_day.track.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-lock me-1"></i> Log Out & Close Session
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

