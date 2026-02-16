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
            <h2 class="breadcrumb-title mb-2">Apply for Pension</h2>
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb justify-content-center mb-0">
                  <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Apply for Pension</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
<!-- /Breadcrumb -->
<div class="content">
   <div class="container">
      <div class="row">
         <div class="col-lg-12">
            <form id="pensionApplicationForm" method="post" enctype="multipart/form-data">
               @csrf
               <!-- ================= Basic Information ================= -->
               <div class="card" id="basic_section">
                  <div class="card-body">
                     <h5 class="fs-18 mb-3">Basic Information</h5>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                           <label class="form-label required-field">Pension Type</label>
                           <select name="pension_type_id" id="pension_type_id" class="select form-control required-field populate"
                              onchange="applyingScemeType(this.value);">
                              <option value="">-Select-</option>
                              @foreach($pensionType as $data)
                              <option value="{{$data->scheme_id}}">{{$data->scheme_name}}</option>
                              @endforeach
                           </select>
                        </div>
                        <div class="col-md-6 mb-3">
                           <label class="form-label required-field">Beneficiary Photograph</label>
                           <div class="profile-upload-group  no-border">
                              <div class="d-flex align-items-center">
                                 <!-- Image Preview -->
                                 <div class="avatar flex-shrink-0 avatar-xxxl avatar-rounded border me-3">
                                    <img id="beneficiary_preview"
                                       src="{{ asset('website_assets/assets/img/user/user-01.jpg') }}"
                                       alt="Beneficiary Image"
                                       class="img-fluid">
                                 </div>
                                 <div class="profile-upload-head">
                                    <h6 class="required-field">Upload Image</h6>
                                    <p class="fs-14 mb-0">
                                       JPG/JPEG only, max 300 KB (800×800 px)
                                    </p>
                                    <div class="new-employee-field">
                                       <div class="d-flex align-items-center mt-2">
                                          <!-- Upload Button -->
                                          <div class="image-upload position-relative mb-0">
                                             <!-- Hidden file input -->
                                             <input type="file"
                                                name="beneficiary_image"
                                                id="beneficiary_image"
                                                class="form-control required-field"
                                                accept="image/jpeg,image/jpg"
                                                onchange="previewBeneficiaryImage(this)"
                                                style="display:none;">
                                             <!-- Upload button triggers file input -->
                                             <label for="beneficiary_image"
                                                class="btn bg-gray-100 btn-sm rounded-pill mb-0"
                                                style="cursor:pointer;">
                                             Upload
                                             </label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">First Name as per Aadhaar</label>
                           <input type="text" name="applicant_first_name" id="applicant_first_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label">Middle Name as per Aadhaar</label>
                           <input type="text" name="applicant_middle_name" id="applicant_middle_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Last Name as per Aadhaar</label>
                           <input type="text" name="applicant_last_name" id="applicant_last_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Full Name as per Aadhaar</label>
                           <input type="text" name="applicant_name" id="applicant_name" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Gender</label>
                           <select name="gender_id" id="gender_id" class="select form-control required-field populate">
                              <option value="">-Select-</option>
                              <option value="1">Male</option>
                              <option value="2">Female</option>
                              <option value="3">Other</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Date of Birth</label>
                           <div class="input-group">
                              <input type="text" name="dob" id="dob" class="form-control" placeholder="DD-MM-YYYY" readonly><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                           </div>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Age</label>
                           <input type="text" name="age" id="age" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Aadhaar No</label>
                           <div class="d-flex">
                              <input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control me-2">
                              <button type="button" class="btn btn-secondary btn-sm" id="aadhaar_verify_btn">Verify</button>
                              <input type="hidden" id="verified_aadhar" class="form-control" name="verified_aadhar">
                              <input type="hidden" id="verified_aadhar_remarks" class="form-control" name="verified_aadhar_remarks">
                           </div>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Father's / Spouse Name</label>
                           <input type="text" name="guardian_name" id="guardian_name" class="form-control">
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
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Mobile No</label>
                           <input type="text" name="mobile_no" id="mobile_no" class="form-control" maxlength="10">
                        </div>
                        <div class="col-md-4 mb-3 d-none" id="tg_registration_div">
                           <label class="form-label required-field">TG Registration No</label>
                           <input type="text" name="tg_registration_no" id="tg_registration_no" class="form-control">
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
                              <option value="">-Select-</option>
                              <option value="1">District1</option>
                              <option value="2">District2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Sub Division</label>
                           <select name="sub_division_id" id="sub_division_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Sub Division1</option>
                              <option value="2">Sub Division2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Address Type</label>
                           <select name="address_type_id" id="address_type_id" class="select form-control required-field populate">
                              <option value="">-Select-</option>
                              <option value="2">Block</option>
                              <option value="1">ULB</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3" id="block_div">
                           <label class="form-label required-field">Block</label>
                           <select name="block_id" id="block_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Block1</option>
                              <option value="2">Block2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3" id="gp_div">
                           <label class="form-label required-field">Gram Panchayat</label>
                           <select name="gp_id" id="gp_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">GP1</option>
                              <option value="2">GP2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3" id="village_div">
                           <label class="form-label required-field">Village</label>
                           <select name="village_id" id="village_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Village1</option>
                              <option value="2">Village2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3" id="municipality_div">
                           <label class="form-label required-field">Municipality</label>
                           <select name="municipality_id" id="municipality_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Municipality1</option>
                              <option value="2">Municipality2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3" id="ward_div">
                           <label class="form-label required-field">Ward</label>
                           <select name="ward_id" id="ward_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Ward1</option>
                              <option value="2">Ward2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">House / Plot No</label><input type="text" name="house_no" id="house_no" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">PIN</label><input type="text" name="pin_code" id="pin_code" class="form-control" maxlength="6"></div>
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
                              <button type="button" class="btn btn-secondary btn-sm">Verify</button>
                           </div>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Category</label>
                           <select name="disability_category_id" id="disability_category_id" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">Category1</option>
                              <option value="2">Category2</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Percentage</label>
                           <input type="text" name="disability_percentage" id="disability_percentage" class="form-control" inputmode="numeric" maxlength="3" placeholder="40 - 100">
                           <div id="disability_percent_error" class="text-danger small mt-1 d-none"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Type</label>
                           <select name="disability_type_condition_id" id="disability_type_condition_id"
                              class="select form-control required-field populate" onchange="disabilityTypeCondition()">
                              <option value="">-Select-</option>
                              <option value="1">Temporary</option>
                              <option value="2">Permanent</option>
                           </select>
                        </div>
                        <div class="col-md-4 mb-3 d-none" id="disability_validity_div">
                           <label class="form-label required-field">Validity Date</label>
                           <input type="date" name="disability_validity_date" id="disability_validity_date"
                              class="form-control" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">Disability Document</label>
                           <input type="file" name="disability_document" id="disability_document"
                              class="form-control" accept="application/pdf">
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
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Account Holder Name</label><input type="text" name="account_holder_name" id="account_holder_name" class="form-control"></div>
                        <div class="col-md-4 mb-3 d-none" id="second_holder_div"><label class="form-label required-field">Second Holder Name</label><input type="text" name="second_account_holder_name" id="second_account_holder_name" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Account Number</label><input type="text" name="account_number" id="account_number" class="form-control"></div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label required-field">IFSC Code</label>
                           <select name="ifsc_code" id="ifsc_code" class="select form-control required-field">
                              <option value="">-Select-</option>
                              <option value="1">IFSC Code1</option>
                              <option value="2">IFSC Code2</option>
                           </select>
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
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Income Certificate / RI / BPL</label><input type="file" name="income_certificate" id="income_certificate" class="form-control" accept="application/pdf"></div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Thumb / Signature</label><input type="file" name="thumb_signature" id="thumb_signature" class="form-control" accept="application/pdf"></div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Aadhaar Copy</label><input type="file" name="aadhaar_document" id="aadhaar_document" class="form-control" accept="application/pdf"></div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Age Proof</label><input type="file" name="age_proof" id="age_proof" class="form-control" accept="application/pdf"></div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Passbook</label><input type="file" name="passbook_document" id="passbook_document" class="form-control" accept="application/pdf"></div>
                        <div class="col-md-4 mb-3"><label class="form-label required-field">Additional Document</label><input type="file" name="additional_document" id="additional_document" class="form-control" accept="application/pdf"></div>
                        <div class="col-md-4 mb-3 d-none" id="tg_document_div">
                           <label class="form-label required-field">TG Certificate</label>
                           <input type="file" name="tg_certificate" id="tg_certificate" class="form-control" accept="application/pdf">
                        </div>
                        <div class="col-md-4 mb-3 d-none" id="widow_document_div">
                           <label class="form-label required-field">Death / Self Certificate</label>
                           <input type="file" name="widow_certificate" id="widow_certificate" class="form-control" accept="application/pdf">
                        </div>
                     </div>
                     <button type="submit" class="btn btn-secondary rounded-pill mt-3">Submit Application</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection 
@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
   let aadhaarVerified = false;
   document.addEventListener('DOMContentLoaded', function () {
   
     /* ================= SELECT2 ================= */
   	$('.select').select2({
   		placeholder: "Select",
   		allowClear: true,
   		width: "100%"
   	});
   
   
     /* ================= Required Star Injection ================= */
   	document.querySelectorAll('.required-field').forEach(field => {
   
   		const parentCol = field.closest('[class*="col-md-"]');
   		const label = parentCol?.querySelector('label');
   
   		if (label && !label.querySelector('.required-star')) {
   			const star = document.createElement('span');
   			star.textContent = ' *';
   			star.className = 'required-star text-danger';
   			label.appendChild(star);
   		}
   
   	});
   
   	const addressType = $('#address_type_id');
   	const blockDiv = $('#block_div');
   	const gpDiv = $('#gp_div');
   	const villageDiv = $('#village_div');
   	const municipalityDiv = $('#municipality_div');
   	const wardDiv = $('#ward_div');
   
   	const blockSelect = $('#block_id');
   	const gpSelect = $('#gp_id');
   	const villageSelect = $('#village_id');
   	const municipalitySelect = $('#municipality_id');
   	const wardSelect = $('#ward_id');
   
   	function hideAll() {
   		blockDiv.hide();
   		gpDiv.hide();
   		villageDiv.hide();
   		municipalityDiv.hide();
   		wardDiv.hide();
   	}
   
   	function resetSelect($select) {
   		$select.val('').trigger('change');
   	}
   
     /*Initial hide*/
   	hideAll();
   
     /*Address type change*/
   	addressType.on('change', function () {
   		const type = $(this).val();
   		hideAll();
   		resetSelect(blockSelect);
   		resetSelect(gpSelect);
   		resetSelect(villageSelect);
   		resetSelect(municipalitySelect);
   		resetSelect(wardSelect);
   
   		if(type === '2') {
   			blockDiv.show();
   		} else if(type === '1') {
   			municipalityDiv.show();
   		}
   	});
   
     /*Block -> GP*/
   	blockSelect.on('change', function () {
   		resetSelect(gpSelect);
   		resetSelect(villageSelect);
   		villageDiv.hide();
   
   		if($(this).val()) gpDiv.show();
   		else gpDiv.hide();
   	});
   
     /*GP -> Village*/
   	gpSelect.on('change', function () {
   		resetSelect(villageSelect);
   		if($(this).val()) villageDiv.show();
   		else villageDiv.hide();
   	});
   
     /*Municipality -> Ward*/
   	municipalitySelect.on('change', function () {
   		resetSelect(wardSelect);
   		if($(this).val()) wardDiv.show();
   		else wardDiv.hide();
   	});
   
   	const schemeSelect = $('#pension_type_id');
   
   	schemeSelect.on('select2:select', function (e) {
   		applyingScemeType(e.params.data.id);
   	});
   
   	schemeSelect.on('change', function () {
   		applyingScemeType(this.value);
   	});
   
   
     /* ================= DIGITS-ONLY INPUT HANDLER ================= */
   	function allowDigitsOnly(input, maxLength = null, maxValue = null) {
   
   		if (!input) return;
   
   		input.addEventListener('input', function () {
   
   			let value = this.value.replace(/\D/g, '');
   
   			if (maxLength !== null) value = value.slice(0, maxLength);
   
   			if (maxValue !== null && value !== '') {
   				value = Math.min(parseInt(value, 10), maxValue).toString();
   			}
   
   			this.value = value;
   		});
   
   		input.addEventListener('paste', function (e) {
   			e.preventDefault();
   
   			let pasted = (e.clipboardData || window.clipboardData).getData('text');
   			pasted = pasted.replace(/\D/g, '');
   
   			if (maxLength !== null) pasted = pasted.slice(0, maxLength);
   
   			if (maxValue !== null && pasted !== '') {
   				pasted = Math.min(parseInt(pasted, 10), maxValue).toString();
   			}
   
   			this.value = pasted;
   		});
   	}
   
   
     /* ================= APPLY DIGIT RULES ================= */
   	allowDigitsOnly(document.getElementById('aadhaar_no'), 12);
   	allowDigitsOnly(document.getElementById('mobile_no'), 10);
   	allowDigitsOnly(document.getElementById('pin_code'), 6);
   	allowDigitsOnly(document.getElementById('house_no'), 3);
   	allowDigitsOnly(document.getElementById('disability_percentage'), 3, 100);
   	allowDigitsOnly(document.getElementById('account_number'), 22);
   
   
     /* ================= MODERN DOB PICKER ================= */
   	flatpickr("#dob", {
   		dateFormat: "d-m-Y",
   		maxDate: "today",
   		allowInput: false,
   		disableMobile: false,
   		monthSelectorType: "dropdown",
   		yearSelectorType: "dropdown",
   		animate: true,
   		clickOpens: true,
   		position: "auto center",
   		onChange: function () {
   			calculateAge();
   		}
   	});
   
   	const dobField = document.getElementById('dob');
   	if (dobField) dobField.setAttribute('readonly', true);
   
   	const bankType = $('#bank_account_type');
   
   	bankType.on('select2:select', function (e) {
   		bankAccountTypeChange(e.params.data.id);
   	});
   
   	bankType.on('change', function () {
   		bankAccountTypeChange(this.value);
   	});
   
   
   
     /* ================= INIT AADHAAR VALIDATION ================= */
   	initAadhaarValidation();
   	disabilityTypeCondition();
   
     /* ================= INITIAL HIDE ================= */
   	hide('tg_registration_div');
   	hide('tg_document_div');
   	hide('widow_document_div');
   	hide('disability_section');
   	hide('disability_validity_div');
   	hide('second_holder_div');
   
        /* ================= AADHAAR SECTION LOCK ================= */
   
   
   
   	const basicSection     = document.getElementById('basic_section');
   	const addressSection   = document.getElementById('address_section');
   	const bankSection      = document.getElementById('bank_section');
   	const documentsSection = document.getElementById('documents_section');
   
   	const aadhaarInput = document.getElementById('aadhaar_no');
   	const verifyBtn    = document.getElementById('aadhaar_verify_btn');
   
   /* Hide all sections except basic on load */
   	hide('address_section');
   	hide('bank_section');
   	hide('documents_section');
   
   /* ================= REAL AADHAAR VERIFICATION ================= */
   
   	const fullNameInput = document.getElementById('applicant_name');
   	const verifiedAadharInput = document.getElementById('verified_aadhar');
   	const verifiedRemarksInput = document.getElementById('verified_aadhar_remarks');
   	const firstNameInput = document.getElementById('applicant_first_name');
   	const middleNameInput = document.getElementById('applicant_middle_name');
   	const lastNameInput = document.getElementById('applicant_last_name');
   
   
   	verifyBtn.addEventListener('click', function () {
   
   		const aadhaarNo = aadhaarInput.value.trim();
   		const fullName  = fullNameInput.value.trim();
   
   		verifiedAadharInput.value = '';
   		verifiedRemarksInput.value = '';
   
   		if (!/^\d{12}$/.test(aadhaarNo)) {
   			Swal.fire('Invalid Aadhaar', 'Enter valid 12 digit Aadhaar number', 'error');
   			return;
   		}
   
   		if (!fullName || fullName.length < 2) {
   			Swal.fire('Invalid Name', 'Enter full name as per Aadhaar', 'error');
   			return;
   		}
   
   		verifyBtn.disabled = true;
   		verifyBtn.innerText = 'Verifying...';
   
   		fetch("{{ route('website.pension.benf_aadhar_verification') }}", {
   			method: "POST",
   			headers: {
   				"Content-Type": "application/json",
   				"X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
   			},
   			body: JSON.stringify({
   				aadhaar_no: aadhaarNo,
   				applicant_name: fullName
   			})
   		})
   		.then(response => response.json())
   		.then(data => {
   
   			let message = data.data ?? '';
   
   			if (data.status === true) {
   
   				verifiedAadharInput.value = 1;
   				verifiedRemarksInput.value = message;
   
   				aadhaarVerified = true;
   
   				show('address_section');
   				show('bank_section');
   				show('documents_section');
   
   				aadhaarInput.setAttribute('readonly', true);
   				fullNameInput.setAttribute('readonly', true);
   				firstNameInput.setAttribute('readonly', true);
   				middleNameInput.setAttribute('readonly', true);
   				lastNameInput.setAttribute('readonly', true);
   
   				Swal.fire('Verified', message || 'Aadhaar verified1 successfully', 'success');
   
   				verifyBtn.innerText = 'Verified';
   				verifyBtn.classList.remove('btn-primary', 'btn-danger');
   				verifyBtn.classList.add('btn-success');
   			} else {
   
   				verifiedAadharInput.value = 0;
   				verifiedRemarksInput.value = message;
   				fullNameInput.setAttribute('readonly', true);
   
   				Swal.fire('Verification Failed',
   					message || data.response || 'Aadhaar verification failed',
   					'error'
   					);
   
   				verifyBtn.disabled = false;
   				verifyBtn.innerText = 'Verify';
   			}
   
   		})
   		.catch(error => {
   			verifiedAadharInput.value = 0;
   			verifiedRemarksInput.value = 'Server error';
   			fullNameInput.setAttribute('readonly', true);
   
   			Swal.fire('Error', 'Server error during verification', 'error');
   
   			verifyBtn.disabled = false;
   			verifyBtn.innerText = 'Verify';
   		});
   
   	});
   
   
   /* Reset verification if Aadhaar or Name edited */
   	[aadhaarInput, fullNameInput].forEach(field => {
   
   		if (!field) return;
   
   		field.addEventListener('input', function () {
   			verifiedAadharInput.value = '';
   			verifiedRemarksInput.value = '';
   
   			aadhaarVerified = false;
   
   			hide('address_section');
   			hide('bank_section');
   			hide('documents_section');
   
   			aadhaarInput.removeAttribute('readonly');
   
   			verifyBtn.disabled = false;
   			verifyBtn.innerText = 'Verify';
   		});
   
   	});
   
   
   	const initialScheme = document.getElementById('pension_type_id')?.value;
   	if (initialScheme !== null && initialScheme !== '') {
   		applyingScemeType(initialScheme);
   	}
   });
   
   
     /* ================= DOB → AGE (Flatpickr Compatible) ================= */
   function calculateAge() {
   
   const dobValue = document.getElementById('dob')?.value;
   const ageField = document.getElementById('age');
   
   if (!dobValue || !ageField) {
   	if (ageField) ageField.value = '';
   	return;
   }
   
   const [day, month, year] = dobValue.split('-');
   const dob = new Date(year, month - 1, day);
   const today = new Date();
   
   let age = today.getFullYear() - dob.getFullYear();
   const m = today.getMonth() - dob.getMonth();
   
   if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
   	age--;
   }
   
   ageField.value = age >= 0 ? age : '';
   validatePensionEligibilityExtended();
   }
   
   
     /* ================= Aadhaar Validation ================= */
   function initAadhaarValidation() {
   
   const aadhaarInputs = document.querySelectorAll('[name="aadhaar_no"]');
   
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
   		return c;
   	}
   };
   
   aadhaarInputs.forEach(function(input) {
   
   	input.addEventListener('blur', function(event) {
   
   		const uid = event.target.value.trim();
   		if (!uid) return;
   
   		if (uid.length !== 12 || Verhoeff.check(uid) !== 0) {
   
   			Swal.fire({
   				icon: 'error',
   				title: 'Invalid Aadhaar Number',
   				text: 'Please enter a valid 12-digit Aadhaar number.',
   				confirmButtonText: 'OK'
   			}).then(() => {
   				event.target.value = '';
   				event.target.focus();
   			});
   
   		}
   
   	});
   
   });
   
   }
   
   
     /* ================= Image Preview ================= */
   function previewBeneficiaryImage(input) {
   
   const file = input.files[0];
   if (!file) return;
   
   if (!['image/jpeg', 'image/jpg'].includes(file.type)) {
   	Swal.fire('Invalid File', 'Only JPG/JPEG images are allowed', 'error');
   	input.value = '';
   	return;
   }
   
   if (file.size > 307200) {
   	Swal.fire('File Too Large', 'Image must be less than 300 KB', 'error');
   	input.value = '';
   	return;
   }
   
   const reader = new FileReader();
   reader.onload = function (e) {
   	document.getElementById('beneficiary_preview').src = e.target.result;
   };
   reader.readAsDataURL(file);
   }
   
     /* ================= Utility ================= */
   function show(id) {
   const el = document.getElementById(id);
   if (el) el.classList.remove('d-none');
   }
   
   function hide(id) {
   const el = document.getElementById(id);
   if (el) el.classList.add('d-none');
   }
   
   function isVisible(el) {
   return el && !el.classList.contains('d-none');
   }
   
   
     /* ================= Pension Type ================= */
   function applyingScemeType(typeId) {
   
   hide('tg_registration_div');
   hide('tg_document_div');
   hide('widow_document_div');
   hide('disability_section');
   
   if (typeId == '9') {
   	show('tg_registration_div');
   	show('tg_document_div');
   }
   
   if (typeId == '2' || typeId == '5') {
   	show('widow_document_div');
   }
   
   if (typeId == '3' || typeId == '4' || typeId == '6') {
   	show('disability_section');
   }
   }
   
   
     /* ================= Disability ================= */
   function disabilityTypeCondition() {
   
   const type = document.getElementById('disability_type_condition_id')?.value;
   
   if (type == '1') {
   	show('disability_validity_div');
   } else {
   	hide('disability_validity_div');
   }
   }
   
   
     /* ================= Bank ================= */
   function bankAccountTypeChange(type) {
   
   if (type == '2') {
   	show('second_holder_div');
   } else {
   	hide('second_holder_div');
   }
   }
   
     /* =========================================================
     PENSION ELIGIBILITY EXTENSION (NON-DESTRUCTIVE ADDITION)
     ========================================================= */
   
     /* --------- NEW RULE DEFINITIONS --------- */
   
     /* Age-based pensions */
   const pensionAgeRulesExtended = {
   1: { min: 60, label: 'Old Age Pension' },
   2: { min: 18, label: 'Widow Pension' },
   5: { min: 18, label: 'Widow Pension AIDS/HIV' },
   7: { min: 30, label: 'Unmarried Woman' },
   8: { min: 18, label: 'Divorcee Woman' },
   10: { min: 18, label: 'Widow Due To Covid' },
   11: { min: 0,  label: 'Orphan Due To Covid' }
   };
   
     /* Disability / Leprosy pensions */
   const pensionDisabilityRulesExtended = {
   3: { minPercent: 40, label: 'Disability Pension' },
   4: { minPercent: 40, label: 'Leprosy Pension' },
   6: { minPercent: 40, label: 'Disability Pension AIDS/HIV' }
   };
   
     /* TG pensions (no age / no % check) */
   const pensionTGTypesExtended = [9];
   
   
     /* --------- INLINE ERROR HELPERS --------- */
   function showInlineError(id, message) {
   const el = document.getElementById(id);
   if (!el) return;
   el.textContent = message;
   el.classList.remove('d-none');
   }
   
   function hideInlineError(id) {
   const el = document.getElementById(id);
   if (el) el.classList.add('d-none');
   }
   
   
     /* --------- EXTENDED ELIGIBILITY CHECK --------- */
   function validatePensionEligibilityExtended() {
   
   const pensionType = Number(
   	document.getElementById('pension_type_id')?.value || 0
   	);
   
   const age = Number(
   	document.getElementById('age')?.value || 0
   	);
   
   const disabilityPercent = Number(
   	document.getElementById('disability_percentage')?.value || 0
   	);
   
   hideInlineError('disability_percent_error');
   
     /* ---- TG Pension ---- */
   if (pensionTGTypesExtended.includes(pensionType)) {
   	return true;
   }
   
     /* ---- Disability Pension ---- */
   if (pensionDisabilityRulesExtended[pensionType]) {
   	const rule = pensionDisabilityRulesExtended[pensionType];
   
   	if (disabilityPercent < rule.minPercent) {
   		Swal.fire({
   			icon: 'error',
   			title: 'Not Eligible',
   			text: `${rule.label} requires minimum ${rule.minPercent}% disability`
   		}).then(() => {
   			const disabilityInput = document.getElementById('disability_percentage');
   			if (disabilityInput) {
   				disabilityInput.value = '';
   				disabilityInput.focus();
   			}
   		});
   		return false;
   	}
   
   	return true;
   }
   
     /* ---- Age-based Pension ---- */
   if (pensionAgeRulesExtended[pensionType]) {
   	const rule = pensionAgeRulesExtended[pensionType];
   
   	if (age < rule.min) {
   		Swal.fire({
   			icon: 'error',
   			title: 'Not Eligible',
   			text: `${rule.label} requires minimum age of ${rule.min} years`
   		}).then(() => {
   			const ageInput = document.getElementById('age');
   			if (ageInput) {
   				ageInput.value = '';
   				const dobInput = document.getElementById('dob');
   				if (dobInput) dobInput.focus();
   			}
   		});
   		return false;
   	}
   }
   
   return true;
   }
</script>
<script>
   document.addEventListener('DOMContentLoaded', function () {
   
   	const pensionForm = document.getElementById('pensionApplicationForm');
   	const disabilityInput = document.getElementById('disability_percentage');
   
         /* ================= REAL-TIME DISABILITY VALIDATION ================= */
   	if (disabilityInput) {
   		disabilityInput.addEventListener('input', function () {
   			if (this.value.length >= 2) {
   				validatePensionEligibilityExtended();
   			}
   		});
   	}
   
         /* ================= FORM SUBMIT VALIDATION ================= */
   	if (pensionForm) {
   		pensionForm.addEventListener('submit', function (e) {
   
   			if (!validateFormFields(pensionForm)) {
   				e.preventDefault();
   				e.stopPropagation();
   				return false;
   			}
   
   			if (!validatePensionEligibilityExtended()) {
   				e.preventDefault();
   				e.stopPropagation();
   				return false;
   			}
   
   			if (!aadhaarVerified) {
   				e.preventDefault();
   				Swal.fire({
   					icon: 'warning',
   					title: 'Aadhaar Verification Required',
   					text: 'Please verify Aadhaar before submitting.'
   				});
   				return false;
   			}
   
   		});
   	}
   
   });
   
      /* ================= UNIVERSAL FILE SIZE VALIDATION (300KB) ================= */
   document.querySelectorAll('input[type="file"]').forEach(input => {
   
   	input.addEventListener('change', function () {
   
   		const file = this.files[0];
   		if (!file) return;
   
   		const maxSize = 307200;
   
   		if (file.size > maxSize) {
   
   			Swal.fire({
   				icon: 'error',
   				title: 'File Too Large',
   				text: 'Maximum allowed file size is 300 KB.'
   			}).then(() => {
   				this.value = '';
   			});
   
   		}
   
   	});
   
   });
   
     /* ================= AADHAAR STYLE NAME VALIDATION ================= */
   
   function validateAadhaarName(inputField, isRequired = true) {
   	if (!inputField) return;
   
   	inputField.addEventListener('input', function () {
   
   		let value = this.value.toUpperCase();
   
   		value = value.replace(/[^A-Z\s]/g, '');
   		value = value.replace(/\s+/g, ' ');
   		value = value.replace(/^\s/, '');
   
   		this.value = value;
   	});
   
   	inputField.addEventListener('blur', function () {
   
   		const value = this.value.trim();
   
   		if (isRequired) {
   			if (value.length < 2) {
   				this.classList.add('is-invalid');
   				this.setCustomValidity('Name must be at least 2 characters.');
   				return;
   			}
   		}
   
   		if (!isRequired && value.length === 0) {
   			this.classList.remove('is-invalid');
   			this.setCustomValidity('');
   			return;
   		}
   
   		this.classList.remove('is-invalid');
   		this.setCustomValidity('');
   	});
   }
   
   
   const firstName  = document.getElementById('applicant_first_name');
   const middleName = document.getElementById('applicant_middle_name');
   const lastName   = document.getElementById('applicant_last_name');
   const fullName   = document.getElementById('applicant_name');
   
      /* Apply Aadhaar validation */
   validateAadhaarName(firstName, true);
   validateAadhaarName(middleName, false);
   validateAadhaarName(lastName, true);
   validateAadhaarName(fullName);
   
      /* ================= AUTO POPULATE FULL NAME ================= */
   
   let fullNameEditedManually = false;
   
   if (fullName) {
   	fullName.addEventListener('input', function () {
   		fullNameEditedManually = true;
   	});
   }
   
   function updateFullName() {
   
   	if (!firstName || !middleName || !lastName || !fullName) return;
   	if (fullNameEditedManually) return;
   
   	const mergedName = [
   		firstName.value.trim(),
   		middleName.value.trim(),
   		lastName.value.trim()
   	]
   	.filter(part => part !== '')
   	.join(' ')
   	.replace(/\s+/g, ' ')
   	.trim();
   
   	fullName.value = mergedName;
   }
   
   [firstName, middleName, lastName].forEach(field => {
   	if (field) {
   		field.addEventListener('input', updateFullName);
   		field.addEventListener('blur', updateFullName);
   	}
   });
   
   
     /* ================= FORM FIELD VALIDATION ================= */
   function validateFormFields(form) {
   
   	/* ===== Beneficiary Image Mandatory Validation ===== */
   	const beneficiaryImageInput = form.querySelector('#beneficiary_image');
   
   	if (beneficiaryImageInput) {
   
   		const isVisible = $(beneficiaryImageInput).closest('.col-md-6').is(':visible');
   
   		if (isVisible && beneficiaryImageInput.files.length === 0) {
   
   			Swal.fire({
   				icon: 'error',
   				title: 'Beneficiary Photograph Required',
   				text: 'Please upload Beneficiary Photograph before submitting.'
   			}).then(() => {
   				document.querySelector('label[for="beneficiary_image"]')?.click();
   			});
   
   			return false;
   		}
   	}
   
   	let firstInvalid = null;
   
   	const requiredLabels = form.querySelectorAll('label.required-field');
   
   	requiredLabels.forEach(label => {
   
   		const parentCol = label.closest('.col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12');
   
   		if (!parentCol) return;
   
             /* Skip if entire column is hidden (d-none or jQuery hide) */
   		if (!$(parentCol).is(':visible')) return;
   
   		const field = parentCol.querySelector('input, select, textarea');
   		if (!field) return;
   
   		let isEmpty = false;
   
   		if (field.classList.contains('select')) {
   			const value = $(field).val();
   			isEmpty = !value || value === '';
   		}
   		else if (field.type === 'file') {
   			isEmpty = field.files.length === 0;
   		}
   		else {
   			isEmpty = field.value.trim() === '';
   		}
   
   		if (isEmpty) {
   
   			if (field.classList.contains('select')) {
   				$(field).next('.select2-container')
   				.find('.select2-selection')
   				.css('border-color', '#dc3545');
   			} else {
   				field.style.borderColor = '#dc3545';
   			}
   
   			if (!firstInvalid) firstInvalid = field;
   
   		} else {
   
   			if (field.classList.contains('select')) {
   				$(field).next('.select2-container')
   				.find('.select2-selection')
   				.css('border-color', '');
   			} else {
   				field.style.borderColor = '';
   			}
   
   		}
   
   	});
   
   	if (firstInvalid) {
   
   		Swal.fire({
   			icon: 'error',
   			title: 'Incomplete Application',
   			text: 'Please fill all required red marked fields before submitting.',
   			confirmButtonText: 'OK'
   		}).then(() => {
   
   			if (firstInvalid.classList.contains('select')) {
   				$(firstInvalid).select2('open');
   			} else {
   				firstInvalid.focus();
   			}
   
   		});
   
   		return false;
   	}
   
   	return true;
   }   
</script>
@endsection