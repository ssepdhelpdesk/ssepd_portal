@section('title') 
Users || Change Password
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
               <h4 class="card-title">@yield('title')</h4>
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
               <div class="col-sm-12 col-xs-12">
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.myprofile.changePasswordStore')}}" onsubmit="return Validate()" name="vform">
                     @csrf
                     @method('post')
                     <div class="row">
                        <div class="col-lg-4" id="oldpassword_div">
                           <div class="form-group">
                              <label for="exampleInputEmail1" class="form-label">Old Password</label>
                              <input type="text" name="oldpassword" value="{{ old('oldpassword') }}" class="form-control textInput" id="oldpassword" placeholder="Enter Old Password">
                              <div id="oldpassword_error"></div>
                              @error('oldpassword')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-lg-4" id="password_div">
                           <div class="form-group">
                              <label for="exampleInputEmail1" class="form-label">Password</label>
                              <input type="text" name="password" value="{{ old('password') }}" class="form-control textInput" id="password" placeholder="Enter Password">
                              <div id="password_error"></div>
                              @error('password')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                        <div class="col-lg-4" id="password_confirm_div">
                           <div class="form-group">
                              <label for="exampleInputEmail1" class="form-label">Confirm Password</label>
                              <input type="text" name="password_confirm" value="{{ old('password_confirm') }}" class="form-control textInput" id="password_confirm" placeholder="Enter Password">
                              <div id="password_confirm_error"></div>
                              @error('password_confirm')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                     </div>
                     @can('my-profile-edit')
                     <button type="submit" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
                     @endcan
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
   // SELECTING ALL TEXT ELEMENTS
   var oldpassword = document.forms['vform']['oldpassword'];
   var password = document.forms['vform']['password'];
   var password_confirm = document.forms['vform']['password_confirm'];
   // SELECTING ALL ERROR DISPLAY ELEMENTS
   var oldpassword_error = document.getElementById('oldpassword_error');
   var password_error = document.getElementById('password_error');
   var password_confirm_error = document.getElementById('password_confirm_error');
   // SETTING ALL EVENT LISTENERS
   oldpassword.addEventListener('blur', nameVerify, true);
   password.addEventListener('blur', passwordVerify, true);
   password_confirm.addEventListener('blur', passwordConfirmVerify, true);
   // validation function
   function Validate() {
   // validate oldpassword
   if (oldpassword.value == "") {
     oldpassword.style.border = "1px solid red";
     document.getElementById('oldpassword_div').style.color = "red";
     oldpassword_error.textContent = "Please enter Old Password";
     oldpassword.focus();
     return false;
   }
   // validate password
   if (password.value == "") {
     password.style.border = "1px solid red";
     document.getElementById('password_div').style.color = "red";
     password_confirm.style.border = "1px solid red";
     password_error.textContent = "Password is required";
     password.focus();
     return false;
   }
   // check if the two passwords match
   if (password.value != password_confirm.value) {
     password.style.border = "1px solid red";
     document.getElementById('password_confirm_div').style.color = "red";
     password_confirm.style.border = "1px solid red";
     password_error.innerHTML = "The two passwords do not match";
     return false;
   }
   }
   // event handler functions
   function nameVerify() {
   if (oldpassword.value != "") {
    oldpassword.style.border = "1px solid #5e6e66";
    document.getElementById('oldpassword_div').style.color = "#5e6e66";
    oldpassword_error.innerHTML = "";
    return true;
   }
   }
   function passwordVerify() {
   if (password.value != "") {
     password.style.border = "1px solid #5e6e66";
     document.getElementById('password_confirm_div').style.color = "#5e6e66";
     document.getElementById('password_div').style.color = "#5e6e66";
     password_error.innerHTML = "";
     return true;
   }
   if (password.value === password_confirm.value) {
     password.style.border = "1px solid #5e6e66";
     document.getElementById('password_confirm_div').style.color = "#5e6e66";
     password_error.innerHTML = "";
     return true;
   }
   }
</script>
@endsection