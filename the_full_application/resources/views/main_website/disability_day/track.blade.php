@extends('main_website.layouts.app')

@section('title', 'Track Stall Application — International Day of Persons With Disabilities 2026')
@section('meta_description', 'Track your stall registration and allotment status for the International Day of Persons With Disabilities 2026.')

@section('style')
<style>
    .track-hero-banner {
        background: linear-gradient(135deg, #0b3954 0%, #087e8b 100%);
        color: #ffffff;
        padding: 40px 0 30px;
        position: relative;
    }
    .track-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        margin-top: -25px;
        margin-bottom: 50px;
        overflow: hidden;
    }
    .track-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 20px 24px;
    }
</style>
@endsection

@section('content')
<div class="track-hero-banner">
    <div class="container text-center">
        <span class="badge bg-white text-dark mb-2 px-3 py-2 rounded-pill">
            <i class="fas fa-search me-1"></i> Public Tracking Portal
        </span>
        <h1 class="h3 fw-bold mb-1">Track Stall Application Status</h1>
        <p class="text-white-50 mb-0">International Day of Persons With Disabilities – 2026 &bull; SSEPD Odisha</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="track-card">
                <div class="track-card-header text-center">
                    <h5 class="fw-bold mb-1 text-primary">Verify Application Credentials</h5>
                    <p class="text-muted small mb-0">Enter your Registration Number and Registered Email to receive an OTP</p>
                </div>
                <div class="p-4">

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-1"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('disability_day.track.send_otp') }}" method="POST" id="trackingForm">
                        @csrf

                        <div class="mb-3">
                            <label for="registration_no" class="form-label fw-semibold">
                                Registration Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('registration_no') is-invalid @enderror font-monospace" 
                                   id="registration_no" 
                                   name="registration_no" 
                                   value="{{ old('registration_no') }}" 
                                   placeholder="e.g. SSEPD/DISABILITYDAY/01/12/2026/123456" 
                                   required 
                                   autofocus>
                            @error('registration_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Case-sensitive format as received during submission.</div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">
                                Registered Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="e.g. contact@organization.org" 
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">A one-time verification code (OTP) will be dispatched to this email.</div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSendOtp">
                                <i class="fas fa-key me-1"></i> Generate Verification OTP
                            </button>
                        </div>
                    </form>

                    <div class="text-center pt-3 border-top">
                        <span class="text-muted small">Need to submit a new stall application?</span>
                        <a href="{{ route('disability_day.registration') }}" class="small fw-semibold ms-1">Register Stall Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.getElementById('trackingForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('btnSendOtp');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Generating OTP...';
    }
});
</script>
@endsection

