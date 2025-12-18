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
						<form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.schememigrationep.nsap_sanction_order_no_check_list') }}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
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
					<div class="col-sm-12 col-xs-12">
						@if($beneficiaries->isEmpty())
						
						@else
						<div class="table-responsive">
							<table id="example23" class="table table-bordered table-striped">
								<thead class="table-dark">
									<tr>
										<th>Sl.No</th>
										<th>Type</th>
										<th>Scheme</th>
										<th>Beneficiary Name</th>
										<th>Father / Husband</th>
										<th>Age</th>
										<th>Gender</th>
										<th>District</th>
										<th>Block / ULB</th>
										<th>GP / Ward</th>
										<th>Aadhaar</th>
										<th>NSAP Order No</th>
										<th>Status</th>
										<th>Migrate</th>
									</tr>
								</thead>
								<tbody>
									@foreach($beneficiaries as $index => $row)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>
											<span class="badge {{ $row['type']=='OAP' ? 'bg-primary' : 'bg-success' }}">
												{{ $row['type'] }}
											</span>
										</td>
										<td>{{ $row['scheme'] }}</td>
										<td>{{ $row['name'] }}</td>
										<td>{{ $row['father'] }}</td>
										<td>{{ $row['age'] }}</td>
										<td>{{ $row['gender'] }}</td>
										<td>{{ $row['district'] }}</td>
										<td>{{ $row['block_ulb'] }}</td>
										<td>{{ $row['gp_ward'] }}</td>
										<td>{{ $row['aadhaar'] }}</td>
										<td>{{ $row['nsap_no'] }}</td>
										<td>{{ $row['status'] }}</td>

										<td>
											@if($beneficiaries->count() === 1)
											<a href="{{ route($row['migration_link'], $row['id']) }}"
											target="_blank"
											class="btn btn-sm btn-warning">
											Migrate
										</a>
										@else
										<span class="text-danger fw-semibold">
											<small>Multiple beneficiary records have been found for the Sanction Order Number 
											<b>{{ $row['nsap_no'] }}</b>.  
											Scheme migration cannot be performed until this <b>duplication is resolved</b>.
											Please verify and correct the <b>duplicate Sanction Order Number records</b>, then retry the migration.</small>
										</span>
										@endif
									</td>
								</tr>
								@endforeach

							</tbody>
						</table>
					</div>
					@endif

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

			$.get("{{ route('admin.schememigrationep.check_benf_nsap_sanction_or_no') }}", 
				{ nsap_sanction_order_no: sanctionNo }, 
				function (res) {

					if (res.status === 0) {
						$('#check_nsap_sanction_order_no').html(
							'<span style="color:#FF0000">This NSAP Sanction Order Not is available in OldAge Pension, Please provide correct Sanction Order No.</span>'
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
						$('#submitBtn').removeClass('d-none');
					}

					else if (res.status === 3) {
						$('#check_nsap_sanction_order_no').html(
							`<span style="color:#01c0c8">
                    This NSAP Sanction Order No is registered with 
                    <b>OldAge Pensioner</b>: ${res.oldage.name_of_the_beneficiary}, 
                    from ${res.oldage.district}, ${res.oldage.block_or_ulb}, 
                    ${res.oldage.gp_or_ward}, ${res.oldage.village}.
                    <br><br>
                    Click on the Submit button to complete the migration process.
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
						$('#submitBtn').removeClass('d-none');
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
<script>
   $(function () {
     $('#example23').DataTable({
       processing: true,
       responsive: false,
       ordering: true,
       scrollX: true,
       lengthMenu: [[30, 500, 1000, -1], [30, 500, 1000, "All"]],
       dom: 'Blfrtip',
       buttons: [
         'copy', 'csv', 'excel', 'pdf', 'print'
      ]
   });
     $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
  });
</script>
@endsection