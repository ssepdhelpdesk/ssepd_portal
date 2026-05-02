@section('title') 
SSEPD-IT || Pension
@endsection 
@extends('website.layout.mainlayout')
@section('style')
<!-- For Date Selection -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
   /* ================= FLATPICKR BRAND OVERRIDE ================= */
   .flatpickr-calendar {
   border-radius: 12px;
   box-shadow: 0 10px 30px rgba(0,0,0,.15);
   font-family: inherit;
   }
   /* Header background */
   .flatpickr-months {
   background: #342777;
   border-radius: 12px 12px 0 0;
   }
   /* Month / Year text */
   .flatpickr-current-month,
   .flatpickr-monthDropdown-months,
   .flatpickr-current-month input.cur-year {
   color: #ffffff !important;
   font-weight: 600;
   }
   /* Navigation arrows */
   .flatpickr-prev-month svg,
   .flatpickr-next-month svg {
   fill: #ffffff;
   }
   /* Selected date */
   .flatpickr-day.selected,
   .flatpickr-day.startRange,
   .flatpickr-day.endRange {
   background: #342777;
   border-color: #342777;
   color: #ffffff;
   }
   /* Today */
   .flatpickr-day.today {
   border-color: #342777;
   }
   /* Hover */
   .flatpickr-day:hover {
   background: rgba(52, 39, 119, 0.12);
   }
   /* ================= FLATPICKR WHITE TEXT FIX ================= */
   /* Weekday labels (Sun Mon Tue...) */
   .flatpickr-weekday {
   color: #ffffff !important;
   font-weight: 600;
   }
   /* Month name dropdown text */
   .flatpickr-monthDropdown-months {
   color: #ffffff !important;
   }
   /* Year input text */
   .flatpickr-current-month input.cur-year {
   color: #ffffff !important;
   font-weight: 600;
   }
   /* Year up/down arrows (spinner) */
   .flatpickr-current-month input.cur-year::-webkit-inner-spin-button,
   .flatpickr-current-month input.cur-year::-webkit-outer-spin-button {
   opacity: 1;
   filter: invert(1); /* makes arrows white */
   }
   /* Firefox year arrows */
   .flatpickr-current-month input.cur-year {
   -moz-appearance: textfield;
   }
   /* Month navigation arrows */
   .flatpickr-prev-month svg,
   .flatpickr-next-month svg {
   fill: #ffffff !important;
   }
   /* Weekday background consistency */
   .flatpickr-weekdays {
   background: #342777;
   border-bottom: 1px solid rgba(255,255,255,0.2);
   }
   /* ================= MONTH DROPDOWN BACKGROUND FIX ================= */
   /* Month dropdown (closed state) */
   .flatpickr-monthDropdown-months {
   background-color: #342777 !important;
   color: #ffffff !important;
   border: none;
   }
   /* Month dropdown options (opened list) */
   .flatpickr-monthDropdown-months option {
   background-color: #342777 !important;
   color: #ffffff !important;
   }
   /* Hover / selected option */
   .flatpickr-monthDropdown-months option:hover,
   .flatpickr-monthDropdown-months option:checked {
   background-color: #2a1f60 !important; /* slightly darker */
   color: #ffffff !important;
   }
   /* Remove native arrow background (Chrome / Edge) */
   .flatpickr-monthDropdown-months::-ms-expand {
   display: none;
   }
   .profile-upload-group.no-border {
   border-bottom: none !important;
   padding-bottom: 0 !important;
   margin-bottom: 0 !important;
   }
</style>
@endsection 
@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-bar text-center">
   <div class="container">
      <div class="row">
         <div class="col-md-12 col-12">
            <h2 class="breadcrumb-title mb-2">Apply for Enhanced Pension</h2>
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb justify-content-center mb-0">
                  <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Apply for Enhanced Pension</li>
               </ol>
            </nav>
         </div>
         @if ($errors->any())
         <div class="alert alert-danger">
            <ul>
               @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
         @endif
         @if(session('error'))
         <div class="alert alert-danger">
            {{ session('error') }}
         </div>
         @endif
      </div>
   </div>
</div>
<!-- /Breadcrumb -->
<div class="content">
   <div class="container">
      <div class="row">
         <div class="col-lg-12">
            <form id="pensionApplicationForm" action="{{ route('website.pension.store') }}" method="post" enctype="multipart/form-data">
               @csrf
               <!-- ================= Basic Information ================= -->
               <div class="card" id="basic_section">
                  <div class="card-body">
                     <h5 class="fs-18 mb-3">Basic Information</h5>
                     <div class="row">
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Name of the PIA/NGO</label>
                           <input type="text" name="pia_ngo_name" id="pia_ngo_name" class="form-control">
                           @error('pia_ngo_name')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label">Middle Name as per Aadhaar</label>
                           <input type="text" name="applicant_middle_name" id="applicant_middle_name" class="form-control">
                           @error('applicant_middle_name')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Last Name as per Aadhaar</label>
                           <input type="text" name="applicant_last_name" id="applicant_last_name" class="form-control">
                           @error('applicant_last_name')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Full Name as per Aadhaar</label>
                           <input type="text" name="applicant_name" id="applicant_name" class="form-control" readonly>
                           @error('applicant_name')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Gender</label>
                           <select name="gender_id" id="gender_id" class="select form-control required-field populate">
                              <option value="">-Select-</option>
                              @foreach($gender as $data)
                              <option value="{{$data->id}}">{{$data->gender_name}}</option>
                              @endforeach
                           </select>
                           @error('gender_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Date of Birth</label>
                           <div class="input-group">
                              <input type="text" name="dob" id="dob" class="form-control" placeholder="DD-MM-YYYY" readonly><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                           </div>
                           @error('dob')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Age</label>
                           <input type="text" name="age" id="age" class="form-control" readonly>
                           @error('age')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Aadhaar No</label>
                           <div class="d-flex">
                              <input type="text" name="aadhaar_no" id="aadhaar_no" value="381669807370" class="form-control me-2">
                              <button type="button" class="btn btn-secondary btn-sm" id="aadhaar_verify_btn">Verify</button>
                              <input type="hidden" id="verified_aadhar" class="form-control" name="verified_aadhar">
                              <input type="hidden" id="verified_aadhar_remarks" class="form-control" name="verified_aadhar_remarks">
                           </div>
                           @error('aadhaar_no')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Guardian Type</label>
                           <select name="guardian_type" id="guardian_type" class="select form-control required-field populate">
                              <option value="">-Select-</option>
                              <option value="1">Father</option>
                              <option value="2">Mother</option>
                              <option value="3">Spouse</option>
                           </select>
                           @error('guardian_type')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Father's / Spouse Name</label>
                           <input type="text" name="guardian_name" id="guardian_name" class="form-control">
                           @error('guardian_name')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Social Category</label>
                           <select name="caste_id" id="caste_id" class="select form-control required-field populate">
                              <option value="">-Select-</option>
                              <option value="1">General</option>
                              <option value="2">OBC</option>
                              <option value="3">SC</option>
                              <option value="4">ST</option>
                              <option value="5">Minority</option>
                           </select>
                           @error('caste_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Mobile No</label>
                           <input type="text" name="mobile_no" id="mobile_no" class="form-control" maxlength="10">
                           <!-- <div id="mobile_no_error" class="text-danger small d-none"></div> -->
                           @error('mobile_no')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3 d-none" id="tg_registration_div">
                           <label class="form-label required-field">TG Registration No</label>
                           <input type="text" name="tg_registration_no" id="tg_registration_no" class="form-control">
                           @error('tg_registration_no')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                     </div>
                  </div>
               </div>
               <!-- ================= Communication Address ================= -->
               <div class="card" id="address_section">
                  <div class="card-body">
                     <h5 class="fs-18 mb-3">Communication Address</h5>
                     <div class="row">
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">District</label>
                           <select name="district_id" id="district_id" class="select form-control required-field">
                              <option value="">-Select District-</option>
                              @foreach($district as $data)
                              <option value="{{$data->district_id}}">{{$data->district_name}}</option>
                              @endforeach
                           </select>
                           @error('district_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Sub Division</label>
                           <select name="sub_division_id" id="sub_division_id" class="select form-control required-field">
                              <option value="">-Select Sub Division-</option>
                           </select>
                           @error('sub_division_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Address Type</label>
                           <select name="address_type_id" id="address_type_id" class="select form-control required-field populate">
                              <option value="">-Select-</option>
                              <option value="2">Block</option>
                              <option value="1">ULB</option>
                           </select>
                           @error('address_type_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="block_div">
                           <label class="form-label required-field">Block</label>
                           <select name="block_id" id="block_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                           </select>
                           @error('block_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="gp_div">
                           <label class="form-label required-field">Gram Panchayat</label>
                           <select name="gp_id" id="gp_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                           </select>
                           @error('gp_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="village_div">
                           <label class="form-label required-field">Village</label>
                           <select name="village_id" id="village_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                           </select>
                           @error('village_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="municipality_div">
                           <label class="form-label required-field">Municipality</label>
                           <select name="municipality_id" id="municipality_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                           </select>
                           @error('municipality_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="ward_div">
                           <label class="form-label required-field">Ward</label>
                           <select name="ward_id" id="ward_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                           </select>
                           @error('ward_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">House / Plot No</label>
                           <input type="text" name="house_no" id="house_no" class="form-control">
                           @error('house_no')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">PIN</label>
                           <input type="text" name="pin_code" id="pin_code" class="form-control" maxlength="6">
                           @error('pin_code')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                     </div>
                  </div>
               </div>
               <!-- ================= Disability Profile ================= -->
               <div class="card d-none" id="disability_section">
                  <div class="card-body">
                     <h5 class="fs-18 mb-3">Disability Profile Details</h5>
                     <div class="row">
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">UDID No</label>
                           <div class="d-flex">
                              <input type="text" name="udid_no" id="udid_no" class="form-control me-2">
                              <button type="button" id="verify_udid_btn" class="btn btn-secondary btn-sm">Verify</button>
                           </div>
                           @error('verify_udid_btn')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div id="udid_result" class="mt-3"></div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Category</label>
                           <select name="disability_category_id" id="disability_category_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Category1</option>
                              <option value="2">Category2</option>
                           </select>
                           @error('disability_category_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Percentage</label>
                           <input type="text" name="disability_percentage" id="disability_percentage" class="form-control" inputmode="numeric" maxlength="3" placeholder="40 - 100">
                           <div id="disability_percent_error" class="text-danger small mt-1 d-none"></div>
                           @error('disability_percentage')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Type</label>
                           <select name="disability_type_condition_id" id="disability_type_condition_id"
                              class="select form-control required-field populate" onchange="disabilityTypeCondition()">
                              <option value="">-Select-</option>
                              <option value="1">Temporary</option>
                              <option value="2">Permanent</option>
                           </select>
                           @error('disability_type_condition_id')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="disability_validity_div">
                           <label class="form-label required-field">Validity Date</label>
                           <div class="input-group">
                              <input type="text" name="disability_validity_date" id="disability_validity_date" class="form-control" placeholder="DD-MM-YYYY" readonly><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                           </div>
                           @error('disability_validity_date')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Document</label>
                           <input type="file" name="disability_document" id="disability_document"
                              class="form-control" accept="application/pdf">
                           @error('disability_document')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                     </div>
                  </div>
               </div>
               <!-- ================= Bank Details ================= -->
               <div class="card" id="bank_section">
                  <div class="card-body">
                     <h5 class="fs-18 mb-3">Bank Account Details</h5>
                     <div class="row">
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Account Type</label>
                           <select name="bank_account_type" id="bank_account_type" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Single</option>
                              <option value="2">Joint</option>
                           </select>
                           @error('bank_account_type')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Account Holder Name</label>
                           <input type="text" name="account_holder_name" id="account_holder_name" class="form-control">
                           @error('account_holder_name')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3 d-none" id="second_holder_div"><label class="form-label required-field">Second Holder Name</label>
                           <input type="text" name="second_account_holder_name" id="second_account_holder_name" class="form-control">
                           @error('second_account_holder_name')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Account Number</label>
                           <input type="text" name="account_number" id="account_number" class="form-control">
                           @error('account_number')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">IFSC Code</label>
                           <select name="ifsc_code" id="ifsc_code" class="select form-control required-field">
                              <option value="">-Select-</option>
                              @foreach($bank as $data)
                              <option value="{{$data->bank_id}}">{{$data->bank_ifsc}}</option>
                              @endforeach
                           </select>
                           @error('ifsc_code')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                     </div>
                  </div>
               </div>
               <!-- ================= Documents Upload ================= -->
               <div class="card" id="documents_section">
                  <div class="card-body">
                     <h5 class="fs-18 mb-3">Documents Upload</h5>
                     <p class="fs-14 mb-3">All documents must be in PDF format. Maximum allowed file size is 300 KB per file.</p>
                     <hr>
                     <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Income Certificate / RI / BPL</label>
                           <input type="file" name="income_certificate" id="income_certificate" class="form-control" accept="application/pdf">
                           @error('income_certificate')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Thumb / Signature</label>
                           <input type="file" name="thumb_signature" id="thumb_signature" class="form-control" accept="application/pdf">
                           @error('thumb_signature')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Aadhaar Copy</label>
                           <input type="file" name="aadhaar_document" id="aadhaar_document" class="form-control" accept="application/pdf">
                           @error('aadhaar_document')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Age Proof</label>
                           <input type="file" name="age_proof" id="age_proof" class="form-control" accept="application/pdf">
                           @error('age_proof')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Passbook</label>
                           <input type="file" name="passbook_document" id="passbook_document" class="form-control" accept="application/pdf">
                           @error('passbook_document')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Additional Document</label>
                           <input type="file" name="additional_document" id="additional_document" class="form-control" accept="application/pdf">
                           @error('additional_document')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3 d-none" id="tg_document_div">
                           <label class="form-label required-field">TG Certificate</label>
                           <input type="file" name="tg_certificate" id="tg_certificate" class="form-control" accept="application/pdf">
                           @error('tg_certificate')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3 d-none" id="widow_document_div">
                           <label class="form-label required-field">Death / Self Certificate</label>
                           <input type="file" name="death_self_certificate" id="death_self_certificate" class="form-control" accept="application/pdf">
                           @error('death_self_certificate')
                           <label class="error">{{ $message }}</label>
                           @enderror
                        </div>
                     </div>
                     <button type="submit" class="btn btn-secondary rounded-pill mt-3">Submit Application</button>
                  </div>
               </div>
            </form>
         </div>
         <div class="col-lg-12">
            <div id="ajaxErrorContainer" class="alert alert-danger" style="display:none;">
               <ul id="ajaxErrorList"></ul>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@endsection