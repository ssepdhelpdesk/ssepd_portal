@section('title') 
NGO || Update
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
         @can('user-create')
         <a href="{{ route('admin.users.create') }}"><button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success"><i class="fas fa-plus-square"></i> Add New</button></a>
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
               <ul class="nav nav-tabs customtab2" role="tablist">
                  <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#tab1" role="tab"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">Basic Details</span></a> </li>
                  <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#part2" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Office Bearer Profile</span></a> </li>
                  <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#part3" role="tab"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down">NGO Address</span></a> </li>
               </ul>
               <!-- Tab panes -->
               <div class="tab-content">
                  <div class="tab-pane active" id="tab1" role="tabpanel">
                     <div class="p-20">
                        <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.update_ngo_application_part_one', $NgoRegistration->id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                           @csrf
                           @method('post')
                           <div class="form-body">
                              <h5 class="card-title">Basic Details</h5>
                              <hr>
                              <div class="row">
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_registration_type_div">
                                       <label class="form-label">NGO Registration Type<span class="itsrequired"> *</span></label>
                                       <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_registration_type">
                                          <option value="">--Select--</option>
                                          <option value="1" {{ old('ngo_registration_type', $NgoRegistration->ngo_registration_type ?? '') == '1' ? 'selected' : '' }}>New</option>
                                          <option value="2" {{ old('ngo_registration_type', $NgoRegistration->ngo_registration_type ?? '') == '2' ? 'selected' : '' }}>Renewal</option>
                                       </select>
                                       <div id="ngo_registration_type_error"></div>
                                       @error('ngo_registration_type')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_category_div">
                                       <label class="form-label">NGO Category<span class="itsrequired"> *</span></label>
                                       <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_category">
                                          <option value="">--Select--</option>
                                          @foreach($categories as $category)
                                          <option value="{{ $category->id }}"
                                          {{ old('ngo_category', $NgoRegistration->ngo_category ?? '') == $category->id ? 'selected' : '' }}>
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
                                       <input type="text" id="ngo_org_name" name="ngo_org_name" value="{{old('ngo_org_name', $NgoRegistration->ngo_org_name)}}" class="form-control" placeholder="NGO Name">
                                       <div id="ngo_org_name_error"></div>
                                       @error('ngo_org_name')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_org_pan_div">
                                       <label class="form-label">NGO Pan No<span class="itsrequired"> *</span></label>
                                       <input type="text" id="ngo_org_pan" name="ngo_org_pan" value="{{old('ngo_org_pan', $NgoRegistration->ngo_org_pan)}}" class="form-control" placeholder="NGO Pan No">
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
                                       <label><a href="{{ url('storage/' . $NgoRegistration->ngo_org_pan_file) }}" target="_blank" style="cursor: pointer;" class="text-black">View PAN</a></label>
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_org_email_div">
                                       <label class="form-label">NGO Email<span class="itsrequired"> *</span></label>
                                       <input type="text" id="ngo_org_email" name="ngo_org_email" value="{{old('ngo_org_email', $NgoRegistration->ngo_org_email)}}" class="form-control" placeholder="NGO Email">
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
                                       <input type="text" id="ngo_org_phone" name="ngo_org_phone" value="{{old('ngo_org_phone', $NgoRegistration->ngo_org_phone)}}" class="form-control" placeholder="NGO Phone No">
                                       <div id="ngo_org_phone_error"></div>
                                       @error('ngo_org_phone')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_org_website_div">
                                       <label class="form-label">NGO Website<span class="itsrequired"> *</span></label>
                                       <input type="text" id="ngo_org_website" name="ngo_org_website" value="{{old('ngo_org_website', $NgoRegistration->ngo_org_website)}}" class="form-control" placeholder="NGO Website">
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
                                          <option value="1" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '1' ? 'selected' : '' }}>Societies Registration Act, 1860</option>
                                          <option value="2" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '2' ? 'selected' : '' }}>Indian Trusts Act, 1882</option>
                                          <option value="3" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '3' ? 'selected' : '' }}>Section 8 of the Companies Act, 2013</option>
                                          <option value="4" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '4' ? 'selected' : '' }}>Registrar of Companies</option>
                                          <option value="5" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '5' ? 'selected' : '' }}>Registrar of Societies</option>
                                          <option value="6" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '6' ? 'selected' : '' }}>Registrar of Cooperative Societies</option>
                                          <option value="7" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '7' ? 'selected' : '' }}>Charity Commissioner</option>
                                          <option value="8" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '8' ? 'selected' : '' }}>International Organisation</option>
                                          <option value="9" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '9' ? 'selected' : '' }}>Sub Registrar</option>
                                          <option value="10" {{ old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '10' ? 'selected' : '' }}>Any Other (Specify)</option>
                                       </select>
                                       <div id="ngo_registered_with_error"></div>
                                       @error('ngo_registered_with')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                       @php
                                       $otherActVisible = old('ngo_registered_with', $NgoRegistration->ngo_registered_with ?? '') == '10';
                                       $otherActValue = old('ngo_other_reg_act', $NgoRegistration->ngo_other_reg_act ?? '');
                                       @endphp
                                       <div id="ngo_other_reg_act_div" style="{{ $otherActVisible ? '' : 'display: none;' }}">
                                          <label class="form-label">Specify NGO Other Registration Act</label>
                                          <input type="text" class="form-control" name="ngo_other_reg_act" value="{{ $otherActValue }}" placeholder="Specify NGO Other Registration Act">
                                       </div>
                                       <div id="ngo_other_reg_act_error"></div>
                                       @error('ngo_other_reg_act')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    @php
                                    $selectedVoType = old('ngo_type_of_vo_or_ngo', $NgoRegistration->ngo_type_of_vo_or_ngo ?? '');
                                    @endphp
                                    <div class="form-group" id="ngo_type_of_vo_or_ngo_div">
                                       <label class="form-label">Type of VO/NGO<span class="itsrequired"> *</span></label>
                                       <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_type_of_vo_or_ngo">
                                          <option value="">--Select--</option>
                                          <option value="1" {{ $selectedVoType == '1' ? 'selected' : '' }}>Private Sector Companies (Sec 8/25)</option>
                                          <option value="2" {{ $selectedVoType == '2' ? 'selected' : '' }}>Registered Societies (Non Government)</option>
                                          <option value="3" {{ $selectedVoType == '3' ? 'selected' : '' }}>Trust  (Non Government)</option>
                                          <option value="4" {{ $selectedVoType == '4' ? 'selected' : '' }}>Other Registered Entities  (Non Government)</option>
                                          <option value="5" {{ $selectedVoType == '5' ? 'selected' : '' }}>Academic Institutions (Govt)</option>
                                          <option value="6" {{ $selectedVoType == '6' ? 'selected' : '' }}>Academic Institutions (Private)</option>
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
                                       <input type="text" id="ngo_reg_no" name="ngo_reg_no" value="{{old('ngo_reg_no', $NgoRegistration->ngo_reg_no)}}" class="form-control" placeholder="NGO Registration No">
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
                                       <label><a href="{{ url('storage/' . $NgoRegistration->ngo_file_rc) }}" target="_blank" style="cursor: pointer;" class="text-black">View RC</a></label>
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_date_of_registration_div">
                                       <label class="form-label">Date of Registration<span class="itsrequired"> *</span></label>
                                       <input type="date" class="form-control" id="ngo_date_of_registration" name="ngo_date_of_registration" value="{{old('ngo_date_of_registration', $NgoRegistration->ngo_date_of_registration)}}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                       <div id="ngo_date_of_registration_error"></div>
                                       @error('ngo_date_of_registration')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_date_of_registration_validity_div">
                                       <label class="form-label">Date of Registration Validity<span class="itsrequired"> *</span></label>
                                       <input type="date" class="form-control" id="ngo_date_of_registration_validity" name="ngo_date_of_registration_validity" value="{{old('ngo_date_of_registration_validity', $NgoRegistration->ngo_date_of_registration_validity)}}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                       <div id="ngo_date_of_registration_validity_error"></div>
                                       @error('ngo_date_of_registration_validity')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    @php
                                    $selectedNature = old('nature_of_organisation', $NgoRegistration->nature_of_organisation ?? '');
                                    $selectedNature = is_array($selectedNature) ? $selectedNature : explode(',', $selectedNature);
                                    @endphp
                                    <div class="form-group" id="nature_of_organisation_div">
                                       <label class="form-label">Nature Of Organization<span class="itsrequired"> *</span></label>
                                       <select class="select2 form-control form-select" multiple data-placeholder="Choose a Category" tabindex="1" name="nature_of_organisation[]">
                                          <option value="">--Select--</option>
                                          <option value="1" {{ in_array('1', $selectedNature) ? 'selected' : '' }}>Persons with Disabilities</option>
                                          <option value="2" {{ in_array('2', $selectedNature) ? 'selected' : '' }}>Senior Citizens</option>
                                          <option value="3" {{ in_array('3', $selectedNature) ? 'selected' : '' }}>Transgender Person</option>
                                          <option value="4" {{ in_array('4', $selectedNature) ? 'selected' : '' }}>Beggars & Destitute</option>
                                          <option value="5" {{ in_array('5', $selectedNature) ? 'selected' : '' }}>Victims of Substance Abusers</option>
                                          <option value="6" {{ in_array('6', $selectedNature) ? 'selected' : '' }}>Others (Specify)</option>
                                       </select>
                                       <div id="nature_of_organisation_error"></div>
                                       @error('nature_of_organisation')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                       <div id="nature_of_organisation_other_div" style="{{ in_array('6', $selectedNature) ? '' : 'display: none;' }}">
                                          <label class="form-label">Specify Nature Of Organization</label>
                                          <input type="text" class="form-control" name="nature_of_organisation_other" value="{{old('nature_of_organisation_other', $NgoRegistration->nature_of_organisation_other)}}" placeholder="Specify Nature Of Organization">
                                       </div>
                                       <div id="nature_of_organisation_error"></div>
                                       @error('nature_of_organisation')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    @php
                                    $selectedOrgType = old('ngo_organisation_type', $NgoRegistration->ngo_organisation_type ?? '');
                                    @endphp
                                    <div class="form-group" id="ngo_organisation_type_div">
                                       <label class="form-label">Organisation Type<span class="itsrequired"> *</span></label>
                                       <select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="ngo_organisation_type">
                                          <option value="">--Select--</option>
                                          <option value="1" {{ $selectedOrgType == '1' ? 'selected' : '' }}>Independent Organisation</option>
                                          <option value="2" {{ $selectedOrgType == '2' ? 'selected' : '' }}>Branch of Other Organisation</option>
                                       </select>
                                       <div id="ngo_organisation_type_error"></div>
                                       @error('ngo_organisation_type')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_file_byelaws_div">
                                       <label class="form-label">Upload Bylaws<span class="itsrequired"> *</span></label>
                                       <input type="file" class="form-control" id="ngo_file_byelaws" name="ngo_file_byelaws" value="{{old('ngo_file_byelaws')}}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                       <div id="ngo_file_byelaws_error"></div>
                                       @error('ngo_file_byelaws')
                                       <label class="error">{{ $message }}</label>
                                       @enderror
                                       <label><a href="{{ url('storage/' . $NgoRegistration->ngo_file_byelaws) }}" target="_blank" style="cursor: pointer;" class="text-black">View bylaws</a></label>
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group" id="ngo_parent_organisation_div">
                                       <label class="form-label">Parent Organisation (If Any)</label>
                                       <input type="text" id="ngo_parent_organisation" name="ngo_parent_organisation" value="{{old('ngo_parent_organisation', $NgoRegistration->ngo_parent_organisation)}}" class="form-control" placeholder="NGO Parent Organisation">
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
                           @if((int) $NgoRegistration->application_stage_id === 1)
                           <div class="form-actions">
                              <button type="submit" value="submit" name="register" class="btn btn-primary text-white"> Submit </button>
                              <button type="submit" value="draft" name="register" class="btn btn-info text-white"> Draft my Application </button>
                              <button type="button" class="btn btn-warning">Cancel</button>
                           </div>
                           @endif
                        </form>
                     </div>
                  </div>
                  <div class="tab-pane  p-20" id="part2" role="tabpanel">
                     <div class="table-responsive">
                        <table id="example23" class="display nowrap table table-hover table-striped border responsive" cellspacing="0" width="100%">
                           <thead>
                              <tr>
                                 <th>S.No.</th>
                                 <th>Office Bearer Name</th>
                                 <th>Gender</th>
                                 <th>Email</th>
                                 <th>Phone No</th>
                                 <th>Designation</th>
                                 <th>Key Designation</th>
                                 <th>Date of Association</th>
                                 <th>Pan No</th>
                                 <th>View PAN</th>
                                 <th>Name as per Aadhar</th>
                                 <th>Date of Birth</th>
                                 <th>Aadhar No</th>
                                 <th>View Aadhar</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tfoot>
                              <tr>
                                 <th>S.No.</th>
                                 <th>Office Bearer Name</th>
                                 <th>Gender</th>
                                 <th>Email</th>
                                 <th>Phone No</th>
                                 <th>Designation</th>
                                 <th>Key Designation</th>
                                 <th>Date of Association</th>
                                 <th>Pan No</th>
                                 <th>View PAN</th>
                                 <th>Name as per Aadhar</th>
                                 <th>Date of Birth</th>
                                 <th>Aadhar No</th>
                                 <th>View Aadhar</th>
                                 <th>Action</th>
                              </tr>
                           </tfoot>
                           <tbody>
                              @if ($NgoPartTwoOfficeBearer->isNotEmpty())
                              @foreach($NgoPartTwoOfficeBearer as $key => $ngoPartTwoOfficeBearer)
                              <tr>
                                 <td>{{ $loop->iteration }}</td>
                                 <td>{{ $ngoPartTwoOfficeBearer->office_bearer_name }}</td>
                                 <td>
                                    @php
                                    $genders = [1 => 'Male', 2 => 'Female', 3 => 'Other'];
                                    @endphp
                                    {{ $genders[$ngoPartTwoOfficeBearer->office_bearer_gender] ?? 'N/A' }}
                                 </td>
                                 <td>{{ $ngoPartTwoOfficeBearer->office_bearer_email }}</td>
                                 <td>{{ $ngoPartTwoOfficeBearer->office_bearer_phone }}</td>
                                 <td>
                                    @php
                                    $designations = [
                                    1 => 'Chairman',
                                    2 => 'President',
                                    3 => 'Vice Chairman',
                                    4 => 'Secretary',
                                    5 => 'Director',
                                    6 => 'Addl. Director',
                                    7 => 'Joint Secretary',
                                    8 => 'Treasurer',
                                    9 => 'Executive Member',
                                    10 => 'Board Member',
                                    11 => 'Academic Adviser',
                                    12 => 'Academic Administrator',
                                    13 => 'Accountant',
                                    14 => 'Coordinator',
                                    15 => 'Other',
                                    ];
                                    @endphp
                                    {{ $designations[$ngoPartTwoOfficeBearer->office_bearer_designation] ?? 'N/A' }}
                                 </td>
                                 <td>
                                    @php
                                    $keyDesignations = [
                                    1 => 'Chief Functionary',
                                    2 => 'Promoter',
                                    3 => 'Patron',
                                    4 => 'Other',
                                    ];
                                    @endphp
                                    {{ $keyDesignations[$ngoPartTwoOfficeBearer->office_bearer_key_designation] ?? 'N/A' }}
                                 </td>
                                 <td>{{ \Carbon\Carbon::parse($ngoPartTwoOfficeBearer->office_bearer_date_of_association)->format('d M, Y') }}</td>
                                 <td>{{ $ngoPartTwoOfficeBearer->office_bearer_pan }}</td>
                                 <td><label class="badge bg-warning"><a href="{{ url('storage/' . $ngoPartTwoOfficeBearer->office_bearer_pan_file) }}" target="_blank" style="cursor: pointer;" class=" text-white">View</a></label></td>
                                 <td>{{ $ngoPartTwoOfficeBearer->office_bearer_name_as_aadhar }}</td>
                                 <td>{{ \Carbon\Carbon::parse($ngoPartTwoOfficeBearer->office_bearer_dob)->format('d M, Y') }}</td>
                                 <td>{{ str_repeat('*', 8) . substr($ngoPartTwoOfficeBearer->office_bearer_aadhar, -4) }}</td>
                                 <td><label class="badge bg-warning"><a href="{{ url('storage/' . $ngoPartTwoOfficeBearer->office_bearer_aadhar_file) }}" target="_blank" style="cursor: pointer;" class=" text-white">View</a></label></td>
                                 <td>
                                    @if((int) $NgoRegistration->application_stage_id === 1)
                                    <label class="badge bg-info">
                                    <a href="javascript:void(0);" class="text-white btn-edit" data-id="{{ $ngoPartTwoOfficeBearer->id }}" style="cursor: pointer;">Edit</a>
                                    </label>
                                    @else
                                    <span class="badge bg-success">Already Submitted</span>
                                    @endif
                                 </td>
                              </tr>
                              @endforeach
                              @endif
                           </tbody>
                        </table>
                        @if((int) $NgoRegistration->application_stage_id === 1)
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editOfficeBearerModal" tabindex="-1" aria-labelledby="editOfficeBearerModalLabel" aria-hidden="true">
                           <div class="modal-dialog modal-lg">
                              <form id="editOfficeBearerForm" class="from-prevent-multiple-submits" name="vform" enctype="multipart/form-data">
                                 @csrf
                                 <div class="modal-content">
                                    <div class="modal-header">
                                       <h5 class="modal-title">Edit Office Bearer</h5>
                                       <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                       <input type="hidden" name="id" id="edit_id">
                                       <div class="row">
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_name_div">
                                                <label class="form-label">Office Bearer Name<span class="itsrequired"> *</span></label>
                                                <input type="text" id="office_bearer_name" name="office_bearer_name" value="{{ old('office_bearer_name')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Name" data-required="true">
                                                <div class="office_bearer_name_error"></div>
                                                @error('office_bearer_name')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_gender_div">
                                                <label class="form-label">Gender<span class="itsrequired"> *</span></label>
                                                <select class="form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_gender" id="office_bearer_gender" data-required="true">
                                                   <option value="">--Select--</option>
                                                   <option value="1">Male</option>
                                                   <option value="2">Female</option>
                                                   <option value="3">Other</option>
                                                </select>
                                                <div class="office_bearer_gender_error"></div>
                                                @error('office_bearer_gender')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_email_div">
                                                <label class="form-label">Email<span class="itsrequired"> *</span></label>
                                                <input type="email" id="office_bearer_email" name="office_bearer_email" value="{{old('office_bearer_email')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Email" data-required="true">
                                                <div class="office_bearer_email_error"></div>
                                                @error('office_bearer_email')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_phone_div">
                                                <label class="form-label">Phone No<span class="itsrequired"> *</span></label>
                                                <input type="text" id="office_bearer_phone" name="office_bearer_phone" value="{{old('office_bearer_phone')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Phone No" data-required="true">
                                                <div class="office_bearer_phone_error"></div>
                                                @error('office_bearer_phone')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_designation_div">
                                                <label class="form-label">Designation<span class="itsrequired"> *</span></label>
                                                <select class="form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_designation" id="office_bearer_designation">
                                                   <option value="">--Select--</option>
                                                   <option value="1">Chairman</option>
                                                   <option value="2">President</option>
                                                   <option value="3">Vice Chairman</option>
                                                   <option value="4">Secretary</option>
                                                   <option value="5">Director</option>
                                                   <option value="6">Addl. Director</option>
                                                   <option value="7">Joint Secretary</option>
                                                   <option value="8">Treasurer</option>
                                                   <option value="9">Executive Member</option>
                                                   <option value="10">Board Member</option>
                                                   <option value="11">Academic Adviser</option>
                                                   <option value="12">Academic Administrator</option>
                                                   <option value="13">Accountant</option>
                                                   <option value="14">Coordinator</option>
                                                   <option value="15">Other</option>
                                                </select>
                                                <div class="office_bearer_designation_error"></div>
                                                @error('office_bearer_designation')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_key_designation_div">
                                                <label class="form-label">Key Designation<span class="itsrequired"> *</span></label>
                                                <select class="form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_key_designation" id="office_bearer_key_designation">
                                                   <option value="">--Select--</option>
                                                   <option value="1">Chief Functionary</option>
                                                   <option value="2">Promoter</option>
                                                   <option value="3">Patron</option>
                                                   <option value="4">Other</option>
                                                </select>
                                                <div class="office_bearer_key_designation_error"></div>
                                                @error('office_bearer_key_designation')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_date_of_association_div">
                                                <label class="form-label">Date of Association<span class="itsrequired"> *</span></label>
                                                <input type="date" class="form-control" id="office_bearer_date_of_association" name="office_bearer_date_of_association" value="{{old('office_bearer_date_of_association')[0] ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                                <div class="office_bearer_date_of_association_error"></div>
                                                @error('office_bearer_date_of_association')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_pan_div">
                                                <label class="form-label">Pan No<span class="itsrequired"> *</span></label>
                                                <input type="text" id="office_bearer_pan" name="office_bearer_pan" value="{{old('office_bearer_pan')[0] ?? '' }}" class="form-control" placeholder="Pan No">
                                                <div class="office_bearer_pan_error"></div>
                                                <div id="check_office_bearer_pan"></div>
                                                @error('office_bearer_pan')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_pan_file_div">
                                                <label class="form-label">Upload PAN<span class="itsrequired"> *</span></label>
                                                <input type="file" class="form-control" id="office_bearer_pan_file" name="office_bearer_pan_file" value="{{old('office_bearer_pan_file')[0] ?? '' }}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                                <div class="office_bearer_pan_file_error"></div>
                                                @error('office_bearer_pan_file')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                                <div id="existing_office_bearer_pan_file" class="mt-2"></div>
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_name_as_aadhar_div">
                                                <label class="form-label">Name as per Aadhar<span class="itsrequired"> *</span></label>
                                                <input type="text" id="office_bearer_name_as_aadhar" name="office_bearer_name_as_aadhar" value="{{old('office_bearer_name_as_aadhar')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Name">
                                                <div class="office_bearer_name_as_aadhar_error"></div>
                                                @error('office_bearer_name_as_aadhar')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_dob_div">
                                                <label class="form-label">Date of Birth<span class="itsrequired"> *</span></label>
                                                <input type="date" class="form-control" id="office_bearer_dob" name="office_bearer_dob" value="{{old('office_bearer_dob')[0] ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                                <div class="office_bearer_dob_error"></div>
                                                @error('office_bearer_dob')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_aadhar_div">
                                                <label class="form-label">Aadhar No<span class="itsrequired"> *</span></label>
                                                <input type="text" id="office_bearer_aadhar" name="office_bearer_aadhar" value="{{old('office_bearer_aadhar')[0] ?? '' }}" class="form-control" placeholder="Aadhar No">
                                                <div class="office_bearer_aadhar_error"></div>
                                                <div id="check_office_bearer_aadhar"></div>
                                                @error('office_bearer_aadhar')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                             </div>
                                          </div>
                                          <div class="col-md-4">
                                             <div class="form-group" id="office_bearer_aadhar_file_div">
                                                <label class="form-label">Upload Aadhar<span class="itsrequired"> *</span></label>
                                                <input type="file" class="form-control" id="office_bearer_aadhar_file" value="{{ old('office_bearer_aadhar_file')[0] ?? '' }}" name="office_bearer_aadhar_file" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                                <div class="office_bearer_aadhar_file_error"></div>
                                                @error('office_bearer_aadhar_file')
                                                <label class="error">{{ $message }}</label>
                                                @enderror
                                                <div id="existing_office_bearer_aadhar_file" class="mt-2"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="modal-footer">
                                       <button type="submit" class="btn btn-success">Update</button>
                                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                 </div>
                              </form>
                           </div>
                        </div>
                        @endif
                     </div>
                     @if((int) $NgoRegistration->application_stage_id === 1)
                     <a href="{{route('admin.ngo.update_ngo_application_part_two_add_another_office_bearer', $ngoPartTwoOfficeBearer->ngo_tbl_id)}}" class="pt-3" target="_blank"> <button type="button" class="btn btn-warning">Want to add Another Bearer ?</button> </a>
                     @endif
                  </div>
                  <div class="tab-pane p-20" id="part3" role="tabpanel">
                     <div class="table-responsive">
                        <table id="example23" class="display nowrap table table-hover table-striped border responsive" cellspacing="0" width="100%">
                           <thead>
                              <tr>
                                 <th>State</th>
                                 <th>District</th>
                                 <th>Address Type</th>
                                 <th>Block/ULB</th>
                                 <th>Grampanchyat</th>
                                 <th>Village</th>
                                 <th>PIN</th>
                              </tr>
                           </thead>
                           <tfoot>
                              <tr>
                                 <th>State</th>
                                 <th>District</th>
                                 <th>Address Type</th>
                                 <th>Block/ULB</th>
                                 <th>Grampanchyat</th>
                                 <th>Village</th>
                                 <th>PIN</th>
                              </tr>
                           </tfoot>
                           <tbody>
                              <tr>
                                 <td>{{ $NgoRegistration->state->state_name ?? '-' }}</td>
                                 <td>{{ $NgoRegistration->district->district_name ?? '-' }}</td>
                                 <td>{{ $NgoRegistration->ngo_address_type == 1 ? 'Block' : ($NgoRegistration->ngo_address_type == 2 ? 'ULB' : '-') }}</td>
                                 <td>{{ $NgoRegistration->block->block_name ?? $NgoRegistration->municipality->municipality_name ?? '-' }}</td>
                                 <td>{{ $NgoRegistration->grampanchayat->gp_name ?? '-' }}</td>
                                 <td>{{ $NgoRegistration->village->village_name ?? '-' }}</td>
                                 <td>{{ $NgoRegistration->pin ?? '-' }}</td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="card">
                        <div class="card-body">
                           <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.part_one_store')}}" onsubmit="return Validate()" name="vform_form_name" enctype="multipart/form-data">
                              @csrf
                              @method('post')
                              <div class="form-body">
                                 <h5 class="card-title">Want to update the Address?</h5>
                                 <hr>
                                 <div class="row">
                                    <div class="col-md-3">
                                       <div class="form-group" id="ngo_address_type_form_div">
                                          <label class="form-label">Address Type<span class="itsrequired"> *</span></label>
                                          <div class="d-flex align-items-center">
                                             <div class="custom-control custom-radio me-3">
                                                <input type="radio" id="block" name="ngo_address_type_form" value="1" class="form-check-input">
                                                <label class="form-check-label" for="block">Block</label>
                                             </div>
                                             <div class="custom-control custom-radio">
                                                <input type="radio" id="ulb" name="ngo_address_type_form" value="2" class="form-check-input">
                                                <label class="form-check-label" for="ulb">ULB</label>
                                             </div>
                                          </div>
                                          <div id="ngo_address_type_form_error"></div>
                                          @error('ngo_address_type_form')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                    </div>
                                 </div>
                                 <!--/row-->
                                 <div class="row" id="dynamic-content"></div>
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
   <!-- row -->
   <!-- ============================================================== -->
   <!-- End Page Content -->
   <!-- ============================================================== -->
</div>
@endsection 
@section('script')
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script>
   $(function () {
      $('#example23').DataTable({
         processing: true,
         responsive: true,
         ordering: true,
         lengthMenu: [[10, 500, 1000, -1], [10, 500, 1000, "All"]],
         dom: 'Blfrtip',
         buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
         ]
      });
      $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
   });   
</script>
<script>
   $(document).ready(function () {
      handleTabScripts('#tab1');
      $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
         const target = $(e.target).attr("href");
         handleTabScripts(target);
      });
   
      function handleTabScripts(tabId) {
         switch (tabId) {
         case '#tab1':
            console.log('Tab 1 script executing...');
            $(document).ready(function () {
               $('#ngo_registered_with').on('change', function () {
                  $('#ngo_other_reg_act_div').toggle($(this).val() === '10');
               });
   
               $('select[name="nature_of_organisation[]"]').on('change', function () {
                  let selected = $(this).val() || [];
                  $('#nature_of_organisation_other_div').toggle(selected.includes('6'));
               });
   
               $('form').on('submit', function () {
                  let isValid = true;
   
                  $('.text-danger').text('');
                  function showError(id, message) {
                     $('#' + id + '_error').text(message).addClass('text-danger');
                     isValid = false;
                  }
   
                  const textFields = [
                     'ngo_org_name', 'ngo_org_pan', 'ngo_org_email', 'ngo_org_phone',
                     'ngo_org_website', 'ngo_reg_no', 'ngo_parent_organisation'
                  ];
                  textFields.forEach(id => {
                     const value = $('#' + id).val().trim();
                     if (!value) showError(id, 'This field is required.');
                  });
   
                  const selects = [
                     'ngo_registration_type', 'ngo_category', 'ngo_registered_with',
                     'ngo_type_of_vo_or_ngo', 'ngo_organisation_type'
                  ];
                  selects.forEach(id => {
                     if (!$(`select[name="${id}"]`).val()) showError(id, 'Please select an option.');
                  });
   
                  const selectedNature = $('select[name="nature_of_organisation[]"]').val();
                  if (!selectedNature || selectedNature.length === 0) {
                     showError('nature_of_organisation', 'Please select at least one option.');
                  } else if (selectedNature.includes('6')) {
                     const otherText = $('input[name="nature_of_organisation_other"]').val().trim();
                     if (!otherText) {
                        $('#nature_of_organisation_error').append('<div class="text-danger">Please specify the nature of organization.</div>');
                        isValid = false;
                     }
                  }
   
                  const regWithValue = $('#ngo_registered_with').val();
                  if (!regWithValue) {
                     showError('ngo_registered_with', 'Please select an option.');
                  } else if (regWithValue === '10') {
                     const otherRegText = $('input[name="ngo_other_reg_act"]').val().trim();
                     if (!otherRegText) {
                        $('#ngo_registered_with_error').append('<div class="text-danger">Please specify the registration act.</div>');
                        isValid = false;
                     }
                  }
   
                  ['ngo_date_of_registration', 'ngo_date_of_registration_validity'].forEach(id => {
                     if (!$(`#${id}`).val()) showError(id, 'Please select a date.');
                  });                  
   
                  return isValid;
               });
            });
            break;
         case '#part2':
            console.log('Tab 2 script executing...');
            $(document).ready(function () {
               $('.btn-edit').on('click', function () {
                  let id = $(this).data('id');
   
                  $.ajax({
                     url: "{{ route('admin.ngo.update_ngo_application_part_two_get_office_bearer') }}",
                     type: 'GET',
                     data: { id: id },
                     success: function (response) {
                        $('#edit_id').val(response.id);
                        $('#office_bearer_name').val(response.office_bearer_name);
                        $('#office_bearer_gender').val(response.office_bearer_gender);
                        $('#office_bearer_email').val(response.office_bearer_email);
                        $('#office_bearer_phone').val(response.office_bearer_phone);
                        $('#office_bearer_designation').val(response.office_bearer_designation);
                        $('#office_bearer_key_designation').val(response.office_bearer_key_designation);
                        $('#office_bearer_date_of_association').val(response.office_bearer_date_of_association);
                        $('#office_bearer_pan').val(response.office_bearer_pan);
                        $('#office_bearer_name_as_aadhar').val(response.office_bearer_name_as_aadhar);
                        $('#office_bearer_dob').val(response.office_bearer_dob);
                        $('#office_bearer_aadhar').val(response.office_bearer_aadhar);
   
                        if (response.office_bearer_pan_file) {
                           $('#existing_office_bearer_pan_file').html(
                        `<a href="${response.office_bearer_pan_file}" target="_blank" class="badge bg-primary text-white">View Uploaded PAN</a>`
                        );
                        } else {
                           $('#existing_office_bearer_pan_file').html('');
                        }
   
                        if (response.office_bearer_aadhar_file) {
                           $('#existing_office_bearer_aadhar_file').html(
                        `<a href="${response.office_bearer_aadhar_file}" target="_blank" class="badge bg-primary text-white">View Uploaded Aadhar</a>`
                        );
                        } else {
                           $('#existing_office_bearer_aadhar_file').html('');
                        }
                        let editModal = new bootstrap.Modal(document.getElementById('editOfficeBearerModal'));
                        editModal.show();
                     },
                     error: function () {
                        Swal.fire("Error", "Something went wrong while fetching the data.", "error");
                     }
                  });
               });
   
               const fileInputs = document.querySelectorAll('[name="office_bearer_pan_file"]');
               fileInputs.forEach(function(input) {
                  input.addEventListener('change', function(event) {
                     const file = event.target.files[0];
                     const fieldName = event.target.name.replace('', '');
                     const errorDiv = document.querySelector(`.${fieldName}_error`);
                     const maxFileSize = 1 * 1024 * 1024;
   
                     errorDiv.innerHTML = '';
                     if (!file) {
                        return;
                     }
   
                     if (file.type !== 'application/pdf') {
                        errorDiv.innerHTML = '<div class="error">Only PDF files are allowed.</div>';
                        event.target.value = '';
                        return;
                     }
                     if (file.size > maxFileSize) {
                        errorDiv.innerHTML = '<div class="error">File size must be less than 1MB.</div>';
                        event.target.value = '';
                        return;
                     }
                  });
               });
   
               const fileAadharInputs = document.querySelectorAll('[name="office_bearer_aadhar_file"]');
               fileAadharInputs.forEach(function(input) {
                  input.addEventListener('change', function(event) {
                     const file = event.target.files[0];
                     const fieldName = event.target.name.replace('', '');
                     const errorDiv = document.querySelector(`.${fieldName}_error`);
                     const maxFileSize = 1 * 1024 * 1024;
   
                     errorDiv.innerHTML = '';
                     if (!file) {
                        return;
                     }
   
                     if (file.type !== 'application/pdf') {
                        errorDiv.innerHTML = '<div class="error">Only PDF files are allowed.</div>';
                        event.target.value = '';
                        return;
                     }
                     if (file.size > maxFileSize) {
                        errorDiv.innerHTML = '<div class="error">File size must be less than 1MB.</div>';
                        event.target.value = '';
                        return;
                     }
                  });
               });
   
               const phoneInputs = document.querySelectorAll('[name="office_bearer_phone"]');
               phoneInputs.forEach(function(input) {
                  input.addEventListener('keypress', function(event) {
                     const char = event.key;
                     if (!/^[0-9]$/.test(char)) {
                        event.preventDefault();
                     }
                  });
   
                  input.addEventListener('blur', function(event) {
                     const phoneNumber = event.target.value.trim();
                     const fieldName = event.target.name.replace('', '');
                     const errorDiv = document.querySelector(`.${fieldName}_error`);
   
                     errorDiv.innerHTML = '';
   
                     const phoneRegex = /^[0-9]{10}$/;
   
                     if (!phoneNumber) {
                        errorDiv.innerHTML = 'Phone number is required.';
                        errorDiv.style.color = 'red';
                        event.target.style.borderColor = 'red';
                        return;
                     }
   
                     if (!phoneRegex.test(phoneNumber)) {
                        errorDiv.innerHTML = 'Please enter a valid 10-digit mobile number.';
                        errorDiv.style.color = 'red';
                        event.target.style.borderColor = 'red';
                        return;
                     }
   
                     event.target.style.borderColor = '';
                  });
               });
   
               const aadhaarInputs = document.querySelectorAll('[name="office_bearer_aadhar"]');
               aadhaarInputs.forEach(function(input) {
                  input.addEventListener('blur', function(event) {
                     const uid = event.target.value.trim();
                     const fieldName = event.target.name.replace('', '');
                     const errorDiv = document.querySelector(`.${fieldName}_error`);
                     errorDiv.innerHTML = '';
   
                     const Verhoeff = {
                        "d": [
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
                        "p": [
                           [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                           [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
                           [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
                           [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
                           [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
                           [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
                           [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
                           [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
                        ],
                        "j": [0, 4, 3, 2, 1, 5, 6, 7, 8, 9],
                        "check": function(str) {
                           var c = 0;
                           str.replace(/\D+/g, "").split("").reverse().join("").replace(/[\d]/g, function(u, i) {
                              c = Verhoeff.d[c][Verhoeff.p[i % 8][parseInt(u, 10)]];
                           });
                           return c;
                        }
                     };
   
                     if (Verhoeff['check'](uid) === 0) {
                        event.target.style.borderColor = '';
                        return true;
                     } else {
                        errorDiv.innerHTML = 'Aadhaar number is not valid!';
                        errorDiv.style.color = 'red';
                        event.target.style.borderColor = 'red';
                        event.target.value = '';
                        return false;
                     }
                  });
               });
               const panInputs = document.querySelectorAll('[name="office_bearer_pan"]');
               panInputs.forEach(function(input) {
                  input.addEventListener('change', function(event) {
                     var inputvalues = event.target.value.trim();
                     var regex = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                     const fieldName = event.target.name.replace('', '');
                     const errorDiv = document.querySelector(`.${fieldName}_error`);
   
                     errorDiv.innerHTML = '';
                     event.target.style.borderColor = '';
   
                     if (!regex.test(inputvalues)) {
                        errorDiv.innerHTML = 'PAN number is not valid!';
                        errorDiv.style.color = 'red';
                        event.target.style.borderColor = 'red';
                        event.target.value = '';
                        return false;
                     }
                     return true;
                  });
               });
   
               $('#editOfficeBearerForm').submit(function (e) {
                  e.preventDefault();
                  let formData = new FormData(this);
   
                  $.ajax({
                     url: "{{ route('admin.ngo.update_ngo_application_part_two_update_office_bearer') }}",
                     type: 'POST',
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function (res) {
                        let editModalEl = document.getElementById('editOfficeBearerModal');
                        let modalInstance = bootstrap.Modal.getInstance(editModalEl);
                        modalInstance.hide();
   
                        Swal.fire({
                           icon: 'success',
                           title: 'Success',
                           text: 'Office bearer updated successfully!',
                           confirmButtonColor: '#3085d6',
                        }).then(() => {
                           location.reload();
                        });
                     },
                     error: function (xhr) {
                        if (xhr.status === 422) {
                           let errors = xhr.responseJSON.errors;
                           $.each(errors, function (key, val) {
                              $(`.${key}_error`).html(`<label class="error">${val[0]}</label>`);
                           });
                        } else {
                           Swal.fire("Error", "Something went wrong during update.", "error");
                        }
                     }
                  });
               });
            });
break;
   case '#part3':
   console.log('Tab 3 script executing...');

         const radios = document.querySelectorAll('input[name="ngo_address_type_form"]');
         const dynamicContent = document.getElementById('dynamic-content');
         const formActions = document.querySelector('.form-actions');
         const form = document.forms['vform_form_name'];

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
               })
               .catch(error => {
                  console.error('Error fetching content:', error);
               });
            });
         });
   break;
   default:
   console.log('No script for this tab.');
   }
   }
   });
</script>
@endsection