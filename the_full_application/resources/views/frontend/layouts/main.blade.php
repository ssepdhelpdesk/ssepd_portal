<!doctype html>
<html lang="zxx">
   <head>
      <!-- Required Meta Tags -->
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <!-- Plugins CSS -->
      <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/plugins.css') }}">
      <!-- Icon Plugins CSS -->
      <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/iconplugins.css') }}">
      <!-- Style CSS -->
      <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/style.css') }}">
      <!-- Responsive CSS -->
      <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/responsive.css') }}">
      <!-- Theme Dark CSS -->
      <link rel="stylesheet" href="{{ asset('frontend_assets/assets/css/theme-dark.css') }}">
      <!-- Title -->
      <title>@yield('title')</title>
      <!-- Favicon -->
      <link rel="icon" type="image/png" href="{{ asset('frontend_assets/assets/images/favicon.png') }}">
      <link href="{{ asset('frontend_assets/assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
      <style>
         .error {
            color: red !important;
         }
      </style>
      @yield('style')
   </head>
   <body>
      <!-- Pre Loader -->
      <div id="preloader">
         <div id="preloader-area">
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
         </div>
         <div class="preloader-section preloader-left"></div>
         <div class="preloader-section preloader-right"></div>
      </div>
      <!-- End Pre Loader -->
      <!-- Top Header -->
      @include('frontend.layouts.header')
      <!-- Top Header End -->
      <!-- Start Navbar Area -->
      @include('frontend.layouts.navbar')
      <!-- End Navbar Area -->
      <section>        
         @yield('content')
      </section>
      <!-- Footer Area -->
      @include('frontend.layouts.footer')
      <!-- Footer Area End -->      
   </body>
   <!-- Jquery Min JS -->
   <script src="{{ asset('frontend_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('frontend_assets/assets/js/jquery.min.js') }}"></script>
      <!-- Plugins JS -->
      <script src="{{ asset('frontend_assets/assets/js/plugins.js') }}"></script>
      <!-- Custom  JS -->
      <script src="{{ asset('frontend_assets/assets/js/custom.js') }}"></script>
      <script src="{{ asset('frontend_assets/assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
      
      <script>
         $(".select2").select2();
         $(".ajax").select2({
            ajax: {
                url: "https://api.github.com/search/repositories",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page,
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: params.page * 30 < data.total_count,
                        },
                    };
                },
                cache: true,
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
        });
      </script>
   @yield('script')
</html>