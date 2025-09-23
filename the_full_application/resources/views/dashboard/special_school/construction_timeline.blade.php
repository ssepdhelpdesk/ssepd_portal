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
                                    @foreach ($phaseNumbers as $phase_no)
                                    @php
                                    $construction = $constructionByPhase[$phase_no] ?? null;
                                    @endphp
                                    <div class="sl-item">
                                       <div class="sl-left">
                                          <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" />
                                       </div>
                                       <div class="sl-right">
                                          <div>
                                             @if ($construction)
                                             <a href="javascript:void(0)" class="link">Phase {{ $phase_no }} Updated On</a>
                                             <span class="sl-date">{{ \Carbon\Carbon::parse($construction->created_date)->format('d F Y') }}</span>
                                             <p>All Photos Uploaded By <a href="javascript:void(0)">{{ $specialSchool->special_school_name }}</a></p>
                                             <div class="row">
                                                @foreach(range(1, 5) as $i)
                                                @php
                                                $image = 'file_construction_image_' . $i;
                                                $lat = 'latitude_' . $i;
                                                $long = 'longitude_' . $i;
                                                @endphp
                                                @if(!empty($construction->$image))
                                                <div class="col-md-4 mb-4">
                                                   <div class="card shadow-lg custom-shadow h-100">
                                                      <div style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                         <img class="card-img-top img-fluid"
                                                         src="{{ url('storage/' . $construction->$image) }}"
                                                         alt="Card image cap"
                                                         style="width: 100%; height: 100%; object-fit: contain;">
                                                      </div>
                                                      <div class="card-body">
                                                         <ul class="list-inline font-14 mb-0">
                                                            <li class="p-l-0 d-inline-block me-3">
                                                               Uploaded On: {{ \Carbon\Carbon::parse($construction->created_date)->format('d F Y') }}
                                                            </li>
                                                            <li class="d-inline-block">
                                                               <a href="javascript:void(0)" class="link">Latitude: {{ $construction->$lat }}</a>
                                                            </li>
                                                            <li class="d-inline-block">
                                                               <a href="javascript:void(0)" class="link">Longitude: {{ $construction->$long }}</a>
                                                            </li>
                                                         </ul>
                                                      </div>
                                                   </div>
                                                </div>
                                                @endif
                                                @endforeach
                                             </div>
                                             @else
                                             <p class="text-danger">No construction data found for Phase {{ $phase_no }}.</p>
                                             @endif
                                          </div>
                                       </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                    @if($latestImage->approve_status == 1 && $latestImage->approver_remarks != NULL && $latestImage->approved_date != NULL && $latestImage->no_of_phase_approved != 0)
                                    @can('special-school-create')
                                    <div class="sl-item">
                                       <div class="sl-left"> <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" /> </div>
                                       <div class="sl-right">
                                          <div>
                                             <a href="javascript:void(0)" class="link">Upload the identified location for toilet construction.</a><small style="color: red;">Please use Google Chrome to fill out this form. Other browsers may not be supported.</small> <span class="sl-date"></span>
                                             <div class="m-t-20 row">
                                                <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.specialschoolconstructions.construction_timeline_store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                                                   @csrf
                                                   @method('post')
                                                   <div class="form-body">
                                                      <div class="row">
                                                         <input type="hidden" class="form-control" id="system_stored_latitude" name="system_stored_latitude" value="{{ old('system_stored_latitude') }}">
                                                         <input type="hidden" class="form-control" id="system_stored_longitude" name="system_stored_longitude" value="{{ old('system_stored_longitude') }}">
                                                         <div class="col-md-12">
                                                            <div class="form-group" id="new_or_existing_div">
                                                               <label for="new_or_existing" class="form-label">Construction Type</label>
                                                               <select class="form-control" name="new_or_existing" id="new_or_existing" required>
                                                                  <option value="">--Select--</option>
                                                                  <option value="1">New</option>
                                                                  <option value="2">Existing</option>
                                                               </select>
                                                               <div class="invalid-feedback">Please select an action (New or Existing).</div>
                                                               @error('new_or_existing')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="file_construction_image_1_div">
                                                               <label class="form-label">Upload Geo tagged Image 1<span class="itsrequired"> *</span></label>
                                                               <input type="file" class="form-control" id="file_construction_image_1" name="file_construction_image_1" value="{{old('file_construction_image_1')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                                               <div id="file_construction_image_1_error"></div>
                                                               @error('file_construction_image_1')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="latitude_1_div">
                                                               <label class="form-label">Latitude of Image 1<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="latitude_1" name="latitude_1" value="{{old('latitude_1')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Latitude">
                                                               <div id="latitude_1_error"></div>
                                                               @error('latitude_1')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="longitude_1_div">
                                                               <label class="form-label">Longitude of Image 1<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="longitude_1" name="longitude_1" value="{{old('longitude_1')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Longitude">
                                                               <div id="longitude_1_error"></div>
                                                               @error('longitude_1')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="file_construction_image_2_div">
                                                               <label class="form-label">Upload Geo tagged Image 2<span class="itsrequired"> *</span></label>
                                                               <input type="file" class="form-control" id="file_construction_image_2" name="file_construction_image_2" value="{{old('file_construction_image_2')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                                               <div id="file_construction_image_2_error"></div>
                                                               @error('file_construction_image_2')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="latitude_2_div">
                                                               <label class="form-label">Latitude of Image 2<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="latitude_2" name="latitude_2" value="{{old('latitude_2')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Latitude">
                                                               <div id="latitude_2_error"></div>
                                                               @error('latitude_2')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="longitude_2_div">
                                                               <label class="form-label">Longitude of Image 2<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="longitude_2" name="longitude_2" value="{{old('longitude_2')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Longitude">
                                                               <div id="longitude_2_error"></div>
                                                               @error('longitude_2')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="file_construction_image_3_div">
                                                               <label class="form-label">Upload Geo tagged Image 3<span class="itsrequired"> *</span></label>
                                                               <input type="file" class="form-control" id="file_construction_image_3" name="file_construction_image_3" value="{{old('file_construction_image_3')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                                               <div id="file_construction_image_3_error"></div>
                                                               @error('file_construction_image_3')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="latitude_3_div">
                                                               <label class="form-label">Latitude of Image 3<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="latitude_3" name="latitude_3" value="{{old('latitude_3')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Latitude">
                                                               <div id="latitude_3_error"></div>
                                                               @error('latitude_3')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="longitude_3_div">
                                                               <label class="form-label">Longitude of Image 3<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="longitude_3" name="longitude_3" value="{{old('longitude_3')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Longitude">
                                                               <div id="longitude_3_error"></div>
                                                               @error('longitude_3')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="file_construction_image_4_div">
                                                               <label class="form-label">Upload Geo tagged Image 4<span class="itsrequired"> *</span></label>
                                                               <input type="file" class="form-control" id="file_construction_image_4" name="file_construction_image_4" value="{{old('file_construction_image_4')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                                               <div id="file_construction_image_4_error"></div>
                                                               @error('file_construction_image_4')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="latitude_4_div">
                                                               <label class="form-label">Latitude of Image 4<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="latitude_4" name="latitude_4" value="{{old('latitude_4')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Latitude">
                                                               <div id="latitude_4_error"></div>
                                                               @error('latitude_4')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="longitude_4_div">
                                                               <label class="form-label">Longitude of Image 4<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="longitude_4" name="longitude_4" value="{{old('longitude_4')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Longitude">
                                                               <div id="longitude_4_error"></div>
                                                               @error('longitude_4')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="file_construction_image_5_div">
                                                               <label class="form-label">Upload Geo tagged Image 5<span class="itsrequired"> *</span></label>
                                                               <input type="file" class="form-control" id="file_construction_image_5" name="file_construction_image_5" value="{{old('file_construction_image_5')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                                               <div id="file_construction_image_5_error"></div>
                                                               @error('file_construction_image_5')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="latitude_5_div">
                                                               <label class="form-label">Latitude of Image 5<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="latitude_5" name="latitude_5" value="{{old('latitude_5')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Latitude">
                                                               <div id="latitude_5_error"></div>
                                                               @error('latitude_5')
                                                               <label class="error">{{ $message }}</label>
                                                               @enderror
                                                            </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group" id="longitude_5_div">
                                                               <label class="form-label">Longitude of Image 5<span class="itsrequired"> *</span></label>
                                                               <input type="text" id="longitude_5" name="longitude_5" value="{{old('longitude_5')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Longitude">
                                                               <div id="longitude_5_error"></div>
                                                               @error('longitude_5')
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
                                    @else
                                    <p class="text-danger">
                                     The construction status that you have uploaded is pending at HO.
                                  </p>
                                  @endif
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