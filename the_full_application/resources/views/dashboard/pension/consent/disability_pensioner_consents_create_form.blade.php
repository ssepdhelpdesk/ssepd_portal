@section('title') 
Pension || Disabled Consent Data Entry
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.pensionforbeneficiaries.disability_pensioner_consents_store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="scheme_name_div">
                                 <label class="form-label">Scheme Name<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="scheme_name" name="scheme_name">
                                    <option value="">Please Select</option>
                                    <option value="MBPDP">MBPDP</option>
                                    <option value="DisabilityPensionAidsHiv">Disability Pension AIDS/HIV</option>
                                 </select>
                                 <div id="scheme_name_error"></div>
                                 @error('scheme_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="name_of_the_beneficiary_div">
                                 <label class="form-label">Beneficiary Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="name_of_the_beneficiary" name="name_of_the_beneficiary" value="{{old('name_of_the_beneficiary')}}" class="form-control" placeholder="Name of the Beneficiary" >
                                 <div id="name_of_the_beneficiary_error"></div>
                                 @error('name_of_the_beneficiary')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="father_or_husband_name_div">
                                 <label class="form-label">Father/Husband Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="father_or_husband_name" name="father_or_husband_name" value="{{old('father_or_husband_name')}}" class="form-control" placeholder="Name of the Father/Husband" >
                                 <div id="father_or_husband_name_error"></div>
                                 @error('father_or_husband_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="date_of_birth_div">
                                 <label class="form-label">DOB (DD-MM-YYYY)<span class="itsrequired"> *</span></label>
                                 <input type="date" id="date_of_birth" name="date_of_birth" value="" class="form-control" placeholder="Name of the Staff">
                                 <div id="date_of_birth_error"></div>
                                 @error('date_of_birth')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="age_div">
                                 <label class="form-label">Age<span class="itsrequired"> *</span></label>
                                 <input type="text" id="age" name="age" value="{{old('age')}}" class="form-control" placeholder="Age" >
                                 <div id="age_error"></div>
                                 @error('age')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="gender_div">
                                 <label class="form-label">Gender<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="gender" name="gender">
                                    <option value="">Please Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                 </select>
                                 <div id="gender_error"></div>
                                 @error('gender')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="udid_no_div">
                                 <label class="form-label">UDID No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="udid_no" name="udid_no" value="{{old('udid_no')}}" class="form-control" placeholder="UDID no of Beneficiary" >
                                 <div id="udid_no_error"></div>
                                 @error('udid_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="disability_category_div">
                                 <label class="form-label">Disability category<span class="itsrequired"> *</span></label>
                                 <input type="text" id="disability_category" name="disability_category" value="{{old('disability_category')}}" class="form-control" placeholder="Disability category of Beneficiary" >
                                 <div id="disability_category_error"></div>
                                 @error('disability_category')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="disability_percentage_div">
                                 <label class="form-label">Disability Percentage<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="disability_percentage" name="disability_percentage">
                                    <option value="">Please Select</option>
                                    <option value="80">80</option>
                                    <option value="81">81</option>
                                    <option value="82">82</option>
                                    <option value="83">83</option>
                                    <option value="84">84</option>
                                    <option value="85">85</option>
                                    <option value="86">86</option>
                                    <option value="87">87</option>
                                    <option value="88">88</option>
                                    <option value="89">89</option>
                                    <option value="90">90</option>
                                    <option value="91">91</option>
                                    <option value="92">92</option>
                                    <option value="93">93</option>
                                    <option value="94">94</option>
                                    <option value="95">95</option>
                                    <option value="96">96</option>
                                    <option value="97">97</option>
                                    <option value="98">98</option>
                                    <option value="99">99</option>
                                    <option value="100">100</option>
                                 </select>
                                 <div id="disability_percentage_error"></div>
                                 @error('disability_percentage')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="aadhaar_no_div">
                                 <label class="form-label">Aadhaar Number<span class="itsrequired"> *</span></label>
                                 <input type="text" id="aadhaar_no" name="aadhaar_no" value="{{old('aadhaar_no')}}" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" placeholder="Aadhaar Number" >
                                 <div id="aadhaar_no_error"></div>
                                 <div id="check_aadhaar_no"></div>
                                 @error('aadhaar_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="nsap_sanction_order_no_div">
                                 <label class="form-label">NSAP Sanction Order No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="nsap_sanction_order_no" name="nsap_sanction_order_no" value="{{old('nsap_sanction_order_no')}}" class="form-control" placeholder="NSAP Sanction Order No" >
                                 <div id="nsap_sanction_order_no_error"></div>
                                 <div id="check_nsap_sanction_order_no"></div>
                                 @error('nsap_sanction_order_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="sub_collector_sanction_order_no_div">
                                 <label class="form-label">Sub-Collector Sanction Order No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="sub_collector_sanction_order_no" name="sub_collector_sanction_order_no" value="{{old('sub_collector_sanction_order_no')}}" class="form-control" placeholder="Sub-Collector Sanction Order No" >
                                 <div id="sub_collector_sanction_order_no_error"></div>
                                 @error('sub_collector_sanction_order_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="pension_amount_div">
                                 <label class="form-label">Pension Amount<span class="itsrequired"> *</span></label>
                                 <input type="text" id="pension_amount" name="pension_amount" value="{{old('pension_amount')}}" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" placeholder="Pension Amount" >
                                 <div id="pension_amount_error"></div>
                                 @error('pension_amount')
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
   /* =========================
      COMMON HELPERS
   ========================= */
   function showError(id, message) {
      const div = document.getElementById(id + '_error');
      if (div) div.innerHTML = `<label class="error">${message}</label>`;
   }
   
   function clearError(id) {
      const div = document.getElementById(id + '_error');
      if (div) div.innerHTML = '';
   }
   
   function getValue(id) {
      const el = document.getElementById(id);
      return el ? el.value.trim() : '';
   }
   
   /* =========================
      VERHOEFF (AADHAAR)
   ========================= */
   const Verhoeff = {
      d: [
         [0,1,2,3,4,5,6,7,8,9],
         [1,2,3,4,0,6,7,8,9,5],
         [2,3,4,0,1,7,8,9,5,6],
         [3,4,0,1,2,8,9,5,6,7],
         [4,0,1,2,3,9,5,6,7,8],
         [5,9,8,7,6,0,4,3,2,1],
         [6,5,9,8,7,1,0,4,3,2],
         [7,6,5,9,8,2,1,0,4,3],
         [8,7,6,5,9,3,2,1,0,4],
         [9,8,7,6,5,4,3,2,1,0]
      ],
      p: [
         [0,1,2,3,4,5,6,7,8,9],
         [1,5,7,6,2,8,3,0,9,4],
         [5,8,0,3,7,9,6,1,4,2],
         [8,9,1,6,0,4,3,5,2,7],
         [9,4,5,3,1,2,6,8,7,0],
         [4,2,8,6,5,7,3,9,0,1],
         [2,7,9,3,8,0,6,4,1,5],
         [7,0,4,6,9,1,3,2,5,8]
      ],
      check: function(str) {
         let c = 0;
         str.replace(/\D+/g, "").split("").reverse().forEach((u, i) => {
            c = this.d[c][this.p[i % 8][parseInt(u, 10)]];
         });
         return c === 0;
      }
   };
   
   /* =========================
      DOCUMENT READY
   ========================= */
   $(document).ready(function () {
      
      /* ===== Aadhaar Validation + Duplicate Check ===== */
      $("#aadhaar_no").on('blur', function () {
         const aadhaar = this.value.trim();
         $('#check_aadhaar_no').html('');
         clearError('aadhaar_no');
         
         if (!aadhaar) {
            $('#check_aadhaar_no').html('<span style="color:red">Please provide an Aadhar number.</span>');
            return;
         }
         
         if (!/^\d{12}$/.test(aadhaar)) {
            $('#check_aadhaar_no').html('<span style="color:red">Aadhar must be exactly 12 digits.</span>');
            this.value = '';
            return;
         }
         
         if (!Verhoeff.check(aadhaar)) {
            showError('aadhaar_no', 'Aadhaar number is not valid!');
            this.value = '';
            return;
         }
         
         $.get("{{ route('admin.pensionforbeneficiaries.check_benf_aadhar') }}",
            { aadhaar_no: aadhaar },
            data => {
               if (data == 0) {
                  $('#check_aadhaar_no').html('<span style="color:#03713E">This Aadhar is available.</span>');
               }
               if (data == 1) {
                  $('#check_aadhaar_no').html('<span style="color:red">This Aadhar is already registered with Disability Pension.</span>');
                  $("#aadhaar_no").val('');
               }
               if (data == 2) {
                  $('#check_aadhaar_no').html('<span style="color:red">This Aadhar is already registered with Old Age Pension.</span>');
                  $("#aadhaar_no").val('');
               }
               if (data == 3) {
                  $('#check_aadhaar_no').html('<span style="color:red">An error occurred. Please try again.</span>');
               }
            }
            ).fail(() => {
               $('#check_aadhaar_no').html('<span style="color:red">An error occurred. Please try again.</span>');
            });
         });
      
      /* ===== NSAP Sanction Validation ===== */
      $("#nsap_sanction_order_no").on('blur', function () {
         const val = this.value.trim();
         $('#check_nsap_sanction_order_no').html('');
         
         if (!val) {
            $('#check_nsap_sanction_order_no').html('<span style="color:red">Please provide NSAP Sanction Order No.</span>');
            return;
         }
         
         $.get("{{ route('admin.pensionforbeneficiaries.check_benf_nsap_sanction_or_no') }}",
            { nsap_sanction_order_no: val },
            data => {
               if (data == 0) {
                $('#check_nsap_sanction_order_no')
                .html('<span style="color:#03713E">This NSAP Sanction Order No is available.</span>');
             }
   
             if (data == 1) {
                $('#check_nsap_sanction_order_no')
                .html('<span style="color:red">This NSAP Sanction Order No is already registered with Disability Pension.</span>');
                $("#nsap_sanction_order_no").val('');
             }
   
             if (data == 2) {
                $('#check_nsap_sanction_order_no')
                .html('<span style="color:red">This NSAP Sanction Order No is already registered with OldAge Pension.</span>');
                $("#nsap_sanction_order_no").val('');
             }
   
             if (data == 3) {
                $('#check_nsap_sanction_order_no')
                .html('<span style="color:red">An error occurred. Please try again.</span>');
             }
          }
          );
      });
      
      /* ===== DOB → AGE ===== */
      $('#date_of_birth').on('change', function () {
         const dob = new Date(this.value);
         const today = new Date();
         let age = today.getFullYear() - dob.getFullYear();
         const m = today.getMonth() - dob.getMonth();
         if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
         $('#age').val(age >= 0 ? age : 0);
         
         if (age < 0 || isNaN(age)) {
            alert('Age should not be less than 0 years.');
            this.value = '';
            $('#age').val('');
         }
      });
   });
   
   /* =========================
      UDID VALIDATION
   ========================= */
   document.getElementById('udid_no').addEventListener('blur', function () {
   const udid = this.value.trim();
   clearError('udid_no');
   
   if (!udid) return;
   
   if (udid.length < 18) {
      showError('udid_no', 'UDID must be at least 18 characters.');
      return;
   }
   
   fetch(`{{ route('admin.pensionforbeneficiaries.check_benf_udidno') }}?udid_no=${encodeURIComponent(udid)}`)
   .then(r => r.json())
   .then(data => {
      if (data === 1) {
         showError('udid_no', 'This UDID is already registered.');
         this.value = '';
      }
      if (data === 2) {
         showError('udid_no', 'Invalid UDID number format.');
         this.value = '';
      }
   });
   });
   
   /* =========================
      FINAL FORM VALIDATION
   ========================= */
   function Validate() {
   let valid = true;
   
   const requiredText = {
      name_of_the_beneficiary: 'Please enter Beneficiary Name.',
      father_or_husband_name: 'Please enter Father Husband Name.',
      date_of_birth: 'Please select DOB.',
      udid_no: 'Please enter UDID No.',
      disability_category: 'Please enter Disability Category.',
      aadhaar_no: 'Please enter Aadhar No.',
      nsap_sanction_order_no: 'Please enter NSAP Sanction Order No.',
      sub_collector_sanction_order_no: 'Please enter SubCollector Signature Order No.',
      pension_amount: 'Please enter Pension Amount.'
   };
   
   Object.keys(requiredText).forEach(id => {
      if (!getValue(id)) {
         showError(id, requiredText[id]);
         valid = false;
      } else {
         clearError(id);
      }
   });
   
   ['scheme_name', 'gender', 'disability_percentage'].forEach(id => {
      const el = document.getElementById(id);
      if (!el || el.value === '') {
         showError(id, 'Please select ' + id.replaceAll('_', ' ') + '.');
         valid = false;
      } else {
         clearError(id);
      }
   });
   
   if (!document.querySelector('input[name="ngo_address_type"]:checked')) {
      showError('ngo_address_type', 'Please select Address Type.');
      valid = false;
   } else {
      clearError('ngo_address_type');
   }
   
   return valid;
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