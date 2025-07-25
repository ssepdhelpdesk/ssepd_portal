@extends('layouts.app')

@section('content')
<div class="container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: url('https://images.pexels.com/photos/6803543/pexels-photo-6803543.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') no-repeat center center; background-size: cover;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-lg" style="background: rgba(255, 255, 255, 0.9);">
                <div class="card-header text-center bg-primary text-white rounded-top">
                    <h3>{{ __('Reset Password') }}</h3>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label text-primary fw-semibold">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control shadow-sm @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email address">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-primary fw-semibold">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control shadow-sm @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Create a new password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label text-primary fw-semibold">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="form-control shadow-sm" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                        </div>

                        <div class="d-grid mb-0">
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
