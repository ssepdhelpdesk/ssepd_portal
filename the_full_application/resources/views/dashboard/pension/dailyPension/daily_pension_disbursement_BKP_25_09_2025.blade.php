@section('title') 
Pension || GP/Ward wise Pension Daily Disbursement
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .readonly-input {
      pointer-events: none;
      background-color: #f8f9fa;
      cursor: default;
   }
   .form-control {
      color: #212529;
      min-height: 38px;
      display: initial;
      width: auto;
   }
   .toast {
      visibility: hidden;
      min-width: 300px;
      margin-left: -150px;
      background-color: #f44336;
      color: white;
      text-align: center;
      border-radius: 8px;
      padding: 16px;
      position: fixed;
      z-index: 9999;
      left: 50%;
      top: 20px;
      font-size: 16px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.2);
      opacity: 0;
      transition: opacity 0.5s, top 0.5s;
   }
   .toast.show {
      visibility: visible;
      opacity: 1;
      top: 40px;
   }
   .table-responsive-scroll {
      max-height: 500px;
      overflow-y: auto;
      overflow-x: auto;
      display: block;
      width: 100%;
   }
   .table-responsive-scroll table {
      width: 100%;
      border-collapse: collapse;
   }
   .table-responsive-scroll thead th {
      position: sticky;
      top: 0;
      z-index: 2;
      background-color: #f8f9fa;
   }
   .is-invalid {
    border: 1px solid red !important;
    background-color: #ffecec;
}
.row-error-msg {
    font-size: 12px;
    color: red;
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
               <div id="toast"></div>
               <div class="col-sm-12 col-xs-12">
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.dailypensiondisbursement.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')                     
                     <div class="form-body">
                        <h5 class="card-title">GP/Ward wise Pension Daily Disbursement <small class="text-primary"></small></h5>
                        <hr>
                        <div class="table-responsive-scroll">
                           <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                              <thead>
                                 <tr>
                                    @if($user->role_name == 'BSSO')
                                    <th>GP Name</th>
                                    @elseif($user->role_name == 'MEO')
                                    <th>Ward Name</th>
                                    @endif
                                    <th>For the Month</th>
                                    <th>Disbursement Date</th>
                                    <th>MBPOAP (Below 80 Years)</th>
                                    <th>MBPOAP (Above 80 Years)</th>
                                    <th>MBPWP</th>
                                    <th>MBPDP</th>
                                    <th>MBPSDP (Below 80%)</th>
                                    <th>MBPSDP (Above 80%)</th>
                                    <th>MBPSDOAP</th>
                                    <th>MBPCLP</th>
                                    <th>MBPWP (Due to Aids)</th>
                                    <th>MBPDP (Due to Aids)</th>
                                    <th>MBPUMW</th>
                                    <th>Orphan due to Covid</th>
                                    <th>Widow due to Covid</th>
                                    <th>Divorcee or Destitute</th>
                                    <th>Transgender</th>
                                    <th>No of Beneficiaries Received Normal Pension</th>
                                    <th>No of Beneficiaries Received Enhanced Pension</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @forelse($gp_ward_id as $index => $gpward)
                                 <tr>
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
                                             type="date" id="disbursement_start_date" name="disbursement_start_date[]" value="" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control" placeholder="">
                                             <div id="disbursement_start_date_error"></div>
                                             @error('disbursement_start_date')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_oap_below_80_years_div">
                                             <input 
                                             type="number" id="mbpy_oap_below_80_years" name="mbpy_oap_below_80_years[]" value="{{ old('mbpy_oap_below_80_years') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_oap_below_80_years_error"></div>
                                             @error('mbpy_oap_below_80_years.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_oap_above_80_years_div">
                                             <input 
                                             type="number" id="mbpy_oap_above_80_years" name="mbpy_oap_above_80_years[]" value="{{ old('mbpy_oap_above_80_years') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_oap_above_80_years_error"></div>
                                             @error('mbpy_oap_above_80_years.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_wp_div">
                                             <input 
                                             type="number" id="mbpy_wp" name="mbpy_wp[]" value="{{ old('mbpy_wp') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_wp_error"></div>
                                             @error('mbpy_wp.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_dp_div">
                                             <input 
                                             type="number" id="mbpy_dp" name="mbpy_dp[]" value="{{ old('mbpy_dp') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_dp_error"></div>
                                             @error('mbpy_dp.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_sdp_below_80_percent_div">
                                             <input 
                                             type="number" id="mbpy_sdp_below_80_percent" name="mbpy_sdp_below_80_percent[]" value="{{ old('mbpy_sdp_below_80_percent') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_sdp_below_80_percent_error"></div>
                                             @error('mbpy_sdp_below_80_percent.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_sdp_above_80_percent_div">
                                             <input 
                                             type="number" id="mbpy_sdp_above_80_percent" name="mbpy_sdp_above_80_percent[]" value="{{ old('mbpy_sdp_above_80_percent') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_sdp_above_80_percent_error"></div>
                                             @error('mbpy_sdp_above_80_percent.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_sdoap_div">
                                             <input 
                                             type="number" id="mbpy_sdoap" name="mbpy_sdoap[]" value="{{ old('mbpy_sdoap') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_sdoap_error"></div>
                                             @error('mbpy_sdoap.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_clp_div">
                                             <input 
                                             type="number" id="mbpy_clp" name="mbpy_clp[]" value="{{ old('mbpy_clp') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_clp_error"></div>
                                             @error('mbpy_clp.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_wp_aids_div">
                                             <input 
                                             type="number" id="mbpy_wp_aids" name="mbpy_wp_aids[]" value="{{ old('mbpy_wp_aids') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_wp_aids_error"></div>
                                             @error('mbpy_wp_aids.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_dp_aids_div">
                                             <input 
                                             type="number" id="mbpy_dp_aids" name="mbpy_dp_aids[]" value="{{ old('mbpy_dp_aids') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_dp_aids_error"></div>
                                             @error('mbpy_dp_aids.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_unmarried_women_div">
                                             <input 
                                             type="number" id="mbpy_unmarried_women" name="mbpy_unmarried_women[]" value="{{ old('mbpy_unmarried_women') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_unmarried_women_error"></div>
                                             @error('mbpy_unmarried_women.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_orphan_due_to_covide_div">
                                             <input 
                                             type="number" id="mbpy_orphan_due_to_covide" name="mbpy_orphan_due_to_covide[]" value="{{ old('mbpy_orphan_due_to_covide') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_orphan_due_to_covide_error"></div>
                                             @error('mbpy_orphan_due_to_covide.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_widow_due_to_covid_div">
                                             <input 
                                             type="number" id="mbpy_widow_due_to_covid" name="mbpy_widow_due_to_covid[]" value="{{ old('mbpy_widow_due_to_covid') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_widow_due_to_covid_error"></div>
                                             @error('mbpy_widow_due_to_covid.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_divorce_or_destitute_div">
                                             <input 
                                             type="number" id="mbpy_divorce_or_destitute" name="mbpy_divorce_or_destitute[]" value="{{ old('mbpy_divorce_or_destitute') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_divorce_or_destitute_error"></div>
                                             @error('mbpy_divorce_or_destitute.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="mbpy_transgender_div">
                                             <input 
                                             type="number" id="mbpy_transgender" name="mbpy_transgender[]" value="{{ old('mbpy_transgender') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="mbpy_transgender_error"></div>
                                             @error('mbpy_transgender.*')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="no_of_normal_pensioners_div">
                                             <input 
                                             type="number" id="no_of_normal_pensioners" name="no_of_normal_pensioners[]" value="{{ old('no_of_normal_pensioners') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="no_of_normal_pensioners_error"></div>
                                             @error('no_of_normal_pensioners')
                                             <label class="error">{{ $message }}</label>
                                             @enderror
                                          </div>
                                       </div>
                                    </td>
                                    <td>
                                       <div class="col-md-3">
                                          <div class="form-group" id="no_of_ep_pensioners_div">
                                             <input 
                                             type="number" id="no_of_ep_pensioners" name="no_of_ep_pensioners[]" value="{{ old('no_of_ep_pensioners') }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                             <div id="no_of_ep_pensioners_error"></div>
                                             @error('no_of_ep_pensioners')
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

@endsection