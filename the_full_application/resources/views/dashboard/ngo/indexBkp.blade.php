@section('title') 
NGO || Create
@endsection 
@extends('dashboard.layouts.main')
@section('style')
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h3 class="card-title">Basic Details</h3>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
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
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_name_div">
                                 <label class="form-label">NGO Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_name" name="ngo_org_name" value="{{old('ngo_org_name')}}" class="form-control" placeholder="NGO Name">
                                 <div id="ngo_org_name_error"></div>
                                 @error('ngo_org_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_pan_div">
                                 <label class="form-label">NGO Pan No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_pan" name="ngo_org_pan" value="{{old('ngo_org_pan')}}" class="form-control" placeholder="NGO Pan No">
                                 <div id="ngo_org_pan_error"></div>
                                 @error('ngo_org_pan')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_pan_file_div">
                                 <label class="form-label">Upload PAN<span class="itsrequired"> *</span></label>
                                 <input type="file" class="form-control" id="ngo_org_pan_file" value="{{old('ngo_org_pan_file')}}" name="ngo_org_pan_file" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                 <div id="ngo_org_pan_file_error"></div>
                                 @error('ngo_org_pan_file')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <!--/row-->
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_email_div">
                                 <label class="form-label">NGO Email<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_email" name="ngo_org_email" value="{{old('ngo_org_email')}}" class="form-control" placeholder="NGO Email">
                                 <div id="ngo_org_email_error"></div>
                                 @error('ngo_org_email')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_phone_div">
                                 <label class="form-label">NGO Phone No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_phone" name="ngo_org_phone" value="{{old('ngo_org_phone')}}" class="form-control" placeholder="NGO Phone No">
                                 <div id="ngo_org_phone_error"></div>
                                 @error('ngo_org_phone')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_website_div">
                                 <label class="form-label">NGO Website<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_website" name="ngo_org_website" value="{{old('ngo_org_website')}}" class="form-control" placeholder="NGO Website">
                                 <div id="ngo_org_website_error"></div>
                                 @error('ngo_org_website')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_registered_with_div">
                                 <label class="form-label">Registered With<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_registered_with">
                                    <option value="">--Select--</option>
                                    <option value="1">Registrar of Companies</option>
                                    <option value="2">Registrar of Societies</option>
                                    <option value="2">Registrar of Cooperative Societies</option>
                                    <option value="2">Charity Commissioner</option>
                                    <option value="2">International Organisation</option>
                                    <option value="2">Sub Registrar</option>
                                    <option value="2">Any Other</option>
                                 </select>
                                 <div id="ngo_registered_with_error"></div>
                                 @error('ngo_registered_with')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <!--/row-->
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_type_of_vo_or_ngo_div">
                                 <label class="form-label">Type of VO/NGO<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_type_of_vo_or_ngo">
                                    <option value="">--Select--</option>
                                    <option value="1">Private Sector Companies (Sec 8/25)</option>
                                    <option value="2">Registered Societies (Non Government)</option>
                                    <option value="2">Trust  (Non Government)</option>
                                    <option value="2">Other Registered Entities  (Non Government)</option>
                                    <option value="2">Academic Institutions (Govt)</option>
                                    <option value="2">Academic Institutions (Private)</option>
                                 </select>
                                 <div id="ngo_type_of_vo_or_ngo_error"></div>
                                 @error('ngo_type_of_vo_or_ngo')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_registration_no_div">
                                 <label class="form-label">Registration No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_registration_no" name="ngo_registration_no" value="{{old('ngo_registration_no')}}" class="form-control" placeholder="NGO Registration No">
                                 <div id="ngo_registration_no_error"></div>
                                 @error('ngo_registration_no')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_file_rc_div">
                                 <label class="form-label">Upload RC<span class="itsrequired"> *</span></label>
                                 <input type="file" class="form-control" id="ngo_file_rc" name="ngo_file_rc" value="{{old('ngo_file_rc')}}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                 <div id="ngo_file_rc_error"></div>
                                 @error('ngo_file_rc')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_date_of_registration_div">
                                 <label class="form-label">Date of Registration<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="ngo_date_of_registration" name="ngo_date_of_registration" value="{{old('ngo_date_of_registration')}}" aria-describedby="inputGroupFileAddon01">
                                 <div id="ngo_date_of_registration_error"></div>
                                 @error('ngo_date_of_registration')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <!--/row-->
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_organisation_type_div">
                                 <label class="form-label">Organisation Type<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_organisation_type">
                                    <option value="">--Select--</option>
                                    <option value="1">Independent Organisation</option>
                                    <option value="2">Branch of Other Organisation</option>
                                 </select>
                                 <div id="ngo_organisation_type_error"></div>
                                 @error('ngo_organisation_type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_parent_organisation_div">
                                 <label class="form-label">Parent Organisation</label>
                                 <input type="text" id="ngo_parent_organisation" name="ngo_parent_organisation" value="{{old('ngo_parent_organisation')}}" class="form-control" placeholder="NGO Parent Organisation">
                                 <div id="ngo_parent_organisation_error"></div>
                                 @error('ngo_parent_organisation')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_reg_velidity_available_div">
                                 <label class="form-label">Is NGO Reg Validity Available<span class="itsrequired"> *</span></label>
                                 <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio me-3">
                                       <input type="radio" id="yes" name="ngo_reg_velidity_available" value="1" class="form-check-input" {{ old('ngo_reg_velidity_available') == 1 ? 'checked' : '' }}>
                                       <label class="form-check-label" for="yes">Yes</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                       <input type="radio" id="no" name="ngo_reg_velidity_available" value="0" class="form-check-input" {{ old('ngo_reg_velidity_available') == 0 ? 'checked' : '' }}>
                                       <label class="form-check-label" for="no">No</label>
                                    </div>
                                 </div>
                                 <div id="ngo_reg_velidity_available_error"></div>
                                 @error('ngo_reg_velidity_available')
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
                     <div class="form-actions">
<!-- <button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
   <button type="button" class="btn btn-warning">Cancel</button> -->
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
   var ngo_category = document.forms['vform']['ngo_category'];
   var ngo_org_name = document.forms['vform']['ngo_org_name'];
   var ngo_org_pan = document.forms['vform']['ngo_org_pan'];
   var ngo_org_pan_file = document.forms['vform']['ngo_org_pan_file'];
   var ngo_org_email = document.forms['vform']['ngo_org_email'];
   var ngo_org_phone = document.forms['vform']['ngo_org_phone'];
   var ngo_org_website = document.forms['vform']['ngo_org_website'];
   var ngo_registered_with = document.forms['vform']['ngo_registered_with'];
   var ngo_type_of_vo_or_ngo = document.forms['vform']['ngo_type_of_vo_or_ngo'];
   var ngo_registration_no = document.forms['vform']['ngo_registration_no'];
   var ngo_file_rc = document.forms['vform']['ngo_file_rc'];
   var ngo_date_of_registration = document.forms['vform']['ngo_date_of_registration'];
   var ngo_organisation_type = document.forms['vform']['ngo_organisation_type'];
   var ngo_parent_organisation = document.forms['vform']['ngo_parent_organisation'];
   var ngo_reg_velidity_available = document.forms['vform']['ngo_reg_velidity_available'];   
   var ngo_address_type = document.forms['vform']['ngo_address_type'];

   var ngo_category_error = document.getElementById('ngo_category_error');
   var ngo_org_name_error = document.getElementById('ngo_org_name_error');
   var ngo_org_pan_error = document.getElementById('ngo_org_pan_error');
   var ngo_org_pan_file_error = document.getElementById('ngo_org_pan_file_error');
   var ngo_org_email_error = document.getElementById('ngo_org_email_error');
   var ngo_org_phone_error = document.getElementById('ngo_org_phone_error');
   var ngo_org_website_error = document.getElementById('ngo_org_website_error');
   var ngo_registered_with_error = document.getElementById('ngo_registered_with_error');
   var ngo_type_of_vo_or_ngo_error = document.getElementById('ngo_type_of_vo_or_ngo_error');
   var ngo_registration_no_error = document.getElementById('ngo_registration_no_error');
   var ngo_file_rc_error = document.getElementById('ngo_file_rc_error');
   var ngo_date_of_registration_error = document.getElementById('ngo_date_of_registration_error');
   var ngo_organisation_type_error = document.getElementById('ngo_organisation_type_error');
   var ngo_parent_organisation_error = document.getElementById('ngo_parent_organisation_error');
   var ngo_reg_velidity_available_error = document.getElementById('ngo_reg_velidity_available_error');   
   var ngo_address_type_error = document.getElementById('ngo_address_type_error');

   ngo_category.addEventListener('change', ngo_category_verify, true);
   ngo_org_name.addEventListener('blur', ngo_org_name_verify, true);
   ngo_org_pan.addEventListener('blur', ngo_org_pan_verify, true);
   ngo_org_pan_file.addEventListener('blur', ngo_org_pan_file_verify, true);
   ngo_org_email.addEventListener('blur', ngo_org_email_verify, true);
   ngo_org_phone.addEventListener('blur', ngo_org_phone_verify, true);
   ngo_org_website.addEventListener('blur', ngo_org_website_verify, true);
   ngo_registered_with.addEventListener('blur', ngo_registered_with_verify, true);
   ngo_type_of_vo_or_ngo.addEventListener('blur', ngo_type_of_vo_or_ngo_verify, true);
   ngo_registration_no.addEventListener('blur', ngo_registration_no_verify, true);
   ngo_file_rc.addEventListener('blur', ngo_file_rc_verify, true);
   ngo_date_of_registration.addEventListener('blur', ngo_date_of_registration_verify, true);
   ngo_organisation_type.addEventListener('blur', ngo_organisation_type_verify, true);
   ngo_parent_organisation.addEventListener('blur', ngo_parent_organisation_verify, true);
   ngo_reg_velidity_available.addEventListener('blur', ngo_reg_velidity_available_verify, true);   
   ngo_address_type.addEventListener('blur', ngo_address_type_verify, true);  

   function Validate(callback) {
      let selected = false;
      if (ngo_category.value == "") {
         ngo_category.style.border = "1px solid red";
         document.getElementById('ngo_category_div').style.color = "red";
         ngo_category_error.textContent = "Ngo Category is required";
         ngo_category.focus();
         return false;
      }
      if (ngo_org_name.value == "") {
         ngo_org_name.style.border = "1px solid red";
         document.getElementById('ngo_org_name_div').style.color = "red";
         ngo_org_name_error.textContent = "Ngo Name is required";
         ngo_org_name.focus();
         return false;
      }
      if (ngo_org_pan.value == "") {
         ngo_org_pan.style.border = "1px solid red";
         document.getElementById('ngo_org_pan_div').style.color = "red";
         ngo_org_pan_error.textContent = "Ngo PAN No is required";
         ngo_org_pan.focus();
         return false;
      }
      if (ngo_org_pan_file.value == "") {
         ngo_org_pan_file.style.border = "1px solid red";
         document.getElementById('ngo_org_pan_file_div').style.color = "red";
         ngo_org_pan_file_error.textContent = "Upload NGO PAN soft copy: only PDF Allowed!!";
         ngo_org_pan_file.focus();
         return false;
      }
      if (ngo_org_email.value == "") {
         ngo_org_email.style.border = "1px solid red";
         document.getElementById('ngo_org_email_div').style.color = "red";
         ngo_org_email_error.textContent = "NGO email Id is required";
         ngo_org_email.focus();
         return false;
      }
      if (ngo_org_phone.value == "") {
         ngo_org_phone.style.border = "1px solid red";
         document.getElementById('ngo_org_phone_div').style.color = "red";
         ngo_org_phone_error.textContent = "NGO Mobile No is required";
         ngo_org_phone.focus();
         return false;
      }
      if (ngo_org_website.value == "") {
         ngo_org_website.style.border = "1px solid red";
         document.getElementById('ngo_org_website_div').style.color = "red";
         ngo_org_website_error.textContent = "NGO Website is required";
         ngo_org_website.focus();
         return false;
      }
      if (ngo_registered_with.value == "") {
         ngo_registered_with.style.border = "1px solid red";
         document.getElementById('ngo_registered_with_div').style.color = "red";
         ngo_registered_with_error.textContent = "Ngo Registered with is required";
         ngo_registered_with.focus();
         return false;
      }
      if (ngo_type_of_vo_or_ngo.value == "") {
         ngo_type_of_vo_or_ngo.style.border = "1px solid red";
         document.getElementById('ngo_type_of_vo_or_ngo_div').style.color = "red";
         ngo_type_of_vo_or_ngo_error.textContent = "Type of NGO is required";
         ngo_type_of_vo_or_ngo.focus();
         return false;
      }
      if (ngo_registration_no.value == "") {
         ngo_registration_no.style.border = "1px solid red";
         document.getElementById('ngo_registration_no_div').style.color = "red";
         ngo_registration_no_error.textContent = "NGO Registration no is required";
         ngo_registration_no.focus();
         return false;
      }
      if (ngo_file_rc.value == "") {
         ngo_file_rc.style.border = "1px solid red";
         document.getElementById('ngo_file_rc_div').style.color = "red";
         ngo_file_rc_error.textContent = "Upload NGO Registration Certificate soft copy: only PDF Allowed!!";
         ngo_file_rc.focus();
         return false;
      }
      if (ngo_date_of_registration.value == "") {
         ngo_date_of_registration.style.border = "1px solid red";
         document.getElementById('ngo_date_of_registration_div').style.color = "red";
         ngo_date_of_registration_error.textContent = "NGO Registration Date is required";
         ngo_date_of_registration.focus();
         return false;
      }
      if (ngo_organisation_type.value == "") {
         ngo_organisation_type.style.border = "1px solid red";
         document.getElementById('ngo_organisation_type_div').style.color = "red";
         ngo_organisation_type_error.textContent = "Ngo Organisation type is required";
         ngo_organisation_type.focus();
         return false;
      }
      if (![...ngo_reg_velidity_available].some(radio => radio.checked)) {
         document.getElementById('ngo_reg_velidity_available_div').style.color = "red";
         ngo_reg_velidity_available_error.textContent = "NGO Reg Validity Available or Not is required";
         return false;
      } else {
         ngo_reg_velidity_available_error.textContent = "";
         document.getElementById('ngo_reg_velidity_available_div').style.color = "#5e6e66";
      }
      if (![...ngo_address_type].some(radio => radio.checked)) {
         document.getElementById('ngo_address_type_div').style.color = "red";
         ngo_address_type_error.textContent = "NGO Address type is required";
         return false;
      } else {
         ngo_address_type_error.textContent = "";
         document.getElementById('ngo_address_type_div').style.color = "#5e6e66";
         return true;
      }   
      return true;
      callback();
   }

   function ngo_category_verify() {
      if (ngo_category.value != "") {
         ngo_category.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_category_div').style.color = "#5e6e66";
         ngo_category_error.innerHTML = "";
         return true;
      } else {
         ngo_category.style.border = "1px solid red";
         document.getElementById('ngo_category_div').style.color = "red";
         ngo_category_error.textContent = "NGO Category is required";
         return false;
      }
   }
   function ngo_org_name_verify() {
      if (ngo_org_name.value != "") {
         ngo_org_name.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_org_name_div').style.color = "#5e6e66";
         ngo_org_name_error.innerHTML = "";
         return true;
      }
   }
   function ngo_org_pan_verify() {
      if (ngo_org_pan.value != "") {
         ngo_org_pan.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_org_pan_div').style.color = "#5e6e66";
         ngo_org_pan_error.innerHTML = "";
         return true;
      }
   }
   function ngo_org_pan_file_verify() {
      if (ngo_org_pan_file.value != "") {
         ngo_org_pan_file.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_org_pan_file_div').style.color = "#5e6e66";
         ngo_org_pan_file_error.innerHTML = "";
         return true;
      }
   }
   function ngo_org_email_verify() {
      if (ngo_org_email.value != "") {
         ngo_org_email.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_org_email_div').style.color = "#5e6e66";
         ngo_org_email_error.innerHTML = "";
         return true;
      }
   }
   function ngo_org_phone_verify() {
      if (ngo_org_phone.value != "") {
         ngo_org_phone.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_org_phone_div').style.color = "#5e6e66";
         ngo_org_phone_error.innerHTML = "";
         return true;
      }
   }
   function ngo_org_website_verify() {
      if (ngo_org_website.value != "") {
         ngo_org_website.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_org_website_div').style.color = "#5e6e66";
         ngo_org_website_error.innerHTML = "";
         return true;
      }
   }
   function ngo_registered_with_verify() {
      if (ngo_registered_with.value != "") {
         ngo_registered_with.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_registered_with_div').style.color = "#5e6e66";
         ngo_registered_with_error.innerHTML = "";
         return true;
      }
   }
   function ngo_type_of_vo_or_ngo_verify() {
      if (ngo_type_of_vo_or_ngo.value != "") {
         ngo_type_of_vo_or_ngo.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_type_of_vo_or_ngo_div').style.color = "#5e6e66";
         ngo_type_of_vo_or_ngo_error.innerHTML = "";
         return true;
      }
   }
   function ngo_registration_no_verify() {
      if (ngo_registration_no.value != "") {
         ngo_registration_no.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_registration_no_div').style.color = "#5e6e66";
         ngo_registration_no_error.innerHTML = "";
         return true;
      }
   }
   function ngo_file_rc_verify() {
      if (ngo_file_rc.value != "") {
         ngo_file_rc.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_file_rc_div').style.color = "#5e6e66";
         ngo_file_rc_error.innerHTML = "";
         return true;
      }
   }
   function ngo_date_of_registration_verify() {
      if (ngo_date_of_registration.value != "") {
         ngo_date_of_registration.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_date_of_registration_div').style.color = "#5e6e66";
         ngo_date_of_registration_error.innerHTML = "";
         return true;
      }
   }
   function ngo_organisation_type_verify() {
      if (ngo_organisation_type.value != "") {
         ngo_organisation_type.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_organisation_type_div').style.color = "#5e6e66";
         ngo_organisation_type_error.innerHTML = "";
         return true;
      }
   }
   function ngo_parent_organisation_verify() {
      if (ngo_parent_organisation.value != "") {
         ngo_parent_organisation.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_parent_organisation_div').style.color = "#5e6e66";
         ngo_parent_organisation_error.innerHTML = "";
         return true;
      }
   }
   function ngo_reg_velidity_available_verify() {
      const isChecked = [...ngo_reg_velidity_available].some(radio => radio.checked);
      if (isChecked) {
         document.getElementById('ngo_reg_velidity_available_div').style.color = "#5e6e66";
         ngo_reg_velidity_available_error.innerHTML = "";
         return true;
      } else {
         document.getElementById('ngo_reg_velidity_available_div').style.color = "red";
         ngo_reg_velidity_available_error.innerHTML = "NGO Reg Validity Available or Not is required";
         return false;
      }
   }
   function ngo_address_type_verify() {
      const isChecked = [...ngo_address_type].some(radio => radio.checked);
      if (isChecked) {
         document.getElementById('ngo_address_type_div').style.color = "#5e6e66";
         ngo_address_type_error.innerHTML = "";
         return true;
      } else {
         document.getElementById('ngo_address_type_div').style.color = "red";
         ngo_address_type_error.innerHTML = "NGO Address type is required";
         return false;
      }
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
                  $('#municipality-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
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

            fetch(`/dashboard/get-address-type-content/${value}`)
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
         const pinField = document.getElementById('pin');
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

      function validateVillageFields() {
         const state = document.getElementById('state-dropdown');
         const district = document.getElementById('district-dropdown');
         const block = document.getElementById('block-dropdown');
         const grampanchayat = document.getElementById('grampanchayat-dropdown');
         const village = document.getElementById('village-dropdown');

         const alertDiv = document.createElement('div');
         alertDiv.classList.add('alert', 'alert-warning', 'alert-rounded', 'alert-dismissible');
         alertDiv.innerHTML = `
         <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
         Please fillup all the required fields.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;

         const alertContainer = document.getElementById('alert-container');
         if (!alertContainer) {
            const newAlertContainer = document.createElement('div');
            newAlertContainer.id = 'alert-container';
            document.body.appendChild(newAlertContainer);
         }

         alertContainer.appendChild(alertDiv);

         setTimeout(() => {
            alertDiv.remove();
         }, 3000);

         if (!state || state.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select State.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         if (!district || district.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select District.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         if (!block || block.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select Block.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         if (!grampanchayat || grampanchayat.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select Grampanchayat.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         if (!village || village.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select Village.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         return Validate();
      }

      function validateMunicipalityFields() {
         const state = document.getElementById('state-dropdown');
         const district = document.getElementById('district-dropdown');
         const municipality = document.getElementById('municipality-dropdown');

         const alertDiv = document.createElement('div');
         alertDiv.classList.add('alert', 'alert-warning', 'alert-rounded', 'alert-dismissible');
         alertDiv.innerHTML = `
         <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
         Please fillup all the required fields.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;

         const alertContainer = document.getElementById('alert-container');
         if (!alertContainer) {
            const newAlertContainer = document.createElement('div');
            newAlertContainer.id = 'alert-container';
            document.body.appendChild(newAlertContainer);
         }

         alertContainer.appendChild(alertDiv);

         setTimeout(() => {
            alertDiv.remove();
         }, 3000);

         if (!state || state.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select State.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         if (!district || district.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select District.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         if (!municipality || municipality.value === '') {
            alertDiv.innerHTML = `
            <img src="https://uxwing.com/wp-content/themes/uxwing/download/emoji-emoticon/sad-icon.png" width="30" class="img-circle" alt="img">
            Please select Municipality.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            return false;
         }

         return Validate();
      }
   });

document.getElementById('ngo_org_pan_file').addEventListener('change', function(event) {
   const file = event.target.files[0];
   const errorDiv = document.getElementById('ngo_org_pan_file_error');
   const maxFileSize = 1 * 1024 * 1024;

   errorDiv.innerHTML = '';

   if (!file) {
      return;
   }

   if (file.type !== 'application/pdf') {
      errorDiv.innerHTML = '<label class="error">Only PDF files are allowed.</label>';
      event.target.value = '';
      return;
   }

   if (file.size > maxFileSize) {
      errorDiv.innerHTML = '<label class="error">File size must be less than 1MB.</label>';
      event.target.value = '';
      return;
   }
});

document.getElementById('ngo_file_rc').addEventListener('change', function(event) {
   const file = event.target.files[0];
   const errorDiv = document.getElementById('ngo_file_rc_error');
   const maxFileSize = 1 * 1024 * 1024;

   errorDiv.innerHTML = '';

   if (!file) {
      return;
   }

   if (file.type !== 'application/pdf') {
      errorDiv.innerHTML = '<label class="error">Only PDF files are allowed.</label>';
      event.target.value = '';
      return;
   }

   if (file.size > maxFileSize) {
      errorDiv.innerHTML = '<label class="error">File size must be less than 1MB.</label>';
      event.target.value = '';
      return;
   }
});

</script>
@endsection