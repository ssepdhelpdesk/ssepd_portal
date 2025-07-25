@section('title') 
SSEPD || NGO Registration
@endsection 
@extends('frontend.layouts.main')
@section('style')
<style>
   .itsrequired {
      color: red;
      font-weight: bold;
   }
</style>
@endsection 
@section('content')
<div class="contact-widget-area pb-70">
   <div class="container">
      <div class="section-title text-center mb-45">
         <span>SEND MESSAGE</span>
         <h2>Ready to get started?</h2>
      </div>
      <div class="contact-form">
         @include('frontend.component.message')
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
         <form class="from-prevent-multiple-submits" method="POST" action="{{ route('frontend.ngo.ngo_initial_part_one_store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
            @csrf
            @method('post')
            <div class="form-body">
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group" id="ngo_registration_type_div">
                        <label class="form-label">NGO Registration Type<span class="itsrequired"> *</span></label>
                        <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_registration_type">
                           <option value="">--Select--</option>
                           <option value="1" {{ old('ngo_registration_type')}}>New</option>
                           <option value="2" {{ old('ngo_registration_type')}}>Renewal</option>
                        </select>
                        <div id="ngo_registration_type_error"></div>
                        @error('ngo_registration_type')
                        <label class="error">{{ $message }}</label>
                        @enderror
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group" id="ngo_category_div">
                        <label class="form-label">NGO Category<span class="itsrequired"> *</span></label>
                        <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_category">
                           <option value="">--Select--</option>
                           <option value="1">RPwD Act</option>
                           <option value="2">Senior Citizen Act</option>
                        </select>
                        <div id="ngo_category_error"></div>
                        @error('ngo_category')
                        <label class="error">{{ $message }}</label>
                        @enderror
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group" id="ngo_org_name_div">
                        <label class="form-label">NGO Name<span class="itsrequired"> *</span></label>
                        <input type="text" id="ngo_org_name" name="ngo_org_name" value="{{old('ngo_org_name')}}" class="form-control" placeholder="NGO Name">
                        <div id="ngo_org_name_error"></div>
                        @error('ngo_org_name')
                        <label class="error">{{ $message }}</label>
                        @enderror
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group" id="ngo_org_pan_div">
                        <label class="form-label">NGO Pan No<span class="itsrequired"> *</span></label>
                        <input type="text" id="ngo_org_pan" name="ngo_org_pan" value="{{old('ngo_org_pan')}}" class="form-control" placeholder="NGO Pan No">
                        <div id="ngo_org_pan_error"></div>
                        <div id="check_ngo_pan_no"></div>
                        @error('ngo_org_pan')
                        <label class="error">{{ $message }}</label>
                        @enderror
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group" id="ngo_org_email_div">
                        <label class="form-label">NGO Email<span class="itsrequired"> *</span></label>
                        <input type="text" id="ngo_org_email" name="ngo_org_email" value="{{old('ngo_org_email')}}" class="form-control" placeholder="NGO Email">
                        <div id="ngo_org_email_error"></div>
                        <div id="check_ngo_org_email"></div>
                        @error('ngo_org_email')
                        <label class="error">{{ $message }}</label>
                        @enderror
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group" id="ngo_org_phone_div">
                        <label class="form-label">NGO Phone No<span class="itsrequired"> *</span></label>
                        <input type="text" id="ngo_org_phone" name="ngo_org_phone" value="{{old('ngo_org_phone')}}" class="form-control" placeholder="NGO Phone No">
                        <div id="ngo_org_phone_error"></div>
                        @error('ngo_org_phone')
                        <label class="error">{{ $message }}</label>
                        @enderror
                     </div>
                  </div>
               </div>
            </div>
            <div class="form-actions">
               <button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits">
                  <i class="spinner fa fa-spinner fa-spin"></i> Submit
               </button>
               <button type="button" class="btn btn-warning">Cancel</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection 
@section('script')
<script>
   document.addEventListener("DOMContentLoaded", () => {
     const form = document.forms['vform'];
     
     const fields = [
       { name: "ngo_registration_type", type: "select" },
       { name: "ngo_category", type: "select" },
       { name: "ngo_org_name", type: "text" },
       { name: "ngo_org_pan", type: "pan" },
       { name: "ngo_org_email", type: "email" },
       { name: "ngo_org_phone", type: "phone" }
    ];
    
    const validators = {
       text: (val) => val.length > 0,
       select: (val) => val !== "",
       email: (val) => /^\S+@\S+\.\S+$/.test(val),
       phone: (val) => /^\d{10}$/.test(val),
       pan: (val) => /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(val)
    };
    
    const defaultMessages = {
       text: "This field is required.",
       select: "Please select an option.",
       email: "Enter a valid email address.",
       phone: "Enter a valid 10-digit phone number.",
       pan: "Enter a valid PAN (e.g., ABCDE1234F)."
    };
    
    const getFieldElement = (name) => form[name];
    const getErrorElement = (name) => document.getElementById(`${name}_error`);
    
    const showError = (name, message) => {
       const el = getErrorElement(name);
       el.textContent = message;
       el.style.color = "red";
    };
    
    const clearError = (name) => {
       const el = getErrorElement(name);
       el.textContent = "";
    };
    
    const validateField = ({ name, type, message }) => {
      const el = getFieldElement(name);
      const value = el && el.value ? el.value.trim() : '';
      
      const isValid = validators[type] ? validators[type](value) : true;
      
      if (!isValid) {
         showError(name, message || defaultMessages[type]);
      } else {
         clearError(name);
      }
      return isValid;
   };
   
   const validateForm = () => {
    return fields.reduce((valid, field) => {
      return validateField(field) && valid;
   }, true);
 };
 
       /*Attach real-time validation*/
 fields.forEach(field => {
    const input = getFieldElement(field.name);
    if (input) {
      const eventType = field.type === "select" ? "change" : "blur";
      input.addEventListener(eventType, () => validateField(field));
   }
});
 window.Validate = () => validateForm();
});
</script>
<script type="text/javascript">
   $(document).ready(function () {
      $("#ngo_org_pan").blur(function () {
         const ngoPan = $(this).val();
         if (!ngoPan.trim()) {
            $('#check_ngo_pan_no').html('<span style="color:#FF0000">Please provide a valid NGO PAN.</span>');
            return;
         }
         $.get("{{ route('frontend.ngo.check_pan_no') }}", { ngo_org_pan: ngoPan }, function (data) {
            if (data == 0) {
               $('#check_ngo_pan_no').html('<span style="color:#03713E"></span>');
            } else if (data == 1) {
               $('#check_ngo_pan_no').html('<span style="color:#FF0000">NGO PAN is already registered.</span>');
               $("#ngo_org_pan").val('');
            } else if (data == 2) {
               $('#check_ngo_pan_no').html('<span style="color:#FF0000">Please provide a valid NGO PAN.</span>');
            }
         }).fail(function () {
            $('#check_ngo_pan_no').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
         });
      });
      
      $("#ngo_org_email").blur(function () {
         const ngoEmail = $(this).val();
         if (!ngoEmail.trim()) {
            $('#check_ngo_org_email').html('<span style="color:#FF0000">Please provide a valid NGO Email ID.</span>');
            return;
         }
         $.get("{{ route('frontend.ngo.check_ngo_org_email') }}", { ngo_org_email: ngoEmail }, function (data) {
            if (data == 0) {
               $('#check_ngo_org_email').html('<span style="color:#03713E"></span>');
            } else if (data == 1) {
               $('#check_ngo_org_email').html('<span style="color:#FF0000">NGO Email ID is already registered.</span>');
               $("#ngo_org_email").val('');
            } else if (data == 2) {
               $('#check_ngo_org_email').html('<span style="color:#FF0000">Please provide a valid NGO Email ID.</span>');
            }
         }).fail(function () {
            $('#check_ngo_org_email').html('<span style="color:#FF0000">An error occurred. Please try again.</span>');
         });
      });
   });
</script>
@endsection