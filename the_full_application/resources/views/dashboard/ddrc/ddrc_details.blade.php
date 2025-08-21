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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ddrc.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
                           <input type="hidden" class="form-control" id="system_stored_latitude" name="system_stored_latitude" value="{{ old('system_stored_latitude') }}">
                           <input type="hidden" class="form-control" id="system_stored_longitude" name="system_stored_longitude" value="{{ old('system_stored_longitude') }}">
                           <div class="col-md-3">
                              <div class="form-group" id="ddrc_name_div">
                                 <label class="form-label">DDRC Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ddrc_name" name="ddrc_name" value="{{old('ddrc_name', $user->name)}}" class="form-control" placeholder="DDRC Name">
                                 <div id="ddrc_name_error"></div>
                                 @error('ddrc_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="file_geo_tagged_image_div">
                                 <label class="form-label">Upload Geo tagged Image 1<span class="itsrequired"> *</span></label>
                                 <input type="file" class="form-control" id="file_geo_tagged_image" name="file_geo_tagged_image" value="{{old('file_geo_tagged_image')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                 <div id="file_geo_tagged_image_error"></div>
                                 @error('file_geo_tagged_image')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ddrc_latitude_div">
                                 <label class="form-label">Latitude of Image<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ddrc_latitude" name="ddrc_latitude" value="{{old('ddrc_latitude')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Latitude">
                                 <div id="ddrc_latitude_error"></div>
                                 @error('ddrc_latitude')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ddrc_longitude_div">
                                 <label class="form-label">Longitude of Image<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ddrc_longitude" name="ddrc_longitude" value="{{old('ddrc_longitude')}}" step="0.000001" min="-90" max="90" class="form-control" placeholder="Longitude">
                                 <div id="ddrc_longitude_error"></div>
                                 @error('ddrc_longitude')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_address_type_div">
                                 <label class="form-label">Address Type<span class="itsrequired"> *</span></label>
                                 <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio me-3">
                                       <input type="radio" id="block" name="ngo_address_type" value="1" class="form-check-input">
                                       <label class="form-check-label" for="block">Block</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                       <input type="radio" id="ulb" name="ngo_address_type" value="2" class="form-check-input">
                                       <label class="form-check-label" for="ulb">ULB</label>
                                    </div>
                                 </div>
                                 <div id="ngo_address_type_error"></div>
                                 @error('ngo_address_type')
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
   function Validate() {
      let isValid = true;

      function showError(id, message) {
         const errorDiv = document.getElementById(id + '_error');
         if (errorDiv) {
            errorDiv.innerHTML = '<label class="error">' + message + '</label>';
         }
         isValid = false;
      }

      function clearError(id) {
         const errorDiv = document.getElementById(id + '_error');
         if (errorDiv) {
            errorDiv.innerHTML = '';
         }
      }

      function getValue(id) {
         const el = document.getElementById(id);
         return el ? el.value.trim() : '';
      }

      const textFields = [
         { id: 'ddrc_name', message: 'Please enter DDRC Name.' },
         { id: 'ddrc_latitude', message: 'Please Provide Latitude from the uploaded Image.' },
         { id: 'ddrc_longitude', message: 'Please Provide Longitude from the uploaded Image.' },
      ];

      textFields.forEach(field => {
         const value = getValue(field.id);
         if (!value) {
            showError(field.id, field.message);
         } else {
            clearError(field.id);
         }
      });

      const file = document.getElementById('file_geo_tagged_image').files[0];
      if (!file) {
       showError('file_geo_tagged_image', 'Please upload the geo-tagged image.');
    } else {
       clearError('file_geo_tagged_image');

       const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
       const maxSize = 2 * 1024 * 1024;

       if (!allowedTypes.includes(file.type)) {
        showError('file_geo_tagged_image', 'Only JPG, JPEG, or PNG files are allowed.');
        isValid = false;
     } else if (file.size > maxSize) {
        showError('file_geo_tagged_image', 'File size must be less than 3MB.');
        isValid = false;
     }
  }

  const radios = document.querySelectorAll('input[name="ngo_address_type"]');
  let selected = false;
  radios.forEach(radio => {
   if (radio.checked) selected = true;
});

  if (!selected) {
   showError('ngo_address_type', 'Please select Address Type.');
} else {
   clearError('ngo_address_type');
}

return isValid;
}
</script>
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script>
   document.addEventListener("DOMContentLoaded", function () {
      const radios = document.querySelectorAll('input[name="ngo_address_type"]');
      const dynamicContent = document.getElementById('dynamic-content');
      const formActions = document.querySelector('.form-actions');
      const form = document.forms['vform'];      
      
      function initializeDropdowns() {
         $(".select2").select2();
         $('.selectpicker').selectpicker();

         $('#state-dropdown').on('change', function () {
            var idState = this.value;
            $("#district-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-districts')}}",
               type: "POST",
               data: {
                  state_id: idState,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (result) {
                  $('#district-dropdown').html('<option value="">-- Select District --</option>');
                  $.each(result.districts, function (key, value) {
                     $("#district-dropdown").append('<option value="' + value.district_id + '">' + value.district_name + '</option>');
                  });
                  $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                  $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                  $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
               }
            });
         });

         $('#district-dropdown').on('change', function () {
            var idDistrict = this.value;
            $("#municipality-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-municipality')}}",
               type: "POST",
               data: {
                  district_id: idDistrict,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
                  $.each(res.municipalities, function (key, value) {
                     $("#municipality-dropdown").append('<option value="' + value.municipality_id + '">' + value.municipality_name + '</option>');
                  });
               }
            });
         });

         $('#district-dropdown').on('change', function () {
            var idDistrict = this.value;
            $("#block-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-block')}}",
               type: "POST",
               data: {
                  district_id: idDistrict,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                  $.each(res.blocks, function (key, value) {
                     $("#block-dropdown").append('<option value="' + value
                        .block_id + '">' + value.block_name + '</option>');
                  });
                  $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
               }
            });
         });

         $('#block-dropdown').on('change', function () {
            var idBlock = this.value;
            $("#grampanchayat-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-grampanchayat')}}",
               type: "POST",
               data: {
                  block_id: idBlock,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                  $.each(res.grampanchayats, function (key, value) {
                     $("#grampanchayat-dropdown").append('<option value="' + value
                        .gp_id + '">' + value.gp_name + '</option>');
                  });
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
               }
            });
         });

         $('#grampanchayat-dropdown').on('change', function () {
            var idGrampanchayat = this.value;
            $("#village-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-village')}}",
               type: "POST",
               data: {
                  gp_id: idGrampanchayat,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                  $.each(res.villages, function (key, value) {
                     $("#village-dropdown").append('<option value="' + value
                        .village_id + '">' + value.village_name + '</option>');
                  });
               }
            });
         });

         $('#municipality-dropdown').on('change', function () {
            var idMunicipality = this.value;
            console.log(idMunicipality);
            $("#ward-dropdown").html('');
            $.ajax({
                url: "{{url('dashboard/locations/fetch-ward')}}",
                type: "POST",
                data: {
                    municipality_id: idMunicipality,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#ward-dropdown').html('<option value="">-- Select Ward --</option>');
                    $.each(res.wards, function (key, value) {
                        $("#ward-dropdown").append('<option value="' + value
                            .ward_code + '">' + value.ward_name + '</option>');
                    });
                }
            });
        });
      }

      radios.forEach(radio => {
         radio.addEventListener('change', function () {
            const value = this.value;

            /*fetch(`/ssepd_ngo_working_portal/dashboard/get-address-type-content/${value}`)*/
            fetch(`{{ url('dashboard/get-address-type-content') }}/${value}`)
            .then(response => {
               if (!response.ok) {
                  throw new Error('Network response was not ok');
               }
               return response.json();
            })
            .then(data => {
               dynamicContent.innerHTML = data.content;
               formActions.innerHTML = data.buttons;

               initializeDropdowns();
               bindValidation(value);
            })
            .catch(error => {
               console.error('Error fetching content:', error);
            });
         });
      });

      function bindValidation(type) {
         const pinField = document.getElementById('ngo_postal_address_pin');
         const submitButton = document.getElementById('submitButton');

         if (pinField && submitButton) {
            pinField.addEventListener('input', function () {
               const pinValue = pinField.value;
               if (pinValue.length === 6 && /^\d+$/.test(pinValue)) {
                  submitButton.style.display = 'inline-block';
               } else {
                  submitButton.style.display = 'none';
               }
            });

            const initialPinValue = pinField.value;
            if (initialPinValue.length === 6 && /^\d+$/.test(initialPinValue)) {
               submitButton.style.display = 'inline-block';
            } else {
               submitButton.style.display = 'none';
            }
         }

         document.getElementById('submitButton').addEventListener('click', function (e) {
            e.preventDefault();

            if (type === '1') {
               if (validateVillageFields()) {
                  form.submit();
               }
            } else if (type === '2') {
               if (validateMunicipalityFields()) {
                  form.submit();
               }
            }
         });
      }

      function showAlert(message, focusElement = null) {
         const alertContainer = document.getElementById('alert-container') || createAlertContainer();
         const alertDiv = document.createElement('div');

         alertDiv.classList.add('alert', 'alert-warning', 'alert-rounded', 'alert-dismissible');
         alertDiv.innerHTML = `
      <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;

         alertContainer.appendChild(alertDiv);

         if (focusElement) focusElement.focus();

         setTimeout(() => alertDiv.remove(), 3000);
      }

      function createAlertContainer() {
         const newContainer = document.createElement('div');
         newContainer.id = 'alert-container';
         document.body.appendChild(newContainer);
         return newContainer;
      }

      function validateFields(fields) {
         for (let field of fields) {
            const element = document.getElementById(field.id);
            if (!element || element.value.trim() === '') {
               showAlert(field.message, element);
               return false;
            }
         }
         return true;
      }

      function validateVillageFields() {
         const fields = [
            { id: 'state-dropdown', message: 'Please select State.' },
            { id: 'district-dropdown', message: 'Please select District.' },
            { id: 'block-dropdown', message: 'Please select Block.' },
            { id: 'grampanchayat-dropdown', message: 'Please select Grampanchayat.' },
            { id: 'village-dropdown', message: 'Please select Village.' },
            { id: 'pin', message: 'Please provide PIN.' },
            { id: 'ngo_postal_address_at', message: 'Please provide At.' },
            { id: 'ngo_postal_address_post', message: 'Please provide Post.' },
            { id: 'ngo_postal_address_via', message: 'Please provide Via.' },
            { id: 'ngo_postal_address_ps', message: 'Please provide Police Station.' },
            { id: 'ngo_postal_address_district', message: 'Please provide District.' },
            { id: 'ngo_postal_address_pin', message: 'Please provide Postal Code.' }
         ];

         return validateFields(fields) && Validate();
      }

      function validateMunicipalityFields() {
         const fields = [
            { id: 'state-dropdown', message: 'Please select State.' },
            { id: 'district-dropdown', message: 'Please select District.' },
            { id: 'municipality-dropdown', message: 'Please select Municipality.' },
            { id: 'ward-dropdown', message: 'Please select Ward.' },
            { id: 'pin', message: 'Please provide PIN.' },
            { id: 'ngo_postal_address_at', message: 'Please provide At.' },
            { id: 'ngo_postal_address_post', message: 'Please provide Post.' },
            { id: 'ngo_postal_address_via', message: 'Please provide Via.' },
            { id: 'ngo_postal_address_ps', message: 'Please provide Police Station.' },
            { id: 'ngo_postal_address_district', message: 'Please provide District.' },
            { id: 'ngo_postal_address_pin', message: 'Please provide Postal Code.' }
         ];

         return validateFields(fields) && Validate();
      }   
      console.log("DOM is fully loaded");
   });
</script>
@endsection