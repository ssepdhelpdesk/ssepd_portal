@section('title') 
SSEPD-IT
@endsection 
@extends('website.layout.mainlayout')
@section('style')

@endsection 
@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-bar text-center">
   <div class="container">
      <div class="row">
         <div class="col-md-12 col-12">
            <h2 class="breadcrumb-title mb-2">DBT Consent</h2>
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb justify-content-center mb-0">
                  <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">DBT Consent</li>
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
         <div class="col-lg-3 theiaStickySidebar">
            <div class="settings-sidebar mb-lg-0">
               <div>
                  <h6 class="mb-3">Basic Information</h6>
                  <ul class="mb-3 pb-1">
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Name: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->applicant_name}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Care of: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->father_husband_name}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Scheme: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->scheme}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Gender: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->gender}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Sanction Order No: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->sanction_order_no}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Disbursement Mode: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->disbursement_mode}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Address: </b><span class="ms-2"> District: {{$nsapPortal27Jan2026CsvData->district}} <br> Block/ULB: {{$nsapPortal27Jan2026CsvData->sub_district_municipality}} <br>GP/Ward: {{$nsapPortal27Jan2026CsvData->gram_panchayat_ward}}</span></a>
                     </li>
                  </ul>
                  <hr>
               </div>
            </div>
         </div>
         <div class="col-lg-9">
            <div class="card mb-0">
               <div class="card-body">
                  <h6 class="fs-18 page-title fw-bold">Fill the DBT Consent Form</h6>
                  <div class="border-bottom mb-4 pb-4">
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
                     <div class="row">
                        <div class="col-md-12">
                           <form action="{{ route('website.pensioners.dbt_consent_store_form', $uuid) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                              <div class="col-md-4 col-12">
                                 <div class="mb-3 position-relative">
                                    <label class="form-label">Name as per Aadhaar<span class="text-danger"> *</span></label>
                                    <div class="position-relative readonly-input">
                                       <input type="text" id="name_of_the_beneficiary" name="name_of_the_beneficiary" value="{{old('name_of_the_beneficiary', $nsapPortal27Jan2026CsvData->applicant_name)}}" class="pass-input form-control" placeholder="Name as per Aadhaar">
                                    </div>
                                    @error('name_of_the_beneficiary')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-4 col-12">
                                 <div class="mb-3 position-relative">
                                    <label class="form-label">Gender<span class="text-danger"> *</span></label>
                                    <div class="position-relative">
                                       <select class="select  pass-input form-control" name="gender" id="gender">
                                          <option>Select</option>
                                          <option value="M">Male</option>
                                          <option value="F">Female</option>
                                          <option value="O">Other</option>
                                       </select>
                                    </div>
                                    @error('gender')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-4 col-12">
                                 <div class="mb-3 position-relative">
                                    <label class="form-label">DOB<span class="text-danger"> *</span></label>
                                    <div class="position-relative">
                                       <input type="date" class="form-control datetimepicker" name="dob" id="dob" max="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy">
                                    </div>
                                    @error('dob')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-md-4 col-12">
                                 <div class="mb-3 position-relative">
                                    <label class="form-label">Aadhaar No <span class="text-danger"> *</span></label>
                                    <div class="position-relative input-group" id="passwordInput">
                                       <input type="text" id="aadhaar_no" name="aadhaar_no" value="{{old('aadhaar_no')}}" class="pass-inputs form-control" placeholder="Aadhaar Number" >
                                       <span class="input-group-btn"><button class="btn btn-info text-white" type="button" id="btnVerifyAadhaar">Verify!</button></span>
                                       <input type="hidden" id="verified_aadhar" class="form-control" name="verified_aadhar">
                                       <input type="hidden" id="verified_aadhar_remarks" class="form-control" name="verified_aadhar_remarks">
                                    </div>
                                    <div class="mt-2 fs-14" id="passwordInfo">
                                       Please, Verify your Aadhaar First.
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
                                 <!-- <div class="col-md-4 col-12">
                                    <div class="mb-3 position-relative readonly-input">
                                       <label class="form-label">District<span class="text-danger"> *</span></label>
                                       <div class="position-relative">
                                          <input type="text" id="district" name="district" value="{{old('district', $nsapPortal27Jan2026CsvData->district)}}" class="pass-input form-control" placeholder="District">
                                       </div>
                                    </div>
                                 </div> -->
                                 <div class="col-md-4 col-12">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Address Type <span class="text-danger"> *</span></label>
                                       <div class="d-flex gap-4 mt-2">
                                          <div class="form-check">
                                             <input class="form-check-input" type="radio" name="address_type" id="address_type_block" value="1">
                                             <label class="form-check-label" for="address_type_block">Block</label>
                                          </div>
                                          <div class="form-check">
                                             <input class="form-check-input" type="radio" name="address_type" id="address_type_ulb" value="2">
                                             <label class="form-check-label" for="address_type_ulb">ULB</label>
                                          </div>
                                          @error('address_type_block')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-4 col-12" id="block_div" style="display:none;">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Block <span class="text-danger"> *</span></label>
                                       <select class="select pass-input form-control" name="block_id" id="block_id">
                                          <option value="">Select Block</option>
                                          @foreach($block as $data)
                                          <option value="{{$data->block_id}}">{{$data->block_name}}</option>
                                          @endforeach
                                       </select>
                                    </div>
                                    @error('block_id')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                                 <div class="col-md-4 col-12" id="ulb_div" style="display:none;">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">ULB <span class="text-danger"> *</span></label>
                                       <select class="select pass-input form-control" name="ulb_id" id="ulb_id">
                                          <option value="">Select ULB</option>
                                          @foreach($municipality as $data)
                                          <option value="{{$data->municipality_id}}">{{$data->municipality_name}}</option>
                                          @endforeach
                                       </select>
                                    </div>
                                    @error('ulb_id')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                                 <div class="col-md-4 col-12" id="gp_div" style="display:none;">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Grampanchayat <span class="text-danger"> *</span></label>
                                       <select class="select pass-input form-control" name="gp_id" id="gp_id">
                                          <option value="">Select Grampanchayat</option>
                                       </select>
                                    </div>
                                    @error('gp_id')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                                 <div class="col-md-4 col-12" id="village_div" style="display:none;">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Village <span class="text-danger"> *</span></label>
                                       <select class="select pass-input form-control" name="village_id" id="village_id">
                                          <option value="">Select Village</option>
                                       </select>
                                    </div>
                                    @error('village_id')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                                 <div class="col-md-4 col-12" id="ward_div" style="display:none;">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Ward <span class="text-danger"> *</span></label>
                                       <select class="select pass-input form-control" name="ward_id" id="ward_id">
                                          <option value="">Select Ward</option>
                                       </select>
                                    </div>
                                    @error('ward_id')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                                 <div class="col-md-4 col-12">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">PIN<span class="text-danger"> *</span></label>
                                       <div class="position-relative">
                                          <input type="text" id="pin" name="pin" value="{{old('pin')}}" maxlength="6" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="pass-input form-control" placeholder="PIN">
                                       </div>
                                       @error('pin')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-4 col-12">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">IFSC<span class="text-danger"> *</span></label>
                                       <div class="position-relative">
                                          <select class="select  pass-input form-control" name="ifsc" id="ifsc">
                                             <option>Select</option>
                                             @foreach($bankMaster as $data)
                                             <option value="{{$data->bank_ifsc}}">{{$data->bank_ifsc}}</option>
                                             @endforeach
                                          </select>
                                       </div>
                                       @error('ifsc')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-4 col-12">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Bank Account Number<span class="text-danger"> *</span></label>
                                       <div class="position-relative">
                                          <input type="text" id="bank_po_account" name="bank_po_account" value="{{old('bank_po_account')}}" maxlength="20" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="pass-input form-control" placeholder="Bank Account Number">
                                       </div>
                                       @error('bank_po_account')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-4 col-12">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Upload Passbook Front Page <span class="text-danger"> *</span></label>
                                       <div class="position-relative">
                                          <input type="file" id="bank_account_file" name="bank_account_file" class="form-control" accept=".pdf">
                                       </div>
                                       @error('bank_account_file')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-12">
                                    <button class="btn btn-secondary" type="submit">Submit</button>
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection 
@section('script')
<script>
   $(document).ready(function () {

    $('.select').select2({
     placeholder: "Select",
     allowClear: true,
     width: "100%"
  });

    function showError(fieldId, msg) {
     $('#' + fieldId + '_error').html('<label class="error text-danger">' + msg + '</label>');
  }

  function clearError(fieldId) {
     $('#' + fieldId + '_error').html('');
  }

  function ensureErrorDiv(fieldId) {
     if ($('#' + fieldId + '_error').length === 0) {
      $('#' + fieldId).after('<div id="' + fieldId + '_error" class="error-div"></div>');
   }
}

let requiredFields = [
  "name_of_the_beneficiary",
  "gender",
  "dob",
  "aadhaar_no",
  "pin",
  "ifsc",
  "bank_po_account",
  "bank_account_file",
  "block_id",
  "gp_id",
  "village_id",
  "ulb_id",
  "ward_id"
];

requiredFields.forEach(function (field) {
  ensureErrorDiv(field);
});

if ($('#address_type_error').length === 0) {
  $('input[name="address_type"]').last().parent().parent().after(
   '<div id="address_type_error" class="error-div"></div>'
   );
}

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
j: [0,4,3,2,1,5,6,7,8,9],
check: function (str) {
   let c = 0;
   str.replace(/\D+/g, "")
   .split("")
   .reverse()
   .join("")
   .replace(/[\d]/g, function (u, i) {
     c = Verhoeff.d[c][Verhoeff.p[i % 8][parseInt(u, 10)]];
  });
   return c;
}
};

$('#aadhaar_no').on('blur', function () {
  let uid = $(this).val().trim();
  clearError("aadhaar_no");

  if(uid === "") return;

  if(!/^\d{12}$/.test(uid)){
   showError("aadhaar_no", "Enter valid 12-digit Aadhaar number");
   $(this).val('');
   return;
}

if (Verhoeff.check(uid) !== 0) {
   showError("aadhaar_no", "Aadhaar number is not valid!");
   $(this).val('');
}
});

$('#aadhaar_no, #name_of_the_beneficiary').on('input', function () {

  $('#verified_aadhar').val('');
  $('#verified_aadhar_remarks').val('');
  $('#aadhaar_verify_result').html('');
  $('#passwordInfo').show();

  $('#btnVerifyAadhaar')
  .prop('disabled', false)
  .removeClass('btn-success')
  .addClass('btn-info')
  .text('Verify!');

  $('#aadhaar_no, #name_of_the_beneficiary').prop('readonly', false);
});

$('#btnVerifyAadhaar').on('click', function () {

  let aadhaar = $('#aadhaar_no').val().trim();
  let name = $('#name_of_the_beneficiary').val().trim();

  $('#aadhaar_verify_result').html('');
  clearError("aadhaar_no");
  clearError("name_of_the_beneficiary");

  if (name === '') {
   showError("name_of_the_beneficiary", "Name is required");
   return;
}

if (!/^\d{12}$/.test(aadhaar)) {
   showError("aadhaar_no", "Enter valid 12-digit Aadhaar number");
   return;
}

if (Verhoeff.check(aadhaar) !== 0) {
   showError("aadhaar_no", "Aadhaar number is not valid!");
   return;
}

$('#btnVerifyAadhaar')
.prop('disabled', true)
.text('Verifying...');

$.ajax({
   url: "{{ route('website.pensioners.consent_aadhar_verification_process') }}",
   type: "POST",
   dataType: "json",
   data: {
    _token: "{{ csrf_token() }}",
    aadhaar_no: aadhaar,
    name_of_the_beneficiary: name
 },

 success: function (res) {

    let message = res.data ?? "Verification failed";
    $('#verified_aadhar_remarks').val(message);

    if (message.toLowerCase().includes('verify successfully')) {

     $('#verified_aadhar').val(1);

     $('#aadhaar_verify_result').html(
      '<span class="badge bg-success">Aadhaar Verified Successfully</span>'
      );

     $('#passwordInfo').hide();

     $('#btnVerifyAadhaar')
     .removeClass('btn-info')
     .addClass('btn-success')
     .text('Verified');

     $('#aadhaar_no, #name_of_the_beneficiary').prop('readonly', true);

  } else {

     $('#verified_aadhar').val(0);

     $('#aadhaar_verify_result').html(
      '<span class="badge bg-danger">Please provide valid details as per Aadhaar.</span>'
      );

     $('#btnVerifyAadhaar')
     .prop('disabled', false)
     .removeClass('btn-success')
     .addClass('btn-info')
     .text('Verify!');
  }
},

error: function () {

 $('#verified_aadhar').val(0);

 $('#aadhaar_verify_result').html(
  '<span class="badge bg-danger">Verification failed. Try again.</span>'
  );

 $('#btnVerifyAadhaar')
 .prop('disabled', false)
 .removeClass('btn-success')
 .addClass('btn-info')
 .text('Verify!');
}
});

});

$('input[name="address_type"]').on('change', function () {

  clearError("block_id");
  clearError("gp_id");
  clearError("village_id");
  clearError("ulb_id");
  clearError("ward_id");
  $('#address_type_error').html('');

  let selected = $(this).val();

  if (selected == "1") {
   $('#block_div').show();
   $('#gp_div').hide();
   $('#village_div').hide();

   $('#ulb_div').hide();
   $('#ward_div').hide();

   $('#ulb_id').val('').trigger('change');
   $('#ward_id').val('').trigger('change');
}

if (selected == "2") {
   $('#ulb_div').show();
   $('#ward_div').hide();

   $('#block_div').hide();
   $('#gp_div').hide();
   $('#village_div').hide();

   $('#block_id').val('').trigger('change');
   $('#gp_id').val('').trigger('change');
   $('#village_id').val('').trigger('change');
}

});

$('#block_id').on('change', function () {

  let blockId = $(this).val();

  $('#gp_id').html('<option value="">Select Grampanchayat</option>').trigger('change');
  $('#village_id').html('<option value="">Select Village</option>').trigger('change');

  $('#gp_div').hide();
  $('#village_div').hide();

  if (blockId == "") return;

  $.ajax({
   url: "{{ route('website.pensioners.get.gps.by.block', ':id') }}".replace(':id', blockId),
   type: "GET",
   dataType: "json",
   success: function (res) {

    if (res.length > 0) {
     $.each(res, function (key, gp) {
      $('#gp_id').append('<option value="' + gp.gp_id + '">' + gp.gp_name + '</option>');
   });
     $('#gp_div').show();
  }
}
});
});

$('#gp_id').on('change', function () {

  let gpId = $(this).val();

  $('#village_id').html('<option value="">Select Village</option>').trigger('change');
  $('#village_div').hide();

  if (gpId == "") return;

  $.ajax({
   url: "{{ route('website.pensioners.get.villages.by.gp', ':id') }}".replace(':id', gpId),
   type: "GET",
   dataType: "json",
   success: function (res) {

    if (res.length > 0) {
     $.each(res, function (key, village) {
      $('#village_id').append('<option value="' + village.village_id + '">' + village.village_name + '</option>');
   });
     $('#village_div').show();
  }
}
});
});

$('#ulb_id').on('change', function () {

  let ulbId = $(this).val();

  $('#ward_id').html('<option value="">Select Ward</option>').trigger('change');
  $('#ward_div').hide();

  if (ulbId == "") return;

  $.ajax({
   url: "{{ route('website.pensioners.get.wards.by.ulb', ':id') }}".replace(':id', ulbId),
   type: "GET",
   dataType: "json",
   success: function (res) {

    if (res.length > 0) {
     $.each(res, function (key, ward) {
      $('#ward_id').append('<option value="' + ward.ward_code + '">' + ward.ward_name + '</option>');
   });
     $('#ward_div').show();
  }
}
});
});

$('#pin').on('input', function () {
  this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});

$('#bank_po_account').on('input', function () {
  this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20);
});

$('form').on('submit', function (e) {

  let isValid = true;

  $('.error-div').html('');
  $('#aadhaar_verify_result').html('');

  let name = $('#name_of_the_beneficiary').val().trim();
  let gender = $('#gender').val();
  let dob = $('#dob').val();
  let aadhaar = $('#aadhaar_no').val().trim();
  let verifiedAadhar = $('#verified_aadhar').val();

  let pin = $('#pin').val().trim();
  let ifsc = $('#ifsc').val();
  let bankAcc = $('#bank_po_account').val().trim();

  if (name === '') {
   showError("name_of_the_beneficiary", "Name is required");
   isValid = false;
}

if (!gender || gender === "Select") {
   showError("gender", "Gender is required");
   isValid = false;
}

if (!dob) {
   showError("dob", "DOB is required");
   isValid = false;
}

if (!/^\d{12}$/.test(aadhaar) || Verhoeff.check(aadhaar) !== 0) {
   showError("aadhaar_no", "Enter valid Aadhaar number");
   isValid = false;
}

if (verifiedAadhar != "1") {
   $('#aadhaar_verify_result').html(
    '<span class="badge bg-danger">Please verify Aadhaar before submitting.</span>'
    );
   isValid = false;
}

let addressType = $('input[name="address_type"]:checked').val();
if (!addressType) {
   $('#address_type_error').html('<label class="error text-danger">Address Type is required</label>');
   isValid = false;
}

if (addressType == "1") {

   let block = $('#block_id').val();
   let gp = $('#gp_id').val();
   let village = $('#village_id').val();

   if (!block) {
    showError("block_id", "Block is required");
    isValid = false;
 }

 if ($('#gp_div').is(':visible') && !gp) {
    showError("gp_id", "Grampanchayat is required");
    isValid = false;
 }

 if ($('#village_div').is(':visible') && !village) {
    showError("village_id", "Village is required");
    isValid = false;
 }
}

if (addressType == "2") {

   let ulb = $('#ulb_id').val();
   let ward = $('#ward_id').val();

   if (!ulb) {
    showError("ulb_id", "ULB is required");
    isValid = false;
 }

 if ($('#ward_div').is(':visible') && !ward) {
    showError("ward_id", "Ward is required");
    isValid = false;
 }
}

if (!/^\d{6}$/.test(pin)) {
   showError("pin", "Valid 6-digit PIN is required");
   isValid = false;
}

if (!ifsc || ifsc === "Select") {
   showError("ifsc", "IFSC is required");
   isValid = false;
}

if (!/^\d{9,20}$/.test(bankAcc)) {
   showError("bank_po_account", "Enter valid Bank Account Number (9 to 20 digits)");
   isValid = false;
}

let fileInput = document.getElementById("bank_account_file");
if (!fileInput || fileInput.files.length === 0) {
   showError("bank_account_file", "PDF file is required");
   isValid = false;
} else {
   let file = fileInput.files[0];
   if (file.type !== "application/pdf") {
      showError("bank_account_file", "Only PDF file is allowed");
      isValid = false;
   }
}

if (!isValid) {
   e.preventDefault();
   $('html, body').animate({
    scrollTop: $(".card-body").offset().top - 100
 }, 400);
}

});

});
</script>


@endsection