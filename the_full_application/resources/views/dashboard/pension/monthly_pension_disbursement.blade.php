@section('title') 
Pension || Funds Requiremt
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.monthlypensiondisbursement.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')                     
                     <div class="form-body">
                        <h5 class="card-title">Block/ULB wise Pension Disbursement Date <small class="text-primary"></small></h5>
                        <hr>
                        <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                           <thead>
                              <tr>
                                 <th>Sl No</th>
                                 @if($user->role_name == 'BSSO')
                                 <th>GP Name</th>
                                 @elseif($user->role_name == 'MEO')
                                 <th>Ward Name</th>
                                 @endif                                 
                                 <th>For the Month</th>
                                 <th>Disbursement Start Date</th>                                 
                              </tr>
                           </thead>
                           <tbody>
                              @forelse($gp_ward_id as $index => $gpward)
                              <tr>
                                 <td>{{ $index + 1 }}</td>
                                 <td>
                                    <div class="col-md-3">                              
                                       @if($user->role_name == 'BSSO')
                                       <div class="form-group" id="gp_ward_name_div">
                                          <input type="hidden" name="gp_ward_id[]" value="{{ $gpward->gp_id }}" class="form-control">
                                          <input 
                                          type="text" id="gp_ward_name" name="gp_ward_name[]" value="{{ $gpward->gp_name }}" class="form-control" placeholder="Enter GP Name">
                                          <div id="gp_ward_name_error"></div>
                                          @error('gp_ward_name')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                       @endif
                                       @if($user->role_name == 'MEO')
                                       <div class="form-group" id="gp_ward_name_div">
                                          <input type="hidden" name="gp_ward_id[]" value="{{ $gpward->ward_code }}" class="form-control">
                                          <input 
                                          type="text" id="gp_ward_name" name="gp_ward_name[]" value="{{ $gpward->ward_name }}" class="form-control" placeholder="Enter Ward Name">
                                          <div id="gp_ward_name_error"></div>
                                          @error('gp_ward_name')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                       @endif
                                    </div>
                                 </td>
                                 <td>
                                    {{ $forTheMonth }}
                                 </td>
                                 <td>
                                    <div class="col-md-3">
                                       <div class="form-group" id="disbursement_start_date_div">
                                          <input 
                                          type="date" id="disbursement_start_date" name="disbursement_start_date[]" value="{{ old('disbursement_start_date') }}" class="form-control" placeholder="">
                                          <div id="disbursement_start_date_error"></div>
                                          @error('disbursement_start_date')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                    </div>    
                                 </td>
                              </tr>
                              @empty
                              <tr>
                                 <td colspan="8" class="text-center text-muted">No Records Found Yet</td>
                              </tr>
                              @endforelse
                           </tbody>
                        </table>
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
     const form = document.forms["vform"];
     const requiredFields = [
      "gp_ward_name",
      "mbpy_oap_above_80_years",
      "mbpy_wp",
      "mbpy_dp",
      "mbpy_sdp_below_80_percent",
      "mbpy_sdp_above_80_percent",
      "mbpy_sdoap",
      "mbpy_clp",
      "mbpy_wp_aids",
      "mbpy_dp_aids",
      "mbpy_unmarried_women",
      "mbpy_orphan_due_to_covide",
      "mbpy_widow_due_to_covid",
      "mbpy_divorce_or_destitute",
      "mbpy_transgender",
      "mbpy_bank_account_number",
      "mbpy_bank_ifsc_code"
   ];

   form.addEventListener("submit", function (e) {
    let hasError = false;

    requiredFields.forEach((id) => {
      const field = document.getElementById(id);
      const errorDiv = document.getElementById(id + "_error");


      errorDiv.innerHTML = "";
      field.classList.remove("is-invalid");

      const value = field.value.trim();

      if (value === "") {
        errorDiv.innerHTML = `<label class="error">This field is required</label>`;
        field.classList.add("is-invalid");
        hasError = true;
     } else {
        if (id === "mbpy_bank_ifsc_code") {
          const ifscRegex = /^[A-Z]{4}0[A-Z0-9]{6}$/;
          if (!ifscRegex.test(value)) {
            errorDiv.innerHTML = `<label class="error">Enter valid IFSC code (e.g., SBIN0001234)</label>`;
            field.classList.add("is-invalid");
            hasError = true;
         }
      } else if (id !== "mbpy_bank_account_number" && !/^\d+$/.test(value)) {
       errorDiv.innerHTML = `<label class="error">Enter a valid number</label>`;
       field.classList.add("is-invalid");
       hasError = true;
    }
 }
});  
    if (hasError) {
      e.preventDefault();
      return false;
   }
   return true;
});
});
</script>
@endsection