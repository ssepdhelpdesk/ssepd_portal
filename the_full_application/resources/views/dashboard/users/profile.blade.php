@section('title') 
Users || Profile
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
  @include('dashboard.component.message')
  <!-- Column -->
  <div class="col-lg-4 col-xlg-3 col-md-5">
    <div class="card">
        <div class="card-body">
            <center class="m-t-30">
                <img src="{{ (!empty($user->profile_photo))? url($user->profile_photo_path):url('profile-pic/no_image.jpg') }}" class="img-circle" width="150" />
                <h4 class="card-title m-t-10">{{$user->name}}</h4>
                <h6 class="card-subtitle">{{$user->email}}</h6>
                @if($currentUserInfo)
                <h6 class="card-subtitle">IP: {{ $currentUserInfo->ip }}</h6>
                <h6 class="card-subtitle">Country Name: {{ $currentUserInfo->countryName }}</h6>
                <h6 class="card-subtitle">Country Code: {{ $currentUserInfo->countryCode }}</h6>
                <h6 class="card-subtitle">Region Code: {{ $currentUserInfo->regionCode }}</h6>
                <h6 class="card-subtitle">Region Name: {{ $currentUserInfo->regionName }}</h6>
                <h6 class="card-subtitle">City Name: {{ $currentUserInfo->cityName }}</h6>
                <h6 class="card-subtitle">Zip Code: {{ $currentUserInfo->zipCode }}</h6>
                <h6 class="card-subtitle">Latitude: {{ $currentUserInfo->latitude }}</h6>
                <h6 class="card-subtitle">Longitude: {{ $currentUserInfo->longitude }}</h6>
                @endif
            </center>
        </div>
        <div>
            <hr>
        </div>
        <div class="card-body">
            <small class="text-muted">Email address </small>
            <h6>{{$user->email}}</h6>
            <small class="text-muted p-t-30 db">Phone</small>
            <h6>{{$user->mobile_no}}</h6>
        </div>
    </div>
</div>
<!-- Column -->
<!-- Column -->
<div class="col-lg-8 col-xlg-9 col-md-7">
    <div class="card">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs profile-tab" role="tablist">
            <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#settings" role="tab">Settings</a> </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="settings" role="tabpanel">
                <div class="card-body">
                    <form class="form-horizontal form-material from-prevent-multiple-submits" method="POST" action="{{route('admin.myprofile.update', $user->id)}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="col-md-12">Profile Picture</label>
                            <div class="col-md-12">
                                <input type="file" name="profile_photo" id="input-file-now-custom-1" class="dropify" data-allowed-file-extensions="jpg png" data-max-file-size="1m" data-default-file="{{ (!empty($user->profile_photo))? url($user->profile_photo_path):url('profile-pic/no_image.jpg') }}" />
                                <div id="carousel_image_error"></div>
                                @error('profile_photo')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">Full Name</label>
                            <div class="col-md-12">
                                <input type="text" placeholder="Johnathan Doe" name="name" value="{{$user->name}}" class="form-control form-control-line">
                                @error('name')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="example-email" class="col-md-12">Email</label>
                            <div class="col-md-12">
                                <input type="email" placeholder="johnathan@admin.com" class="form-control form-control-line" name="email" value="{{$user->email}}" id="example-email">
                                @error('email')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">Phone No</label>
                            <div class="col-md-12">
                                <input type="text" placeholder="123 456 7890" class="form-control form-control-line" name="mobile_no" value="{{$user->mobile_no}}">
                                @error('mobile_no')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button type="submit" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Column -->
</div>
<!-- row -->
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
</div>
@endsection 
@section('script')
<script src="{{ asset('assets/assets/node_modules/dropify/dist/js/dropify.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();

        // Translated
        $('.dropify-fr').dropify({
            messages: {
            default: 'Glissez-déposez un fichier ici ou cliquez',
                replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
                remove: 'Supprimer',
                error: 'Désolé, le fichier trop volumineux'
            }
        });

        // Used events
        var drEvent = $('#input-file-events').dropify();

        drEvent.on('dropify.beforeClear', function(event, element) {
            return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        });

        drEvent.on('dropify.afterClear', function(event, element) {
            alert('File deleted');
        });

        drEvent.on('dropify.errors', function(event, element) {
            console.log('Has Errors');
        });

        var drDestroy = $('#input-file-to-destroy').dropify();
        drDestroy = drDestroy.data('dropify')
        $('#toggleDropify').on('click', function(e) {
            e.preventDefault();
            if (drDestroy.isDropified()) {
                drDestroy.destroy();
            } else {
                drDestroy.init();
            }
        })
    });
</script>
@endsection