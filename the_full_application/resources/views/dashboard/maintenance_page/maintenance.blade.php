@section('title') 
SSEPD PORTAL
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<link href="{{ asset('dashboard_assets/dist/css/style.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('dashboard_assets/dist/css/pages/error-pages.css') }}" rel="stylesheet"/>
@endsection 
@section('content')
<div class="container-fluid">
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="row page-titles">
   <div class="col-md-7 align-self-center">
      <div class="d-flex align-items-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
         </ol>
      </div>
   </div>
   <div class="col-md-5 align-self-center text-end">
   </div>
</div>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Info box -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- End Info box -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Over Visitor, Our income , slaes different and  sales prediction -->
<!-- ============================================================== -->
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 120px); background:linear-gradient(135deg,#e3f2fd,#bbdefb,#90caf9);">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card border-0" style="border-radius:20px; background:#ffffff; box-shadow:0 15px 40px rgba(0,0,0,0.15);">
                <div class="card-body py-5 text-center">

                    <div style="font-size:90px;">🚧</div>

                    <h1 style="font-size:55px; font-weight:700; color:#ff9800; margin-bottom:15px;">
                        Under Maintenance
                    </h1>

                    <h3 style="color:#1565c0; font-weight:600; margin-bottom:20px;">
                        We'll Be Live Soon!
                    </h3>

                    <p style="font-size:18px; color:#616161; line-height:1.8; margin-bottom:35px;">
                        We are currently performing scheduled maintenance to improve your experience.
                        <br>
                        Please check back shortly.
                    </p>

                    <a href="{{ url('/') }}"
                        style="display:inline-block; padding:12px 35px; background:linear-gradient(45deg,#2196f3,#0d47a1); color:#fff; text-decoration:none; border-radius:30px; font-weight:600; box-shadow:0 8px 20px rgba(33,150,243,.35); transition:.3s;">
                        🏠 Back to Home
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================== -->
<!-- Comment - table -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- End Comment - chats -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Over Visitor, Our income , slaes different and  sales prediction -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Todo, chat, notification -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Right sidebar -->
<!-- ============================================================== -->
<!-- .right-sidebar -->

<!-- ============================================================== -->
<!-- End Right sidebar -->
<!-- ============================================================== -->
</div>
@endsection 
@section('script')
<script src="{{ asset('dashboard_assets/assets/node_modules/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/dist/js/waves.js') }}"></script>
@endsection