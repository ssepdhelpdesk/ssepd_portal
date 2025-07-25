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
            <h5 class="card-title">Other Act Registration</h5>
            <hr>
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
               <div class="table-responsive">
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.part_three_store', $id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <table class="table color-table info-table">
                        <thead>
                           <tr>
                              <th>Act</th>
                              <th>Authority</th>
                              <th>Regd. No.</th>
                              <th>Date of Regn.</th>
                              <th>Validity Till</th>
                              <th>Certificates</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>Income Tax Act 1961 (Sec. 12AA)</td>
                              <td>
                                 <div class="form-group" id="authority_one_div">
                                    <input type="text" id="authority_one" name="authority_one" value="{{ old('authority_one') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_one_error"></div>
                                    @error('authority_one')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_one_div">
                                    <input type="text" id="regd_no_one" name="regd_no_one" value="{{ old('regd_no_one') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_one_error"></div>
                                    @error('regd_no_one')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_one_div">
                                    <input type="date" class="form-control" id="regd_date_one" name="regd_date_one" value="{{old('regd_date_one') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_one_error"></div>
                                    @error('regd_date_one')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_one_div">
                                    <input type="date" class="form-control" id="validity_date_one" name="validity_date_one" value="{{old('validity_date_one') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_one_error"></div>
                                    @error('validity_date_one')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_one_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_one" value="{{ old('regd_certificate_file_one') ?? '' }}" name="regd_certificate_file_one" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_one_error"></div>
                                    @error('regd_certificate_file_one')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Income Tax Act 1961 (Sec. 80G)</td>
                              <td>
                                 <div class="form-group" id="authority_two_div">
                                    <input type="text" id="authority_two" name="authority_two" value="{{ old('authority_two') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_two_error"></div>
                                    @error('authority_two')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_two_div">
                                    <input type="text" id="regd_no_two" name="regd_no_two" value="{{ old('regd_no_two') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_two_error"></div>
                                    @error('regd_no_two')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_two_div">
                                    <input type="date" class="form-control" id="regd_date_two" name="regd_date_two" value="{{old('regd_date_two') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_two_error"></div>
                                    @error('regd_date_two')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_two_div">
                                    <input type="date" class="form-control" id="validity_date_two" name="validity_date_two" value="{{old('validity_date_two') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_two_error"></div>
                                    @error('validity_date_two')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_two_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_two" value="{{ old('regd_certificate_file_two') ?? '' }}" name="regd_certificate_file_two" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_two_error"></div>
                                    @error('regd_certificate_file_two')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Registration ID under the NGO Darpan Portal of Government of India</td>
                              <td>
                                 <div class="form-group" id="authority_three_div">
                                    <input type="text" id="authority_three" name="authority_three" value="{{ old('authority_three') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_three_error"></div>
                                    @error('authority_three')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_three_div">
                                    <input type="text" id="regd_no_three" name="regd_no_three" value="{{ old('regd_no_three') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_three_error"></div>
                                    @error('regd_no_three')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_three_div">
                                    <input type="date" class="form-control" id="regd_date_three" name="regd_date_three" value="{{old('regd_date_three') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_three_error"></div>
                                    @error('regd_date_three')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_three_div">
                                    <input type="date" class="form-control" id="validity_date_three" name="validity_date_three" value="{{old('validity_date_three') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_three_error"></div>
                                    @error('validity_date_three')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_three_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_three" value="{{ old('regd_certificate_file_three') ?? '' }}" name="regd_certificate_file_three" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_three_error"></div>
                                    @error('regd_certificate_file_three')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Foreign Contribution (Regulation) Act, 2010</td>
                              <td>
                                 <div class="form-group" id="authority_four_div">
                                    <input type="text" id="authority_four" name="authority_four" value="{{ old('authority_four') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_four_error"></div>
                                    @error('authority_four')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_four_div">
                                    <input type="text" id="regd_no_four" name="regd_no_four" value="{{ old('regd_no_four') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_four_error"></div>
                                    @error('regd_no_four')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_four_div">
                                    <input type="date" class="form-control" id="regd_date_four" name="regd_date_four" value="{{old('regd_date_four') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_four_error"></div>
                                    @error('regd_date_four')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_four_div">
                                    <input type="date" class="form-control" id="validity_date_four" name="validity_date_four" value="{{old('validity_date_four') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_four_error"></div>
                                    @error('validity_date_four')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_four_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_four" value="{{ old('regd_certificate_file_four') ?? '' }}" name="regd_certificate_file_four" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_four_error"></div>
                                    @error('regd_certificate_file_four')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Section 50 of the RPwD Act, 2016</td>
                              <td>
                                 <div class="form-group" id="authority_five_div">
                                    <input type="text" id="authority_five" name="authority_five" value="{{ old('authority_five') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_five_error"></div>
                                    @error('authority_five')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_five_div">
                                    <input type="text" id="regd_no_five" name="regd_no_five" value="{{ old('regd_no_five') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_five_error"></div>
                                    @error('regd_no_five')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_five_div">
                                    <input type="date" class="form-control" id="regd_date_five" name="regd_date_five" value="{{old('regd_date_five') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_five_error"></div>
                                    @error('regd_date_five')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_five_div">
                                    <input type="date" class="form-control" id="validity_date_five" name="validity_date_five" value="{{old('validity_date_five') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_five_error"></div>
                                    @error('validity_date_five')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_five_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_five" value="{{ old('regd_certificate_file_five') ?? '' }}" name="regd_certificate_file_five" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_five_error"></div>
                                    @error('regd_certificate_file_five')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Section 65 of the Mental Health Care Act, 2017</td>
                              <td>
                                 <div class="form-group" id="authority_six_div">
                                    <input type="text" id="authority_six" name="authority_six" value="{{ old('authority_six') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_six_error"></div>
                                    @error('authority_six')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_six_div">
                                    <input type="text" id="regd_no_six" name="regd_no_six" value="{{ old('regd_no_six') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_six_error"></div>
                                    @error('regd_no_six')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_six_div">
                                    <input type="date" class="form-control" id="regd_date_six" name="regd_date_six" value="{{old('regd_date_six') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_six_error"></div>
                                    @error('regd_date_six')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_six_div">
                                    <input type="date" class="form-control" id="validity_date_six" name="validity_date_six" value="{{old('validity_date_six') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_six_error"></div>
                                    @error('validity_date_six')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_six_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_six" value="{{ old('regd_certificate_file_six') ?? '' }}" name="regd_certificate_file_six" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_six_error"></div>
                                    @error('regd_certificate_file_six')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Section 12 of the National Trust Act, 1999</td>
                              <td>
                                 <div class="form-group" id="authority_seven_div">
                                    <input type="text" id="authority_seven" name="authority_seven" value="{{ old('authority_seven') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_seven_error"></div>
                                    @error('authority_seven')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_seven_div">
                                    <input type="text" id="regd_no_seven" name="regd_no_seven" value="{{ old('regd_no_seven') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_seven_error"></div>
                                    @error('regd_no_seven')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_seven_div">
                                    <input type="date" class="form-control" id="regd_date_seven" name="regd_date_seven" value="{{old('regd_date_seven') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_seven_error"></div>
                                    @error('regd_date_seven')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_seven_div">
                                    <input type="date" class="form-control" id="validity_date_seven" name="validity_date_seven" value="{{old('validity_date_seven') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_seven_error"></div>
                                    @error('validity_date_seven')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_seven_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_seven" value="{{ old('regd_certificate_file_seven') ?? '' }}" name="regd_certificate_file_seven" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_seven_error"></div>
                                    @error('regd_certificate_file_seven')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>MWPSC Act, 2007</td>
                              <td>
                                 <div class="form-group" id="authority_eight_div">
                                    <input type="text" id="authority_eight" name="authority_eight" value="{{ old('authority_eight') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_eight_error"></div>
                                    @error('authority_eight')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_eight_div">
                                    <input type="text" id="regd_no_eight" name="regd_no_eight" value="{{ old('regd_no_eight') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_eight_error"></div>
                                    @error('regd_no_eight')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_eight_div">
                                    <input type="date" class="form-control" id="regd_date_eight" name="regd_date_eight" value="{{old('regd_date_eight') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_eight_error"></div>
                                    @error('regd_date_eight')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_eight_div">
                                    <input type="date" class="form-control" id="validity_date_eight" name="validity_date_eight" value="{{old('validity_date_eight') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_eight_error"></div>
                                    @error('validity_date_eight')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_eight_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_eight" value="{{ old('regd_certificate_file_eight') ?? '' }}" name="regd_certificate_file_eight" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_eight_error"></div>
                                    @error('regd_certificate_file_eight')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Juvenile Justice Act 2015</td>
                              <td>
                                 <div class="form-group" id="authority_nine_div">
                                    <input type="text" id="authority_nine" name="authority_nine" value="{{ old('authority_nine') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_nine_error"></div>
                                    @error('authority_nine')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_nine_div">
                                    <input type="text" id="regd_no_nine" name="regd_no_nine" value="{{ old('regd_no_nine') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_nine_error"></div>
                                    @error('regd_no_nine')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_nine_div">
                                    <input type="date" class="form-control" id="regd_date_nine" name="regd_date_nine" value="{{old('regd_date_nine') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_nine_error"></div>
                                    @error('regd_date_nine')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_nine_div">
                                    <input type="date" class="form-control" id="validity_date_nine" name="validity_date_nine" value="{{old('validity_date_nine') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_nine_error"></div>
                                    @error('validity_date_nine')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_nine_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_nine" value="{{ old('regd_certificate_file_nine') ?? '' }}" name="regd_certificate_file_nine" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_nine_error"></div>
                                    @error('regd_certificate_file_nine')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <div class="form-group" id="authority_other_act_div">
                                    <label class="form-label">Any Other (Specify)</label>
                                    <input type="text" id="authority_other_act" name="authority_other_act" value="{{ old('authority_other_act') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_other_error"></div>
                                    @error('authority_other_act')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="authority_other_div">
                                    <input type="text" id="authority_other" name="authority_other" value="{{ old('authority_other') ?? '' }}" class="form-control" data-required="true">
                                    <div class="authority_other_error"></div>
                                    @error('authority_other')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_no_other_div">
                                    <input type="text" id="regd_no_other" name="regd_no_other" value="{{ old('regd_no_other') ?? '' }}" class="form-control" data-required="true">
                                    <div class="regd_no_other_error"></div>
                                    @error('regd_no_other')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_date_other_div">
                                    <input type="date" class="form-control" id="regd_date_other" name="regd_date_other" value="{{old('regd_date_other') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_date_other_error"></div>
                                    @error('regd_date_other')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="validity_date_other_div">
                                    <input type="date" class="form-control" id="validity_date_other" name="validity_date_other" value="{{old('validity_date_other') ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
                                    <div class="validity_date_other_error"></div>
                                    @error('validity_date_other')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="regd_certificate_file_other_div">
                                    <input type="file" class="form-control" id="regd_certificate_file_other" value="{{ old('regd_certificate_file_other') ?? '' }}" name="regd_certificate_file_other" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="regd_certificate_file_other_error"></div>
                                    @error('regd_certificate_file_other')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <div class="form-actions">
                        <button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
                     </div>
                  </form>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
   const form = document.forms['vform'];
   const submitButton = document.getElementById('submitButton');

   function validateRow(row) {
      let isAnyFieldFilled = false;
      const inputs = row.querySelectorAll("input, select");
      const errors = [];

      inputs.forEach(input => {
         if (input.value.trim() !== "") {
            isAnyFieldFilled = true;
         }
      });

      if (isAnyFieldFilled) {
         inputs.forEach(input => {
            const errorDiv = input.parentElement.querySelector('.error');
            if (input.value.trim() === "") {
               errors.push(input);
               if (errorDiv) {
                  errorDiv.textContent = "This field is required.";
                  errorDiv.style.color = "red";
               } else {
                  const errorLabel = document.createElement('label');
                  errorLabel.classList.add('error');
                  errorLabel.style.color = "red";
                  errorLabel.textContent = "This field is required.";
                  input.parentElement.appendChild(errorLabel);
               }
            } else {
               if (errorDiv) {
                  errorDiv.textContent = "";
               }
            }
         });
      }

      return errors.length === 0;
   }

   submitButton.addEventListener("click", function (event) {
      let isValid = true;
      const rows = form.querySelectorAll("tr");

      const isAnyRowFilled = Array.from(rows).some(row => {
         return Array.from(row.querySelectorAll("input, select")).some(input => input.value.trim() !== "");
      });

      if (!isAnyRowFilled) {
         Swal.fire({
            icon: "warning",
            title: "Oops...",
            text: "Please fill at least one ACT before submitting!",
            confirmButtonColor: "#d33"
         });
         event.preventDefault();
         return;
      }

      rows.forEach(row => {
         if (!validateRow(row)) {
            isValid = false;
         }
      });

      if (!isValid) {
         event.preventDefault();
      }
   });

   const maxFileSize = 1 * 1024 * 1024;
   const allowedFileType = 'application/pdf';

   function validateFileInput(input) {
      const file = input.files[0];
      const fieldName = input.name;
      let errorDiv = document.querySelector(`.${fieldName}_error`);

      if (!errorDiv) {
         errorDiv = document.createElement("div");
         errorDiv.classList.add("error");
         errorDiv.classList.add(`${fieldName}_error`);
         input.parentElement.appendChild(errorDiv);
      }
      errorDiv.innerHTML = "";

      if (!file) return;

      if (file.type !== allowedFileType) {
         Swal.fire({
            icon: "error",
            title: "Invalid File Type",
            text: "Only PDF files are allowed!",
            confirmButtonColor: "#d33"
         });
         input.value = "";
         return;
      }

      if (file.size > maxFileSize) {
         Swal.fire({
            icon: "error",
            title: "File Too Large",
            text: "File size must be less than 1MB!",
            confirmButtonColor: "#d33"
         });
         input.value = "";
         return;
      }
   }

   const fileInputs = document.querySelectorAll('input[type="file"]');
   fileInputs.forEach(function (input) {
      input.addEventListener('change', function () {
         validateFileInput(input);
      });
   });
});
</script>
@endsection