<!DOCTYPE html> 
<html lang="en">
	<head>
	
		<!-- Meta Tags -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Dreams LMS is a powerful Learning Management System template designed for educators, training institutions, and businesses. Manage courses, track student progress, conduct virtual classes, and enhance e-learning experiences with an intuitive and feature-rich platform.">
		<meta name="keywords" content="LMS template, Learning Management System, e-learning software, online course platform, student management, education portal, virtual classroom, training management system, course tracking, online education">
		<meta name="author" content="Dreams Technologies">
		<meta name="robots" content="index, follow">
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		
		<title>@yield('title')</title>

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ asset('website_assets/assets/img/favicon.png') }}"> 
		<link rel="apple-touch-icon" href="{{ asset('website_assets/assets/img/apple-icon.png') }}">

		<!-- Theme Settings Js -->
		<script src="{{ asset('website_assets/assets/js/theme-script.js') }}"></script>
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/css/bootstrap.min.css') }}">
		
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/fontawesome/css/all.min.css') }}">

		<!-- isax css -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/css/iconsax.css') }}">

		<!-- Owl Carousel CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/css/owl.carousel.min.css') }}">
		<link rel="stylesheet" href="{{ asset('website_assets/assets/css/owl.theme.default.min.css') }}">

		<!-- Feathericon CSS -->
        <link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/feather/feather.css') }}">	

		<!-- Slick CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/slick/slick.css') }}">
		<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/slick/slick-theme.css') }}">
		
		<!-- Select2 CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/select2/css/select2.min.css') }}">

		<!-- Swiper CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/swiper/css/swiper.min.css') }}">
		
		<!-- Aos CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/aos/aos.css') }}">

		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('website_assets/assets/css/style.css') }}">

		@yield('style')	
	</head>
	<body class="home-five">

		<!-- Main Wrapper -->
		<div class="main-wrapper">

			<!-- Header Topbar-->
			@include('website.layout.partials.top-header')
			<!-- /Header Topbar-->
		
			<!-- Header -->
			@include('website.layout.partials.header')
			<!-- /Header -->
			
			<!-- Home Banner -->
			<!-- @include('website.layout.partials.banner') -->
			<!-- /Home Banner -->
			
			<!--Online Courses -->
			<!-- @include('website.layout.partials.bannercards') -->
			<!-- /Online Courses -->
			
			@yield('content')
			
			<!-- Footer -->
			@include('website.layout.partials.footer')
			<!-- /Footer -->
		   
		</div>
	   <!-- /Main Wrapper -->
	  
		<!-- jQuery -->
		<script src="{{ asset('website_assets/assets/js/jquery-3.7.1.min.js') }}"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="{{ asset('website_assets/assets/js/bootstrap.bundle.min.js') }}"></script>	
		
		<!-- Owl Carousel JS -->
		<script src="{{ asset('website_assets/assets/js/owl.carousel.min.js') }}"></script>
		
		<!-- Aos -->
		<script src="{{ asset('website_assets/assets/plugins/aos/aos.js') }}"></script>
		
		<!-- counterup JS -->
		<script src="{{ asset('website_assets/assets/js/jquery.waypoints.js') }}"></script>
		<script src="{{ asset('website_assets/assets/js/jquery.counterup.min.js') }}"></script>
		
		<!-- Select2 JS -->
		<script src="{{ asset('website_assets/assets/plugins/select2/js/select2.min.js') }}"></script>	

		<!-- Slick Slider -->
		<script src="{{ asset('website_assets/assets/plugins/slick/slick.js') }}"></script>

		<!-- Swiper Slider -->
		<script src="{{ asset('website_assets/assets/plugins/swiper/js/swiper.min.js') }}"></script>
		
		<!-- Custom JS -->
		<script src="{{ asset('website_assets/assets/js/script.js') }}"></script>

		@yield('script')		
	</body>
</html>