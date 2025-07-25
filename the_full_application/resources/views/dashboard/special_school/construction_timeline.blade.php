@section('title') 
Special School || Construction Progress Details
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .readonly-input {
   pointer-events: none;
   background-color: #f8f9fa;
   cursor: default;
   }
   .custom-shadow {
   box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
   border-radius: 10px;
   }
   .card.custom-shadow:hover {
   box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
   transition: box-shadow 0.3s ease-in-out;
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
         @can('role-create')
         <a href="{{ route('admin.roles.create') }}"><button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success"><i class="fas fa-plus-square"></i> Add New</button></a>
         @endcan
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
                  <div class="col-lg-8 col-xlg-9 col-md-7">
                     <div class="card">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs profile-tab" role="tablist">
                           <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">Timeline</a> </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                           <div class="tab-pane active" id="home" role="tabpanel">
                              <div class="card-body">
                                 <div class="profiletimeline">
                                    <div class="sl-item">
                                       <div class="sl-left"> <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" /> </div>
                                       <div class="sl-right">
                                          <div>
                                             <a href="javascript:void(0)" class="link">Last Updated On</a> <span class="sl-date"> {{ $timeDifferenceText ?? 'No upload yet' }} </span>
                                             <p>All Photos Uploaded By <a href="javascript:void(0)"> {{ $specialSchool->special_school_name }}</a></p>
                                             <div class="row">
                                                @foreach($specialSchoolConstruction as $constructionDetails)
                                                <div class="col-md-4 mb-4">
                                                   <div class="card shadow-lg custom-shadow h-100">
                                                      <div style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                         <img class="card-img-top img-fluid"
                                                            src="{{ url('storage/' . $constructionDetails->file_construction_image) }}"
                                                            alt="Card image cap"
                                                            style="width: 100%; height: 100%; object-fit: contain;">
                                                      </div>
                                                      <div class="card-body">
                                                         <ul class="list-inline font-14 mb-0">
                                                            <li class="p-l-0 d-inline-block me-3">Uploaded On: {{ \Carbon\Carbon::parse($constructionDetails->created_date)->format('d F Y') }}</li>
                                                            <li class="d-inline-block"><a href="javascript:void(0)" class="link">Latitude: {{ $constructionDetails->latitude }}</a></li>
                                                            <li class="d-inline-block"><a href="javascript:void(0)" class="link">Longitude: {{ $constructionDetails->longitude }}</a></li>
                                                         </ul>
                                                      </div>
                                                   </div>
                                                </div>
                                                @endforeach
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <hr>
                                    @foreach($specialSchoolConstruction as $constructionDetails)
                                    <div class="sl-item">
                                       <div class="sl-left"> <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" /> </div>
                                       <div class="sl-right">
                                          <div>
                                             <a href="javascript:void(0)" class="link">Uploaded On</a> <span class="sl-date">{{ \Carbon\Carbon::parse($constructionDetails->created_date)->format('d F Y') }}</span>
                                             <blockquote class="m-t-10">
                                                <div class="card shadow-lg custom-shadow" style="max-width: 400px;">
                                                   <div style="height: 200px; width: 100%; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                      <img 
                                                         class="card-img-top img-fluid" 
                                                         src="{{ url('storage/' . $constructionDetails->file_construction_image) }}"
                                                         alt="Card image cap" 
                                                         style="width: 100%; height: 100%; object-fit: contain;">
                                                   </div>
                                                </div>
                                             </blockquote>
                                          </div>
                                       </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                    @can('special-school-create')
                                    <div class="sl-item">
                                       <div class="sl-left"> <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" /> </div>
                                       <div class="sl-right">
                                          <div>
                                             <a href="javascript:void(0)" class="link">Upload Current Status Image</a> <span class="sl-date"></span>
                                             <div class="m-t-20 row">
                                                <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.specialschoolconstructions.construction_timeline_store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                                                   @csrf
                                                   @method('post')
                                                   <div class="form-body">
                                                      <div class="row">
                                                         <input type="hidden" class="form-control" id="system_stored_latitude" name="system_stored_latitude" value="{{ old('system_stored_latitude') }}">
                                                         <input type="hidden" class="form-control" id="system_stored_longitude" name="system_stored_longitude" value="{{ old('system_stored_longitude') }}">
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="file_construction_image_div">
                                                               <label class="form-label">Upload Geo tagged Image <span class="itsrequired"> *</span></label>
                                                               <input type="file" class="form-control" id="file_construction_image" name="file_construction_image" value="{{old('file_construction_image')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                                               <div id="file_construction_image_error"></div>
                                                               @error('file_construction_image')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="latitude_div">
                                                               <label class="form-label">Latitude<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="latitude" name="latitude" value="{{old('latitude')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Latitude">
                                                               <div id="latitude_error"></div>
                                                               @error('latitude')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="longitude_div">
                                                               <label class="form-label">Longitude<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="longitude" name="longitude" value="{{old('longitude')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Longitude">
                                                               <div id="longitude_error"></div>
                                                               @error('longitude')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-12">
                                                            <div class="form-group" id="any_remarks_div">
                                                               <label class="form-label">Remarks<span class="itsrequired"> *</span></label>
                                                               <textarea class="form-control" rows="3" name="any_remarks"></textarea>
                                                               <div id="any_remarks_error"></div>
                                                               @error('any_remarks')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="form-actions">
                                                      <button type="submit" onclick="return IsEmpty();" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Update</button>
                                                   </div>
                                                </form>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <hr>
                                    @endcan
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
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
<script>
   document.addEventListener("DOMContentLoaded", function () {
       if (navigator.geolocation) {
           navigator.geolocation.getCurrentPosition(
               function (position) {
                   let lat = position.coords.latitude.toFixed(6);
                   let lon = position.coords.longitude.toFixed(6);
   
                   document.getElementById("system_stored_latitude").value = lat;
                   document.getElementById("system_stored_longitude").value = lon;
   
                   console.log("📍 Location captured:", lat, lon);
               },
               function (error) {
                   switch (error.code) {
                       case error.PERMISSION_DENIED:
                           alert("Geolocation permission denied by the user.");
                           break;
                       case error.POSITION_UNAVAILABLE:
                           alert("Location information is unavailable.");
                           break;
                       case error.TIMEOUT:
                           alert("The request to get user location timed out.");
                           break;
                       default:
                           alert("An unknown error occurred while fetching location.");
                           break;
                   }
                   console.warn("Geolocation error:", error.message);
               },
               {
                   enableHighAccuracy: true,
                   timeout: 5000,
                   maximumAge: 0
               }
           );
       } else {
           alert("Geolocation is not supported by your browser.");
       }
   });
</script>
@endsection