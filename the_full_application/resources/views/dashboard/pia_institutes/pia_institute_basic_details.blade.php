@section('title') 
PIA || Institute Basic Details
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.piainstitutes.pia_institute_basic_details_update', $piainstitute->id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="institute_name_div">
                                 <label class="form-label">Institute Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="institute_name" name="institute_name" value="{{old('institute_name')}}" class="form-control" placeholder="Name of the Institute" >
                                 <div id="institute_name_error"></div>
                                 @error('institute_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="institute_type_id_div">
                                 <label class="form-label">Institute Type<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="institute_type_id" name="institute_type_id">
                                    <option value="">Please Select</option>
                                    <option value="1">Geriatric Center</option>
                                    <option value="2">Disha Center</option>
                                    <option value="3">Sahaya Center</option>
                                    <option value="4">Old Age Home</option>
                                    <option value="5">Half Way Home</option>
                                    <option value="6">Therapeutic Center</option>
                                 </select>
                                 <div id="institute_type_id_error"></div>
                                 @error('institute_type_id')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="institute_email_id_div">
                                 <label class="form-label">Institute Email ID<span class="itsrequired"> *</span></label>
                                 <input type="email" id="institute_email_id" name="institute_email_id" value="{{old('institute_email_id')}}" class="form-control" placeholder="Institute Email ID" >
                                 <div id="institute_email_id_error"></div>
                                 @error('institute_email_id')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="date_of_registration_div">
                                 <label class="form-label">Date of Registration (DD-MM-YYYY)<span class="itsrequired"> *</span></label>
                                 <input type="date" id="date_of_registration" name="date_of_registration" value="{{old('date_of_registration')}}" max="{{ date('Y-m-d') }}" class="form-control" placeholder="Date of Registration">
                                 <div id="date_of_registration_error"></div>
                                 @error('date_of_registration')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="registration_no_div">
                                 <label class="form-label">Registration No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="registration_no" name="registration_no" value="{{old('registration_no')}}" class="form-control" placeholder="Registration No" >
                                 <div id="registration_no_error"></div>
                                 <div id="check_registration_no"></div>
                                 @error('registration_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="registration_certificate_div">
                                 <label class="form-label">Upload Registration Certificate<span class="itsrequired"> *</span></label>
                                 <input type="file" id="registration_certificate" name="registration_certificate" value="{{old('registration_certificate')}}" class="form-control" placeholder="Registration No" accept="application/pdf">
                                 <div id="registration_certificate_error"></div>
                                 @error('registration_certificate')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="which_govt_div">
                                 <label class="form-label">Grant-in-Aid<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="which_govt" name="which_govt">
                                    <option value="">Please Select</option>
                                    <option value="1">Govt. of Odisha</option>
                                    <option value="2">Govt. of India</option>
                                 </select>
                                 <div id="which_govt_error"></div>
                                 @error('which_govt')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="grantee_code_div">
                                 <label class="form-label">Institute Grantee code<span class="itsrequired"> *</span></label>
                                 <input type="text" id="grantee_code" name="grantee_code" value="{{old('grantee_code')}}" class="form-control" placeholder="Institute Grantee code" >
                                 <div id="grantee_code_error"></div>
                                 <div id="check_grantee_code"></div>
                                 @error('grantee_code')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="nodal_officer_name_div">
                                 <label class="form-label">Nodal Officer Name of the Institute<span class="itsrequired"> *</span></label>
                                 <input type="text" id="nodal_officer_name" name="nodal_officer_name" value="{{old('nodal_officer_name')}}" class="form-control" placeholder="Nodal Officer Name" >
                                 <div id="nodal_officer_name_error"></div>
                                 <div id="check_nodal_officer_name"></div>
                                 @error('nodal_officer_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="nodal_officer_contact_number_div">
                                 <label class="form-label">Nodal Officer Mobile No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="nodal_officer_contact_number" name="nodal_officer_contact_number"
                                    class="form-control" maxlength="10" inputmode="numeric" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)" placeholder="Nodal Officer Mobile No" >
                                 <div id="nodal_officer_contact_number_error"></div>
                                 <div id="check_nodal_officer_contact_number"></div>
                                 @error('nodal_officer_contact_number')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="pia_name_div">
                                 <label class="form-label">PIA/NGO Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="pia_name" name="pia_name" value="{{old('pia_name')}}" class="form-control" placeholder="PIA/NGO Name" >
                                 <div id="pia_name_error"></div>
                                 <div id="check_pia_name"></div>
                                 @error('pia_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="pia_nodal_officer_name_div">
                                 <label class="form-label">PIA/NGO Nodal Officer Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="pia_nodal_officer_name" name="pia_nodal_officer_name" value="{{old('pia_nodal_officer_name')}}" class="form-control" placeholder="PIA/NGO Nodal Officer Name" >
                                 <div id="pia_nodal_officer_name_error"></div>
                                 <div id="check_pia_nodal_officer_name"></div>
                                 @error('pia_nodal_officer_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="pia_nodal_officer_contact_no_div">
                                 <label class="form-label">PIA/NGO Nodal Officer Mobile No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="pia_nodal_officer_contact_no" name="pia_nodal_officer_contact_no"
                                    class="form-control" maxlength="10" inputmode="numeric" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)" placeholder="PIA/NGO Nodal Officer Mobile No" >
                                 <div id="pia_nodal_officer_contact_no_error"></div>
                                 <div id="check_pia_nodal_officer_contact_no"></div>
                                 @error('pia_nodal_officer_contact_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <!-- <div class="col-md-3">
                              <div class="form-group" id="ngo_address_type_div">
                                 <label class="form-label">Address Type<span class="itsrequired"> *</span></label> 
                                 <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio me-3"> <input type="radio" id="block" name="ngo_address_type" value="1" class="form-check-input"> <label class="form-check-label" for="block">Block</label> </div>
                                    <div class="custom-control custom-radio"> <input type="radio" id="ulb" name="ngo_address_type" value="2" class="form-check-input"> <label class="form-check-label" for="ulb">ULB</label> </div>
                                 </div>
                                 <div id="ngo_address_type_error"></div>
                                 @error('ngo_address_type') <label class="error">{{ $message }}</label> @enderror 
                              </div>
                              </div> -->
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_address_type_div">
                                 <label class="form-label">Institute Address Type<span class="itsrequired"> *</span></label>
                                 <div class="d-flex align-items-center">
                                    @if(in_array(auth()->user()->role_id, [4, 6]))
                                    <div class="custom-control custom-radio me-3">
                                       <input type="radio" id="block" name="ngo_address_type" value="1"
                                          class="form-check-input">
                                       <label class="form-check-label" for="block">Block</label>
                                    </div>
                                    @elseif(auth()->user()->role_id == 5)
                                    <div class="custom-control custom-radio">
                                       <input type="radio" id="ulb" name="ngo_address_type" value="2"
                                          class="form-check-input">
                                       <label class="form-check-label" for="ulb">ULB</label>
                                    </div>
                                    @else
                                    <div class="custom-control custom-radio me-3">
                                       <input type="radio" id="block" name="ngo_address_type" value="1"
                                          class="form-check-input">
                                       <label class="form-check-label" for="block">Block</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                       <input type="radio" id="ulb" name="ngo_address_type" value="2"
                                          class="form-check-input">
                                       <label class="form-check-label" for="ulb">ULB</label>
                                    </div>
                                    @endif
                                 </div>
                                 <div id="ngo_address_type_error"></div>
                                 @error('ngo_address_type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                        </div>
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
<script type="text/javascript">
   $(document).ready(function () {
      $("#registration_no").blur(function () {
         const registration_no = $(this).val().trim();
   
         $('#check_registration_no').html('');
   
         if (!registration_no) {
            $('#check_registration_no').html('<span style="color:#FF0000">Please provide a Registration Number.</span>');
            return;
         }
   
         $.get("{{ route('admin.piainstitutes.check_registration_no') }}", 
            { registration_no: registration_no }, 
            function (data) {
               if (data == 0) {
                  $('#check_registration_no').html('<span style="color:#03713E">This Registration No is available.</span>');
               } else if (data == 1) {
                  $('#check_registration_no').html('<span style="color:#FF0000">This Registration No is already registered.</span>');
                  $("#registration_no").val('');
               }
            }
         ).fail(function () {
            $('#check_registration_no').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
         });
      });
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
      $("#grantee_code").blur(function () {
         const grantee_code = $(this).val().trim();
   
         $('#check_grantee_code').html('');
   
         if (!grantee_code) {
            $('#check_grantee_code').html('<span style="color:#FF0000">Please provide a Institute Grantee code.</span>');
            return;
         }
   
         $.get("{{ route('admin.piainstitutes.check_grantee_code') }}", 
            { grantee_code: grantee_code }, 
            function (data) {
               if (data == 0) {
                  $('#check_grantee_code').html('<span style="color:#03713E">This Institute Grantee code is available.</span>');
               } else if (data == 1) {
                  $('#check_grantee_code').html('<span style="color:#FF0000">This Institute Grantee code is already registered.</span>');
                  $("#grantee_code").val('');
               }
            }
         ).fail(function () {
            $('#check_grantee_code').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
         });
      });
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
   { id: 'institute_name', message: 'Please enter Institute Name.' },
   { id: 'institute_email_id', message: 'Please enter Institute Email ID.' },
   { id: 'date_of_registration', message: 'Please select Date of Registration.' },
   { id: 'registration_no', message: 'Please enter Registration No.' },
   { id: 'grantee_code', message: 'Please enter Grantee Code.' },
   { id: 'nodal_officer_name', message: 'Please enter Nodal Officer Name.' },
   { id: 'nodal_officer_contact_number', message: 'Please enter Nodal Officer Mobile No.' },
   { id: 'pia_name', message: 'Please enter PIA/NGO Name.' },
   { id: 'pia_nodal_officer_name', message: 'Please enter PIA/NGO Nodal Officer Name.' },
   { id: 'pia_nodal_officer_contact_no', message: 'Please enter PIA/NGO Mobile No.' }
   ];
   
   textFields.forEach(field => {
   const value = getValue(field.id);
   if (!value) {
   showError(field.id, field.message);
   } else {
   clearError(field.id);
   }
   });
   
   const email = getValue('institute_email_id');
   if (email && !/^\S+@\S+\.\S+$/.test(email)) {
   showError('institute_email_id', 'Please enter a valid email.');
   }
   
   const mobileFields = [
    'nodal_officer_contact_number',
    'pia_nodal_officer_contact_no'
   ];
   
   mobileFields.forEach(id => {
    const value = getValue(id);
    if (value && !/^[0-9]{10}$/.test(value)) {
        showError(id, 'Enter valid 10-digit mobile number.');
    }
   });
   
   const fileInput = document.getElementById('registration_certificate');
   
   if (!fileInput || fileInput.files.length === 0) {
   showError('registration_certificate', 'Please upload Registration Certificate.');
   } else {
   const file = fileInput.files[0];
   
   if (file.type !== 'application/pdf') {
     showError('registration_certificate', 'Only PDF files are allowed.');
     fileInput.value = '';
   } 
   else if (file.size > 2 * 1024 * 1024) {
     showError('registration_certificate', 'File size must be less than 2MB.');
     fileInput.value = '';
   } 
   else {
     clearError('registration_certificate');
   }
   }
   
   const selectFields = [
   { name: 'institute_type_id', message: 'Please select Institute Type.' },
   { name: 'which_govt', message: 'Please select Grant-in-Aid.' }
   ];
   
   selectFields.forEach(field => {
   const el = document.getElementsByName(field.name)[0];
   if (!el || el.value === '') {
   showError(field.name, field.message);
   } else {
   clearError(field.name);
   }
   });
   
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
   
      const fileInput = document.getElementById('registration_certificate');
   
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                console.log(`Selected: ${file.name} (${(file.size / 1024).toFixed(2)} KB)`);
            }
        });
    }
   
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
                  $('#ward-dropdown').html('<option value="">-- Select Ward --</option>');
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
            //el.value = 'Not Required to Provide';
            el.readOnly = false;
         }
      });

        const heading = document.getElementById('postal_address_heading');
if (heading) {
    heading.innerText = 'PIA/NGO Address';
}

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