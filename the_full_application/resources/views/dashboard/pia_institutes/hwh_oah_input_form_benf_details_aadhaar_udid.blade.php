@section('title') 
PIA || HWH/OAH Institute Beneficiary Details Form
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.piainstitutes.pia_institute_benf_details_store', $piainstitute->id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
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
                                 <input type="date" id="date_of_birth" name="date_of_birth" value="{{old('date_of_birth')}}" max="{{ date('Y-m-d') }}" class="form-control" placeholder="DOB">
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
                              <div class="form-group" id="beneficiary_mobile_div">
                                 <label class="form-label">Beneficiary Mobile No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="beneficiary_mobile" name="beneficiary_mobile"
                                    class="form-control" maxlength="10" inputmode="numeric" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)" placeholder="Beneficiary Mobile No" >
                                 <div id="beneficiary_mobile_error"></div>
                                 <div id="check_beneficiary_mobile"></div>
                                 @error('beneficiary_mobile')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="gender_div">
                                 <label class="form-label">Gender<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="gender" name="gender">
                                    <option value="">Please Select</option>
                                    @foreach($gender as $g)
                                    <option value="{{ $g->id }}">{{ $g->gender_name }}</option>
                                    @endforeach
                                 </select>
                                 <div id="gender_error"></div>
                                 @error('gender')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="having_aadhaar_div">
                                 <label class="form-label">Having Aadhaar?<span class="itsrequired"> *</span></label>
                                 <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio me-3">
                                       <input type="radio" id="having_aadhaar_yes" name="having_aadhaar" value="1"
                                          class="form-check-input" {{ old('having_aadhaar', '1') == '1' ? 'checked' : '' }}>
                                       <label class="form-check-label" for="having_aadhaar_yes">Yes</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                       <input type="radio" id="having_aadhaar_no" name="having_aadhaar" value="2"
                                          class="form-check-input" {{ old('having_aadhaar') == '2' ? 'checked' : '' }}>
                                       <label class="form-check-label" for="having_aadhaar_no">No</label>
                                    </div>
                                 </div>
                                 <div id="having_aadhaar_error"></div>
                                 @error('having_aadhaar')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3 aadhaar_section">
                              <div class="form-group" id="aadhaar_no_div">
                                 <label class="form-label">Aadhaar Number<span class="itsrequired"> *</span></label>
                                 <div class="input-group">
                                    <input type="text" id="aadhaar_no" name="aadhaar_no" value="{{old('aadhaar_no')}}" class="form-control" placeholder="Aadhaar Number" >
                                    <span class="input-group-btn"><button class="btn btn-info text-white" type="button" id="btnVerifyAadhaar">Verify!</button></span>
                                    <input type="hidden" id="verified_aadhar" class="form-control" name="verified_aadhar">
                                    <input type="hidden" id="verified_aadhar_remarks" class="form-control" name="verified_aadhar_remarks">
                                 </div>
                                 <div id="aadhaar_verify_result" class="mt-1"></div>
                                 <div id="aadhaar_no_error"></div>
                                 <div id="check_aadhaar_no"></div>
                                 @error('aadhaar_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                                 @error('verified_aadhar')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                                 @error('verified_aadhar_remarks')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3 aadhaar_section">
                              <div class="form-group" id="aadhaar_file_div">
                                 <label class="form-label">Upload Aadhaar Card<span class="itsrequired"> *</span></label>
                                 <input type="file" id="aadhaar_file" name="aadhaar_file" value="{{old('aadhaar_file')}}" class="form-control" placeholder="Registration No" accept="application/pdf">
                                 <div id="aadhaar_file_error"></div>
                                 @error('aadhaar_file')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3 aadhaar_reason_section">
                              <div class="form-group" id="aadhaar_not_available_reason_div">
                                 <label class="form-label">Reason for Non-Availability of Aadhaar<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="aadhaar_not_available_reason" name="aadhaar_not_available_reason">
                                    <option value="">Please Select</option>
                                    <option value="Rescue from Field" {{ old('aadhaar_not_available_reason') == 'Rescue from Field' ? 'selected' : '' }}>Rescue from Field</option>
                                    <option value="Finger Print Issue" {{ old('aadhaar_not_available_reason') == 'Finger Print Issue' ? 'selected' : '' }}>Finger Print Issue</option>
                                    <option value="Iris Issue" {{ old('aadhaar_not_available_reason') == 'Iris Issue' ? 'selected' : '' }}>Iris Issue</option>
                                 </select>
                                 <div id="aadhaar_not_available_reason_error"></div>
                                 @error('aadhaar_not_available_reason')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                               </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="beneficiary_file_div">
                                 <label class="form-label">Upload Beneficiary Image<span class="itsrequired"> *</span></label>
                                 <input type="file" id="beneficiary_file" name="beneficiary_file" value="{{old('beneficiary_file')}}" class="form-control" placeholder="Registration No" accept=".jpg,.jpeg,.png">
                                 <div id="beneficiary_file_error"></div>
                                 @error('beneficiary_file')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="date_of_joining_div">
                                 <label class="form-label">Date of Joining (DD-MM-YYYY)<span class="itsrequired"> *</span></label>
                                 <input type="date" id="date_of_joining" name="date_of_joining" value="{{old('date_of_joining')}}" max="{{ date('Y-m-d') }}" class="form-control" placeholder="DOB">
                                 <div id="date_of_joining_error"></div>
                                 @error('date_of_joining')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>                           
                           <div class="col-md-3">
                              <div class="form-group" id="bank_ac_no_div">
                                 <label class="form-label">Bank Account No<span class="itsrequired"> </span></label>
                                 <input type="text" id="bank_ac_no" name="bank_ac_no" value="{{old('bank_ac_no')}}" class="form-control" placeholder="Bank Account No" >
                                 <div id="bank_ac_no_error"></div>
                                 <div id="check_bank_ac_no"></div>
                                 @error('bank_ac_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="bank_ifsc_div">
                                 <label class="form-label">IFSC Code<span class="itsrequired"> </span></label>
                                 <select class="form-control show-tick ms select2" id="bank_ifsc" name="bank_ifsc">
                                    <option value="">Please Select</option>
                                    @foreach($bankmaster as $bank_ifsc)
                                    <option value="{{ $bank_ifsc->bank_id }}">{{ $bank_ifsc->bank_ifsc }}</option>
                                    @endforeach
                                 </select>
                                 <div id="bank_ifsc_error"></div>
                                 @error('bank_ifsc')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="beneficiary_bank_file_div">
                                 <label class="form-label">Upload Beneficiary Bank Passbook<span class="itsrequired"> </span></label>
                                 <input type="file" id="beneficiary_bank_file" name="beneficiary_bank_file" value="{{old('beneficiary_bank_file')}}" class="form-control" placeholder="Registration No" accept="application/pdf">
                                 <div id="beneficiary_bank_file_error"></div>
                                 @error('beneficiary_bank_file')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="is_disabled_div">
                                 <label class="form-label">Is a Disabled Person?<span class="itsrequired"> *</span></label>
                                 <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio me-3">
                                       <input type="radio" id="yes" name="is_disabled" value="1"
                                          class="form-check-input">
                                       <label class="form-check-label" for="yes">Yes</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                       <input type="radio" id="no" name="is_disabled" value="2"
                                          class="form-check-input" checked>
                                       <label class="form-check-label" for="no">No</label>
                                    </div>
                                 </div>
                                 <div id="is_disabled_error"></div>
                                 @error('is_disabled')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3 disability_section">
                              <div class="form-group" id="udid_no_div">
                                 <label class="form-label">UDID No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="udid_no" name="udid_no" value="{{old('udid_no')}}" class="form-control" placeholder="UDID no of Beneficiary" >
                                 <div id="udid_no_error"></div>
                                 <div id="check_udid_no"></div>
                                 @error('udid_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3 disability_section">
                              <div class="form-group" id="beneficiary_udid_file_div">
                                 <label class="form-label">Upload UDID Certificate<span class="itsrequired"> *</span></label>
                                 <input type="file" id="beneficiary_udid_file" name="beneficiary_udid_file" value="{{old('beneficiary_udid_file')}}" class="form-control" placeholder="UDID Certificate" accept="application/pdf">
                                 <div id="beneficiary_udid_file_error"></div>
                                 @error('beneficiary_udid_file')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3 disability_section">
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
                              <div class="form-group" id="therapy_type_div">
                                 <label class="form-label">Therapy Type<span class="itsrequired"> *</span></label>
                                 <select class="form-control show-tick ms select2" id="therapy_type" name="therapy_type">
                                    <option value="">Please Select</option>
                                    <option value="1">Residential</option>
                                    <option value="2">Non-Residential</option>
                                 </select>
                                 <div id="therapy_type_error"></div>
                                 @error('therapy_type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_address_type_div">
                                 <label class="form-label">Address Type<span class="itsrequired"> *</span></label>
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
      $("#aadhaar_no").blur(function () {
         const staffAadhar = $(this).val().trim();
         const is12DigitNumber = /^\d{12}$/.test(staffAadhar);
   
         $('#check_aadhaar_no').html('');
   
         if (!staffAadhar) {
            $('#check_aadhaar_no').html('<span style="color:#FF0000">Please provide an Aadhar number.</span>');
            return;
         }
   
         if (!is12DigitNumber) {
            $('#check_aadhaar_no').html('<span style="color:#FF0000">Aadhar must be exactly 12 digits.</span>');
            return;
         }
   
         $.get("{{ route('admin.piainstitutes.check_benf_aadhar') }}", 
            { aadhaar_no: staffAadhar }, 
            function (data) {
               if (data == 0) {
                  $('#check_aadhaar_no').html('<span style="color:#03713E">This Aadhar is available.</span>');
               } else if (data == 1) {
                  $('#check_aadhaar_no').html('<span style="color:#FF0000">This Aadhar is already registered.</span>');
                  $("#aadhaar_no").val('');
               } else if (data == 2) {
                  $('#check_aadhaar_no').html('<span style="color:#FF0000">Please provide a valid Aadhar.</span>');
               }
            }
            ).fail(function () {
               $('#check_aadhaar_no').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
            });
         });
   });
</script>
<script>
   const aadhaarInputs = document.querySelectorAll('[name="aadhaar_no"]');
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
<script type="text/javascript">
   $(document).ready(function () {
      $("#udid_no").blur(function () {
         const udid_no = $(this).val().trim();
   
         $('#check_udid_no').html('');
   
         if (!udid_no) {
            $('#check_udid_no').html('<span style="color:#FF0000">Please provide a UDID No.</span>');
            return;
         }
   
         $.get("{{ route('admin.piainstitutes.check_benf_udid') }}", 
            { udid_no: udid_no }, 
            function (data) {
               if (data == 0) {
                  $('#check_udid_no').html('<span style="color:#03713E">This UDID No is available.</span>');
               } else if (data == 1) {
                  $('#check_udid_no').html('<span style="color:#FF0000">This UDID No is already registered.</span>');
                  $("#udid_no").val('');
               }
            }
         ).fail(function () {
            $('#check_udid_no').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
         });
      });
   });
</script>
<script>
   $(document).ready(function () {
   
    $('#aadhaar_no, #name_of_the_beneficiary').on('input', function () {
     $('#verified_aadhar').val('');
     $('#verified_aadhar_remarks').val('');
     $('#aadhaar_verify_result').html('');
   
     $('#btnVerifyAadhaar')
     .prop('disabled', false)
     .removeClass('btn-success')
     .addClass('btn-info')
     .text('Verify!');
   
     $('#aadhaar_no, #name_of_the_beneficiary').prop('readonly', false);
   });
   
    $(document).on('click', '#btnVerifyAadhaar', function () {
   
     let aadhaar = $('#aadhaar_no').val().trim();
     let name    = $('#name_of_the_beneficiary').val().trim();
   
     $('#aadhaar_verify_result').html('');
     $('#verified_aadhar').val('');
     $('#verified_aadhar_remarks').val('');
   
     if (!/^\d{12}$/.test(aadhaar)) {
      $('#aadhaar_verify_result').html(
       '<span class="text-danger">Enter a valid 12-digit Aadhaar number</span>'
       );
      return;
   }
   
   if (name === '') {
      $('#aadhaar_verify_result').html(
       '<span class="text-danger">Enter beneficiary name first</span>'
       );
      return;
   }
   
   $('#btnVerifyAadhaar')
   .prop('disabled', true)
   .text('Verifying...');
   
   $.ajax({
      url: "{{ route('admin.oldage3500data.oldage_aadhar_verification_process') }}",
      type: "POST",
      dataType: "json",
      data: {
       _token: "{{ csrf_token() }}",
       aadhaar_no: aadhaar,
       name_of_the_beneficiary: name
    },
   
    success: function (res) {
       let message = res.data ?? '';
   
       $('#verified_aadhar_remarks').val(message);
   
       if (typeof message === 'string' && message.toLowerCase().includes('verify successfully')) {
   
        $('#verified_aadhar').val(1);
   
        $('#aadhaar_verify_result').html(
         '<span class="badge bg-success">Aadhaar Verified Successfully</span>'
         );
   
        $('#btnVerifyAadhaar')
        .prop('disabled', true)
        .removeClass('btn-info')
        .addClass('btn-success')
        .text('Verified');
   
        $('#aadhaar_no, #name_of_the_beneficiary').prop('readonly', true);
   
     } else {
        $('#verified_aadhar').val(0);
   
        $('#aadhaar_verify_result').html(
         '<span class="badge bg-danger">' + message + '</span>'
         );
   
        $('#btnVerifyAadhaar')
        .prop('disabled', false)
        .removeClass('btn-success')
        .addClass('btn-info')
        .text('Verify!');
   
        $('#aadhaar_no, #name_of_the_beneficiary').prop('readonly', false);
     }
   },
   
   error: function (xhr) {
    let msg = 'Verification failed';
    if (xhr.responseJSON) {
     msg = xhr.responseJSON.exception ??
     xhr.responseJSON.response ??
     msg;
   }
   
   $('#verified_aadhar').val(0);
   $('#verified_aadhar_remarks').val(msg);
   
   $('#aadhaar_verify_result').html(
     '<span class="badge bg-danger">' + msg + '</span>'
     );
   
   $('#btnVerifyAadhaar')
   .prop('disabled', false)
   .removeClass('btn-success')
   .addClass('btn-info')
   .text('Verify!');
   
   $('#aadhaar_no, #name_of_the_beneficiary').prop('readonly', false);
   }
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
   
     // ========================
     // BASIC REQUIRED FIELDS
     // ========================
    const requiredFields = [
    { id: 'name_of_the_beneficiary', message: 'Please enter Beneficiary Name.' },
    { id: 'father_or_husband_name', message: 'Please enter Father/Husband Name.' },
    { id: 'beneficiary_mobile', message: 'Please enter Mobile Number.' },
    { id: 'date_of_birth', message: 'Please select DOB.' },
    { id: 'date_of_joining', message: 'Please select Date of Joining.' }
    /*{ id: 'bank_ac_no', message: 'Please enter Bank Account No.' }*/
    ];
    
    requiredFields.forEach(field => {
    const value = getValue(field.id);
    if (!value) {
    showError(field.id, field.message);
    } else {
    clearError(field.id);
    }
    });
    
     // ========================
     // SELECT VALIDATION
     // ========================
    const selectFields = [
    { name: 'gender', message: 'Please select Gender.' },
    { name: 'therapy_type', message: 'Please select Therapy Type.' }
    /*{ name: 'bank_ifsc', message: 'Please select IFSC Code.' }*/
    ];
    
    selectFields.forEach(field => {
    const el = document.getElementsByName(field.name)[0];
    if (!el || !el.value) {
    showError(field.name, field.message);
    } else {
    clearError(field.name);
    }
    });
    
     // ========================
     // RADIO VALIDATION
     // ========================
    const addressRadios = document.querySelectorAll('input[name="ngo_address_type"]');
    let addressSelected = false;
    
    addressRadios.forEach(radio => {
    if (radio.checked) addressSelected = true;
    });
    
    if (!addressSelected) {
    showError('ngo_address_type', 'Please select Address Type.');
    } else {
    clearError('ngo_address_type');
    }
    
    const havingAadhaarRadios = document.querySelectorAll('input[name="having_aadhaar"]');
    let havingAadhaarSelected = false;
    havingAadhaarRadios.forEach(radio => {
    if (radio.checked) havingAadhaarSelected = true;
    });
    
    if (!havingAadhaarSelected) {
    showError('having_aadhaar', 'Please select whether having Aadhaar.');
    } else {
    clearError('having_aadhaar');
    }
    
     // ========================
     // FILE VALIDATION
     // ========================
    const fileFields = [
    { id: 'beneficiary_file', message: 'Please upload Beneficiary Image.' }
    /*{ id: 'beneficiary_bank_file', message: 'Please upload Bank Passbook.' }*/
    ];
    
    fileFields.forEach(field => {
    const el = document.getElementById(field.id);
    if (!el || el.files.length === 0) {
    showError(field.id, field.message);
    } else {
    clearError(field.id);
    }
    });
    
     // ========================
     // DISABILITY CONDITIONAL VALIDATION
     // ========================
    const isDisabled = document.querySelector('input[name="is_disabled"]:checked')?.value;
    
    if (isDisabled == '1') {
         // Required ONLY if YES
    const disabilityFields = [
    { id: 'udid_no', message: 'Please enter UDID No.' },
    { id: 'beneficiary_udid_file', message: 'Please upload UDID Certificate.' },
    { id: 'disability_category', message: 'Please enter Disability Category.' }
    ];
    
    disabilityFields.forEach(field => {
    const el = document.getElementById(field.id);
    
    if (field.id === 'beneficiary_udid_file') {
      if (!el || el.files.length === 0) {
        showError(field.id, field.message);
     } else {
        clearError(field.id);
     }
    } else {
    if (!getValue(field.id)) {
     showError(field.id, field.message);
    } else {
     clearError(field.id);
    }
    }
    });
    } else {
         // Clear errors if NOT required
    ['udid_no', 'beneficiary_udid_file', 'disability_category'].forEach(id => clearError(id));
    }
    
     // ========================
     // AADHAAR CONDITIONAL VALIDATION
     // ========================
    const havingAadhaar = document.querySelector('input[name="having_aadhaar"]:checked')?.value;
    
    if (havingAadhaar == '1') {
        const aadhaarVal = getValue('aadhaar_no');
        if (!aadhaarVal) {
            showError('aadhaar_no', 'Please enter Aadhaar No.');
        } else if (!/^\d{12}$/.test(aadhaarVal)) {
            showError('aadhaar_no', 'Aadhaar must be exactly 12 digits.');
        } else {
            clearError('aadhaar_no');
        }

        const aadhaarFile = document.getElementById('aadhaar_file');
        if (!aadhaarFile || aadhaarFile.files.length === 0) {
            showError('aadhaar_file', 'Please upload Aadhaar file.');
        } else {
            clearError('aadhaar_file');
        }

        const verified = document.getElementById('verified_aadhar').value;
        if (verified != '1') {
            document.getElementById('aadhaar_verify_result').innerHTML =
            '<span class="text-danger">Please verify Aadhaar before submitting.</span>';
            isValid = false;
        }

        clearError('aadhaar_not_available_reason');
    } else {
        const reasonVal = document.getElementById('aadhaar_not_available_reason').value;
        if (!reasonVal) {
            showError('aadhaar_not_available_reason', 'Please select Reason for Non-Availability of Aadhaar.');
        } else {
            clearError('aadhaar_not_available_reason');
        }

        clearError('aadhaar_no');
        clearError('aadhaar_file');
    }
    
     // ========================
    // DATE VALIDATION
    // ========================
    const dobVal = getValue('date_of_birth');
    const dojVal = getValue('date_of_joining');
    
    if (dobVal && dojVal) {
    const dob = new Date(dobVal);
    const doj = new Date(dojVal);
    
    if (doj <= dob) {
     showError('date_of_joining', 'Date of Joining must be greater than DOB.');
     isValid = false;
    } else {
     clearError('date_of_joining');
    }
    }
    
    // ========================
    // NUMERIC VALIDATION
    // ========================
    
    // Aadhaar
    const aadhaar = getValue('aadhaar_no');
    if (havingAadhaar == '1' && aadhaar && !/^\d{12}$/.test(aadhaar)) {
    showError('aadhaar_no', 'Aadhaar must be exactly 12 digits.');
    }
   
   // Bank Account
   const bank = getValue('bank_ac_no');
   if (bank && !/^\d+$/.test(bank)) {
   showError('bank_ac_no', 'Bank Account No must contain only digits.');
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
<script>
   $(document).ready(function() {
      $('#age').prop('readonly', true);
   
      $('#date_of_birth').change(function(){
         var dob = new Date($(this).val());
         var today = new Date();
         var age = today.getFullYear() - dob.getFullYear();
         var m = today.getMonth() - dob.getMonth();
   
         if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) { 
            age--; 
         }
   
         if(age < 0 || isNaN(age)) { 
            age = 0; 
         }
   
         $('#age').val(age);
   
         if(age < 0){
            alert('Age should not be less than 0 years.');
            $('#date_of_birth').val('');
            $('#age').val('');
            $('#submitButton').prop('disabled', true);
         } else {
            $('#submitButton').prop('disabled', false);
         }
      });       
   });
</script>
<script>
   $(document).ready(function () {

    function toggleDisabilityFields() {
        const isDisabled = $('input[name="is_disabled"]:checked').val();

        if (isDisabled == '2') { // NO

            // Hide entire section (clean way)
            $('.disability_section').hide();

            // Disable + clear inputs inside
            $('.disability_section')
                .find('input, select')
                .val('')
                .prop('disabled', true);

        } else { // YES

            // Show section
            $('.disability_section').show();

            // Enable inputs
            $('.disability_section')
                .find('input, select')
                .prop('disabled', false);
        }
    }

    // On page load
    toggleDisabilityFields();

    // On change
    $('input[name="is_disabled"]').on('change', function () {
        toggleDisabilityFields();
    });

});
</script>
<script>
   $(document).ready(function () {
      function toggleAadhaarFields() {
         const havingAadhaar = $('input[name="having_aadhaar"]:checked').val();

         if (havingAadhaar == '2') { // NO
            // Hide entire Aadhaar section (clean way)
            $('.aadhaar_section').hide();

            // Disable + clear inputs inside
            $('.aadhaar_section')
               .find('input, select')
               .val('')
               .prop('disabled', true);

            // Clear hidden verify values
            $('#verified_aadhar').val('');
            $('#verified_aadhar_remarks').val('');

            // Hide verify messages & results
            $('#check_aadhaar_no').html('');
            $('#aadhaar_verify_result').html('');

            // Show Aadhaar reason section
            $('.aadhaar_reason_section').show();

            // Enable inputs inside reason section
            $('.aadhaar_reason_section')
               .find('input, select')
               .prop('disabled', false);

         } else { // YES
            // Show Aadhaar section
            $('.aadhaar_section').show();

            // Enable inputs inside
            $('.aadhaar_section')
               .find('input, select')
               .prop('disabled', false);

            // Hide Aadhaar reason section
            $('.aadhaar_reason_section').hide();

            // Disable + clear inputs inside reason section
            $('.aadhaar_reason_section')
               .find('input, select')
               .val('')
               .prop('disabled', true);
         }
         if ($.fn.select2) {
            $('.select2').trigger('change.select2');
         }
      }

      // On page load
      toggleAadhaarFields();

      // On change
      $('input[name="having_aadhaar"]').on('change', function () {
         toggleAadhaarFields();
      });
   });
</script>
@endsection