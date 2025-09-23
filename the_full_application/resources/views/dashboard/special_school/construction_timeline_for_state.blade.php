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
                                    $construction = DB::table('special_school_constructions')
                                    ->where('special_school_id', $id)
                                    ->where('phase_no', $phase_no)
                                    ->first();
                                    @endphp
                                    <div class="sl-item">
                                       <div class="sl-left">
                                          <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" />
                                       </div>
                                       <div class="sl-right">
                                          <div>
                                             @if ($construction)
                                             <a href="javascript:void(0)" class="link">Phase {{ $phase_no }} Updated On</a>
                                             <span class="sl-date">
                                                {{ \Carbon\Carbon::parse($construction->created_date)->format('d F Y') }}
                                             </span>
                                             <p style="margin: 0;">
                                               <strong>Management Name:</strong> 
                                               <a href="javascript:void(0)">{{ $specialSchool->special_school_management_name }}</a>
                                            </p>

                                            <p style="margin: 0;">
                                               <strong>School Name:</strong> 
                                               <a href="javascript:void(0)">{{ $specialSchool->special_school_name }}</a>
                                            </p>

                                            <p style="margin: 0;">
                                               <strong>Address:</strong> 
                                               <a href="javascript:void(0)">
                                                 @if($specialSchool->school_address_type == 1)
                                                 {{ collect([$specialSchool->village->village_name ?? null, $specialSchool->grampanchayat->gp_name ?? null, $specialSchool->district->district_name ?? null])->filter()->implode(', ') }}
                                                 @elseif($specialSchool->school_address_type == 2)
                                                 {{ collect([optional($specialSchool->ward)->ward_name, $specialSchool->municipality->municipalities ?? null, $specialSchool->district->district_name ?? null])->filter()->implode(', ') }}
                                                 @endif
                                              </a>
                                           </p>

                                           <p style="margin: 0;">
                                            <strong>Construction Type:</strong> 
                                            <a href="javascript:void(0)">
                                              {{ $construction->new_or_existing == 1 ? 'New' : ($construction->new_or_existing == 2 ? 'Existing' : '') }}
                                           </a>
                                        </p>

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
                                                <a href="{{ url('storage/' . $construction->$image) }}" target="_blank">
                                                   <div style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                      <img class="card-img-top img-fluid"
                                                      src="{{ url('storage/' . $construction->$image) }}"
                                                      alt="Card image cap"
                                                      style="width: 100%; height: 100%; object-fit: contain;">
                                                   </div>
                                                </a>
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
                              @can('special-school-approve-form')
                              @php
                              $approve_status = DB::table('special_school_constructions')
                              ->where('special_school_id', $id)
                              ->where('phase_no', $phase_no)
                              ->value('approve_status');
                              @endphp
                              @if($approve_status == 0)
                              <div class="sl-item">
                                 <div class="sl-left"> <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" /> </div>
                                 <div class="sl-right">
                                    <div>
                                       <a href="javascript:void(0)" class="link"></a><span class="sl-date"></span>
                                       <div class="m-t-20 row">
                                          <form id="approvalForm" method="POST" action="{{ route('admin.specialschoolconstructions.approve_construction_status_store', $id) }}" enctype="multipart/form-data" novalidate>
                                             @csrf
                                             <div class="form-group mb-3">
                                                <label for="approve_status" class="form-label">Action</label>
                                                <select class="form-control" name="approve_status" id="approve_status" required>
                                                   <option value="">--Select--</option>
                                                   <option value="1">Approve</option>
                                                   <option value="2">Reject</option>
                                                   <option value="3">In Waiting</option>
                                                </select>
                                                <div class="invalid-feedback">Please select an action (Approve or Reject).</div>
                                                @error('approve_status')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                             <div class="form-group mb-3">
                                                <label for="approver_remarks" class="form-label">Any Remarks</label>
                                                <textarea class="form-control" name="approver_remarks" id="approver_remarks" rows="3" required></textarea>
                                                <div class="invalid-feedback">Remarks are required.</div>
                                                @error('approver_remarks')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                             <button type="submit" class="btn btn-primary">Submit</button>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <hr>
                              @endif
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
<script>
   document.addEventListener("DOMContentLoaded", function () {
     const form = document.getElementById("approvalForm");

     form.addEventListener("submit", function (event) {
       let isValid = true;

       const approveStatus = document.getElementById("approve_status");
       if (!approveStatus.value) {
         approveStatus.classList.add("is-invalid");
         isValid = false;
      } else {
         approveStatus.classList.remove("is-invalid");
      }
      
      const remarks = document.getElementById("approver_remarks");
      if (!remarks.value.trim()) {
         remarks.classList.add("is-invalid");
         isValid = false;
      } else {
         remarks.classList.remove("is-invalid");
      }
      
      if (!isValid) {
         event.preventDefault();
         event.stopPropagation();
      }
   });

     document.querySelectorAll("#approvalForm select, #approvalForm textarea").forEach(input => {
       input.addEventListener("input", () => {
         if (input.value.trim()) {
           input.classList.remove("is-invalid");
        }
     });
    });
  });
</script>
@endsection