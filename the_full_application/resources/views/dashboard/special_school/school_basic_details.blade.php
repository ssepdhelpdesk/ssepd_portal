@section('title') 
Special School || Index
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.specialschool.store_school_basic_details')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="special_school_name_div">
                                 <label class="form-label">Special School Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="special_school_name" name="special_school_name" value="{{old('special_school_name', $specialSchoolMapping->special_school_name)}}" class="form-control" placeholder="Special School Name">
                                 <div id="special_school_name_error"></div>
                                 @error('special_school_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="special_school_management_name_div">
                                 <label class="form-label">Management Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="special_school_management_name" name="special_school_management_name" value="{{old('special_school_management_name', $specialSchoolMapping->management_name)}}" class="form-control" placeholder="Management Name">
                                 <div id="special_school_management_name_error"></div>
                                 @error('special_school_management_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="school_establishment_date_div">
                                 <label class="form-label">Date of Establishment of School<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="school_establishment_date" name="school_establishment_date" value="{{old('school_establishment_date')}}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                 <div id="school_establishment_date_error"></div>
                                 @error('school_establishment_date')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="school_category_div">
                                 <label class="form-label">Category of School<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="school_category">
                                    <option value="">--Select--</option>
                                    <option value="1">Visually Impaired (VI)</option>
                                    <option value="2">Hearing Impaired (HI)</option>
                                    <option value="3">Mentally Retarded / Intellectually Disabled (MR/ID)</option>
                                    <option value="4">Cerebral Palsy (CP)</option>
                                    <option value="5">Autism Spectrum Disorder (ASD)</option>
                                 </select>
                                 <div id="school_category_error"></div>
                                 @error('school_category')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="school_type_div">
                                 <label class="form-label">School Type<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Type" tabindex="1" name="school_type">
                                    <option value="">--Select--</option>
                                    <option value="1">Residentials</option>
                                    <option value="2">Non Residential</option>
                                 </select>
                                 <div id="school_type_error"></div>
                                 @error('school_type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="act_reg_no_div">
                                 <label class="form-label">Regd No of PWD Act 1995/RPwD Act 2016<span class="itsrequired"> *</span></label>
                                 <input type="text" id="act_reg_no" name="act_reg_no" value="{{old('act_reg_no')}}" class="form-control" placeholder="NGO Registration No">
                                 <div id="act_reg_no_error"></div>
                                 @error('act_reg_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="file_act_reg_div">
                                 <label class="form-label">Upload Act File<span class="itsrequired"> *</span></label>
                                 <input type="file" class="form-control" id="file_act_reg" name="file_act_reg" value="{{old('file_act_reg')}}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                 <div id="file_act_reg_error"></div>
                                 @error('file_act_reg')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="school_email_id_div">
                                 <label class="form-label">School Email<span class="itsrequired"> *</span></label>
                                 <input type="text" id="school_email_id" name="school_email_id" value="{{old('school_email_id')}}" class="form-control" placeholder="NGO Email">
                                 <div id="school_email_id_error"></div>
                                 <div id="check_school_email_id"></div>
                                 @error('school_email_id')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="school_mobile_no_div">
                                 <label class="form-label">School Phone No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="school_mobile_no" name="school_mobile_no" value="{{old('school_mobile_no')}}" class="form-control" placeholder="NGO Phone No">
                                 <div id="school_mobile_no_error"></div>
                                 @error('school_mobile_no')
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
      { id: 'special_school_name', message: 'Please enter Special School Name.' },
      { id: 'special_school_management_name', message: 'Please enter Management Name.' },
      { id: 'school_establishment_date', message: 'Please select Establishment Date.' },
      { id: 'act_reg_no', message: 'Please enter Registration No.' },
      { id: 'school_email_id', message: 'Please enter School Email ID.' },
      { id: 'school_mobile_no', message: 'Please enter School Phone No.' }
   ];

   textFields.forEach(field => {
      const value = getValue(field.id);
      if (!value) {
         showError(field.id, field.message);
      } else {
         clearError(field.id);
      }
   });

   const selectFields = [
      { name: 'school_category', message: 'Please select Category of School.' },
      { name: 'school_type', message: 'Please select School Type.' }
   ];

   selectFields.forEach(field => {
      const el = document.getElementsByName(field.name)[0];
      if (!el || !el.value) {
         showError(field.name, field.message);
      } else {
         clearError(field.name);
      }
   });

   const file = document.getElementById('file_act_reg').files[0];
   if (!file) {
      showError('file_act_reg', 'Please upload the Act file.');
   } else {
      clearError('file_act_reg');

      const allowedType = 'application/pdf';
      const maxSize = 3 * 1024 * 1024;

      if (file.type !== allowedType) {
         showError('file_act_reg', 'Only PDF files are allowed.');
         isValid = false;
      } else if (file.size > maxSize) {
         showError('file_act_reg', 'File size must be less than 3MB.');
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