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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.pension.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Basic Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_oap_below_80_years_div">
                                 <label class="form-label">MBPOAP (Below 80 Years)<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_oap_below_80_years" name="mbpy_oap_below_80_years" value="{{ old('mbpy_oap_below_80_years') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_oap_below_80_years_error"></div>
                                 @error('mbpy_oap_below_80_years')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_oap_above_80_years_div">
                                 <label class="form-label">MBPOAP (Above 80 Years)<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_oap_above_80_years" name="mbpy_oap_above_80_years" value="{{ old('mbpy_oap_above_80_years') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_oap_above_80_years_error"></div>
                                 @error('mbpy_oap_above_80_years')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_wp_div">
                                 <label class="form-label">MBPWP<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_wp" name="mbpy_wp" value="{{ old('mbpy_wp') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_wp_error"></div>
                                 @error('mbpy_wp')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_dp_div">
                                 <label class="form-label">MBPDP<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_dp" name="mbpy_dp" value="{{ old('mbpy_dp') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_dp_error"></div>
                                 @error('mbpy_dp')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_sdp_below_80_percent_div">
                                 <label class="form-label">MBPSDP (Below 80%)<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_sdp_below_80_percent" name="mbpy_sdp_below_80_percent" value="{{ old('mbpy_sdp_below_80_percent') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_sdp_below_80_percent_error"></div>
                                 @error('mbpy_sdp_below_80_percent')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_sdp_above_80_percent_div">
                                 <label class="form-label">MBPSDP (Above 80%)<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_sdp_above_80_percent" name="mbpy_sdp_above_80_percent" value="{{ old('mbpy_sdp_above_80_percent') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_sdp_above_80_percent_error"></div>
                                 @error('mbpy_sdp_above_80_percent')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_sdoap_div">
                                 <label class="form-label">MBPSDOAP<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_sdoap" name="mbpy_sdoap" value="{{ old('mbpy_sdoap') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_sdoap_error"></div>
                                 @error('mbpy_sdoap')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_clp_div">
                                 <label class="form-label">MBPCLP<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_clp" name="mbpy_clp" value="{{ old('mbpy_clp') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_clp_error"></div>
                                 @error('mbpy_clp')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_wp_aids_div">
                                 <label class="form-label">MBPWP (Due to Aids)<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_wp_aids" name="mbpy_wp_aids" value="{{ old('mbpy_wp_aids') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_wp_aids_error"></div>
                                 @error('mbpy_wp_aids')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_dp_aids_div">
                                 <label class="form-label">MBPDP (Due to Aids)<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_dp_aids" name="mbpy_dp_aids" value="{{ old('mbpy_dp_aids') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_dp_aids_error"></div>
                                 @error('mbpy_dp_aids')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_unmarried_women_div">
                                 <label class="form-label">MBPUMW<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_unmarried_women" name="mbpy_unmarried_women" value="{{ old('mbpy_unmarried_women') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_unmarried_women_error"></div>
                                 @error('mbpy_unmarried_women')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_orphan_due_to_covide_div">
                                 <label class="form-label">Orphan due to Covid<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_orphan_due_to_covide" name="mbpy_orphan_due_to_covide" value="{{ old('mbpy_orphan_due_to_covide') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_orphan_due_to_covide_error"></div>
                                 @error('mbpy_orphan_due_to_covide')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_widow_due_to_covid_div">
                                 <label class="form-label">Widow due to Covid<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_widow_due_to_covid" name="mbpy_widow_due_to_covid" value="{{ old('mbpy_widow_due_to_covid') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_widow_due_to_covid_error"></div>
                                 @error('mbpy_widow_due_to_covid')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_divorce_or_destitute_div">
                                 <label class="form-label">Divorcee or Destitute<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_divorce_or_destitute" name="mbpy_divorce_or_destitute" value="{{ old('mbpy_divorce_or_destitute') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_divorce_or_destitute_error"></div>
                                 @error('mbpy_divorce_or_destitute')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="mbpy_transgender_div">
                                 <label class="form-label">Transgender<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="mbpy_transgender" name="mbpy_transgender" value="{{ old('mbpy_transgender') }}" class="form-control" placeholder="Enter count" min="0" step="1">
                                 <div id="mbpy_transgender_error"></div>
                                 @error('mbpy_transgender')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="form-actions">
                        <button type="submit" onclick="return IsEmpty();" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.forms["vform"];
    const requiredFields = [
        "mbpy_oap_below_80_years",
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
        "mbpy_transgender"
    ];

    form.addEventListener("submit", function (e) {
        let hasError = false;

        requiredFields.forEach((id) => {
            const field = document.getElementById(id);
            const errorDiv = document.getElementById(id + "_error");

            // Clear previous errors
            errorDiv.innerHTML = "";
            field.classList.remove("is-invalid");

            const value = field.value.trim();

            if (value === "") {
                errorDiv.innerHTML = `<label class="error">This field is required</label>`;
                field.classList.add("is-invalid");
                hasError = true;
            } else if (!/^\d+$/.test(value)) {
                errorDiv.innerHTML = `<label class="error">Enter a valid number</label>`;
                field.classList.add("is-invalid");
                hasError = true;
            }
        });

        if (hasError) {
            e.preventDefault(); // Stop form submission
            return false;
        }
        return true;
    });
});
</script>

@endsection