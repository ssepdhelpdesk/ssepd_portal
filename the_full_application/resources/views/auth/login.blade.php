@extends('layouts.app')
@section('content')
<div class="container-fluid" style="min-height: 100vh; background: url('https://images.pexels.com/photos/577585/pexels-photo-577585.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') no-repeat center center; background-size: cover; position: relative;">
   <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
      <div class="col-md-6 col-lg-4">
         <div class="card shadow-lg border-0 rounded-lg" style="background: rgba(255, 255, 255, 0.9);">
            <div class="card-header text-center bg-primary text-white rounded-top">
               <h3>{{ __('Login') }}</h3>
            </div>
            <div class="card-body p-4">
               <form method="POST" action="{{ route('login') }}">
                  @csrf
                  <div class="mb-3">
                     <label for="username" class="form-label text-primary fw-semibold">{{ __('Email or User ID') }}</label>
                     <input id="username" type="text" class="form-control shadow-sm @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autofocus placeholder="Enter your email or user ID">
                     @error('username')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                     @enderror
                  </div>
                  <div class="mb-3">
                     <label for="password" class="form-label text-primary fw-semibold">{{ __('Password') }}</label>
                     <input id="password" type="password" class="form-control shadow-sm @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">
                     @error('password')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                     @enderror
                  </div>
                  <div class="mb-3">
                    <label for="captcha" class="form-label text-primary fw-semibold">{{ __('Captcha') }}</label>
                    <div class="captcha d-flex align-items-center mb-3">
                      <span id="captcha-img">{!! captcha_img('math_blue') !!}</span>
                      <button type="button" class="btn btn-success btn-refresh ms-2" id="reload-captcha">
                        <i class="fa fa-refresh"></i>
                     </button>
                  </div>
                  <input id="captcha" type="text" class="form-control {{ $errors->has('captcha') ? ' has-error' : '' }}" name="captcha" required placeholder="Enter Answer" style="margin-top: 10px;">
                  @if ($errors->has('captcha'))
                  <span class="help-block" role="alert">
                     <strong>{{ $errors->first('captcha') }}</strong>
                  </span>
                  @endif
               </div>
               <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                  <label class="form-check-label text-secondary" for="remember">
                     {{ __('Remember Me') }}
                  </label>
               </div>
               <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary fw-bold shadow-sm">{{ __('Login') }}</button>
               </div>
               <div class="text-center mt-3">
                  @if (Route::has('password.request'))
                  <a class="text-primary-600 fw-semibold" href="{{ route('password.request') }}">
                     {{ __('Forgot Your Password?') }}
                  </a>
                  @endif
               </div>
            </form>
         </div>
         <div class="card-footer text-center bg-light rounded-bottom">
            <span class="text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-bold">Sign up</a></span>
         </div>
      </div>
   </div>
</div>
</div>
@endsection
