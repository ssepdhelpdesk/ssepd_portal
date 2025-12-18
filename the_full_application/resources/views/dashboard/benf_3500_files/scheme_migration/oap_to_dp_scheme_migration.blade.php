@section('title') 
EP Pension || Scheme Migration
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
						<form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.schememigrationep.oap_to_dp') }}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
							@csrf
							@method('post')
							<div class="form-body">
								<h5 class="card-title">Enter Sanction Order No</h5>
								<hr>
								<div class="row align-items-end">
									<div class="col-md-4 col-sm-8">
										<div class="form-group" id="nsap_sanction_order_no_div">
											<label class="form-label">NSAP Sanction Order No <span class="itsrequired">*</span></label>
											<div class="input-group">
												<input type="text" id="nsap_sanction_order_no" name="nsap_sanction_order_no" value="{{ old('nsap_sanction_order_no') }}" class="form-control" placeholder="NSAP Sanction Order No">
												<button type="submit" id="submitBtn" class="btn btn-primary d-none"> Submit </button>
											</div>
											<div id="nsap_sanction_order_no_error"></div>
											<div id="check_nsap_sanction_order_no"></div>
											@error('nsap_sanction_order_no')
											<label class="error">{{ $message }}</label>
											@enderror
										</div>
									</div>
								</div>
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
$(document).ready(function () {

    $("#nsap_sanction_order_no").blur(function () {

        const sanctionNo = $(this).val().trim();
        $('#check_nsap_sanction_order_no').html('');
        $('#submitBtn').addClass('d-none');

        if (!sanctionNo) {
            $('#check_nsap_sanction_order_no').html(
                '<span style="color:#FF0000">Please provide NSAP Sanction Order No.</span>'
            );
            return;
        }

        $.get("{{ route('admin.schememigrationep.check_oldage_benf_nsap_sanction_or_no') }}", 
        { nsap_sanction_order_no: sanctionNo }, 
        function (res) {

            if (res.status === 0) {
                $('#check_nsap_sanction_order_no').html(
                    '<span style="color:#03713E">This NSAP Sanction Order No is available.</span>'
                );
            }

            else if (res.status === 1) {
                $('#check_nsap_sanction_order_no').html(
                    `<span style="color:#FF0000">
                    This NSAP Sanction Order No is already registered with 
                    <b>OldAge Pensioner</b>: ${res.oldage.name_of_the_beneficiary}, 
                    from ${res.oldage.district}, ${res.oldage.block_or_ulb}, 
                    ${res.oldage.gp_or_ward}, ${res.oldage.village}
                    <br><br>
                    as well as <b>Disability Pensioner</b>: ${res.disability.name_of_the_beneficiary}, 
                    from ${res.disability.district}, ${res.disability.block_or_ulb}, 
                    ${res.disability.gp_or_ward}, ${res.disability.village}.
                    <br><br>
                    So you are unable to Migrate this Beneficiary, Please contact with Administrator.
                    </span>`
                );
            }

            else if (res.status === 3) {
                $('#check_nsap_sanction_order_no').html(
                    `<span style="color:#01c0c8">
                    This NSAP Sanction Order No is registered with 
                    <b>OldAge Pensioner</b>: ${res.oldage.name_of_the_beneficiary}, 
                    from ${res.oldage.district}, ${res.oldage.block_or_ulb}, 
                    ${res.oldage.gp_or_ward}, ${res.oldage.village}.
                    </span>`
                );

                $('#submitBtn').removeClass('d-none');
            }

            else if (res.status === 4) {
                $('#check_nsap_sanction_order_no').html(
                    `<span style="color:#FF0000">
                    This NSAP Sanction Order No is registered with 
                    <b>Disability Pensioner</b>: ${res.disability.name_of_the_beneficiary}, 
                    from ${res.disability.district}, ${res.disability.block_or_ulb}, 
                    ${res.disability.gp_or_ward}, ${res.disability.village}.
                    <br><br>
                    So you are unable to Migrate this Beneficiary from Oldage to Disability, Please contact with Administrator.
                    </span>`
                );
            }

            else if (res.status === 2) {
                $('#check_nsap_sanction_order_no').html(
                    '<span style="color:#FF0000">Please provide a valid NSAP Sanction Order No.</span>'
                );
            }
        })
        .fail(function () {
            $('#check_nsap_sanction_order_no').html(
                '<span style="color:#FF0000">An error occurred. Please try again.</span>'
            );
        });

    });

});
</script>

@endsection