@section('title') 
DDRC || Basic Details
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .readonly-input {
      pointer-events: none;
      background-color: #f8f9fa;
      cursor: default;
   }
</style>
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
               <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <button onclick="history.back()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info"><i class="fas fa-arrow-alt-circle-left"></i> Go Back</button>
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->
   <!-- Start Page Content -->
   <!-- ============================================================== -->
   <!-- row -->
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-body">
               <h4 class="card-title"></h4>
               @include('dashboard.component.message')
               @if (count($errors) > 0)
               <div class="alert alert-danger">
                  <strong>Whoops!</strong> There were some problems with your input.<br><br>
                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <div id="alert-container"></div>
               <div class="col-sm-12 col-xs-12">
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ddrc.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="title_div">
                                 <label class="form-label">Notification Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="title" name="title" value="{{old('title', $user->name)}}" class="form-control" placeholder="Notification Name">
                                 <div id="title_error"></div>
                                 @error('title')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="start_date_div">
                                 <label class="form-label">Start Date<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="start_date" name="start_date" value="{{old('start_date')}}">
                                 <div id="start_date_error"></div>
                                 @error('start_date')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="start_time_div">
                                 <label class="form-label">Start Time<span class="itsrequired"> *</span></label>
                                 <input type="time" id="start_time" name="start_time" value="{{old('start_time')}}" class="form-control" placeholder="Time">
                                 <div id="start_time_error"></div>
                                 @error('start_time')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="message_div">
                                 <label class="form-label">Message<span class="itsrequired"> *</span></label>
                                 <textarea id="message" name="message" class="form-control" rows="3"></textarea>
                                 <div id="message_error"></div>
                                 @error('message')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <!--/row-->
                        <div class="row" id="dynamic-content"></div>
                     </div>
                     <div class="form-actions">
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- row -->
   <!-- ============================================================== -->
   <!-- End Page Content -->
   <!-- ============================================================== -->
</div>
@endsection 
@section('script')

<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
@endsection