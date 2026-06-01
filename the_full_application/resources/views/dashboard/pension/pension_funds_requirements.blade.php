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
               <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.pension.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                  @csrf
                  @method('post')
                  <div class="form-body">
                     <h5 class="card-title">Block/ULB wise fund requirement under MBPY <small class="text-primary">Provide the beneficiary count, not the amount.</small></h5>
                     <hr>
                     <div class="row">
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_oap_below_80_years_div">
                              <label class="form-label">MBPOAP (Below 80 Years)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_oap_below_80_years" name="mbpy_oap_below_80_years" value="{{ old('mbpy_oap_below_80_years') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_oap_below_80_years_error"></div>
                              @error('mbpy_oap_below_80_years')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_oap_below_80_years_div">
                              <label class="form-label">Funds Required for MBPOAP (Below 80 Years)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_oap_below_80_years" name="funds_mbpy_oap_below_80_years" readonly value="{{ old('funds_mbpy_oap_below_80_years') }}" class="form-control" placeholder="Funds Required for MBPOAP (Below 80 Years)" min="0" step="1">
                              <div id="funds_mbpy_oap_below_80_years_error"></div>
                              @error('funds_mbpy_oap_below_80_years')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_oap_above_80_years_div">
                              <label class="form-label">MBPOAP (Above 80 Years)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_oap_above_80_years" name="mbpy_oap_above_80_years" value="{{ old('mbpy_oap_above_80_years') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_oap_above_80_years_error"></div>
                              @error('mbpy_oap_above_80_years')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_oap_above_80_years_div">
                              <label class="form-label">Funds Required for MBPOAP (Above 80 Years)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_oap_above_80_years" name="funds_mbpy_oap_above_80_years" readonly value="{{ old('funds_mbpy_oap_above_80_years') }}" class="form-control" placeholder="Funds Required for MBPOAP (Above 80 Years)" min="0" step="1">
                              <div id="funds_mbpy_oap_above_80_years_error"></div>
                              @error('funds_mbpy_oap_above_80_years')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_wp_div">
                              <label class="form-label">MBPWP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_wp" name="mbpy_wp" value="{{ old('mbpy_wp') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_wp_error"></div>
                              @error('mbpy_wp')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_wp_div">
                              <label class="form-label">Funds Required for MBPWP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_wp" name="funds_mbpy_wp" readonly value="{{ old('funds_mbpy_wp') }}" class="form-control" placeholder="Funds Required for MBPWP" min="0" step="1">
                              <div id="funds_mbpy_wp_error"></div>
                              @error('funds_mbpy_wp')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_dp_div">
                              <label class="form-label">MBPDP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_dp" name="mbpy_dp" value="{{ old('mbpy_dp') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_dp_error"></div>
                              @error('mbpy_dp')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_dp_div">
                              <label class="form-label">Funds Required for MBPDP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_dp" name="funds_mbpy_dp" readonly value="{{ old('funds_mbpy_dp') }}" class="form-control" placeholder="Funds Required for MBPDP" min="0" step="1">
                              <div id="funds_mbpy_dp_error"></div>
                              @error('funds_mbpy_dp')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_sdp_below_80_percent_div">
                              <label class="form-label">MBPSDP (Below 80%)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_sdp_below_80_percent" name="mbpy_sdp_below_80_percent" value="{{ old('mbpy_sdp_below_80_percent') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_sdp_below_80_percent_error"></div>
                              @error('mbpy_sdp_below_80_percent')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_sdp_below_80_percent_div">
                              <label class="form-label">Funds Required for MBPSDP (Below 80%)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_sdp_below_80_percent" name="funds_mbpy_sdp_below_80_percent" readonly value="{{ old('funds_mbpy_sdp_below_80_percent') }}" class="form-control" placeholder="Funds Required for MBPSDP (Below 80%)" min="0" step="1">
                              <div id="funds_mbpy_sdp_below_80_percent_error"></div>
                              @error('funds_mbpy_sdp_below_80_percent')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_sdp_above_80_percent_div">
                              <label class="form-label">MBPSDP (Above 80%)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_sdp_above_80_percent" name="mbpy_sdp_above_80_percent" value="{{ old('mbpy_sdp_above_80_percent') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_sdp_above_80_percent_error"></div>
                              @error('mbpy_sdp_above_80_percent')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_sdp_above_80_percent_div">
                              <label class="form-label">Funds Required for MBPSDP (Above 80%)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_sdp_above_80_percent" name="funds_mbpy_sdp_above_80_percent" readonly value="{{ old('funds_mbpy_sdp_above_80_percent') }}" class="form-control" placeholder="Funds Required for MBPSDP (Above 80%)" min="0" step="1">
                              <div id="funds_mbpy_sdp_above_80_percent_error"></div>
                              @error('funds_mbpy_sdp_above_80_percent')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_sdoap_div">
                              <label class="form-label">MBPSDOAP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_sdoap" name="mbpy_sdoap" value="{{ old('mbpy_sdoap') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_sdoap_error"></div>
                              @error('mbpy_sdoap')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_sdoap_div">
                              <label class="form-label">Funds Required for MBPSDOAP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_sdoap" name="funds_mbpy_sdoap" readonly value="{{ old('funds_mbpy_sdoap') }}" class="form-control" placeholder="Funds Required for MBPSDOAP" min="0" step="1">
                              <div id="funds_mbpy_sdoap_error"></div>
                              @error('funds_mbpy_sdoap')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_clp_div">
                              <label class="form-label">MBPCLP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_clp" name="mbpy_clp" value="{{ old('mbpy_clp') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_clp_error"></div>
                              @error('mbpy_clp')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_clp_div">
                              <label class="form-label">Funds Required for MBPCLP<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_clp" name="funds_mbpy_clp" readonly value="{{ old('funds_mbpy_clp') }}" class="form-control" placeholder="Funds Required for MBPCLP" min="0" step="1">
                              <div id="funds_mbpy_clp_error"></div>
                              @error('funds_mbpy_clp')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_wp_aids_div">
                              <label class="form-label">MBPWP (Due to Aids)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_wp_aids" name="mbpy_wp_aids" value="{{ old('mbpy_wp_aids') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_wp_aids_error"></div>
                              @error('mbpy_wp_aids')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_wp_aids_div">
                              <label class="form-label">Funds Required for MBPWP (Due to Aids)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_wp_aids" name="funds_mbpy_wp_aids" readonly value="{{ old('funds_mbpy_wp_aids') }}" class="form-control" placeholder="Funds Required for MBPWP (Due to Aids)" min="0" step="1">
                              <div id="funds_mbpy_wp_aids_error"></div>
                              @error('funds_mbpy_wp_aids')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_dp_aids_div">
                              <label class="form-label">MBPDP (Due to Aids)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_dp_aids" name="mbpy_dp_aids" value="{{ old('mbpy_dp_aids') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_dp_aids_error"></div>
                              @error('mbpy_dp_aids')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_dp_aids_div">
                              <label class="form-label">Funds Required for MBPDP (Due to Aids)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_dp_aids" name="funds_mbpy_dp_aids" readonly value="{{ old('funds_mbpy_dp_aids') }}" class="form-control" placeholder="Funds Required for MBPDP (Due to Aids)" min="0" step="1">
                              <div id="funds_mbpy_dp_aids_error"></div>
                              @error('funds_mbpy_dp_aids')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_unmarried_women_div">
                              <label class="form-label">MBPUMW<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_unmarried_women" name="mbpy_unmarried_women" value="{{ old('mbpy_unmarried_women') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_unmarried_women_error"></div>
                              @error('mbpy_unmarried_women')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_unmarried_women_div">
                              <label class="form-label">Funds Required for MBPUMW<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_unmarried_women" name="funds_mbpy_unmarried_women" readonly value="{{ old('funds_mbpy_unmarried_women') }}" class="form-control" placeholder="Funds Required for MBPUMW" min="0" step="1">
                              <div id="funds_mbpy_unmarried_women_error"></div>
                              @error('funds_mbpy_unmarried_women')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_orphan_due_to_covide_div">
                              <label class="form-label">Orphan due to Covid<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_orphan_due_to_covide" name="mbpy_orphan_due_to_covide" value="{{ old('mbpy_orphan_due_to_covide') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_orphan_due_to_covide_error"></div>
                              @error('mbpy_orphan_due_to_covide')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_orphan_due_to_covide_div">
                              <label class="form-label">Funds Required for Orphan due to Covid<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_orphan_due_to_covide" name="funds_mbpy_orphan_due_to_covide" readonly value="{{ old('funds_mbpy_orphan_due_to_covide') }}" class="form-control" placeholder="Funds Required for Orphan due to Covid" min="0" step="1">
                              <div id="funds_mbpy_orphan_due_to_covide_error"></div>
                              @error('funds_mbpy_orphan_due_to_covide')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_widow_due_to_covid_div">
                              <label class="form-label">Widow due to Covid<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_widow_due_to_covid" name="mbpy_widow_due_to_covid" value="{{ old('mbpy_widow_due_to_covid') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_widow_due_to_covid_error"></div>
                              @error('mbpy_widow_due_to_covid')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_widow_due_to_covid_div">
                              <label class="form-label">Funds Required for Widow due to Covid<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_widow_due_to_covid" name="funds_mbpy_widow_due_to_covid" readonly value="{{ old('funds_mbpy_widow_due_to_covid') }}" class="form-control" placeholder="Funds Required for Widow due to Covid" min="0" step="1">
                              <div id="funds_mbpy_widow_due_to_covid_error"></div>
                              @error('funds_mbpy_widow_due_to_covid')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_divorce_or_destitute_div">
                              <label class="form-label">Divorcee or Destitute<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_divorce_or_destitute" name="mbpy_divorce_or_destitute" value="{{ old('mbpy_divorce_or_destitute') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_divorce_or_destitute_error"></div>
                              @error('mbpy_divorce_or_destitute')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_divorce_or_destitute_div">
                              <label class="form-label">Funds Required for Divorcee or Destitute<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_divorce_or_destitute" name="funds_mbpy_divorce_or_destitute" readonly value="{{ old('funds_mbpy_divorce_or_destitute') }}" class="form-control" placeholder="Funds Required for Divorcee or Destitute" min="0" step="1">
                              <div id="funds_mbpy_divorce_or_destitute_error"></div>
                              @error('funds_mbpy_divorce_or_destitute')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_transgender_div">
                              <label class="form-label">Transgender<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_transgender" name="mbpy_transgender" value="{{ old('mbpy_transgender') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                              <div id="mbpy_transgender_error"></div>
                              @error('mbpy_transgender')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_transgender_div">
                              <label class="form-label">Funds Required for Transgender<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_transgender" name="funds_mbpy_transgender" readonly value="{{ old('funds_mbpy_transgender') }}" class="form-control" placeholder="Funds Required for Transgender" min="0" step="1">
                              <div id="funds_mbpy_transgender_error"></div>
                              @error('funds_mbpy_transgender')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_total_beneficiaries_div">
                              <label class="form-label">Total Beneficiaries<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_total_beneficiaries" name="mbpy_total_beneficiaries" readonly value="{{ old('mbpy_total_beneficiaries') }}" class="form-control" placeholder="Total Nos. of Beneficiaries" min="0" step="1">
                              <div id="mbpy_total_beneficiaries_error"></div>
                              @error('mbpy_total_beneficiaries')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="funds_mbpy_total_beneficiaries_div">
                              <label class="form-label">Total Funds Required<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="funds_mbpy_total_beneficiaries" name="funds_mbpy_total_beneficiaries" readonly value="{{ old('funds_mbpy_total_beneficiaries') }}" class="form-control" placeholder="Total Funds Requirements" min="0" step="1">
                              <div id="funds_mbpy_total_beneficiaries_error"></div>
                              @error('funds_mbpy_total_beneficiaries')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="total_unspent_fund_div">
                              <label class="form-label">Total Unspent Funds (Till date)<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="total_unspent_fund" name="total_unspent_fund" value="{{ old('total_unspent_fund') }}" class="form-control" placeholder="Enter Total Unspent Funds (Till date)" min="0" step="1">
                              <div id="total_unspent_fund_error"></div>
                              @error('total_unspent_fund')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_bank_account_number_div">
                              <label class="form-label">Bank Account Number<span class="itsrequired"> *</span></label>
                              <input 
                              type="number" id="mbpy_bank_account_number" name="mbpy_bank_account_number" value="{{ old('mbpy_bank_account_number') }}" class="form-control" placeholder="Enter beneficiary count">
                              <div id="mbpy_bank_account_number_error"></div>
                              @error('mbpy_bank_account_number')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group" id="mbpy_bank_ifsc_code_div">
                              <label class="form-label">IFSC Code<span class="itsrequired"> *</span></label>
                              <input 
                              type="text" id="mbpy_bank_ifsc_code" name="mbpy_bank_ifsc_code" value="{{ old('mbpy_bank_ifsc_code') }}" class="form-control" placeholder="Enter beneficiary count">
                              <div id="mbpy_bank_ifsc_code_error"></div>
                              @error('mbpy_bank_ifsc_code')
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
$(document).ready(function () {
    Swal.fire({
        icon: 'warning',
        title: 'Important Instructions',
        html: `
            <div style="text-align:left;">
                <p><strong>Please provide the actual number of beneficiaries eligible to receive pension for the month.</strong></p>
                <p>Do <strong>not</strong> calculate or reduce the beneficiary count based on any unspent balance available in your account.</p>
                <p>The beneficiary count should include <strong>all eligible beneficiaries</strong> who are to receive pension, irrespective of the unspent funds currently available.</p>
                <p>Providing incorrect beneficiary counts may result in inaccurate fund requirement calculations and delay pension disbursement.</p>
            </div>
        `,
        confirmButtonText: 'I Understand',
        allowOutsideClick: false
    });
});
</script>
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
         "mbpy_transgender",
         "total_unspent_fund",
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapping = {
        mbpy_oap_below_80_years: 1000,
        mbpy_oap_above_80_years: 3500,
        mbpy_wp: 1000,
        mbpy_dp: 1000,
        mbpy_sdp_below_80_percent: 1200,
        mbpy_sdp_above_80_percent: 3500,
        mbpy_sdoap: 3500,
        mbpy_clp: 1000,
        mbpy_wp_aids: 1000,
        mbpy_dp_aids: 1000,
        mbpy_unmarried_women: 1000,
        mbpy_orphan_due_to_covide: 1000,
        mbpy_widow_due_to_covid: 1000,
        mbpy_divorce_or_destitute: 1000,
        mbpy_transgender: 1000
    };

    const totalBeneficiariesInput = document.getElementById('mbpy_total_beneficiaries');
    const totalFundsInput = document.getElementById('funds_mbpy_total_beneficiaries');

    function calculateFunds(sourceId, rate) {
        const sourceInput = document.getElementById(sourceId);
        const targetInput = document.getElementById('funds_' + sourceId);
        if (!sourceInput || !targetInput) return;

        sourceInput.addEventListener('input', function () {
            const value = parseInt(sourceInput.value) || 0;
            targetInput.value = value * rate;
            calculateTotals();
        });
    }

    Object.keys(mapping).forEach(id => {
        calculateFunds(id, mapping[id]);
    });

    function calculateTotals() {
        let totalBeneficiaries = 0;
        let totalFunds = 0;

        Object.keys(mapping).forEach(id => {
            const benInput = document.getElementById(id);
            const fundInput = document.getElementById('funds_' + id);

            const benValue = parseInt(benInput?.value || 0);
            const fundValue = parseInt(fundInput?.value || 0);

            totalBeneficiaries += benValue;
            totalFunds += fundValue;
        });

        if (totalBeneficiariesInput) totalBeneficiariesInput.value = totalBeneficiaries;
        if (totalFundsInput) totalFundsInput.value = totalFunds;
    }

    calculateTotals();
});
</script>
@endsection