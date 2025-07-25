<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- Favicon icon -->
      <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('dashboard_assets/assets/images/favicon.png') }}">
      <title>{{ __('Login') }}</title>
      <!-- page css -->
      <link href="{{ asset('dashboard_assets/dist/css/pages/login-register-lock.css') }}" rel="stylesheet">
      <!-- Custom CSS -->
      <link href="{{ asset('dashboard_assets/dist/css/style.min.css') }}" rel="stylesheet">
      <link
         rel="stylesheet"
         href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
         integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
         crossorigin="anonymous"
         referrerpolicy="no-referrer"
         />
      <style type="text/css">
         .invalid-feedback{
         color: red;
         }
      </style>
      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <body class="skin-default card-no-border">
      <!-- ============================================================== -->
      <!-- Preloader - style you can find in spinners.css -->
      <!-- ============================================================== -->
      <div class="preloader">
         <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">LARAVEL</p>
         </div>
      </div>
      <!-- ============================================================== -->
      <!-- Main wrapper - style you can find in pages.scss -->
      <!-- ============================================================== -->
      <section id="wrapper">
         <div class="login-register" style="background-image: url('{{ asset('dashboard_assets/assets/images/background/login-register.jpg') }}');">
            <div class="login-box card">
               <div class="card-body">
                  <form class="form-horizontal form-material" id="loginform" method="POST" action="{{ route('login') }}">
                     @csrf
                     <h3 class="text-center m-b-20">Sign In</h3>
                     <div class="form-group ">
                        <div class="col-xs-12">
                           <input class="form-control shadow-sm @error('username') is-invalid @enderror" type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Enter your email or user ID"> 
                        </div>
                        @error('username')
                        <span class="invalid-feedback" role="alert">
                        <strong class="invalid-feedback">{{ $message }}</strong>
                        </span>
                        @enderror
                     </div>
                     <div class="form-group">
                        <div class="col-xs-12">
                           <input class="form-control shadow-sm @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password"> 
                        </div>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                        <strong class="invalid-feedback">{{ $message }}</strong>
                        </span>
                        @enderror
                     </div>
                     <div class="form-group row">
                        <div class="col-md-12">
                           <div class="d-flex no-block align-items-center">
                              <div class="form-check">
                                 <input type="checkbox" class="form-check-input" id="remember" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                 <label class="form-check-label" for="remember">Remember me</label>
                              </div>
                              <div class="ms-auto">
                                 @if (Route::has('password.request'))
                                 <a href="{{ route('password.request') }}" id="to-recover" class="text-muted"><i class="fas fa-lock m-r-5"></i> Forgot Your Password?</a> 
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="form-group text-center">
                        <div class="col-xs-12 p-b-20">
                           <button class="btn w-100 btn-lg btn-info btn-rounded text-white" type="submit">Log In</button>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                           <div class="social">
                              <button class="btn  btn-facebook" data-bs-toggle="tooltip" title="Login with Facebook"> <i aria-hidden="true" class="fab fa-facebook-f"></i> </button>
                              <button class="btn btn-googleplus" data-bs-toggle="tooltip" title="Login with Google"> <i aria-hidden="true" class="fab fa-google-plus-g"></i> </button>
                           </div>
                        </div>
                     </div>
                     <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                           Don't have an account? <a href="{{ route('register') }}" class="text-info m-l-5"><b>Sign Up</b></a>
                        </div>
                     </div>
                  </form>
                  <form class="form-horizontal" id="recoverform" action="index.html">
                     <div class="form-group ">
                        <div class="col-xs-12">
                           <h3>Recover Password</h3>
                           <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                        </div>
                     </div>
                     <div class="form-group ">
                        <div class="col-xs-12">
                           <input class="form-control" type="text" required="" placeholder="Email"> 
                        </div>
                     </div>
                     <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                           <button class="btn btn-primary btn-lg w-100 text-uppercase waves-effect waves-light" type="submit">Reset</button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </section>
      <!-- ============================================================== -->
      <!-- End Wrapper -->
      <!-- ============================================================== -->
      <!-- ============================================================== -->
      <!-- All Jquery -->
      <!-- ============================================================== -->
      <script src="{{ asset('dashboard_assets/assets/node_modules/jquery/dist/jquery.min.js') }}"></script>
      <!-- Bootstrap tether Core JavaScript -->
      <script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
      <!--Custom JavaScript -->
      <script type="text/javascript">
         $(function() {
           $(".preloader").fadeOut();
         });
         $(function() {
           $('[data-bs-toggle="tooltip"]').tooltip()
         });
         // ============================================================== 
         // Login and Recover Password 
         // ============================================================== 
         $('#to-recover').on("click", function() {
           $("#loginform").slideUp();
           $("#recoverform").fadeIn();
         });
      </script>
   </body>
</html>