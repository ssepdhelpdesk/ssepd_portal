@section('title') 
SSEPD PORTAL
@endsection 
@extends('dashboard.layouts.main')
@section('style')
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

   @if($ssepdNotification->count() > 0)
   @foreach($ssepdNotification as $notification)
   @php
   $priorityConfig = match($notification->priority) {
     'low' => [
     'class' => 'alert-info',
     'icon' => 'fa-info-circle',
     'headingColor' => 'text-info',
     'label' => 'Info Notice',
     ],
     'medium' => [
     'class' => 'alert-success',
     'icon' => 'fa-check-circle',
     'headingColor' => 'text-success',
     'label' => 'General Update',
     ],
     'high' => [
     'class' => 'alert-warning',
     'icon' => 'fa-exclamation-triangle',
     'headingColor' => 'text-warning',
     'label' => 'Important Alert',
     ],
     'urgent' => [
     'class' => 'alert-danger',
     'icon' => 'fa-fire',
     'headingColor' => 'text-danger',
     'label' => '🚨 Urgent Notice',
     ],
     default => [
     'class' => 'alert-secondary',
     'icon' => 'fa-bell',
     'headingColor' => 'text-secondary',
     'label' => 'Notification',
     ],
  };
  @endphp

  <div class="alert {{ $priorityConfig['class'] }} alert-dismissible fade show shadow-sm rounded-3 border-0 mb-3" role="alert">
   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

   <h5 class="{{ $priorityConfig['headingColor'] }} fw-bold mb-2">
     <i class="fa {{ $priorityConfig['icon'] }} me-1"></i>
     {{ $loop->iteration }} • {{ $priorityConfig['label'] }}
  </h5>

  <h4 class="fw-semibold"><strong>{{ $notification->title }}</strong></h4>
  <p class="mb-1">{{ $notification->message }}</p>

  <small class="text-muted d-block mt-2">
     <i class="fa fa-calendar me-1"></i>
     From: {{ \Carbon\Carbon::parse($notification->start_date . ' ' . $notification->start_time)->format('d M Y, g:i A') }}
     &nbsp;→&nbsp;
     To: {{ \Carbon\Carbon::parse($notification->end_date . ' ' . $notification->end_time)->format('d M Y, g:i A') }}
  </small>
</div>
@endforeach
@endif


<!-- ============================================================== -->
<!-- End Info box -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Over Visitor, Our income , slaes different and  sales prediction -->
<!-- ============================================================== -->

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
@endsection