@section('title') 
Disability Day 2026 || Participant Details: {{ $participant->registration_no }}
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
    .bg-pink {
        background-color: #d63384 !important;
    }
    .bg-purple {
        background-color: #6f42c1 !important;
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
                    <li class="breadcrumb-item"><a href="{{ route('admin.disability_day_participant.index') }}">Disability Day 2026</a></li>
                    <li class="breadcrumb-item active">{{ $participant->registration_no }}</li>
                </ol>
            </div>
        </div>
        <div class="col-md-5 align-self-center text-end">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.disability_day_participant.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Registrations
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @include('dashboard.component.message')

    <div class="row">
        <!-- Main Column -->
        <div class="col-lg-8">

            <!-- Card 1: Registration Header & Participant Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Official Registration No</span>
                        <h4 class="mb-0 text-primary font-monospace fw-bold">{{ $participant->registration_no }}</h4>
                    </div>
                    <div>
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i> Active Registration
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Participant Full Name</div>
                            <div class="detail-val fw-bold text-dark fs-5">{{ $participant->participant_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Special School / Institute</div>
                            <div class="detail-val fw-semibold text-primary">
                                <i class="fas fa-school me-1 text-secondary"></i> {{ $participant->school_name }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Gender</div>
                            <div class="detail-val">
                                @if($participant->gender === 'Male')
                                    <span class="badge bg-info text-dark px-2 py-1"><i class="fas fa-mars me-1"></i> Male</span>
                                @elseif($participant->gender === 'Female')
                                    <span class="badge bg-pink text-white px-2 py-1"><i class="fas fa-venus me-1"></i> Female</span>
                                @else
                                    <span class="badge bg-secondary text-white px-2 py-1"><i class="fas fa-genderless me-1"></i> Other</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">T-Shirt Size</div>
                            <div class="detail-val">
                                <span class="badge bg-dark font-monospace fs-6 px-3 py-1">{{ $participant->tshirt_size }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Declared Escorts</div>
                            <div class="detail-val fw-bold">
                                {{ $participant->number_of_escorts }} Escort(s)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Financial Year</div>
                            <div class="detail-val font-monospace">{{ $participant->financial_year ?: '2026-27' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Complete Address of the Participant -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-dark fw-bold">
                        <i class="fas fa-map-marked-alt text-primary me-2"></i> Complete Participant Address
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Address Type Badge -->
                    <div class="mb-3">
                        <span class="detail-label d-block">Address Classification:</span>
                        @if($participant->address_type === 1)
                            <span class="badge bg-success fs-7 px-3 py-2"><i class="fas fa-tree me-1"></i> Village / Block Area (Rural)</span>
                        @else
                            <span class="badge bg-primary fs-7 px-3 py-2"><i class="fas fa-city me-1"></i> Municipality / ULB Area (Urban)</span>
                        @endif
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">Administrative Location Hierarchy</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">State</div>
                            <div class="detail-val">{{ $participant->state_name ?: ($participant->state ? $participant->state->state_name : 'Odisha') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">District</div>
                            <div class="detail-val fw-semibold">{{ $participant->district_name ?: ($participant->district ? $participant->district->district_name : '—') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">PIN Code</div>
                            <div class="detail-val font-monospace">{{ $participant->pin }}</div>
                        </div>

                        @if($participant->address_type === 1)
                            <div class="col-md-4">
                                <div class="detail-label">Block</div>
                                <div class="detail-val">{{ $participant->block_name ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-label">Grampanchayat (GP)</div>
                                <div class="detail-val">{{ $participant->gp_name ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-label">Village</div>
                                <div class="detail-val">{{ $participant->village_name ?: '—' }}</div>
                            </div>
                        @else
                            <div class="col-md-8">
                                <div class="detail-label">Municipality / Urban Local Body (ULB)</div>
                                <div class="detail-val">{{ $participant->municipality_name ?: '—' }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 3: Associated Escorts Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 text-dark fw-bold">
                        <i class="fas fa-user-friends text-primary me-2"></i> Accompanying Escort Details
                    </h5>
                    <span class="badge bg-light text-dark border">Total: {{ $participant->escorts->count() }} Record(s)</span>
                </div>
                <div class="card-body p-0">
                    @if($participant->escorts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Escort Full Name</th>
                                        <th>Mobile Number</th>
                                        <th>Email Address</th>
                                        <th>Relationship with Participant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($participant->escorts as $escort)
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $escort->escort_index }}</td>
                                            <td class="fw-semibold text-dark">{{ $escort->name }}</td>
                                            <td class="font-monospace">
                                                <i class="fas fa-phone-alt text-success me-1"></i> +91 {{ $escort->mobile }}
                                            </td>
                                            <td>
                                                @if(!empty($escort->email))
                                                    <a href="mailto:{{ $escort->email }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope text-primary me-1"></i> {{ $escort->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted small">Not provided</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1">
                                                    {{ $escort->relationship }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-walking fa-2x mb-2 text-secondary"></i>
                            <p class="mb-0">This participant is registered without any accompanying escorts (0 Escorts declared).</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 4: Participation Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-dark fw-bold">
                        <i class="fas fa-trophy text-primary me-2"></i> Event Participation Category
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Participation Category</div>
                            <div class="detail-val">
                                @if($participant->participation_in === 'Cultural')
                                    <span class="badge bg-purple fs-6 px-3 py-2"><i class="fas fa-music me-1"></i> Cultural (Dance, Music, Drama, Art)</span>
                                @elseif($participant->participation_in === 'Sports')
                                    <span class="badge bg-success fs-6 px-3 py-2"><i class="fas fa-running me-1"></i> Sports (Athletics, Para-Sports)</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="fas fa-trophy me-1"></i> Others</span>
                                @endif
                            </div>
                        </div>

                        @if($participant->participation_in === 'Others' && !empty($participant->participation_other))
                            <div class="col-md-6">
                                <div class="detail-label">Specific Participation Details</div>
                                <div class="detail-val fw-semibold text-dark">{{ $participant->participation_other }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar Metadata Column -->
        <div class="col-lg-4">

            <!-- Event Reference Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-1"></i> Event Details</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Event:</span>
                            <span class="fw-bold text-end">Disability Day 2026</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Dates:</span>
                            <span class="fw-semibold text-end">01 Dec – 03 Dec 2026</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Venue:</span>
                            <span class="fw-semibold text-end">S'O'A University, Bhubaneswar</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Organised By:</span>
                            <span class="fw-semibold text-end">SSEPD Dept, Govt of Odisha</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Audit Trail & System Timestamps Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-info-circle text-primary me-1"></i> System Metadata</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Registration Status:</span>
                            <span class="badge bg-success">Active / Registered</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Applied Date (IST):</span>
                            <span class="fw-bold font-monospace">{{ $participant->applied_at ? $participant->applied_at->format('d M Y, h:i A') : 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Created Timestamp:</span>
                            <span class="font-monospace text-muted">{{ $participant->created_at ? $participant->created_at->format('d-m-Y H:i:s') : 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Record ID:</span>
                            <span class="font-monospace">#{{ $participant->id }}</span>
                        </li>
                    </ul>

                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print Registration Acknowledgment
                        </button>
                        <a href="{{ route('admin.disability_day_participant.index') }}" class="btn btn-light border">
                            <i class="fas fa-arrow-left me-1"></i> Return to Participant List
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
