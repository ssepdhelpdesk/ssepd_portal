<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>{{ config('app.name', 'Laravel') }}</title>
      <!-- Fonts -->
      <link rel="dns-prefetch" href="//fonts.bunny.net">
      <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
      <!-- Scripts -->
      @vite(['resources/sass/app.scss', 'resources/js/app.js'])
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <link href="{{ asset('dashboard_assets/dist/css/pages/login-register-lock.css') }}" rel="stylesheet">
      <link href="{{ asset('dashboard_assets/dist/css/style.min.css') }}" rel="stylesheet">
      <style>
         .help-block {
         width: 100%;
         margin-top: .25rem;
         font-size: .875em;
         color: #dc3545;
         }
      </style>
   </head>
   <body class="skin-default card-no-border">
      <div class="preloader">
         <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">LARAVEL</p>
         </div>
      </div>
      <div id="app">
         <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
               <a class="navbar-brand" href="{{ url('/') }}">
               SIEP 2.0
               </a>
               <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
               <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <!-- Left Side Of Navbar -->
                  <ul class="navbar-nav me-auto">
                  </ul>
                  <!-- Right Side Of Navbar -->
                  <ul class="navbar-nav ms-auto">
                     <!-- Authentication Links -->
                     @guest
                     @if (Route::has('login'))
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                     </li>
                     @endif
                     @if (Route::has('register'))
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                     </li>
                     @endif
                     @else
                     <li><a class="nav-link" href="{{ route('admin.users.index') }}">Manage Users</a></li>
                     <li><a class="nav-link" href="{{ route('admin.roles.index') }}">Manage Role</a></li>
                     <li><a class="nav-link" href="{{ route('admin.products.index') }}">Manage Product</a></li>
                     <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                           <a class="dropdown-item" href="{{ route('logout') }}"
                              onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                           {{ __('Logout') }}
                           </a>
                           <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                              @csrf
                           </form>
                        </div>
                     </li>
                     @endguest
                  </ul>
               </div>
            </div>
         </nav>
         <main class="py-4">
            <div class="container">
               <div class="row justify-content-center">
                  <div class="col-md-12">
                     <div class="card">
                        <div class="card-body">
                           @yield('content')
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </main>
      </div>
   </body>
   <script src="{{ asset('dashboard_assets/assets/node_modules/jquery/dist/jquery.min.js') }}"></script>
   <!-- Bootstrap tether Core JavaScript -->
   <script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
   <script type="text/javascript">
      $(".btn-refresh").click(function(){
        $.ajax({
           type:'GET',      
           url:'/refresh_captcha',      
           success:function(data){      
              $(".captcha span").html(data.captcha);      
           }      
        });      
      });
      $(function() {
           $(".preloader").fadeOut();
         });
         $(function() {
           $('[data-bs-toggle="tooltip"]').tooltip()
         });
   </script>
</html>