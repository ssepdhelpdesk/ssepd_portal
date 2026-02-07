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

/* Weekday row */
.flatpickr-weekdays {
   background: #342777;
}

.flatpickr-weekday {
   color: #ffffff;
   font-weight: 500;
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
            <h2 class="breadcrumb-title mb-2">Settings</h2>
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb justify-content-center mb-0">
                  <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Settings</li>
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
            <form method="post" enctype="multipart/form-data">
               @csrf
<!-- ================= Basic Information ================= -->
<div class="card">
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
               JPG/JPEG only, max 500 KB (800×800 px)
            </p>
            <div class="new-employee-field">
               <div class="d-flex align-items-center mt-2">
                  <!-- Upload Button -->
                  <div class="image-upload position-relative mb-0">
                     <!-- Hidden file input -->
                     <input type="file"
                        name="beneficiary_image"
                        id="beneficiary_image"
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
         <label class="form-label required-field">Name as per Aadhaar</label>
         <input type="text" name="applicant_name" id="applicant_name" class="form-control">
      </div>
      <div class="col-md-4 mb-3">
         <label class="form-label required-field">Gender</label>
         <select name="gender_id" id="gender_id" class="select form-control required-field populate"
         onchange="checkValidation11();">
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
<div class="card">
   <div class="card-body">
      <h5 class="fs-18 mb-3">Communication Address</h5>
      <div class="row">
         <div class="col-md-4 mb-3"><label class="form-label required-field">District</label>
            <select name="district_id" id="district_id" class="select form-control required-field">

            </select>
         </div>
         <div class="col-md-4 mb-3"><label class="form-label required-field">Sub Division</label>
            <select name="sub_division_id" id="sub_division_id" class="select form-control required-field">

            </select>
         </div>
         <div class="col-md-4 mb-3">
            <label class="form-label required-field">Address Type</label>
            <select name="address_type_id" id="address_type_id" class="select form-control required-field populate">
               <option value="">-Select-</option>
               <option value="1">Block</option>
               <option value="2">ULB</option>
            </select>
         </div>
         <div class="col-md-4 mb-3"><label class="form-label required-field">Block</label>
            <select name="block_id" id="block_id" class="select form-control required-field">

            </select>
         </div>
         <div class="col-md-4 mb-3"><label class="form-label required-field">Gram Panchayat</label>
            <select name="gp_id" id="gp_id" class="select form-control required-field">

            </select>
         </div>
         <div class="col-md-4 mb-3"><label class="form-label required-field">Village</label>
            <select name="village_id" id="village_id" class="select form-control required-field">

            </select>
         </div>
         <div class="col-md-4 mb-3"><label class="form-label required-field">Municipality</label>
            <select name="municipality_id" id="municipality_id" class="select form-control required-field">

            </select>
         </div>
         <div class="col-md-4 mb-3"><label class="form-label required-field">Ward</label>
            <select name="ward_id" id="ward_id" class="select form-control required-field">

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
         <div class="col-md-4 mb-3"><label class="form-label required-field">Disability Category</label>
            <select name="disability_category_id" id="disability_category_id" class="select form-control required-field">

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
<div class="card">
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
         <div class="col-md-4 mb-3"><label class="form-label required-field">IFSC Code</label>
            <select name="ifsc_code" id="ifsc_code" class="select form-control required-field">

            </select>
         </div>

      </div>      
   </div>
</div>
<!-- ================= Documents Upload ================= -->
<div class="card">
   <div class="card-body">
      <h5 class="fs-18 mb-3">Documents Upload</h5>
      <p class="fs-14 mb-3">All documents must be in PDF format. Maximum allowed file size is 500 KB per file.</p>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.required-field').forEach(field => {
        const label = field.closest('.col-md-4')?.querySelector('label');
        if (label && !label.querySelector('.required-star')) {
            const star = document.createElement('span');
            star.textContent = ' *';
            star.className = 'required-star text-danger';
            label.appendChild(star);
        }
    });
});
</script>
<script>
function previewBeneficiaryImage(input) {

    const file = input.files[0];
    if (!file) return;

    // Validate file type
    if (!['image/jpeg', 'image/jpg'].includes(file.type)) {
        Swal.fire('Invalid File', 'Only JPG/JPEG images are allowed', 'error');
        input.value = '';
        return;
    }

    // Validate size (500 KB)
    if (file.size > 512000) {
        Swal.fire('File Too Large', 'Image must be less than 500 KB', 'error');
        input.value = '';
        return;
    }

    // Preview image
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('beneficiary_preview').src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
   document.addEventListener('DOMContentLoaded', function () {
      $('.select').select2({
         placeholder: "Select",
         allowClear: true,
         width: "100%"
      });

      hide('tg_registration_div');
      hide('tg_document_div');
      hide('widow_document_div');
      hide('disability_section');
      hide('disability_validity_div');
      hide('second_holder_div');

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

      document.getElementById('dob').setAttribute('readonly', true);

      document.getElementById('bank_account_type')?.addEventListener('change', function () {
         bankAccountTypeChange(this.value);
      });

/* ================= FORM SUBMIT VALIDATION ================= */
      document.querySelector('form')?.addEventListener('submit', function (e) {

/* Check pension eligibility FIRST */
         if (!validatePensionEligibilityExtended()) {
            e.preventDefault();
            return;
         }

/* Then check required fields */
         if (!validateForm()) {
            e.preventDefault();
            return;
         }

      });

/* ================= Aadhaar Validation ================= */
      initAadhaarValidation();
   });

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

      if (typeId == '3' || typeId == '6') {
         show('disability_section');
      }
   }

/* ================= DOB → AGE (Flatpickr Compatible) ================= */
   function calculateAge() {

      const dobValue = document.getElementById('dob').value;
      const ageField = document.getElementById('age');

      if (!dobValue) {
         ageField.value = '';
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
   }

/* ================= Disability ================= */
   function disabilityTypeCondition() {
      const type = document.getElementById('disability_type_condition_id').value;
      type == '1' ? show('disability_validity_div') : hide('disability_validity_div');
   }

/* ================= Bank ================= */
   function bankAccountTypeChange(type) {
      type == '2' ? show('second_holder_div') : hide('second_holder_div');
   }

/* ================= FORM VALIDATION ================= */
   function validateForm() {
    let firstInvalid = null;

    const elements = document.querySelectorAll('input, select');

    elements.forEach(el => {
        if (el.type === 'button' || el.type === 'submit') return;

        const parentCol = el.closest('.col-md-4');
        if (parentCol && parentCol.classList.contains('d-none')) return;

        let isEmpty = false;

        // Special handling for Select2
        if (el.classList.contains('select')) {
            const value = $(el).val();
            isEmpty = !value || value === '';
        } else if (el.type === 'file') {
            isEmpty = el.files.length === 0;
        } else {
            isEmpty = el.value.trim() === '';
        }

        if (isEmpty) {
            // Show red border
            if (el.classList.contains('select')) {
                $(el).next('.select2-container').find('.select2-selection').css('border-color', '#dc3545');
            } else {
                el.style.borderColor = '#dc3545';
            }

            if (!firstInvalid) firstInvalid = el;
        } else {
            // Reset border
            if (el.classList.contains('select')) {
                $(el).next('.select2-container').find('.select2-selection').css('border-color', '');
            } else {
                el.style.borderColor = '';
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

/* ================= Aadhaar Validation ================= */
   function initAadhaarValidation() {

      const aadhaarInputs = document.querySelectorAll('[name="aadhaar_no"]');

      aadhaarInputs.forEach(function(input) {
         input.addEventListener('blur', function(event) {

            const uid = event.target.value.trim();
            if (!uid) return;

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

            if (Verhoeff.check(uid) !== 0) {
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
</script>

<script>
   document.addEventListener('DOMContentLoaded', function () {

/* ================= DIGITS-ONLY INPUT HANDLER ================= */

      function allowDigitsOnly(input, maxLength = null, maxValue = null) {

         if (!input) return;

/* Typing */
         input.addEventListener('input', function () {

            let value = this.value.replace(/\D/g, '');

            if (maxLength !== null) {
               value = value.slice(0, maxLength);
            }

            if (maxValue !== null && value !== '') {
               value = Math.min(parseInt(value, 10), maxValue).toString();
            }

            this.value = value;
         });

/* Paste */
         input.addEventListener('paste', function (e) {
            e.preventDefault();
            let pasted = (e.clipboardData || window.clipboardData).getData('text');
            pasted = pasted.replace(/\D/g, '');

            if (maxLength !== null) {
               pasted = pasted.slice(0, maxLength);
            }

            if (maxValue !== null && pasted !== '') {
               pasted = Math.min(parseInt(pasted, 10), maxValue).toString();
            }

            this.value = pasted;
         });

      }

/* ================= APPLY RULES FOR DIGIT ONLY ================= */

      allowDigitsOnly(document.getElementById('aadhaar_no'), 12);
      allowDigitsOnly(document.getElementById('mobile_no'), 10);
      allowDigitsOnly(document.getElementById('pin_code'), 6);
      allowDigitsOnly(document.getElementById('house_no'), 3);
      allowDigitsOnly(document.getElementById('disability_percentage'), 3, 100);
      allowDigitsOnly(document.getElementById('account_number'), 22);

   });
</script>


<script>
/* =========================================================
PENSION ELIGIBILITY EXTENSION (NON-DESTRUCTIVE ADDITION)
========================================================= */

/* --------- NEW RULE DEFINITIONS --------- */

/* Age-based pensions (existing logic respected) */
   const pensionAgeRulesExtended = {
      1: { min: 60, label: 'Old Age Pension' },
      2: { min: 18, label: 'Widow Pension' },
      5: { min: 18, label: 'Widow Pension AIDS/HIV' },
      7: { min: 30, label: 'Unmarried Woman' },
      8: { min: 18, label: 'Divorcee Woman' },
      10: { min: 18, label: 'Widow Due To Covid' },
      11: { min: 0,  label: 'Orphan Due To Covid' }
   };

/* Disability / Leprosy pensions (percentage-based) */
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

/* ---- TG Pension (no age, no % validation) ---- */
      if (pensionTGTypesExtended.includes(pensionType)) {
         return true;
      }

/* ---- Disability / Leprosy Pension ---- */
      if (pensionDisabilityRulesExtended[pensionType]) {

         const rule = pensionDisabilityRulesExtended[pensionType];

         if (disabilityPercent < rule.minPercent) {
            Swal.fire({
               icon: 'error',
               title: 'Not Eligible',
               text: `${rule.label} requires minimum ${rule.minPercent}% disability`
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
            });
            return false;
         }
      }

      return true;
   }


/* --------- SAFE FORM SUBMIT HOOK --------- */
/* This wraps your EXISTING validateForm() without modifying it */

   (function () {

      if (typeof window.validateForm === 'function') {

         const originalValidateForm = window.validateForm;

         window.validateForm = function () {

/* Run existing validation first */
            const result = originalValidateForm.apply(this, arguments);

            if (result === false) {
               return false;
            }

/* Run extended eligibility check */
            return validatePensionEligibilityExtended();
         };

      }

   })();
</script>
@endsection