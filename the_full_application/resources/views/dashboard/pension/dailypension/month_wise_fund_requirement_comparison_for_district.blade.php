@section('title') 
Pension || Monthly Fund Comparison
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
	.wrap-text {
		white-space: normal !important;
		word-break: break-word;
		max-width: 200px;
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
					<div class="table-responsive m-t-40">
						<div class="card-header bg-light mb-3">
							<h5 class="mb-2">📅 Filter by Month</h5>
							<form method="GET" action="{{ route('admin.dailypensiondisbursement.month_wise_fund_requirement_comparison_for_district') }}" class="d-flex align-items-center">
								<div class="d-flex align-items-center flex-wrap">
									<label class="me-2 fw-bold">From Month:</label>
									<select name="from_the_month" class="form-select w-auto me-3">
										@foreach($dateConfig as $config)
										<option value="{{ $config->for_the_month }}" {{ $from_the_month == $config->for_the_month ? 'selected' : '' }}>
											{{ $config->for_the_month }}
										</option>
										@endforeach
									</select>

									<label class="me-2 fw-bold">To Month:</label>
									<select name="to_the_month" class="form-select w-auto me-3">
										@foreach($dateConfig as $config)
										<option value="{{ $config->for_the_month }}" {{ $to_the_month == $config->for_the_month ? 'selected' : '' }}>
											{{ $config->for_the_month }}
										</option>
										@endforeach
									</select>

									<button type="submit" class="btn btn-primary">
										<i class="bi bi-search me-1"></i> Compare
									</button>
								</div>
							</form>


						</div>
						<table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
							<thead class="table-light">
								<tr class="text-center">
									<th>Sl. No</th>
									<th>District</th>
									<th>Block / Municipality</th>
									<th>Total Beneficiaries ({{ $from_the_month }})</th>
									<th>Total Beneficiaries ({{ $to_the_month }})</th>
									<th>Fund Requirement ({{ $from_the_month }})</th>
									<th>Fund Requirement ({{ $to_the_month }})</th>
									<th>Difference (₹)</th>
									<th>Remarks</th>
								</tr>
							</thead>

							<tbody>
								@forelse($comparisonData as $index => $data)
								<tr>
									<td class="text-center">{{ $index + 1 }}</td>
									<td>{{ $data->district_name ?? '-' }}</td>
									<td>{{ $data->block_or_municipality_name ?? '-' }}</td>
									<td class="text-end">{{ number_format($data->beneficiaries_from_month ?? 0) }}</td>
									<td class="text-end">{{ number_format($data->beneficiaries_to_month ?? 0) }}</td>
									<td class="text-end">₹{{ number_format($data->funds_from_month ?? 0, 2) }}</td>
									<td class="text-end">₹{{ number_format($data->funds_to_month ?? 0, 2) }}</td>
									<td class="text-end fw-bold {{ ($data->difference_of_funds ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
										₹{{ number_format($data->difference_of_funds ?? 0, 2) }}
									</td>
									<td>{{ $data->remarks ?? '-' }}</td>
								</tr>
								@empty
								<tr>
									<td colspan="11" class="text-center text-muted">No data found for the selected months.</td>
								</tr>
								@endforelse
							</tbody>
						</table>

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