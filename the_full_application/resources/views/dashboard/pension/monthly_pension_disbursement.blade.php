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
    background-color: #f44336; /* red for error */
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
                                 <th>No of Beneficiaries Received Normal Pension</th>
                                 <th>No of Beneficiaries Received EP Pension</th>                                 
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
                                          type="date" id="disbursement_start_date" name="disbursement_start_date[]" value="{{ old('disbursement_start_date') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control" placeholder="">
                                          <div id="disbursement_start_date_error"></div>
                                          @error('disbursement_start_date')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                    </div>    
                                 </td>
                                 <td>
                                    <div class="col-md-3">
                                       <div class="form-group" id="no_of_normal_pensioners_div">
                                          <input 
                                          type="number" id="no_of_normal_pensioners" name="no_of_normal_pensioners[]" value="{{ old('no_of_normal_pensioners', 0) }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
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
                                          type="number" id="no_of_ep_pensioners" name="no_of_ep_pensioners[]" value="{{ old('no_of_ep_pensioners', 0) }}" class="form-control" placeholder="Enter beneficiary count" min="0" step="1">
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
function showToast(message) {
    const toast = document.getElementById("toast");
    toast.innerText = message;
    toast.className = "toast show";

    setTimeout(() => {
        toast.className = toast.className.replace("show", "");
    }, 3000); // Toast disappears after 3 seconds
}

function Validate() {
    let rows = document.querySelectorAll("#example23 tbody tr");
    let isRowFilled = false;

    rows.forEach((row) => {
        let normal = parseInt(row.querySelector("input[name='no_of_normal_pensioners[]']").value) || 0;
        let ep = parseInt(row.querySelector("input[name='no_of_ep_pensioners[]']").value) || 0;
        let date = row.querySelector("input[name='disbursement_start_date[]']").value;

        if (date !== "" && (normal > 0 || ep > 0)) {
            isRowFilled = true;
        }
    });

    if (!isRowFilled) {
        showToast("Please fill at least one row with a valid date and beneficiaries count (normal or EP > 0).");
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}
</script>
@endsection