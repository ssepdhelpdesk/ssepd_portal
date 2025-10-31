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

<!-- ============================================================== -->
<!-- End Info box -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Over Visitor, Our income , slaes different and  sales prediction -->
<!-- ============================================================== -->
<div class="row">
   <div class="col-md-4">
      <div class="card">
         <div class="card-body">
            <h5 class="card-title">YOU HAVE {{ $ssepdNotification->count() }} NEW NOTIFICATION</h5>
            <div class="message-box ps ps--theme_default ps--active-y" id="msg" style="height: 430px; position: relative;">
               <div class="message-widget message-scroll">

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

                        <a href="javascript:void(0)" class="d-flex align-items-start border-bottom pb-2 mb-2">
                           <div class="user-img me-3">
                              <img src="https://cdn-icons-png.flaticon.com/512/6828/6828737.png" alt="user" class="img-circle" width="50">
                              <span class="profile-status online pull-right"></span>
                           </div>
                           <div class="mail-contnet">
                              <h5 class="{{ $priorityConfig['headingColor'] }} fw-bold mb-1">
                                 <i class="fa {{ $priorityConfig['icon'] }} me-1"></i> {{ $notification->title }}
                              </h5>
                              <p class="mb-1 fw-semibold">{{ $notification->message }}</p>
                              <small class="text-muted">
                                 <i class="fa fa-calendar me-1"></i>
                                 {{ \Carbon\Carbon::parse($notification->start_date . ' ' . $notification->start_time)->format('d M Y, g:i A') }}
                                 →
                                 {{ \Carbon\Carbon::parse($notification->end_date . ' ' . $notification->end_time)->format('d M Y, g:i A') }}
                              </small>
                           </div>
                        </a>
                     @endforeach
                  @else
                     <div class="text-center text-muted mt-5">
                        <i class="fa fa-bell-slash fa-2x mb-2"></i>
                        <p>No new notifications</p>
                     </div>
                  @endif

               </div>
               <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;">
                  <div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div>
               </div>
               <div class="ps__scrollbar-y-rail" style="top: 0px; height: 430px; right: 0px;">
                  <div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 348px;"></div>
               </div>
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
@endsection