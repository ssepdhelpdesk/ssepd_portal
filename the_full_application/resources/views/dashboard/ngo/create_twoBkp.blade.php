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
						<form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.part_one_store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
							@csrf
							@method('post')
							<div class="form-body">
								<div id="education_fields"></div>
								<h3 class="card-title">Office Bearer Details</h3>
								<hr>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_name_div">
											<label class="form-label">Office Bearer Name<span class="itsrequired"> *</span></label>
											<input type="text" id="office_bearer_name" name="office_bearer_name[]" value="{{ old('office_bearer_name')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Name" data-required="true">
											<div class="office_bearer_name_error"></div>
											@error('office_bearer_name')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_gender_div">
											<label class="form-label">Gender<span class="itsrequired"> *</span></label>
											<select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_gender[]" data-required="true">
												<option value="">--Select--</option>
												<option value="1">Male</option>
												<option value="2">Female</option>
												<option value="3">Other</option>
											</select>
											<div class="office_bearer_gender_error"></div>
											@error('office_bearer_gender')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_email_div">
											<label class="form-label">Email<span class="itsrequired"> *</span></label>
											<input type="email" id="office_bearer_email" name="office_bearer_email[]" value="{{old('office_bearer_email')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Email" data-required="true">
											<div class="office_bearer_email_error"></div>
											@error('office_bearer_email')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_phone_div">
											<label class="form-label">Phone No<span class="itsrequired"> *</span></label>
											<input type="text" id="office_bearer_phone" name="office_bearer_phone[]" value="{{old('office_bearer_phone')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Phone No" data-required="true">
											<div class="office_bearer_phone_error"></div>
											@error('office_bearer_phone')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
								</div>
								<!--/row-->
								<div class="row">
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_designation_div">
											<label class="form-label">Designation<span class="itsrequired"> *</span></label>
											<select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_designation[]">
												<option value="">--Select--</option>
												<option value="1">Chairman</option>
												<option value="2">President</option>
												<option value="3">Vice Chairman</option>
												<option value="4">Secretary</option>
												<option value="5">Director</option>
												<option value="6">Addl. Director</option>
												<option value="7">Joint Secretary</option>
												<option value="8">Treasurer</option>
												<option value="9">Executive Member</option>
												<option value="10">Board Member</option>
												<option value="11">Academic Adviser</option>
												<option value="12">Academic Administrator</option>
												<option value="13">Accountant</option>
												<option value="14">Coordinator</option>
												<option value="15">Other</option>
											</select>
											<div class="office_bearer_designation_error"></div>
											@error('office_bearer_designation')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_key_designation_div">
											<label class="form-label">Key Designation<span class="itsrequired"> *</span></label>
											<select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_key_designation[]">
												<option value="">--Select--</option>
												<option value="1">Chief Functionary</option>
												<option value="2">Promoter</option>
												<option value="3">Patron</option>
												<option value="4">Other</option>
											</select>
											<div class="office_bearer_key_designation_error"></div>
											@error('office_bearer_key_designation')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_pan_div">
											<label class="form-label">Pan No<span class="itsrequired"> *</span></label>
											<input type="text" id="office_bearer_pan" name="office_bearer_pan[]" value="{{old('office_bearer_pan')[0] ?? '' }}" class="form-control" placeholder="Pan No">
											<div class="office_bearer_pan_error"></div>
											@error('office_bearer_pan')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_pan_file_div">
											<label class="form-label">Upload PAN<span class="itsrequired"> *</span></label>
											<input type="file" class="form-control" id="office_bearer_pan_file" name="office_bearer_pan_file[]" value="{{old('office_bearer_pan_file')[0] ?? '' }}" accept=".pdf" aria-describedby="inputGroupFileAddon01">
											<div class="office_bearer_pan_file_error"></div>
											@error('office_bearer_pan_file')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_name_as_aadhar_div">
											<label class="form-label">Name as per Aadhar<span class="itsrequired"> *</span></label>
											<input type="text" id="office_bearer_name_as_aadhar" name="office_bearer_name_as_aadhar[]" value="{{old('office_bearer_name_as_aadhar')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Name">
											<div class="office_bearer_name_as_aadhar_error"></div>
											@error('office_bearer_name_as_aadhar')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group" id="office_bearer_dob_div">
											<label class="form-label">Date of Birth<span class="itsrequired"> *</span></label>
											<input type="date" class="form-control" id="office_bearer_dob" name="office_bearer_dob[]" value="{{old('office_bearer_dob')[0] ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
											<div class="office_bearer_dob_error"></div>
											@error('office_bearer_dob')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" id="office_bearer_aadhar_div">
											<label class="form-label">Aadhar No<span class="itsrequired"> *</span></label>
											<input type="text" id="office_bearer_aadhar" name="office_bearer_aadhar[]" value="{{old('office_bearer_aadhar')[0] ?? '' }}" class="form-control" placeholder="Aadhar No">
											<div class="office_bearer_aadhar_error"></div>
											@error('office_bearer_aadhar')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
									<div class="col-md-4">
										<div class="row align-items-center">
											<div class="col-md-9">
												<div class="form-group" id="office_bearer_aadhar_file_div">
													<label class="form-label">Upload Aadhar<span class="itsrequired"> *</span></label>
													<input type="file" class="form-control" id="office_bearer_aadhar_file" value="{{ old('office_bearer_aadhar_file')[0] ?? '' }}" name="office_bearer_aadhar_file[]" accept=".pdf" aria-describedby="inputGroupFileAddon01">
													<div class="office_bearer_aadhar_file_error"></div>
													@error('office_bearer_aadhar_file')
													<label class="error">{{ $message }}</label>
													@enderror
												</div>
											</div>
											<div class="col-md-3">
												<div class="input-group-append">
													<button class="btn btn-success text-white" type="button" onclick="education_fields();">
														<i class="fa fa-plus"></i>
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!--/row-->
							</div>
							<div class="form-actions">
								<button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
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
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		const form = document.forms['vform'];
		const submitButton = document.getElementById('submitButton');

		submitButton.addEventListener('click', function (e) {
			e.preventDefault();
			if (validateFormFields()) {
				form.submit();
			}
		});

		function validateFormFields() {
			clearErrors();

			const fields = [
				{name: 'office_bearer_name[]', errorClass: 'office_bearer_name_error', message: 'Please Provide Office Bearer Name.'},
				{name: 'office_bearer_gender[]', errorClass: 'office_bearer_gender_error', message: 'Please Select Gender.'},
				{name: 'office_bearer_email[]', errorClass: 'office_bearer_email_error', message: 'Please Provide Office Bearer Email.'},
				{name: 'office_bearer_phone[]', errorClass: 'office_bearer_phone_error', message: 'Please Provide Office Bearer Phone No.'},
				{name: 'office_bearer_designation[]', errorClass: 'office_bearer_designation_error', message: 'Please Select Designation.'},
				{name: 'office_bearer_key_designation[]', errorClass: 'office_bearer_key_designation_error', message: 'Please Select Key Designation.'},
				{name: 'office_bearer_pan[]', errorClass: 'office_bearer_pan_error', message: 'Please Provide PAN No.'},
				{name: 'office_bearer_pan_file[]', errorClass: 'office_bearer_pan_file_error', message: 'Please Upload PAN.'},
				{name: 'office_bearer_name_as_aadhar[]', errorClass: 'office_bearer_name_as_aadhar_error', message: 'Please Provide Name as per Aadhar.'},
				{name: 'office_bearer_dob[]', errorClass: 'office_bearer_dob_error', message: 'Please Provide Date of Birth.'},
				{name: 'office_bearer_aadhar[]', errorClass: 'office_bearer_aadhar_error', message: 'Please Provide Aadhar No.'},
				{name: 'office_bearer_aadhar_file[]', errorClass: 'office_bearer_aadhar_file_error', message: 'Please Upload Aadhar.'}
				];

			for (let field of fields) {
				const inputElement = document.querySelector(`[name='${field.name}']`);
				const errorElement = document.querySelector(`.${field.errorClass}`);

				if (!inputElement || inputElement.value.trim() === '') {
					errorElement.innerHTML = `<span style="color: red;">${field.message}</span>`;
					inputElement.style.borderColor = "red";
					return false;
				} else {
					inputElement.style.borderColor = "";
				}
			}

			return true;
		}

		function clearErrors() {
			const errorContainers = document.querySelectorAll('.office_bearer_name_error, .office_bearer_gender_error, .office_bearer_email_error, .office_bearer_phone_error, .office_bearer_designation_error, .office_bearer_key_designation_error, .office_bearer_pan_error, .office_bearer_pan_file_error, .office_bearer_name_as_aadhar_error, .office_bearer_dob_error, .office_bearer_aadhar_error, .office_bearer_aadhar_file_error');

			errorContainers.forEach(container => {
				if (container) container.innerHTML = '';
			});

			const inputs = document.querySelectorAll("[name='office_bearer_name[]'], [name='office_bearer_gender[]'], [name='office_bearer_email[]'], [name='office_bearer_phone[]'], [name='office_bearer_designation[]'], [name='office_bearer_key_designation[]'], [name='office_bearer_pan[]'], [name='office_bearer_pan_file[]'], [name='office_bearer_name_as_aadhar[]'], [name='office_bearer_dob[]'], [name='office_bearer_aadhar[]'], [name='office_bearer_aadhar_file[]']");

			inputs.forEach(input => {
				input.style.borderColor = "";
			});
		}

		const fileInputs = document.querySelectorAll('[name="office_bearer_pan_file[]"]');
		fileInputs.forEach(function(input) {
			input.addEventListener('change', function(event) {
				const file = event.target.files[0];
				const fieldName = event.target.name.replace('[]', '');
				const errorDiv = document.querySelector(`.${fieldName}_error`);
				const maxFileSize = 1 * 1024 * 1024;

				errorDiv.innerHTML = '';
				if (!file) {
					return;
				}

				if (file.type !== 'application/pdf') {
					errorDiv.innerHTML = '<div class="error">Only PDF files are allowed.</div>';
					event.target.value = '';
					return;
				}
				if (file.size > maxFileSize) {
					errorDiv.innerHTML = '<div class="error">File size must be less than 1MB.</div>';
					event.target.value = '';
					return;
				}
			});
		});
		const fileAadharInputs = document.querySelectorAll('[name="office_bearer_aadhar_file[]"]');
		fileAadharInputs.forEach(function(input) {
			input.addEventListener('change', function(event) {
				const file = event.target.files[0];
				const fieldName = event.target.name.replace('[]', '');
				const errorDiv = document.querySelector(`.${fieldName}_error`);
				const maxFileSize = 1 * 1024 * 1024;

				errorDiv.innerHTML = '';
				if (!file) {
					return;
				}

				if (file.type !== 'application/pdf') {
					errorDiv.innerHTML = '<div class="error">Only PDF files are allowed.</div>';
					event.target.value = '';
					return;
				}
				if (file.size > maxFileSize) {
					errorDiv.innerHTML = '<div class="error">File size must be less than 1MB.</div>';
					event.target.value = '';
					return;
				}
			});
		});
		const phoneInputs = document.querySelectorAll('[name="office_bearer_phone[]"]');
		phoneInputs.forEach(function(input) {
			input.addEventListener('keypress', function(event) {
				const char = event.key;
				if (!/^[0-9]$/.test(char)) {
					event.preventDefault();
				}
			});

			input.addEventListener('blur', function(event) {
				const phoneNumber = event.target.value.trim();
				const fieldName = event.target.name.replace('[]', '');
				const errorDiv = document.querySelector(`.${fieldName}_error`);

				errorDiv.innerHTML = '';

				const phoneRegex = /^[0-9]{10}$/;

				if (!phoneNumber) {
					errorDiv.innerHTML = 'Phone number is required.';
					errorDiv.style.color = 'red';
					event.target.style.borderColor = 'red';
					return;
				}

				if (!phoneRegex.test(phoneNumber)) {
					errorDiv.innerHTML = 'Please enter a valid 10-digit mobile number.';
					errorDiv.style.color = 'red';
					event.target.style.borderColor = 'red';
					return;
				}

				event.target.style.borderColor = '';
			});
		});
		const aadhaarInputs = document.querySelectorAll('[name="office_bearer_aadhar[]"]');
		aadhaarInputs.forEach(function(input) {
			input.addEventListener('blur', function(event) {
				const uid = event.target.value.trim();
				const fieldName = event.target.name.replace('[]', '');
				const errorDiv = document.querySelector(`.${fieldName}_error`);
				errorDiv.innerHTML = '';

				const Verhoeff = {
					"d": [
						[0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
						[1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
						[2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
						[3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
						[4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
						[5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
						[6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
						[7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
						[8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
						[9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
						],
					"p": [
						[0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
						[1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
						[5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
						[8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
						[9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
						[4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
						[2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
						[7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
						],
					"j": [0, 4, 3, 2, 1, 5, 6, 7, 8, 9],
					"check": function(str) {
						var c = 0;
						str.replace(/\D+/g, "").split("").reverse().join("").replace(/[\d]/g, function(u, i) {
							c = Verhoeff.d[c][Verhoeff.p[i % 8][parseInt(u, 10)]];
						});
						return c;
					}
				};

				if (Verhoeff['check'](uid) === 0) {
					event.target.style.borderColor = '';
					return true;
				} else {
					errorDiv.innerHTML = 'Aadhaar number is not valid!';
					errorDiv.style.color = 'red';
					event.target.style.borderColor = 'red';
					event.target.value = '';
					return false;
				}
			});
		});
		const panInputs = document.querySelectorAll('[name="office_bearer_pan[]"]');
		panInputs.forEach(function(input) {
			input.addEventListener('change', function(event) {
				var inputvalues = event.target.value.trim();
				var regex = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
				const fieldName = event.target.name.replace('[]', '');
				const errorDiv = document.querySelector(`.${fieldName}_error`);

				errorDiv.innerHTML = '';
				event.target.style.borderColor = '';

				if (!regex.test(inputvalues)) {
					errorDiv.innerHTML = 'PAN number is not valid!';
					errorDiv.style.color = 'red';
					event.target.style.borderColor = 'red';
					event.target.value = '';
					return false;
				}
				return true;
			});
		});
	});
</script>
<script type="text/javascript">
	var room = 1;

	function education_fields() {

		room++;
		var objTo = document.getElementById('education_fields')
		var divtest = document.createElement("div");
		divtest.setAttribute("class", "form-group removeclass" + room);
		var rdiv = 'removeclass' + room;
		divtest.innerHTML = '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body"><h4 class="card-title">Dynamic Form Fields</h4><div id="education_fields"></div><div class="row"><h3 class="card-title">Office Bearer Details</h3><hr><div class="row"><div class="col-md-3"><div class="form-group" id="office_bearer_name_div"><label class="form-label">Office Bearer Name<span class="itsrequired"> *</span></label><input type="text" id="office_bearer_name" name="office_bearer_name[]" value="{{ old('office_bearer_name')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Name" data-required="true"><div class="office_bearer_name_error"></div>@error('office_bearer_name')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="form-group" id="office_bearer_gender_div"><label class="form-label">Gender<span class="itsrequired"> *</span></label><select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_gender[]" data-required="true"><option value="">--Select--</option><option value="1">Male</option><option value="2">Female</option><option value="3">Other</option></select><div class="office_bearer_gender_error"></div>@error('office_bearer_gender')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="form-group" id="office_bearer_email_div"><label class="form-label">Email<span class="itsrequired"> *</span></label><input type="email" id="office_bearer_email" name="office_bearer_email[]" value="{{old('office_bearer_email')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Email" data-required="true"><div class="office_bearer_email_error"></div>@error('office_bearer_email')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="form-group" id="office_bearer_phone_div"><label class="form-label">Phone No<span class="itsrequired"> *</span></label><input type="text" id="office_bearer_phone" name="office_bearer_phone[]" value="{{old('office_bearer_phone')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Phone No" data-required="true"><div class="office_bearer_phone_error"></div>@error('office_bearer_phone')<label class="error">{{ $message }}</label>@enderror</div></div></div><div class="row"><div class="col-md-3"><div class="form-group" id="office_bearer_designation_div"><label class="form-label">Designation<span class="itsrequired"> *</span></label><select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_designation[]"><option value="">--Select--</option><option value="1">Chairman</option><option value="2">President</option><option value="3">Vice Chairman</option><option value="4">Secretary</option><option value="5">Director</option><option value="6">Addl. Director</option><option value="7">Joint Secretary</option><option value="8">Treasurer</option><option value="9">Executive Member</option><option value="10">Board Member</option><option value="11">Academic Adviser</option><option value="12">Academic Administrator</option><option value="13">Accountant</option><option value="14">Coordinator</option><option value="15">Other</option></select><div class="office_bearer_designation_error"></div>@error('office_bearer_designation')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="form-group" id="office_bearer_key_designation_div"><label class="form-label">Key Designation<span class="itsrequired"> *</span></label><select class="select2 form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="office_bearer_key_designation[]"><option value="">--Select--</option><option value="1">Chief Functionary</option><option value="2">Promoter</option><option value="3">Patron</option><option value="4">Other</option></select><div class="office_bearer_key_designation_error"></div>@error('office_bearer_key_designation')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="form-group" id="office_bearer_pan_div"><label class="form-label">Pan No<span class="itsrequired"> *</span></label><input type="text" id="office_bearer_pan" name="office_bearer_pan[]" value="{{old('office_bearer_pan')[0] ?? '' }}" class="form-control" placeholder="Pan No"><div class="office_bearer_pan_error"></div>@error('office_bearer_pan')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="form-group" id="office_bearer_pan_file_div"><label class="form-label">Upload PAN<span class="itsrequired"> *</span></label><input type="file" class="form-control" id="office_bearer_pan_file" name="office_bearer_pan_file[]" value="{{old('office_bearer_pan_file')[0] ?? '' }}" accept=".pdf" aria-describedby="inputGroupFileAddon01"><div class="office_bearer_pan_file_error"></div>@error('office_bearer_pan_file')<label class="error">{{ $message }}</label>@enderror</div></div></div><div class="row"><div class="col-md-3"><div class="form-group" id="office_bearer_name_as_aadhar_div"><label class="form-label">Name as per Aadhar<span class="itsrequired"> *</span></label><input type="text" id="office_bearer_name_as_aadhar" name="office_bearer_name_as_aadhar[]" value="{{old('office_bearer_name_as_aadhar')[0] ?? '' }}" class="form-control" placeholder="Office Bearer Name"><div class="office_bearer_name_as_aadhar_error"></div>@error('office_bearer_name_as_aadhar')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-2"><div class="form-group" id="office_bearer_dob_div"><label class="form-label">Date of Birth<span class="itsrequired"> *</span></label><input type="date" class="form-control" id="office_bearer_dob" name="office_bearer_dob[]" value="{{old('office_bearer_dob')[0] ?? '' }}" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01"><div class="office_bearer_dob_error"></div>@error('office_bearer_dob')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="form-group" id="office_bearer_aadhar_div"><label class="form-label">Aadhar No<span class="itsrequired"> *</span></label><input type="text" id="office_bearer_aadhar" name="office_bearer_aadhar[]" value="{{old('office_bearer_aadhar')[0] ?? '' }}" class="form-control" placeholder="Aadhar No"><div class="office_bearer_aadhar_error"></div>@error('office_bearer_aadhar')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-4"><div class="row align-items-center"><div class="col-md-9"><div class="form-group" id="office_bearer_aadhar_file_div"><label class="form-label">Upload Aadhar<span class="itsrequired"> *</span></label><input type="file" class="form-control" id="office_bearer_aadhar_file" value="{{ old('office_bearer_aadhar_file')[0] ?? '' }}" name="office_bearer_aadhar_file[]" accept=".pdf" aria-describedby="inputGroupFileAddon01"><div class="office_bearer_aadhar_file_error"></div>@error('office_bearer_aadhar_file')<label class="error">{{ $message }}</label>@enderror</div></div><div class="col-md-3"><div class="input-group-append"><button class="btn btn-danger" type="button" onclick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div></div></div></div></div></div></div>';
		objTo.appendChild(divtest)
	}

	function remove_education_fields(rid) {
		$('.removeclass' + rid).remove();
	}
</script>

@endsection