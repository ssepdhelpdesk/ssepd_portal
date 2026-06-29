@extends('layouts.app')
@section('content')
<div class="container-fluid" style="min-height: 100vh; background: url('https://images.pexels.com/photos/6803543/pexels-photo-6803543.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') no-repeat center center; background-size: cover; position: relative;">
   <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
      <div class="col-md-6 col-lg-4">
         <div class="card shadow-lg border-0 rounded-lg" style="background: rgba(255, 255, 255, 0.9);">
            <div class="card-header text-center bg-primary text-white rounded-top">
               <h3>{{ __('Register') }}</h3>
            </div>
            <div class="card-body p-4">
               <form method="POST" action="{{ route('register') }}">
                  @csrf
                  <div class="mb-3">
                     <label for="name" class="form-label text-primary fw-semibold">{{ __('Name') }}</label>
                     <input id="name" type="text" class="form-control shadow-sm @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your name">
                     @error('name')
                     <span class="invalid-feedback" role="alert">
                     <strong>{{ $message }}</strong>
                     </span>
                     @enderror
                  </div>
                  <div class="mb-3">
                     <label for="user_id" class="form-label text-primary fw-semibold">{{ __('User Id') }}</label>
                     <input id="user_id" type="text" class="form-control shadow-sm @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" required autocomplete="user_id" placeholder="Enter your user id">
                     @error('user_id')
                     <span class="invalid-feedback" role="alert">
                     <strong>{{ $message }}</strong>
                     </span>
                     @enderror
                  </div>
                  <div class="mb-3">
                     <label for="email" class="form-label text-primary fw-semibold">{{ __('Email Address') }}</label>
                     <input id="email" type="email" class="form-control shadow-sm @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email">
                     @error('email')
                     <span class="invalid-feedback" role="alert">
                     <strong>{{ $message }}</strong>
                     </span>
                     @enderror
                  </div>
                  <div class="mb-3">
                     <label for="password" class="form-label text-primary fw-semibold">{{ __('Password') }}</label>
                     <input id="password" type="password" class="form-control shadow-sm @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Create a password">
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
                  <div class="mb-3">
                     <label for="captcha" class="form-label text-primary fw-semibold">{{ __('Captcha') }}</label>
                     <div class="captcha d-flex align-items-center mb-3">
                        <span>{!! captcha_img() !!}</span>
                        <button type="button" class="btn btn-success btn-refresh ms-2"><i class="fa fa-refresh"></i></button>
                     </div>
                     <input id="captcha" type="text" class="form-control {{ $errors->has('captcha') ? ' has-error' : '' }}" name="captcha" required placeholder="Enter Captcha" style="margin-top: 10px;">
                     @if ($errors->has('captcha'))
                     <span class="help-block" role="alert">
                     <strong>{{ $errors->first('captcha') }}</strong>
                     </span>
                     @endif
                  </div>
                  <div class="d-grid gap-2 mb-0">
                     <button type="submit" class="btn btn-primary fw-bold shadow-sm">{{ __('Register') }}</button>
                  </div>
               </form>
            </div>
            <div class="card-footer text-center bg-light rounded-bottom">
               <span class="text-muted">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold">Login here</a></span>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection