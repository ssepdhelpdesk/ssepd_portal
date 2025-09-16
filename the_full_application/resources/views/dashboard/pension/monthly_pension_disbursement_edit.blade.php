@section('title') 
Pension || GP/Ward wise Daily Basis Pension Disbursement for the month - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.monthlypensiondisbursement.update', $monthly_pension_disbursemenet->id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">GP/Ward wise Daily Basis Pension Disbursement for the month - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }} <small class="text-primary"></small></h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-3">
                              @if($monthly_pension_disbursemenet->staff_address_type == '1')
                              <div class="form-group" id="mbpy_oap_below_80_years_div">
                                 <label class="form-label">GP Name<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="text" id="gp_ward_name" name="gp_ward_name" value="{{ $monthly_pension_disbursemenet->grampanchayat->gp_name }}" class="form-control" placeholder="Enter GP Name" min="0" step="1">
                                 <div id="gp_ward_name_error"></div>
                                 @error('gp_ward_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                              @endif
                              @if($monthly_pension_disbursemenet->staff_address_type == '2')
                              <div class="form-group" id="mbpy_oap_below_80_years_div">
                                 <label class="form-label">Ward Name<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="text" id="gp_ward_name" name="gp_ward_name" value="{{ $monthly_pension_disbursemenet->ward->ward_name }}" class="form-control" placeholder="Enter Ward Name" min="0" step="1">
                                 <div id="gp_ward_name_error"></div>
                                 @error('gp_ward_name')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                              @endif
                           </div>                           
                           <div class="col-md-3">
                              <div class="form-group" id="for_the_month_div">
                                 <label class="form-label">For The Month<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="text" id="for_the_month" name="for_the_month" value="{{ old('for_the_month', $monthly_pension_disbursemenet->for_the_month) }}" class="form-control" placeholder="Enter beneficiary count">
                                 <div id="for_the_month_error"></div>
                                 @error('for_the_month')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="disbursement_start_date_div">
                                 <label class="form-label">Disbursement Date<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="date" id="disbursement_start_date" name="disbursement_start_date" value="{{ old('disbursement_start_date', $monthly_pension_disbursemenet->disbursement_start_date) }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control" placeholder="Enter beneficiary count">
                                 <div id="disbursement_start_date_error"></div>
                                 @error('disbursement_start_date')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="no_of_normal_pensioners_div">
                                 <label class="form-label">No of Normal Pension Disbursed<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="no_of_normal_pensioners" name="no_of_normal_pensioners" value="{{ old('no_of_normal_pensioners', $monthly_pension_disbursemenet->no_of_normal_pensioners) }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                 <div id="no_of_normal_pensioners_error"></div>
                                 @error('no_of_normal_pensioners')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="no_of_ep_pensioners_div">
                                 <label class="form-label">No of EP Pension Disbursed<span class="itsrequired"> *</span></label>
                                 <input 
                                 type="number" id="no_of_ep_pensioners" name="no_of_ep_pensioners" value="{{ old('no_of_ep_pensioners', $monthly_pension_disbursemenet->no_of_ep_pensioners) }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
                                 <div id="no_of_ep_pensioners_error"></div>
                                 @error('no_of_ep_pensioners')
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

@endsection