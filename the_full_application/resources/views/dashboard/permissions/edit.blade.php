@section('title') 
UPDATE || PERMISSIONS
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
                    @include('dashboard.component.message')
                    <div class="col-sm-12 col-xs-12">
                        <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.permissions.update', $permissions->id)}}" onsubmit="return Validate()" name="vform">
                            @csrf
                            @method('post')
                            <div class="row">
                                <div class="col-lg-12" id="username_div">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class="form-label">Permission Name</label>
                                        <input type="text" name="name" value="{{ old('name', $permissions->name) }}" class="form-control textInput" id="role_id" placeholder="Enter Permission Name">
                                        <div id="name_error"></div>
                                        @error('name')
                                        <label class="error">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
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
   var username = document.forms['vform']['role_id'];
   var email = document.forms['vform']['permission'];
   var password = document.forms['vform']['password'];
   var password_confirm = document.forms['vform']['password_confirm'];
   // SELECTING ALL ERROR DISPLAY ELEMENTS
   var name_error = document.getElementById('name_error');
   var email_error = document.getElementById('email_error');
   var password_error = document.getElementById('password_error');
   // SETTING ALL EVENT LISTENERS
   username.addEventListener('blur', nameVerify, true);
   email.addEventListener('blur', emailVerify, true);
   password.addEventListener('blur', passwordVerify, true);
   // validation function
   function Validate() {
   // validate username
   if (username.value == "") {
     username.style.border = "1px solid red";
     document.getElementById('username_div').style.color = "red";
     name_error.textContent = "Please enter Permission";
     username.focus();
     return false;
   }
   // validate email
   if (email.value == "") {
     email.style.border = "1px solid red";
     document.getElementById('email_div').style.color = "red";
     email_error.textContent = "Email is required";
     email.focus();
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
     document.getElementById('pass_confirm_div').style.color = "red";
     password_confirm.style.border = "1px solid red";
     password_error.innerHTML = "The two passwords do not match";
     return false;
   }
   }
   // event handler functions
   function nameVerify() {
   if (username.value != "") {
    username.style.border = "1px solid #5e6e66";
    document.getElementById('username_div').style.color = "#5e6e66";
    name_error.innerHTML = "";
    return true;
   }
   }
   function emailVerify() {
   if (email.value != "") {
     email.style.border = "1px solid #5e6e66";
     document.getElementById('email_div').style.color = "#5e6e66";
     email_error.innerHTML = "";
     return true;
   }
   }
   function passwordVerify() {
   if (password.value != "") {
     password.style.border = "1px solid #5e6e66";
     document.getElementById('pass_confirm_div').style.color = "#5e6e66";
     document.getElementById('password_div').style.color = "#5e6e66";
     password_error.innerHTML = "";
     return true;
   }
   if (password.value === password_confirm.value) {
     password.style.border = "1px solid #5e6e66";
     document.getElementById('pass_confirm_div').style.color = "#5e6e66";
     password_error.innerHTML = "";
     return true;
   }
   }
</script>
@endsection