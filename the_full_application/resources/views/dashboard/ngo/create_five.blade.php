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
						<form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.part_five_store', $id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
							@csrf
							@method('post')
							<div class="form-body">
								<h5 class="card-title">List Of Beneficiaries: <a href="{{ url('storage/ngo_files/NGOs_List_Of_Beneficiaries.xlsx') }}">Download the sample Excel file for uploading beneficiary details</a></h5>
								<ul class="list-group">
									<li class="list-group-item list-group-item-warning">
										<b>Please download the .xlsx file provided above, fill in the beneficiary details as instructed, and upload it here.</b>
										<ul>
											<li><strong>beneficiary_name:</strong> Enter the full name of the beneficiary.</li>
											<li><strong>gender:</strong> Must be one of the following values: <i>Male</i>, <i>Female</i>, or <i>Other</i>.</li>
											<li><strong>date_of_birth:</strong> Use the format <code>DD-MM-YYYY</code>. For example: 07-02-1992.</li>
											<li><strong>qualification:</strong> Specify the beneficiary’s educational qualification.</li>
											<li><strong>date_of_association:</strong> Use the format <code>DD-MM-YYYY</code>. For example: 31-03-2025.</li>
											<li><strong>aadhar_number:</strong> Must be a 12-digit numeric value. For example: 381667521475.</li>
											<li><strong>mobile_no:</strong> Must be a 10-digit numeric value. For example: 7008731607.</li>
										</ul>
									</li>
								</ul>
								<hr>
								<div class="row">
									<div class="col-lg-12">
										<div class="form-group" id="ngo_org_beneficiaries_excel_file_div">
											<label class="form-label">Upload List Of Beneficiaries (.xlsx File)<span class="itsrequired"> *</span></label>
											<input type="file" class="form-control" id="ngo_org_beneficiaries_excel_file" value="{{old('ngo_org_beneficiaries_excel_file')}}" name="ngo_org_beneficiaries_excel_file" aria-describedby="inputGroupFileAddon01">
											<div id="ngo_org_beneficiaries_excel_file_error"></div>                                 
											@error('ngo_org_beneficiaries_excel_file')
											<label class="error">{{ $message }}</label>
											@enderror
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
<script>
	document.getElementById('ngo_org_beneficiaries_excel_file').addEventListener('change', function(event) {
		const file = event.target.files[0];
		const errorDiv = document.getElementById('ngo_org_beneficiaries_excel_file_error');
		const maxFileSize = 30 * 1024 * 1024;

		errorDiv.innerHTML = '';

		if (!file) {
			return;
		}

		
		if (file.size > maxFileSize) {
			errorDiv.innerHTML = '<label class="error">File size must be less than 30MB.</label>';
			event.target.value = '';
			return;
		}
	});
</script>
@endsection