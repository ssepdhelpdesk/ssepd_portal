@section('title') 
Pension || Pension Disburshing Officer
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
               <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.pension.pension_authority_store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                  @csrf
                  @method('post')
                  <div class="form-body">
                     <h5 class="card-title">Pension Disbursing Officer Details <small class="text-primary">Provide the Details.</small></h5>
                     <hr>
                     <div class="row">
                        @if($user->role_id == 4)
                        <div class="col-md-3">
                           <div class="form-group" id="grampanchayat_div">
                              <label class="form-label">Grampanchayat<span class="itsrequired"> *</span></label>
                              <select class="form-control show-tick ms select2" multiple id="grampanchayat" name="grampanchayat[]">
                                 <option disabled>Please Select</option>
                                 @foreach($grampanchayats as $gp)
                                 <option value="{{ $gp->gp_id }}">{{ $gp->gp_name }}</option>
                                 @endforeach
                              </select>
                              <div id="grampanchayat_error"></div>
                              @error('grampanchayat')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        @elseif($user->role_id == 5)
                        <div class="col-md-3">
                           <div class="form-group" id="ward_div">
                              <label class="form-label">Ward<span class="itsrequired"> *</span></label>
                              <select class="form-control show-tick ms select2" multiple id="ward" name="ward[]">
                                 <option disabled>Please Select</option>
                                 @foreach($wards as $ward)
                                 <option value="{{ $ward->ward_code }}">{{ $ward->ward_name }}</option>
                                 @endforeach
                              </select>
                              <div id="ward_error"></div>
                              @error('ward')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                           <div class="form-group" id="authority_name_div">
                              <label class="form-label">Officer Name<span class="itsrequired"> *</span></label>
                              <input 
                              type="text" id="authority_name" name="authority_name" value="{{ old('authority_name') }}" class="form-control" placeholder="Enter Officer Name">
                              <div id="authority_name_error"></div>
                              @error('authority_name')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" id="authority_mobile_no_div">
                              <label class="form-label">Officer Mobile No<span class="itsrequired"> *</span></label>
                              <input 
                              type="text" id="authority_mobile_no" name="authority_mobile_no" value="{{ old('authority_mobile_no') }}" class="form-control" placeholder="Enter Officer Mobile No">
                              <div id="authority_mobile_no_error"></div>
                              @error('authority_mobile_no')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" id="authority_designation_div">
                              <label class="form-label">Designation<span class="itsrequired"> *</span></label>
                              <select class="form-control show-tick ms select2" id="authority_designation" name="authority_designation">
                                 <option >Please Select</option>
                                 <option value="1">PEO</option>
                                 <option value="2">CO</option>
                                 <option value="3">Tax Collector</option>
                                 <option value="4">JA</option>
                                 <option value="5">PA</option>
                                 <option value="6">ADEO</option>
                                 <option value="7">GRS</option>
                                 <option value="8">Other</option>
                                 <option value="9">Jogana Sahayak (JS)</option>
                              </select>
                              <div id="authority_designation_error"></div>
                              @error('authority_designation')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                     </div>
                  </div>
                  @php
                     $today = \Carbon\Carbon::today();
                     @endphp

                     @if($today->between(\Carbon\Carbon::parse($startDate), \Carbon\Carbon::parse($endDate)))
                     <div class="form-actions">
                      <button type="submit" onclick="return IsEmpty();" name="register"
                      class="btn btn-primary text-white from-prevent-multiple-submits">
                      <i class="spinner fa fa-spinner fa-spin"></i> Submit
                   </button>
                </div>
                @else
                <div class="alert alert-warning">
                   Form submission is allowed only between 
                   {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }} and 
                   {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}.
                </div>
                @endif
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form[name='vform']");

    form.addEventListener("submit", function (e) {
        if (!validateForm()) e.preventDefault();
    });

    ["authority_name", "authority_mobile_no", "authority_designation", "grampanchayat", "ward"].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener("change", () => validateField(id));
        if (el && el.tagName === "INPUT") el.addEventListener("blur", () => validateField(id));
    });
});

function validateForm() {
    let valid = true;
    ["authority_name", "authority_mobile_no", "authority_designation", "grampanchayat", "ward"].forEach(id => {
        const el = document.getElementById(id);
        if (el && !validateField(id)) valid = false;
    });
    return valid;
}

function validateField(id) {
    const field = document.getElementById(id);
    const errorDiv = document.getElementById(id + "_error");
    let msg = "";

    switch (id) {
        case "authority_name":
            if (!field.value.trim()) msg = "Officer Name is required.";
            break;
        case "authority_mobile_no":
            if (!/^[0-9]{10}$/.test(field.value.trim())) msg = "Enter a valid 10-digit mobile number.";
            break;
        case "authority_designation":
            if (!field.value.trim() || field.value === "Please Select") msg = "Please select a designation.";
            break;
        case "grampanchayat":
            if (field.selectedOptions.length === 0) msg = "Please select at least one Grampanchayat.";
            break;
        case "ward":
            if (field.selectedOptions.length === 0) msg = "Please select at least one Ward.";
            break;
    }

    errorDiv.innerHTML = msg ? `<span class="text-danger">${msg}</span>` : "";
    return msg === "";
}
</script>

@endsection