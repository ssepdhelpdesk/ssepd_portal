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
					<div class="table-responsive">
						<form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.part_four_store', $id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
							@csrf
							@method('post')
							<div class="form-body">
								<h5 class="card-title">Other Recognitions/Affiliations Under Government Of Odisha</h5>
								<hr>
								<div class="row">
									<div class="col-lg-12" id="on_examination_div">
										<div class="form-group">
											<div class="table-responsive col-lg-12 col-md-12">
												<input type="button" class="btn btn-primary btn-sm" value="Add Row" onclick="addRow('dataTable')" />
												<input type="button" class="btn btn-danger btn-sm" value="Delete Row" onclick="deleteRow('dataTable')" />
												<table id="dataTable" width="350px" border="1" class="table color-table info-table">
													<thead>
														<tr>
															<th>Chk Selection</th>
															<th>Project Title</th>
															<th>Approving Authority</th>
															<th>Date of Approval</th>
															<th>Project Location</th>
															<th>No. of Beneficiaries</th>
															<th>Project Cost</th>
															<th>Current Status</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>
																<div class="form-group">
																	<input type="checkbox" name="chk">
																</div>
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="project_title[]" class="form-control">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="approving_authority[]" class="form-control">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="date" name="date_of_approval[]" class="form-control" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<textarea name="project_location[]" rows="4" cols="30">At-    , Po-    , PS-    , Block/ULB-    , District-     , PIN- </textarea>
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="number" name="no_of_beneficiaries[]" class="form-control" min="1">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="number" name="project_cost[]" class="form-control" step="0.01" min="0">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="current_status[]" class="form-control">
																</div> 
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<h5 class="card-title">Trained Staff Details</h5>
								<hr>
								<div class="row">
									<div class="col-lg-12" id="on_examination_div">
										<div class="form-group">
											<div class="table-responsive col-lg-12 col-md-12">
												<input type="button" class="btn btn-primary btn-sm" value="Add Row" onclick="addRow('dataTable1')" />
												<input type="button" class="btn btn-danger btn-sm" value="Delete Row" onclick="deleteRow('dataTable1')" />
												<table id="dataTable1" width="350px" border="1" class="table color-table info-table">
													<thead>
														<tr>
															<th>Chk Selection</th>
															<th>Name</th>
															<th>Designation</th>
															<th>Role</th>
															<th>Category</th>
															<th>Type</th>
															<th>Qualification</th>
															<th>Date of Joining</th>
															<th>Aadhar Number</th>
															<th>Contact No</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>
																<div class="form-group">
																	<input type="checkbox" name="chk">
																</div>
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="staff_name[]" class="form-control">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="staff_designation[]" class="form-control">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="staff_role[]" class="form-control">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<select class="form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="staff_category[]">
																		<option value="">--Select--</option>
																		<option value="1">Professional/ Technical</option>
																		<option value="2">Assisting/ Attending</option>
																		<option value="3">Community Workers</option>
																		<option value="4">Others(Specify)</option>
																	</select>
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<select class="form-control form-select" data-placeholder="Choose a Category" tabindex="1" name="staff_category_type[]">
																		<option value="">--Select--</option>
																		<option value="1">Fulltime</option>
																		<option value="2">Parttime</option>
																	</select>
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="staff_qualification[]" class="form-control">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="date" name="staff_date_of_joining[]" class="form-control" max="{{ date('Y-m-d') }}" aria-describedby="inputGroupFileAddon01">
																</div> 
															</td>
															<td>
																<div class="form-group">
																	<input type="text" name="staff_aadhar_number[]" class="form-control staff-aadhar-input" onblur="validateAadhaar(this)">
																</div>
																<small class="aadhaar-error text-danger"></small>
															</td>
															<td>
																<div class="form-group">
																	<input type="tel" name="staff_mob_no[]" class="form-control" pattern="[6-9]{1}[0-9]{9}" maxlength="10">
																</div> 
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
								</div>
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
<SCRIPT language="javascript">
	function addRow(tableID) {
		var table = document.getElementById(tableID);
		var tbody = table.getElementsByTagName('tbody')[0];
		var rowCount = tbody.rows.length;
		var row = tbody.insertRow(rowCount);
		var colCount = tbody.rows[0].cells.length;

		for (var i = 0; i < colCount; i++) {
			var newcell = row.insertCell(i);
			newcell.innerHTML = tbody.rows[0].cells[i].innerHTML;
			var child = newcell.childNodes[0];
			if (child) {
				switch (child.type) {
				case "text":
				case "textarea":
					child.value = "";
					break;
				case "checkbox":
					child.checked = false;
					break;
				case "select-one":
					child.selectedIndex = 0;
					break;
				}
			}
		}
	}
	function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var tbody = table.getElementsByTagName('tbody')[0];
			var rowCount = tbody.rows.length;

			for (var i = 0; i < rowCount; i++) {
				var row = tbody.rows[i];
				var chkbox = row.cells[0].querySelector('input[type="checkbox"]');
				if (chkbox && chkbox.checked) {
					if (rowCount <= 1) {
						alert("Cannot delete all the rows.");
						return;
					}
					tbody.deleteRow(i);
					rowCount--;
					i--;
				}
			}
		} catch (e) {
			alert(e);
		}
	}
</SCRIPT>
<script>
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
			let isValid = true;

			document.querySelectorAll('#dataTable tbody tr').forEach((row) => {
				const title = row.querySelector("[name='project_title[]']");
				const authority = row.querySelector("[name='approving_authority[]']");
				const date = row.querySelector("[name='date_of_approval[]']");
				const location = row.querySelector("[name='project_location[]']");
				const beneficiaries = row.querySelector("[name='no_of_beneficiaries[]']");
				const cost = row.querySelector("[name='project_cost[]']");
				const status = row.querySelector("[name='current_status[]']");

				if (!title.value.trim()) { markError(title, 'Project Title is required'); isValid = false; }
				if (!authority.value.trim()) { markError(authority, 'Approving Authority is required'); isValid = false; }
				if (!date.value) { markError(date, 'Date of Approval is required'); isValid = false; }
				if (!location.value.trim()) { markError(location, 'Project Location is required'); isValid = false; }
				if (!beneficiaries.value || beneficiaries.value <= 0) { markError(beneficiaries, 'Enter valid number of beneficiaries'); isValid = false; }
				if (!cost.value || cost.value <= 0) { markError(cost, 'Enter valid project cost'); isValid = false; }
				if (!status.value.trim()) { markError(status, 'Current Status is required'); isValid = false; }
			});

			document.querySelectorAll('#dataTable1 tbody tr').forEach((row) => {
				const name = row.querySelector("[name='staff_name[]']");
				const designation = row.querySelector("[name='staff_designation[]']");
				const role = row.querySelector("[name='staff_role[]']");
				const category = row.querySelector("[name='staff_category[]']");
				const type = row.querySelector("[name='staff_category_type[]']");
				const qualification = row.querySelector("[name='staff_qualification[]']");
				const joining = row.querySelector("[name='staff_date_of_joining[]']");
				const aadhar = row.querySelector("[name='staff_aadhar_number[]']");
				const phone = row.querySelector("[name='staff_mob_no[]']");

				if (!name.value.trim()) { markError(name, 'Staff Name is required'); isValid = false; }
				if (!designation.value.trim()) { markError(designation, 'Designation is required'); isValid = false; }
				if (!role.value.trim()) { markError(role, 'Role is required'); isValid = false; }
				if (!category.value) { markError(category, 'Select Category'); isValid = false; }
				if (!type.value) { markError(type, 'Select Type'); isValid = false; }
				if (!qualification.value.trim()) { markError(qualification, 'Qualification is required'); isValid = false; }
				if (!joining.value) { markError(joining, 'Joining date is required'); isValid = false; }
				if (!aadhar.value.trim()) { markError(aadhar, 'Aadhar Number is required'); isValid = false; }
				if (!phone.value.match(/^[6-9][0-9]{9}$/)) { markError(phone, 'Valid Mobile Number is required'); isValid = false; }
			});

			return isValid;
		}

		function markError(input, message) {
			input.style.borderColor = 'red';
			const existing = input.parentNode.querySelector('.error-msg');
			if (!existing) {
				const error = document.createElement('div');
				error.className = 'error-msg';
				error.style.color = 'red';
				error.innerText = message;
				input.parentNode.appendChild(error);
			}
		}

		function clearErrors() {
			document.querySelectorAll('.error-msg').forEach(el => el.remove());
			document.querySelectorAll('input, textarea, select').forEach(el => el.style.borderColor = '');
		}
	});
</script>
<script>
	const Verhoeff = {
		d: [[0,1,2,3,4,5,6,7,8,9],[1,2,3,4,0,6,7,8,9,5],[2,3,4,0,1,7,8,9,5,6],
			[3,4,0,1,2,8,9,5,6,7],[4,0,1,2,3,9,5,6,7,8],[5,9,8,7,6,0,4,3,2,1],
			[6,5,9,8,7,1,0,4,3,2],[7,6,5,9,8,2,1,0,4,3],[8,7,6,5,9,3,2,1,0,4],
			[9,8,7,6,5,4,3,2,1,0]],
		p: [[0,1,2,3,4,5,6,7,8,9],[1,5,7,6,2,8,3,0,9,4],[5,8,0,3,7,9,6,1,4,2],
			[8,9,1,6,0,4,3,5,2,7],[9,4,5,3,1,2,6,8,7,0],[4,2,8,6,5,7,3,9,0,1],
			[2,7,9,3,8,0,6,4,1,5],[7,0,4,6,9,1,3,2,5,8]],
		check(str) {
			let c = 0;
			str.replace(/\D+/g, "").split("").reverse().forEach((num, i) => {
				c = Verhoeff.d[c][Verhoeff.p[i % 8][parseInt(num, 10)]];
			});
			return c;
		}
	};

	function validateAadhaar(input) {
		const uid = input.value.trim();
		if (uid === "") return;

		clearError(input);

		if (!/^\d{12}$/.test(uid)) {
			markError(input, 'Aadhaar must be a 12-digit number!');
			input.value = '';
			return;
		}

		if (Verhoeff.check(uid) !== 0) {
			markError(input, 'Invalid Aadhaar number!');
			input.value = '';
			return;
		}

		fetch(`{{ route('admin.ngo.check_trained_staff_aadhar_no') }}?staff_aadhar_number=${uid}`)
			.then(response => response.json())
			.then(data => {
				if (data === 1) {
					markError(input, 'This Aadhaar number is already registered!');
					input.value = '';
				}
			})
			.catch(error => {
				console.error('Error checking Aadhaar:', error);
				markError(input, 'Server error occurred while checking Aadhaar.');
			});
	}

	function markError(input, message) {
		input.style.borderColor = 'red';
		const existing = input.parentNode.querySelector('.error-msg');
		if (!existing) {
			const error = document.createElement('div');
			error.className = 'error-msg';
			error.style.color = 'red';
			error.innerText = message;
			input.parentNode.appendChild(error);
		}
	}

	function clearError(input) {
		input.style.borderColor = '';
		const existing = input.parentNode.querySelector('.error-msg');
		if (existing) {
			existing.remove();
		}
	}
</script>
@endsection