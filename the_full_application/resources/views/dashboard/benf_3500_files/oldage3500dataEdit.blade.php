@section('title') 
Special School || Staff Details
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.oldage3500data.update', $oldAge3500Pensioner->id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Update 3500 Beneficiary Address</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="name_of_the_beneficiary_div">
                                 <label class="form-label">Beneficiary Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="name_of_the_beneficiary" name="name_of_the_beneficiary" value="{{old('name_of_the_beneficiary', $oldAge3500Pensioner->name_of_the_beneficiary)}}" class="form-control" placeholder="Name of the Staff" readonly>
                                 <div id="name_of_the_beneficiary_error"></div>
                                 @error('name_of_the_beneficiary')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="father_or_husband_name_div">
                                 <label class="form-label">Father/Husband Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="father_or_husband_name" name="father_or_husband_name" value="{{old('father_or_husband_name', $oldAge3500Pensioner->father_or_husband_name)}}" class="form-control" placeholder="Name of the Staff" readonly>
                                 <div id="father_or_husband_name_error"></div>
                                 @error('father_or_husband_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="date_of_birth_div">
                                 <label class="form-label">DOB (DD-MM-YYYY)<span class="itsrequired"> *</span></label>
                                 <input type="text" 
                                 id="date_of_birth" 
                                 name="date_of_birth" 
                                 value="{{ 
                                  $oldAge3500Pensioner->date_of_birth === 'Not Provided By District' 
                                  ? 'Not Provided By District' 
                                  : \Carbon\Carbon::parse($oldAge3500Pensioner->date_of_birth)->format('d-m-Y') 
                               }}" 
                               class="form-control" 
                               placeholder="Name of the Staff" 
                               readonly>
                               <div id="date_of_birth_error"></div>
                               @error('date_of_birth')
                               <label class="error">{{ $message }}</label>
                               @enderror
                            </div>
                         </div>
                         <div class="col-md-3">
                           <div class="form-group" id="age_div">
                              <label class="form-label">Age<span class="itsrequired"> *</span></label>
                              <input type="text" id="age" name="age" value="{{old('age', $oldAge3500Pensioner->age)}}" class="form-control" placeholder="Name of the Staff" readonly>
                              <div id="age_error"></div>
                              @error('age')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" id="district_div">
                              <label class="form-label">district<span class="itsrequired"> *</span></label>
                              <input type="text" id="district" name="district" value="{{old('district', $oldAge3500Pensioner->district)}}" class="form-control" placeholder="Name of the Staff" readonly>
                              <div id="district_error"></div>
                              @error('district')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" id="block_or_ulb_div">
                              <label class="form-label">Block/ULB Name<span class="itsrequired"> *</span></label>
                              <input type="text" id="block_or_ulb" name="block_or_ulb" value="{{old('block_or_ulb', $oldAge3500Pensioner->block_or_ulb)}}" class="form-control" placeholder="Name of the Staff" readonly>
                              <div id="block_or_ulb_error"></div>
                              @error('block_or_ulb')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" id="gp_or_ward_div">
                              <label class="form-label">GP/Ward Name<span class="itsrequired"> *</span></label>
                              <input type="text" id="gp_or_ward" name="gp_or_ward" value="{{old('gp_or_ward', $oldAge3500Pensioner->gp_or_ward)}}" class="form-control" placeholder="Name of the Staff" readonly>
                              <div id="gp_or_ward_error"></div>
                              @error('gp_or_ward')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" id="village_div">
                              <label class="form-label">Village Name<span class="itsrequired"> *</span></label>
                              <input type="text" id="village" name="village" value="{{old('village', $oldAge3500Pensioner->village)}}" class="form-control" placeholder="Name of the Staff" readonly>
                              <div id="village_error"></div>
                              @error('village')
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
                  <div class="form-actions"></div>
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
   function Validate() {
     const radios = document.getElementsByName("ngo_address_type");
     let isChecked = false;

     for (let i = 0; i < radios.length; i++) {
       if (radios[i].checked) {
         isChecked = true;
         break;
      }
   }

   const errorDiv = document.getElementById("ngo_address_type_error");

   if (!isChecked) {
    errorDiv.innerHTML = "<label class='error'>Please select an address type.</label>";
        return false;
     } else {
       errorDiv.innerHTML = "";
        return true;
     }
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
      }

      radios.forEach(radio => {
         radio.addEventListener('change', function () {
            const value = this.value;

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
         const naFields = [
           'ngo_postal_address_at',
           'ngo_postal_address_post',
           'ngo_postal_address_via',
           'ngo_postal_address_ps',
           'ngo_postal_address_district',
           'ngo_postal_address_pin'
        ];

        naFields.forEach(function (id) {
           const el = document.getElementById(id);
           if (el) {
            el.value = 'Not Required to Provide';
            el.readOnly = true;
         }
      });
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
            submitButton.style.display = 'inline-block';
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
      alertDiv.innerHTML = `<img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

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