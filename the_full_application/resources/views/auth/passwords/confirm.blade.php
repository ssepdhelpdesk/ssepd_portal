@extends('layouts.app')

@section('content')
<div class="container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: url('https://images.pexels.com/photos/6803543/pexels-photo-6803543.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') no-repeat center center; background-size: cover;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-lg" style="background: rgba(255, 255, 255, 0.9);">
                <div class="card-header text-center bg-info text-white rounded-top">
                    <h3>{{ __('Confirm Password') }}</h3>
                </div>

                <div class="card-body p-4">
                    <p class="text-info fw-semibold">{{ __('Please confirm your password before continuing.') }}</p>

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control shadow-sm @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-info fw-bold shadow-sm">
                                    {{ __('Confirm Password') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link text-info" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
