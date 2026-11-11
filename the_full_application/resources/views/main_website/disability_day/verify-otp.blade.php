@extends('main_website.layouts.app')

@section('title', 'Verify OTP — Application Tracking')

@section('style')
<style>
    .track-hero-banner {
        background: linear-gradient(135deg, #0b3954 0%, #087e8b 100%);
        color: #ffffff;
        padding: 40px 0 30px;
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
    .otp-input-large {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 1.8rem;
        letter-spacing: 8px;
        text-align: center;
        font-weight: 700;
        color: #0b3954;
    }
</style>
@endsection

@section('content')
<div class="track-hero-banner text-center">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Verify One-Time Password (OTP)</h1>
        <p class="text-white-50 mb-0">Security verification for Application Tracking</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="track-card">
                <div class="p-4 text-center">

                    <div class="mb-3">
                        <span class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="fas fa-shield-alt text-primary" style="font-size: 28px;"></i>
                        </span>
                    </div>

                    <h5 class="fw-bold mb-1">Enter Verification Code</h5>
                    <p class="text-muted small mb-3">
                        A 6-digit code has been sent to:<br>
                        <strong class="text-dark">{{ $tracking['masked_email'] ?? 'your email' }}</strong>
                    </p>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show text-start" role="alert">
                        <i class="fas fa-info-circle me-1"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @error('otp')
                    <div class="alert alert-danger text-start">
                        <i class="fas fa-times-circle me-1"></i> {{ $message }}
                    </div>
                    @enderror

                    <form action="{{ route('disability_day.track.verify_otp.post') }}" method="POST" id="otpForm">
                        @csrf

                        <div class="mb-3">
                            <input type="text" 
                                   name="otp" 
                                   id="otpInput" 
                                   class="form-control form-control-lg otp-input-large" 
                                   maxlength="6" 
                                   pattern="[0-9]{6}" 
                                   placeholder="&bull;&bull;&bull;&bull;&bull;&bull;" 
                                   required 
                                   autofocus 
                                   autocomplete="one-time-code">
                            <div class="form-text small text-muted mt-2">
                                <i class="fas fa-clock me-1"></i> Code expires in <span id="countdownTimer" class="fw-bold text-danger">05:00</span>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnVerify">
                                <i class="fas fa-check-circle me-1"></i> Verify & View Application
                            </button>
                        </div>
                    </form>

                    <!-- Resend OTP Form -->
                    <form action="{{ route('disability_day.track.resend_otp') }}" method="POST" id="resendForm" class="mt-3">
                        @csrf
                        <div class="small text-muted mb-2">Didn't receive the email code?</div>
                        <button type="submit" class="btn btn-link btn-sm text-decoration-none fw-semibold" id="btnResend" disabled>
                            <i class="fas fa-redo me-1"></i> Resend OTP (<span id="resendTimer">60</span>s)
                        </button>
                    </form>

                    <div class="text-center pt-3 mt-3 border-top">
                        <a href="{{ route('disability_day.track') }}" class="small text-muted">
                            <i class="fas fa-arrow-left me-1"></i> Change Registration Number / Email
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 5-minute expiry countdown
    let expirySeconds = 300;
    const countdownEl = document.getElementById('countdownTimer');
    const timerInterval = setInterval(() => {
        expirySeconds--;
        if (expirySeconds <= 0) {
            clearInterval(timerInterval);
            if (countdownEl) {
                countdownEl.innerText = 'EXPIRED';
            }
        } else {
            const minutes = String(Math.floor(expirySeconds / 60)).padStart(2, '0');
            const seconds = String(expirySeconds % 60).padStart(2, '0');
            if (countdownEl) {
                countdownEl.innerText = `${minutes}:${seconds}`;
            }
        }
    }, 1000);

    // 60-second Resend OTP Cooldown
    let resendSeconds = 60;
    const btnResend = document.getElementById('btnResend');
    const resendTimerEl = document.getElementById('resendTimer');

    const resendInterval = setInterval(() => {
        resendSeconds--;
        if (resendSeconds <= 0) {
            clearInterval(resendInterval);
            if (btnResend) {
                btnResend.disabled = false;
                btnResend.innerHTML = '<i class="fas fa-redo me-1"></i> Resend OTP Now';
            }
        } else {
            if (resendTimerEl) {
                resendTimerEl.innerText = resendSeconds;
            }
        }
    }, 1000);

    // Auto-filter non-digits
    const otpInput = document.getElementById('otpInput');
    if (otpInput) {
        otpInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    document.getElementById('otpForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('btnVerify');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Verifying...';
        }
    });
});
</script>
@endsection

