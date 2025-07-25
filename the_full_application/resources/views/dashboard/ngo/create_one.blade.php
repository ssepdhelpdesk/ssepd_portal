@section('title') 
NGO || Create
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.part_one_after_initial_store', $id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group readonly-input" id="ngo_registration_type_div">
                                 <label class="form-label">NGO Registration Type<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select readonly-input" data-placeholder="Choose a Category" tabindex="1" name="ngo_registration_type">
                                    <option value="">--Select--</option>
                                    <option value="1" {{ old('ngo_registration_type', $ngoRegistration->ngo_registration_type ?? '') == '1' ? 'selected' : '' }}>New</option>
                                    <option value="2" {{ old('ngo_registration_type', $ngoRegistration->ngo_registration_type ?? '') == '2' ? 'selected' : '' }}>Renewal</option>
                                 </select>
                                 <div id="ngo_registration_type_error"></div>
                                 @error('ngo_registration_type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group readonly-input" id="ngo_category_div">
                                 <label class="form-label">NGO Category<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select readonly-input" data-placeholder="Choose a Category" tabindex="1" name="ngo_category">
                                    <option value="">--Select--</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                    {{ old('ngo_category', $ngoRegistration->ngo_category ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->ngo_category_name }}
                                    </option>
                                    @endforeach
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
                                 <input type="text" id="ngo_org_name" name="ngo_org_name" value="{{old('ngo_org_name', $ngoRegistration->ngo_org_name)}}" class="form-control readonly-input" placeholder="NGO Name">
                                 <div id="ngo_org_name_error"></div>
                                 @error('ngo_org_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_pan_div">
                                 <label class="form-label">NGO Pan No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_pan" name="ngo_org_pan" value="{{old('ngo_org_pan', $ngoRegistration->ngo_org_pan)}}" class="form-control readonly-input" placeholder="NGO Pan No">
                                 <div id="ngo_org_pan_error"></div>
                                 <div id="check_ngo_pan_no"></div>
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
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_email_div">
                                 <label class="form-label">NGO Email<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_email" name="ngo_org_email" value="{{old('ngo_org_email', $ngoRegistration->ngo_org_email)}}" class="form-control readonly-input" placeholder="NGO Email">
                                 <div id="ngo_org_email_error"></div>
                                 <div id="check_ngo_org_email"></div>
                                 @error('ngo_org_email')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_org_phone_div">
                                 <label class="form-label">NGO Phone No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_org_phone" name="ngo_org_phone" value="{{old('ngo_org_phone', $ngoRegistration->ngo_org_phone)}}" class="form-control readonly-input" placeholder="NGO Phone No">
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
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_registered_with" id="ngo_registered_with">
                                    <option value="">--Select--</option>
                                    <option value="1">Societies Registration Act, 1860</option>
                                    <option value="2">Indian Trusts Act, 1882</option>
                                    <option value="3">Section 8 of the Companies Act, 2013</option>
                                    <option value="4">Registrar of Companies</option>
                                    <option value="5">Registrar of Societies</option>
                                    <option value="6">Registrar of Cooperative Societies</option>
                                    <option value="7">Charity Commissioner</option>
                                    <option value="8">International Organisation</option>
                                    <option value="9">Sub Registrar</option>
                                    <option value="10">Any Other (Specify)</option>
                                 </select>
                                 <div id="ngo_registered_with_error"></div>
                                 @error('ngo_registered_with')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                                 <div id="ngo_other_reg_act_div" style="display: none;">
                                    <label class="form-label">Specify NGO Other Registration Act</label>
                                    <input type="text" class="form-control" name="ngo_other_reg_act">
                                 </div>
                                 <div id="ngo_other_reg_act_error"></div>
                                 @error('ngo_other_reg_act')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_type_of_vo_or_ngo_div">
                                 <label class="form-label">Type of VO/NGO<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_type_of_vo_or_ngo">
                                    <option value="">--Select--</option>
                                    <option value="1">Private Sector Companies (Sec 8/25)</option>
                                    <option value="2">Registered Societies (Non Government)</option>
                                    <option value="3">Trust  (Non Government)</option>
                                    <option value="4">Other Registered Entities  (Non Government)</option>
                                    <option value="5">Academic Institutions (Govt)</option>
                                    <option value="6">Academic Institutions (Private)</option>
                                 </select>
                                 <div id="ngo_type_of_vo_or_ngo_error"></div>
                                 @error('ngo_type_of_vo_or_ngo')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_reg_no_div">
                                 <label class="form-label">Registration No<span class="itsrequired"> *</span></label>
                                 <input type="text" id="ngo_reg_no" name="ngo_reg_no" value="{{old('ngo_reg_no')}}" class="form-control" placeholder="NGO Registration No">
                                 <div id="ngo_reg_no_error"></div>
                                 @error('ngo_reg_no')
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
                                 <input type="date" class="form-control" id="ngo_date_of_registration" name="ngo_date_of_registration" value="{{old('ngo_date_of_registration')}}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                 <div id="ngo_date_of_registration_error"></div>
                                 @error('ngo_date_of_registration')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_date_of_registration_validity_div">
                                 <label class="form-label">Date of Registration Validity<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="ngo_date_of_registration_validity" name="ngo_date_of_registration_validity" value="{{old('ngo_date_of_registration_validity')}}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                 <div id="ngo_date_of_registration_validity_error"></div>
                                 @error('ngo_date_of_registration_validity')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="nature_of_organisation_div">
                                 <label class="form-label">Nature Of Organization<span class="itsrequired"> *</span></label>
                                 <select class="select2 form-control form-select" multiple data-placeholder="Choose a Category" tabindex="1" name="nature_of_organisation[]">
                                    <option value="">--Select--</option>
                                    <option value="1">Persons with Disabilities</option>
                                    <option value="2">Senior Citizens</option>
                                    <option value="3">Transgender Person</option>
                                    <option value="4">Beggars & Destitute</option>
                                    <option value="5">Victims of Substance Abusers</option>
                                    <option value="6">Others (Specify)</option>
                                 </select>
                                 <div id="nature_of_organisation_error"></div>
                                 @error('nature_of_organisation')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                                 <div id="nature_of_organisation_other_div" style="display: none;">
                                    <label class="form-label">Specify Nature Of Organization</label>
                                    <input type="text" class="form-control" name="nature_of_organisation_other" placeholder="Specify Nature Of Organization">
                                 </div>
                                 <div id="nature_of_organisation_error"></div>
                                 @error('nature_of_organisation')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
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
                              <div class="form-group" id="ngo_file_byelaws_div">
                                 <label class="form-label">Upload Byelaws<span class="itsrequired"> *</span></label>
                                 <input type="file" class="form-control" id="ngo_file_byelaws" name="ngo_file_byelaws" value="{{old('ngo_file_byelaws')}}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                 <div id="ngo_file_byelaws_error"></div>
                                 @error('ngo_file_byelaws')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="ngo_parent_organisation_div">
                                 <label class="form-label">Parent Organisation (If Any)</label>
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
      $("#ngo_org_pan").blur(function () {
         const ngoPan = $(this).val();
         if (!ngoPan.trim()) {
            $('#check_ngo_pan_no').html('<span style="color:#FF0000">Please provide a valid NGO PAN.</span>');
            return;
         }
         $.get("{{ route('admin.ngo.check_pan_no') }}", { ngo_org_pan: ngoPan }, function (data) {
            if (data == 0) {
               $('#check_ngo_pan_no').html('<span style="color:#03713E">NGO PAN is available.</span>');
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
         $.get("{{ route('admin.ngo.check_ngo_org_email') }}", { ngo_org_email: ngoEmail }, function (data) {
            if (data == 0) {
               $('#check_ngo_org_email').html('<span style="color:#03713E">NGO Email ID is available.</span>');
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
<script type="text/javascript">
   var ngo_registration_type = document.forms['vform']['ngo_registration_type'];
   var ngo_category = document.forms['vform']['ngo_category'];
   var ngo_org_name = document.forms['vform']['ngo_org_name'];
   var ngo_org_pan = document.forms['vform']['ngo_org_pan'];
   var ngo_org_pan_file = document.forms['vform']['ngo_org_pan_file'];
   var ngo_org_email = document.forms['vform']['ngo_org_email'];
   var ngo_org_phone = document.forms['vform']['ngo_org_phone'];
   var ngo_org_website = document.forms['vform']['ngo_org_website'];
   var ngo_registered_with = document.forms['vform']['ngo_registered_with'];
   var ngo_type_of_vo_or_ngo = document.forms['vform']['ngo_type_of_vo_or_ngo'];
   var ngo_reg_no = document.forms['vform']['ngo_reg_no'];
   var ngo_file_rc = document.forms['vform']['ngo_file_rc'];
   var ngo_date_of_registration = document.forms['vform']['ngo_date_of_registration'];
   var ngo_date_of_registration_validity = document.forms['vform']['ngo_date_of_registration_validity'];
   var nature_of_organisation = document.forms['vform']['nature_of_organisation[]'];
   var ngo_organisation_type = document.forms['vform']['ngo_organisation_type'];
   var ngo_file_byelaws = document.forms['vform']['ngo_file_byelaws'];
   var ngo_parent_organisation = document.forms['vform']['ngo_parent_organisation'];
   var ngo_reg_velidity_available = document.forms['vform']['ngo_reg_velidity_available'];   
   var ngo_address_type = document.forms['vform']['ngo_address_type'];
   
   var ngo_registration_type_error = document.getElementById('ngo_registration_type_error');
   var ngo_category_error = document.getElementById('ngo_category_error');
   var ngo_org_name_error = document.getElementById('ngo_org_name_error');
   var ngo_org_pan_error = document.getElementById('ngo_org_pan_error');
   var ngo_org_pan_file_error = document.getElementById('ngo_org_pan_file_error');
   var ngo_org_email_error = document.getElementById('ngo_org_email_error');
   var ngo_org_phone_error = document.getElementById('ngo_org_phone_error');
   var ngo_org_website_error = document.getElementById('ngo_org_website_error');
   var ngo_registered_with_error = document.getElementById('ngo_registered_with_error');
   var ngo_type_of_vo_or_ngo_error = document.getElementById('ngo_type_of_vo_or_ngo_error');
   var ngo_reg_no_error = document.getElementById('ngo_reg_no_error');
   var ngo_file_rc_error = document.getElementById('ngo_file_rc_error');
   var ngo_date_of_registration_error = document.getElementById('ngo_date_of_registration_error');
   var ngo_date_of_registration_validity_error = document.getElementById('ngo_date_of_registration_validity_error');
   var nature_of_organisation_error = document.getElementById('nature_of_organisation_error');
   var ngo_organisation_type_error = document.getElementById('ngo_organisation_type_error');
   var ngo_file_byelaws_error = document.getElementById('ngo_file_byelaws_error');
   var ngo_parent_organisation_error = document.getElementById('ngo_parent_organisation_error');
   var ngo_reg_velidity_available_error = document.getElementById('ngo_reg_velidity_available_error');   
   var ngo_address_type_error = document.getElementById('ngo_address_type_error');
   
   ngo_registration_type.addEventListener('change', ngo_registration_type_verify, true);
   ngo_category.addEventListener('change', ngo_category_verify, true);
   ngo_org_name.addEventListener('blur', ngo_org_name_verify, true);
   ngo_org_pan.addEventListener('blur', ngo_org_pan_verify, true);
   ngo_org_pan_file.addEventListener('blur', ngo_org_pan_file_verify, true);
   ngo_org_email.addEventListener('blur', ngo_org_email_verify, true);
   ngo_org_phone.addEventListener('blur', ngo_org_phone_verify, true);
   ngo_org_website.addEventListener('blur', ngo_org_website_verify, true);
   ngo_registered_with.addEventListener('blur', ngo_registered_with_verify, true);
   ngo_type_of_vo_or_ngo.addEventListener('blur', ngo_type_of_vo_or_ngo_verify, true);
   ngo_reg_no.addEventListener('blur', ngo_reg_no_verify, true);
   ngo_file_rc.addEventListener('blur', ngo_file_rc_verify, true);
   ngo_date_of_registration.addEventListener('blur', ngo_date_of_registration_verify, true);
   ngo_date_of_registration_validity.addEventListener('blur', ngo_date_of_registration_validity_verify, true);
   nature_of_organisation.addEventListener('blur', nature_of_organisation_verify, true);
   ngo_organisation_type.addEventListener('blur', ngo_organisation_type_verify, true);
   ngo_file_byelaws.addEventListener('blur', ngo_file_byelaws_verify, true);
   ngo_parent_organisation.addEventListener('blur', ngo_parent_organisation_verify, true);
   ngo_reg_velidity_available.addEventListener('blur', ngo_reg_velidity_available_verify, true);   
   ngo_address_type.addEventListener('blur', ngo_address_type_verify, true);  
   
   function Validate(callback) {
      let selected = false;
      if (ngo_registration_type.value == "") {
         ngo_registration_type.style.border = "1px solid red";
         document.getElementById('ngo_registration_type_div').style.color = "red";
         ngo_registration_type_error.textContent = "NGO Registration Type is required";
         ngo_registration_type.focus();
         return false;
      }
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
      if (ngo_reg_no.value == "") {
         ngo_reg_no.style.border = "1px solid red";
         document.getElementById('ngo_reg_no_div').style.color = "red";
         ngo_reg_no_error.textContent = "NGO Registration no is required";
         ngo_reg_no.focus();
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
      if (ngo_date_of_registration_validity.value == "") {
         ngo_date_of_registration_validity.style.border = "1px solid red";
         document.getElementById('ngo_date_of_registration_validity_div').style.color = "red";
         ngo_date_of_registration_validity_error.textContent = "NGO Registration Date Validity is required";
         ngo_date_of_registration_validity.focus();
         return false;
      }
      if (nature_of_organisation.value == "") {
         nature_of_organisation.style.border = "1px solid red";
         document.getElementById('nature_of_organisation_div').style.color = "red";
         nature_of_organisation_error.textContent = "NGO Nature of Organisation is required";
         nature_of_organisation.focus();
         return false;
      }
      if (ngo_organisation_type.value == "") {
         ngo_organisation_type.style.border = "1px solid red";
         document.getElementById('ngo_organisation_type_div').style.color = "red";
         ngo_organisation_type_error.textContent = "Ngo Organisation type is required";
         ngo_organisation_type.focus();
         return false;
      }
      if (ngo_file_byelaws.value == "") {
         ngo_file_byelaws.style.border = "1px solid red";
         document.getElementById('ngo_file_byelaws_div').style.color = "red";
         ngo_file_byelaws_error.textContent = "Upload NGO Byelaws soft copy: only PDF Allowed!!";
         ngo_file_byelaws.focus();
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
   
   function ngo_registration_type_verify() {
      if (ngo_registration_type.value != "") {
         ngo_registration_type.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_registration_type_div').style.color = "#5e6e66";
         ngo_registration_type_error.innerHTML = "";
         return true;
      } else {
         ngo_registration_type.style.border = "1px solid red";
         document.getElementById('ngo_registration_type_div').style.color = "red";
         ngo_registration_type_error.textContent = "NGO Registration Type is required";
         return false;
      }
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
   function ngo_reg_no_verify() {
      if (ngo_reg_no.value != "") {
         ngo_reg_no.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_reg_no_div').style.color = "#5e6e66";
         ngo_reg_no_error.innerHTML = "";
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
   function ngo_date_of_registration_validity_verify() {
      if (ngo_date_of_registration_validity.value != "") {
         ngo_date_of_registration_validity.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_date_of_registration_validity_div').style.color = "#5e6e66";
         ngo_date_of_registration_validity_error.innerHTML = "";
         return true;
      }
   }
   function nature_of_organisation_verify() {
      if (nature_of_organisation.value != "") {
         nature_of_organisation.style.border = "1px solid #5e6e66";
         document.getElementById('nature_of_organisation_div').style.color = "#5e6e66";
         nature_of_organisation_error.innerHTML = "";
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
   function ngo_file_byelaws_verify() {
      if (ngo_file_byelaws.value != "") {
         ngo_file_byelaws.style.border = "1px solid #5e6e66";
         document.getElementById('ngo_file_byelaws_div').style.color = "#5e6e66";
         ngo_file_byelaws_error.innerHTML = "";
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
   
      function validateVillageFields() {
         const state = document.getElementById('state-dropdown');
         const district = document.getElementById('district-dropdown');
         const block = document.getElementById('block-dropdown');
         const grampanchayat = document.getElementById('grampanchayat-dropdown');
         const village = document.getElementById('village-dropdown');
         const pin = document.getElementById('pin');
         const ngo_postal_address_at = document.getElementById('ngo_postal_address_at');
         const ngo_postal_address_post = document.getElementById('ngo_postal_address_post');
         const ngo_postal_address_ps = document.getElementById('ngo_postal_address_ps');
         const ngo_postal_address_pin = document.getElementById('ngo_postal_address_pin');
         const ngoRegisteredWith = document.getElementById('ngo_registered_with');
         const ngoOtherRegActInput = document.querySelector('input[name="ngo_other_reg_act"]');
         const natureOfOrganisation = document.querySelector('[name="nature_of_organisation[]"]');
         const selectedValues = Array.from(natureOfOrganisation.selectedOptions).map(option => option.value);
         const natureOfOrganisationOtherInput = document.querySelector('input[name="nature_of_organisation_other"]');
   
         const alertDiv = document.createElement('div');
         alertDiv.classList.add('alert', 'alert-warning', 'alert-rounded', 'alert-dismissible');
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
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
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please select State.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            state.focus();
            return false;
         }
   
         if (!district || district.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please select District.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            district.focus();
            return false;
         }
   
         if (!block || block.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please select Block.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            block.focus();
            return false;
         }
   
         if (!grampanchayat || grampanchayat.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please select Grampanchayat.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            grampanchayat.focus();
            return false;
         }
   
         if (!village || village.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please select Village.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            village.focus();
            return false;
         }
   
         if (!pin || pin.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please provide PIN.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            pin.focus();
            return false;
         }
   
         if (!ngo_postal_address_at || ngo_postal_address_at.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please provide At.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            ngo_postal_address_at.focus();
            return false;
         }
   
         if (!ngo_postal_address_post || ngo_postal_address_post.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please provide Post.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            ngo_postal_address_post.focus();
            return false;
         }
   
         if (!ngo_postal_address_ps || ngo_postal_address_ps.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please provide Police Station.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            ngo_postal_address_ps.focus();
            return false;
         }
   
         if (!ngo_postal_address_pin || ngo_postal_address_pin.value === '') {
            alertDiv.innerHTML = `
            <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
            Please provide Postal Code.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            ngo_postal_address_pin.focus();
            return false;
         }
   
         if (ngoRegisteredWith.value === '10' && ngoOtherRegActInput.value.trim() === '') {
           alertDiv.innerHTML = `
           <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
           Please specify the NGO Other Registration Act.
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
           `;
           ngoOtherRegActInput.focus();
           return false;
        }
   
        if (selectedValues.includes('6') && natureOfOrganisationOtherInput.value.trim() === '') {
           alertDiv.innerHTML = `
           <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
           Please specify the Nature of Organization.
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
           `;
           natureOfOrganisationOtherInput.focus();
           return false;
        }
   
        return Validate();
     }
   
     function validateMunicipalityFields() {
      const state = document.getElementById('state-dropdown');
      const district = document.getElementById('district-dropdown');
      const municipality = document.getElementById('municipality-dropdown');
      const pin = document.getElementById('pin');
      const ngo_postal_address_at = document.getElementById('ngo_postal_address_at');
      const ngo_postal_address_post = document.getElementById('ngo_postal_address_post');
      const ngo_postal_address_ps = document.getElementById('ngo_postal_address_ps');
      const ngo_postal_address_pin = document.getElementById('ngo_postal_address_pin');
      const ngoRegisteredWith = document.getElementById('ngo_registered_with');
      const ngoOtherRegActInput = document.querySelector('input[name="ngo_other_reg_act"]');
      const natureOfOrganisation = document.querySelector('[name="nature_of_organisation[]"]');
      const selectedValues = Array.from(natureOfOrganisation.selectedOptions).map(option => option.value);
      const natureOfOrganisationOtherInput = document.querySelector('input[name="nature_of_organisation_other"]');
   
      const alertDiv = document.createElement('div');
      alertDiv.classList.add('alert', 'alert-warning', 'alert-rounded', 'alert-dismissible');
      alertDiv.innerHTML = `
      <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
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
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please select State.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         state.focus();
         return false;
      }
   
      if (!district || district.value === '') {
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please select District.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         district.focus();
         return false;
      }
   
      if (!municipality || municipality.value === '') {
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please select Municipality.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         municipality.focus();
         return false;
      }
   
      if (!pin || pin.value === '') {
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please provide PIN.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         pin.focus();
         return false;
      }
   
      if (!ngo_postal_address_at || ngo_postal_address_at.value === '') {
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please provide At.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         ngo_postal_address_at.focus();
         return false;
      }
   
      if (!ngo_postal_address_post || ngo_postal_address_post.value === '') {
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please provide Post.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         ngo_postal_address_post.focus();
         return false;
      }
   
      if (!ngo_postal_address_ps || ngo_postal_address_ps.value === '') {
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please provide Police Station.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         ngo_postal_address_ps.focus();
         return false;
      }
   
      if (!ngo_postal_address_pin || ngo_postal_address_pin.value === '') {
         alertDiv.innerHTML = `
         <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
         Please provide Postal Code.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         `;
         ngo_postal_address_pin.focus();
         return false;
      }
   
      if (ngoRegisteredWith.value === '10' && ngoOtherRegActInput.value.trim() === '') {
        alertDiv.innerHTML = `
        <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
        Please specify the NGO Other Registration Act.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        ngoOtherRegActInput.focus();
        return false;
     }
   
     if (selectedValues.includes('6') && natureOfOrganisationOtherInput.value.trim() === '') {
        alertDiv.innerHTML = `
        <img src="{{ url('storage/sad-icon.png') }}" width="30" class="img-circle" alt="img">
        Please specify the Nature of Organization.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        natureOfOrganisationOtherInput.focus();
        return false;
     }
   
     return Validate();
   }
   
   $(document).on('change', '#ngo_registered_with', function () {
     const otherRegActDiv = document.getElementById('ngo_other_reg_act_div');
     if (this.value === '10') {
       otherRegActDiv.style.display = 'block';
    } else {
       otherRegActDiv.style.display = 'none';
    }
   });
   
   $(document).on('change', '[name="nature_of_organisation[]"]', function () {
     const otherRegActDivv = document.getElementById('nature_of_organisation_other_div');
     const selectedValues = Array.from(this.selectedOptions).map(option => option.value);
     if (selectedValues.includes('6')) {
       otherRegActDivv.style.display = 'block';
    } else {
       otherRegActDivv.style.display = 'none';
    }
   });
   
   console.log("DOM is fully loaded");
   });
   
   document.getElementById('ngo_org_pan_file').addEventListener('change', function(event) {
   const file = event.target.files[0];
   const errorDiv = document.getElementById('ngo_org_pan_file_error');
   const maxFileSize = 3 * 1024 * 1024;
   
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
      errorDiv.innerHTML = '<label class="error">File size must be less than 3MB.</label>';
      event.target.value = '';
      return;
   }
   });
   
   document.getElementById('ngo_file_rc').addEventListener('change', function(event) {
   const file = event.target.files[0];
   const errorDiv = document.getElementById('ngo_file_rc_error');
   const maxFileSize = 3 * 1024 * 1024;
   
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
      errorDiv.innerHTML = '<label class="error">File size must be less than 3MB.</label>';
      event.target.value = '';
      return;
   }
   });
   
   document.getElementById('ngo_file_byelaws').addEventListener('change', function(event) {
   const file = event.target.files[0];
   const errorDiv = document.getElementById('ngo_file_byelaws_error');
   const maxFileSize = 3 * 1024 * 1024;
   
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
      errorDiv.innerHTML = '<label class="error">File size must be less than 3MB.</label>';
      event.target.value = '';
      return;
   }
   });
</script>
@endsection