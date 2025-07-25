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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.specialschool.store_school_staff_details')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Add Staff Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="special_school_staff_name_div">
                                 <label class="form-label">Staff Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="special_school_staff_name" name="special_school_staff_name" value="{{old('special_school_staff_name')}}" class="form-control" placeholder="Name of the Staff">
                                 <div id="special_school_staff_name_error"></div>
                                 @error('special_school_staff_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="staff_engagement_date_div">
                                 <label class="form-label">Date Of Engagement<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="staff_engagement_date" name="staff_engagement_date" value="{{old('staff_engagement_date')}}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                 <div id="staff_engagement_date_error"></div>
                                 @error('staff_engagement_date')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="staff_designation_div">
                                 <label class="form-label">Staff Designation<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="staff_designation">
                                    <option value="">-- Select Staff Designation --</option>
                                    <option value="1">Sr.SES/Principal/HM</option>
                                    <option value="2">Asst. Teacher (TG)</option>
                                    <option value="3">Classical/ Language Teacher</option>
                                    <option value="4">Asst. Teacher (TI)</option>
                                    <option value="5">Asst. Teacher (TM)</option>
                                    <option value="6">MCD/DHLS Teacher</option>
                                    <option value="7">Occupational Therapist</option>
                                    <option value="8">P.E.T.</option>
                                    <option value="9">Art Teacher</option>
                                    <option value="10">Music Teacher</option>
                                    <option value="11">Mobility Teacher</option>
                                    <option value="12">Craft teacher</option>
                                    <option value="13">Computer Teacher</option>
                                    <option value="14">Matron/Warden</option>
                                    <option value="15">Clerk-cum-Accountant</option>
                                    <option value="16">Cook</option>
                                    <option value="17">Cook-Helper</option>
                                    <option value="18">Attendant</option>
                                    <option value="19">Sweeper-cum-Watchman</option>
                                    <option value="20">Part time post, if any</option>
                                 </select>
                                 <div id="staff_designation_error"></div>
                                 @error('staff_designation')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="staff_employment_type_div">
                                 <label class="form-label">Employment Type<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Type" tabindex="1" name="staff_employment_type">
                                    <option value="">--Select--</option>
                                    <option value="1">Regular</option>
                                    <option value="2">Contractual</option>
                                    <option value="3">Part-Time</option>
                                 </select>
                                 <div id="staff_employment_type_error"></div>
                                 @error('staff_employment_type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="highest_qualification_div">
                                 <label class="form-label">Highest Qualification<span class="itsrequired"> *</span></label>
                                 <input type="text" id="highest_qualification" name="highest_qualification" value="{{old('highest_qualification')}}" class="form-control" placeholder="Highest Qualification">
                                 <div id="highest_qualification_error"></div>
                                 @error('highest_qualification')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="basic_remuneration_div">
                                 <label class="form-label">Basic Remuneration<span class="itsrequired"> *</span></label>
                                 <input type="number" step="0.01" min="0" id="basic_remuneration" name="basic_remuneration" value="{{ old('basic_remuneration') }}" class="form-control" placeholder="Basic Remuneration">
                                 <div id="basic_remuneration_error"></div>
                                 @error('basic_remuneration')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="special_school_staff_aadhar_no_div">
                                 <label class="form-label">Aadhar No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="special_school_staff_aadhar_no" name="special_school_staff_aadhar_no" value="{{old('special_school_staff_aadhar_no')}}" class="form-control" minlength="12" maxlength="12" placeholder="Aadhar No">
                                 <div id="special_school_staff_aadhar_no_error"></div>
                                 <div id="check_special_school_staff_aadhar_no"></div>
                                 @error('special_school_staff_aadhar_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="special_school_file_staff_aadhar_div">
                                 <label class="form-label">Upload Aadhar<span class="itsrequired"> *</span></label>
                                 <input type="file" class="form-control" id="special_school_file_staff_aadhar" name="special_school_file_staff_aadhar" value="{{old('special_school_file_staff_aadhar')}}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                 <div id="special_school_file_staff_aadhar_error"></div>
                                 @error('special_school_file_staff_aadhar')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="staff_email_id_div">
                                 <label class="form-label">Email ID<span class="itsrequired"> *</span></label>
                                 <input type="email" id="staff_email_id" name="staff_email_id" value="{{old('staff_email_id')}}" class="form-control" placeholder="Email ID">
                                 <div id="staff_email_id_error"></div>
                                 @error('staff_email_id')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="staff_mob_no_div">
                                 <label class="form-label">Mobile No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="staff_mob_no" name="staff_mob_no" value="{{old('staff_mob_no')}}" class="form-control" placeholder="Mobile No">
                                 <div id="staff_mob_no_error"></div>
                                 @error('staff_mob_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="staff_date_of_birth_div">
                                 <label class="form-label">Date Of Birth<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="staff_date_of_birth" name="staff_date_of_birth" value="{{old('staff_date_of_birth')}}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                 <div id="staff_date_of_birth_error"></div>
                                 @error('staff_date_of_birth')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="file_staff_image_div">
                                 <label class="form-label">Upload Staff Image <span class="itsrequired">(Geo-tagged photo inside school premises)</span></label>
                                 <input type="file" class="form-control" id="file_staff_image" name="file_staff_image" value="{{old('file_staff_image')}}" accept="image/*" aria-describedby="inputGroupFileAddon01">
                                 <div id="file_staff_image_error"></div>
                                 @error('file_staff_image')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="disability_type_div">
                                 <label class="form-label">Disability Type<span class="itsrequired"> (If Any)</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Type" tabindex="1" name="disability_type">
                                    <option value="">-- Select Disability Type --</option>
                                    <option value="1">Locomotor Disability</option>
                                    <option value="2">Leprosy Cured Person</option>
                                    <option value="3">Cerebral Palsy</option>
                                    <option value="4">Dwarfism</option>
                                    <option value="5">Muscular Dystrophy</option>
                                    <option value="6">Acid Attack Victim</option>
                                    <option value="7">Blindness</option>
                                    <option value="8">Low Vision</option>
                                    <option value="9">Deaf</option>
                                    <option value="10">Hard of Hearing</option>
                                    <option value="11">Specific Learning Disabilities</option>
                                    <option value="12">Autism Spectrum Disorder</option>
                                    <option value="13">Multiple Sclerosis</option>
                                    <option value="14">Parkinson’s Disease</option>
                                    <option value="15">Haemophilia</option>
                                    <option value="16">Thalassemia</option>
                                    <option value="17">Sickle Cell Disease</option>
                                    <option value="18">Combination of two or more disabilities</option>
                                 </select>
                                 <div id="disability_type_error"></div>
                                 @error('disability_type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="udid_no_div">
                                 <label class="form-label">UDID No<span class="itsrequired"> (If Any)</span></label>
                                 <input type="text" id="udid_no" name="udid_no" value="{{old('udid_no')}}" class="form-control" placeholder="UDID No">
                                 <div id="udid_no_error"></div>
                                 @error('udid_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="file_udid_certificate_div">
                                 <label class="form-label">UDID Certificate<span class="itsrequired"> (If Any)</span></label>
                                 <input type="file" class="form-control" id="file_udid_certificate" name="file_udid_certificate" value="{{old('file_udid_certificate')}}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                 <div id="file_udid_certificate_error"></div>
                                 @error('file_udid_certificate')
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
<script type="text/javascript">
   $(document).ready(function () {
      $("#special_school_staff_aadhar_no").blur(function () {
         const staffAadhar = $(this).val().trim();
         const is12DigitNumber = /^\d{12}$/.test(staffAadhar);
   
         $('#check_special_school_staff_aadhar_no').html('');
   
         if (!staffAadhar) {
            $('#check_special_school_staff_aadhar_no').html('<span style="color:#FF0000">Please provide an Aadhar number.</span>');
            return;
         }
   
         if (!is12DigitNumber) {
            $('#check_special_school_staff_aadhar_no').html('<span style="color:#FF0000">Aadhar must be exactly 12 digits.</span>');
            return;
         }
   
         $.get("{{ route('admin.specialschool.check_staff_aadhar') }}", 
            { special_school_staff_aadhar_no: staffAadhar }, 
            function (data) {
               if (data == 0) {
                  $('#check_special_school_staff_aadhar_no').html('<span style="color:#03713E">This Aadhar is available.</span>');
               } else if (data == 1) {
                  $('#check_special_school_staff_aadhar_no').html('<span style="color:#FF0000">This Aadhar is already registered.</span>');
                  $("#special_school_staff_aadhar_no").val('');
               } else if (data == 2) {
                  $('#check_special_school_staff_aadhar_no').html('<span style="color:#FF0000">Please provide a valid Aadhar.</span>');
               }
            }
         ).fail(function () {
            $('#check_special_school_staff_aadhar_no').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
         });
      });
   });
</script>
<script>
   const aadhaarInputs = document.querySelectorAll('[name="special_school_staff_aadhar_no"]');
   aadhaarInputs.forEach(function(input) {
      input.addEventListener('blur', function(event) {
         const uid = event.target.value.trim();
         const fieldName = event.target.name;
         const errorDiv = document.querySelector(`#${fieldName}_error`);
         if (errorDiv) errorDiv.innerHTML = '';
         
         const Verhoeff = {
            d: [
                  [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                  [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
                  [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
                  [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
                  [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
                  [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
                  [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
                  [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
                  [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
                  [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
               ],
            p: [
                  [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                  [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
                  [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
                  [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
                  [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
                  [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
                  [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
                  [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
               ],
            j: [0, 4, 3, 2, 1, 5, 6, 7, 8, 9],
            check: function(str) {
               var c = 0;
               str.replace(/\D+/g, "").split("").reverse().join("").replace(/[\d]/g, function(u, i) {
                  c = Verhoeff.d[c][Verhoeff.p[i % 8][parseInt(u, 10)]];
               });
               return c;
            }
         };

         if (uid === "") return;

         if (Verhoeff.check(uid) === 0) {
            event.target.style.borderColor = '';
         } else {
            if (errorDiv) {
               errorDiv.innerHTML = '<label class="error">Aadhaar number is not valid!</label>';
               errorDiv.style.color = 'red';
            }
            event.target.style.borderColor = 'red';
            event.target.value = '';
         }
      });
   });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
   const udidInput = document.getElementById('udid_no');
   const errorDiv = document.getElementById('udid_no_error');

   udidInput.addEventListener('blur', function () {
      const udid = udidInput.value.trim();
      errorDiv.innerHTML = '';

      if (!udid) {
         return;
      }

      if (udid.length < 18) {
         errorDiv.innerHTML = '<label class="error">UDID must be at least 18 characters.</label>';
         return;
      }

      fetch(`{{ route('admin.specialschool.check_staff_udidno') }}?udid_no=${encodeURIComponent(udid)}`)
         .then(response => response.json())
         .then(data => {
            if (data === 1) {
               errorDiv.innerHTML = '<label class="error">This UDID is already registered.</label>';
               udidInput.style.borderColor = 'red';
               udidInput.value = '';
            } else if (data === 0) {
               errorDiv.innerHTML = '<span style="color:#03713E">This UDID is available.</span>';
               udidInput.style.borderColor = '';
            } else if (data === 2) {
               errorDiv.innerHTML = '<label class="error">Invalid UDID number format.</label>';
               udidInput.style.borderColor = 'red';
               udidInput.value = '';
            }
         })
         .catch(error => {
            console.error('Error checking UDID:', error);
            errorDiv.innerHTML = '<label class="error">An error occurred. Try again.</label>';
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
         { id: 'special_school_staff_name', message: 'Please enter Staff Name.' },
         { id: 'staff_engagement_date', message: 'Please select Engagement Date.' },
         { id: 'basic_remuneration', message: 'Please enter Basic Remuneratiuon.' },
         { id: 'highest_qualification', message: 'Please enter Highest Qualification.' },
         { id: 'special_school_staff_aadhar_no', message: 'Please enter Aadhar No.' },
         { id: 'staff_mob_no', message: 'Please enter Mobile No.' },
         { id: 'staff_date_of_birth', message: 'Please select DOB.' }
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
         { name: 'staff_designation', message: 'Please select Designation.' },
         { name: 'staff_employment_type', message: 'Please select Employment Type.' }
      ];
   
      selectFields.forEach(field => {
         const el = document.getElementsByName(field.name)[0];
         if (!el || !el.value) {
            showError(field.name, field.message);
         } else {
            clearError(field.name);
         }
      });
   
      const aadharFile = document.getElementById('special_school_file_staff_aadhar').files[0];
      if (!aadharFile) {
         showError('special_school_file_staff_aadhar', 'Please upload the Aadhar file.');
      } else {
         clearError('special_school_file_staff_aadhar');
   
         const allowedType = 'application/pdf';
         const maxSize = 3 * 1024 * 1024;
   
         if (aadharFile.type !== allowedType) {
            showError('special_school_file_staff_aadhar', 'Only PDF files are allowed.');
            isValid = false;
         } else if (aadharFile.size > maxSize) {
            showError('special_school_file_staff_aadhar', 'File size must be less than 3MB.');
            isValid = false;
         }
      }
   
      const imageFile = document.getElementById('file_staff_image').files[0];
      if (!imageFile) {
         showError('file_staff_image', 'Please upload the Staff Geo-tagged photo inside school premises.');
      } else {
         clearError('file_staff_image');
   
         const allowedTypes = ['image/jpeg', 'image/png'];
         const maxSize = 3 * 1024 * 1024;
   
         if (!allowedTypes.includes(imageFile.type)) {
            showError('file_staff_image', 'Only JPG or PNG image files are allowed.');
            isValid = false;
         } else if (imageFile.size > maxSize) {
            showError('file_staff_image', 'File size must be less than 3MB.');
            isValid = false;
         }
      }
   
      const disabilityType = document.getElementsByName('disability_type')[0].value;
      const udidNo = getValue('udid_no');
      const udidFile = document.getElementById('file_udid_certificate').files[0];
   
      const anyDisabilityFilled = disabilityType || udidNo || udidFile;
   
      if (anyDisabilityFilled) {
         if (!disabilityType) {
            showError('disability_type', 'Please select Disability Type.');
         } else {
            clearError('disability_type');
         }
   
         if (!udidNo) {
            showError('udid_no', 'Please enter UDID Number.');
         } else {
            clearError('udid_no');
         }
   
         if (!udidFile) {
            showError('file_udid_certificate', 'Please upload UDID Certificate.');
         } else {
            const allowedType = 'application/pdf';
            const maxSize = 3 * 1024 * 1024;
   
            if (udidFile.type !== allowedType) {
               showError('file_udid_certificate', 'Only PDF files are allowed.');
            } else if (udidFile.size > maxSize) {
               showError('file_udid_certificate', 'File size must be less than 3MB.');
            } else {
               clearError('file_udid_certificate');
            }
         }
      } else {
         clearError('disability_type');
         clearError('udid_no');
         clearError('file_udid_certificate');
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