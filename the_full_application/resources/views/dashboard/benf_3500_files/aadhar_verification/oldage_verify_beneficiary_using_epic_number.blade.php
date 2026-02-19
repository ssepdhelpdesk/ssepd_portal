@section('title') 
OldAge EP Pension || Scheme Migration Using EPIC
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.oldage3500data.oldage_verify_beneficiary_using_epic_number_update', $oldAge3500Pensioner->id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
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
                                    <option value="MBPOAP" {{ old('scheme_name', $oldAge3500Pensioner->scheme_name ?? '') == 'MBPOAP' ? 'selected' : '' }}>MBPOAP</option>
                                    <option value="IGNOAP" {{ old('scheme_name', $oldAge3500Pensioner->scheme_name ?? '') == 'IGNOAP' ? 'selected' : '' }}>IGNOAP</option>
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
                                 <input type="text" id="name_of_the_beneficiary" name="name_of_the_beneficiary" value="{{old('name_of_the_beneficiary', $oldAge3500Pensioner->name_of_the_beneficiary)}}" class="form-control" placeholder="Name of the Staff">
                                 <div id="name_of_the_beneficiary_error"></div>
                                 @error('name_of_the_beneficiary')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="father_or_husband_name_div">
                                 <label class="form-label">Father/Husband Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="father_or_husband_name" name="father_or_husband_name" value="{{old('father_or_husband_name', $oldAge3500Pensioner->father_or_husband_name)}}" class="form-control" placeholder="Name of the Staff">
                                 <div id="father_or_husband_name_error"></div>
                                 @error('father_or_husband_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="date_of_birth_div">
                                 <label class="form-label">DOB (DD-MM-YYYY)<span class="itsrequired"> *</span></label>
                                 @php
                                 $dob = $oldAge3500Pensioner->date_of_birth;
                                 $age = $oldAge3500Pensioner->age;
                                 $isReadonly = false;
                                 $parsedDate = null;
                                 try {
                                 if ($dob && strtolower($dob) !== 'not provided by district') {
                                 $parsedDate = \Carbon\Carbon::parse($dob);
                                 $year = (int) $parsedDate->format('Y');
                                 if ($year >= 1900 && $year <= now()->year && !empty($age) && $age >= 80 && $age <= 140) {
                                 $isReadonly = true;
                                 }
                                 }
                                 } catch (\Exception $e) {
                                 $parsedDate = null;
                                 $isReadonly = false;
                                 }
                                 @endphp
                                 <input type="date"
                                    id="date_of_birth"
                                    name="date_of_birth"
                                    value="{{ $parsedDate ? $parsedDate->format('Y-m-d') : '' }}"
                                    class="form-control"
                                    placeholder="Enter Date of Birth"
                                    max="{{ now()->format('Y-m-d') }}">
                                 @if($dob == '' || $dob == null || $dob == 'Not Provided By District' || $age < 80 || $age > 140 || $age == '' || $age == null || $age == 'Not Provided By District')
                                 <small class="text-danger">
                                 Invalid DOB detected ({{ $dob }})/ or AGE ({{ $age }}). Please correct it.
                                 </small>
                                 @endif
                                 <div id="date_of_birth_error"></div>
                                 @error('date_of_birth')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="age_div">
                                 <label class="form-label">Age<span class="itsrequired"> *</span></label>
                                 <input type="text" id="age" name="age" value="{{old('age', $oldAge3500Pensioner->age)}}" class="form-control" placeholder="Age" readonly>
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
                                    <option value="Male" {{ $oldAge3500Pensioner->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $oldAge3500Pensioner->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                 </select>
                                 <div id="gender_error"></div>
                                 @error('gender')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="epic_no_div">
                                 <label class="form-label">EPIC Number<span class="itsrequired"> *</span></label>
                                 <div class="input-group">
                                    <input type="text" id="epic_no" name="epic_no" value="{{old('epic_no', $oldAge3500Pensioner->epic_no)}}" class="form-control" placeholder="EPIC Number">
                                 </div>
                                 <div id="epic_no_error"></div>
                                 <div id="check_epic_no"></div>
                                 @error('epic_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="nsap_sanction_order_no_div">
                                 <label class="form-label">NSAP Sanction Order No<span class="itsrequired"> *</span></label>
                                 @php
                                 $nsap = trim(old('nsap_sanction_order_no', $oldAge3500Pensioner->nsap_sanction_order_no ?? ''));
                                 $isReadonlyNsap = preg_match('/^OR-S-\d{6,}$/', $nsap) === 1;
                                 @endphp
                                 <input type="text"
                                 id="nsap_sanction_order_no"
                                 name="nsap_sanction_order_no"
                                 value="{{ $nsap }}"
                                 class="form-control"
                                 placeholder="Enter NSAP Sanction Order No"
                                 @if($isReadonlyNsap) readonly @endif>
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
                                 <input type="text" id="sub_collector_sanction_order_no" name="sub_collector_sanction_order_no" value="{{old('sub_collector_sanction_order_no', $oldAge3500Pensioner->sub_collector_sanction_order_no)}}" class="form-control" placeholder="Sub-Collector Sanction Order No" >
                                 <div id="sub_collector_sanction_order_no_error"></div>
                                 @error('sub_collector_sanction_order_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="pension_month_div">
                                 <label class="form-label">Pension Month(Effective From)<span class="itsrequired"> *</span></label>
                                 <input type="month" id="pension_month" name="pension_month" value="{{old('pension_month', $oldAge3500Pensioner->pension_month)}}" class="form-control" placeholder="Pension Month(Effective From)" onkeydown="return false" onpaste="return false" required="" readonly>
                                 <div id="pension_month_error"></div>
                                 @error('pension_month')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="pension_month_div">
                                 <label class="form-label">Current Address<span class="itsrequired"> *</span></label>
                                 @if($oldAge3500Pensioner->address_type == 1)
                                 <textarea class="form-control" readonly>Village: {{$oldAge3500Pensioner->village}}, GP: {{$oldAge3500Pensioner->gp_or_ward}}, Block: {{$oldAge3500Pensioner->block_or_ulb}}, Dist: {{$oldAge3500Pensioner->district}} </textarea>
                                 @elseif($oldAge3500Pensioner->address_type == 2)
                                 <textarea class="form-control" readonly>Ward: {{$oldAge3500Pensioner->gp_or_ward}}, ULB: {{$oldAge3500Pensioner->block_or_ulb}}, Dist: {{$oldAge3500Pensioner->district}} </textarea>
                                 @endif
                                 <div id="pension_month_error"></div>
                                 @error('pension_month')
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   $(document).ready(function () {
   
     $('#date_of_birth').on('change', function () {
   
       const dob = new Date(this.value);
       const today = new Date();
   
       let age = today.getFullYear() - dob.getFullYear();
       const monthDiff = today.getMonth() - dob.getMonth();
   
       if (monthDiff < 0 ||
         (monthDiff === 0 && today.getDate() < dob.getDate())) {
         age--;
   }
   
   if (age < 0 || isNaN(age)) age = 0;
   
   $('#age').val(age);
   
   if (age < 80) {
   
      Swal.fire({
        icon: 'warning',
        title: 'Invalid Age',
        text: 'Age should not be less than 80 years.',
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
        allowOutsideClick: false
     });
   
      $('#date_of_birth').val('');
      $('#age').val('');
      $('#submitButton').prop('disabled', true);
   
   } else {
      $('#submitButton').prop('disabled', false);
   }
   
   });
   
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
      $("#nsap_sanction_order_no").blur(function () {
         const sanctionNo = $(this).val().trim();
   
         $('#check_nsap_sanction_order_no').html('');
   
         if (!sanctionNo) {
            $('#check_nsap_sanction_order_no').html('<span style="color:#FF0000">Please provide NSAP Sanction Order No.</span>');
            return;
         }
   
         $.get("{{ route('admin.oldage3500data.check_benf_nsap_sanction_or_no') }}", 
            { nsap_sanction_order_no: sanctionNo }, 
            function (data) {
               if (data == 0) {
                  $('#check_nsap_sanction_order_no').html('<span style="color:#03713E">This NSAP Sanction Order No is available.</span>');
               } else if (data == 1) {
                  $('#check_nsap_sanction_order_no').html('<span style="color:#FF0000">This NSAP Sanction Order No is already registered.</span>');
                  $("#nsap_sanction_order_no").val('');
               } else if (data == 2) {
                  $('#check_nsap_sanction_order_no').html('<span style="color:#FF0000">Please provide a valid NSAP Sanction Order No.</span>');
               }
            }
            ).fail(function () {
               $('#check_nsap_sanction_order_no').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
            });
         });
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
      $("#epic_no").blur(function () {
         const epicNo = $(this).val().trim();
   
         $('#check_epic_no').html('');
   
         if (!epicNo) {
            $('#check_epic_no').html('<span style="color:#FF0000">Please provide EPIC No.</span>');
            return;
         }
   
         $.get("{{ route('admin.oldage3500data.oldage_check_verify_beneficiary_using_epic_number') }}", 
            { epic_no: epicNo }, 
            function (data) {
               if (data == 0) {
                  $('#check_epic_no').html('<span style="color:#03713E">This EPIC No is available.</span>');
               } else if (data == 1) {
                  $('#check_epic_no').html('<span style="color:#FF0000">This EPIC No is already registered.</span>');
                  $("#epic_no").val('');
               } else if (data == 2) {
                  $('#check_epic_no').html('<span style="color:#FF0000">Please provide a valid EPIC No.</span>');
               }
            }
            ).fail(function () {
               $('#check_epic_no').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
            });
         });
   });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const epicInput = document.getElementById('epic_no');
    const epicError = document.getElementById('epic_no_error');

    epicInput.style.textTransform = "uppercase";
    epicInput.setAttribute("maxlength", "10");

    const epicRegex = /^[A-Z]{3}[0-9]{7}$/;

    epicInput.addEventListener('input', function () {

        let value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

        value = value.substring(0, 10);

        this.value = value;

        if (value.length === 0) {
            epicError.innerHTML = '';
            return;
        }

        if (!epicRegex.test(value)) {
            epicError.innerHTML =
                '<span class="text-danger">Invalid EPIC format. Example: ABC1234567</span>';
        } else {
            epicError.innerHTML =
                '<span class="text-success">Valid EPIC format</span>';
        }
    });

});
</script>
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
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
         { id: 'name_of_the_beneficiary', message: 'Please enter Beneficiary Name.' },
         { id: 'father_or_husband_name', message: 'Please enter Father Husband Name.' },
         { id: 'date_of_birth', message: 'Please select DOB.' },
         { id: 'epic_no', message: 'Please enter EPIC No.' },
         { id: 'nsap_sanction_order_no', message: 'Please enter NSAP Sanction Order No.' },
         { id: 'sub_collector_sanction_order_no', message: 'Please enter SubCollector Signature Order No.' },
         { id: 'pension_month', message: 'Please Choose Pension Month.' }
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
         { name: 'scheme_name', message: 'Please select Scheme.' },
         { name: 'gender', message: 'Please select Gender.' }
      ];
   
      selectFields.forEach(field => {
         const el = document.getElementsByName(field.name)[0];
         if (!el || !el.value) {
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
                  if (form.checkValidity() && Validate()) {
                   form.requestSubmit();
                }
             }
          } else if (type === '2') {
            if (validateMunicipalityFields()) {
               if (form.checkValidity() && Validate()) {
                form.requestSubmit();
             }
          }
       }
    });
      }
   
      function showAlert(message, focusElement = null) {
   
        Swal.fire({
          icon: 'warning',
          title: 'Check All Required Fields!',
          text: message,
          timer: 1000,
          timerProgressBar: true,
          showConfirmButton: false,
          allowOutsideClick: false
       });
   
        if (focusElement) {
          setTimeout(() => focusElement.focus(), 300);
       }
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