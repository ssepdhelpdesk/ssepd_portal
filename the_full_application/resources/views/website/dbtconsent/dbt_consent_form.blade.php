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
                     <div class="row">
                        <div class="col-md-12">
                           <form action="">
                              <div class="row">
                                 <div class="col-md-4 col-12">
                                    <div class="mb-3 position-relative">
                                       <label class="form-label">Name as per Aadhaar<span class="text-danger"> *</span></label>
                                       <div class="position-relative">
                                          <input type="text" id="applicant_name" name="applicant_name" value="{{old('applicant_name', $nsapPortal27Jan2026CsvData->applicant_name)}}" class="pass-input form-control" placeholder="Name as per Aadhaar">
                                       </div>
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
                                 <div class="col-12">
                                    <button class="btn btn-secondary" type="submit">Change Password</button>
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
<script>
   $(document).ready(function () {

      $('#aadhaar_no, #applicant_name').on('input', function () {
         $('#verified_aadhar').val('');
         $('#verified_aadhar_remarks').val('');
         $('#aadhaar_verify_result').html('');

         $('#btnVerifyAadhaar')
         .prop('disabled', false)
         .removeClass('btn-success')
         .addClass('btn-info')
         .text('Verify!');

         $('#aadhaar_no, #applicant_name').prop('readonly', false);
      });

      $(document).on('click', '#btnVerifyAadhaar', function () {

         let aadhaar = $('#aadhaar_no').val().trim();
         let name    = $('#applicant_name').val().trim();

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
            url: "{{ route('website.pensioners.consent_aadhar_verification_process') }}",
            type: "POST",
            dataType: "json",
            data: {
               _token: "{{ csrf_token() }}",
               aadhaar_no: aadhaar,
               applicant_name: name
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

                  $('#aadhaar_no, #applicant_name').prop('readonly', true);

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

                  $('#aadhaar_no, #applicant_name').prop('readonly', false);
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

               $('#aadhaar_no, #applicant_name').prop('readonly', false);
            }
         });
      });
   });
</script>
@endsection