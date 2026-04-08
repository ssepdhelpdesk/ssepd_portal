@section('title') 
Pension || GP/Ward wise Pension Disburshed on {{ \Carbon\Carbon::parse($dailypensiondisbursementdata->disbursement_start_date)->format('D, d-M-Y') }}
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
		background-color: #f44336;
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
	.table-responsive-scroll {
		max-height: 500px;
		overflow-y: auto;
		overflow-x: auto;
		display: block;
		width: 100%;
	}
	.table-responsive-scroll table {
		width: 100%;
		border-collapse: collapse;
	}
	.table-responsive-scroll thead th {
		position: sticky;
		top: 0;
		z-index: 2;
		background-color: #f8f9fa;
	}
	.is-invalid {
		border: 1px solid red !important;
		background-color: #ffecec;
	}
	.row-error-msg {
		font-size: 12px;
		color: red;
	}
</style>
@endsection 

@section('content')
<div class="container-fluid">
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
			<button onclick="history.back()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info">
				<i class="fas fa-arrow-alt-circle-left"></i> Go Back
			</button>         
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					@include('dashboard.component.message')
					@if ($errors->any())
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

					<form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.dailypensiondisbursement.daily_pension_disbursement_approval_process', $dailypensiondisbursementdata->id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
						@csrf
						@method('post')                     

						<div class="form-body">
							<h5 class="card-title">GP/Ward wise Pension Disburshed on {{ \Carbon\Carbon::parse($dailypensiondisbursementdata->disbursement_start_date)->format('D, d-M-Y') }}</h5>
							<hr>
							<div class="table-responsive-scroll">
								<table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>{{ $dailypensiondisbursementdata->block_id ? 'Block Name' : 'ULB Name' }}</th>
											<th>{{ $dailypensiondisbursementdata->gp_id ? 'GP Name' : 'Ward Name' }}</th>
											<th>For the Month</th>
											<th>Disbursement Date</th>
											<th>MBPOAP (Below 80 Years)</th>
											<th>MBPOAP (Above 80 Years)</th>
											<th>MBPWP</th>
											<th>MBPDP</th>
											<th>MBPSDP (Below 80%)</th>
											<th>MBPSDP (Above 80%)</th>
											<th>MBPSDOAP</th>
											<th>MBPCLP</th>
											<th>MBPWP (Due to Aids)</th>
											<th>MBPDP (Due to Aids)</th>
											<th>MBPUMW</th>
											<th>Orphan due to Covid</th>
											<th>Widow due to Covid</th>
											<th>Divorcee or Destitute</th>
											<th>Transgender</th>
											<th>Death Reported</th>
											<th>No of Beneficiaries Received Normal Pension</th>
											<th>No of Beneficiaries Received Enhanced Pension</th>
										</tr>
									</thead>
									@php
									$fields = [
									'mbpy_oap_below_80_years',
									'mbpy_oap_above_80_years',
									'mbpy_wp',
									'mbpy_dp',
									'mbpy_sdp_below_80_percent',
									'mbpy_sdp_above_80_percent',
									'mbpy_sdoap',
									'mbpy_clp',
									'mbpy_wp_aids',
									'mbpy_dp_aids',
									'mbpy_unmarried_women',
									'mbpy_orphan_due_to_covide',
									'mbpy_widow_due_to_covid',
									'mbpy_divorce_or_destitute',
									'mbpy_transgender',
									'death_reported',
									'no_of_normal_pensioners',
									'no_of_ep_pensioners'
									];
									@endphp

									<tbody>
										<tr>
											{{-- Block/ULB Name --}}
											@php
											$isBlock = !is_null($dailypensiondisbursementdata->block_id);
											@endphp
											<td>
												<input type="hidden" name="gp_ward_id"
												value="{{ $isBlock ? $dailypensiondisbursementdata->block_id : $dailypensiondisbursementdata->municipality_id }}">

												<input 
												type="text" 
												name="block_ulb_name" 
												value="{{ 
													$isBlock 
													? optional($dailypensiondisbursementdata->block)->block_name 
													: optional($dailypensiondisbursementdata->municipality)->municipality_name 
												}}" 
												class="form-control readonly-input" 
												readonly>
											</td>

											{{-- GP/Ward Name --}}
											@php
											$isGP = !is_null($dailypensiondisbursementdata->gp_id);
											@endphp
											<td>
												<input type="hidden" name="gp_ward_id"
												value="{{ $isGP ? $dailypensiondisbursementdata->gp_id : $dailypensiondisbursementdata->ward_id }}">

												<input 
												type="text" 
												name="gp_ward_name" 
												value="{{ 
													$isGP 
													? optional($dailypensiondisbursementdata->grampanchayat)->gp_name 
													: optional($dailypensiondisbursementdata->ward)->ward_name 
												}}" 
												class="form-control readonly-input" 
												readonly>
											</td>

											{{-- For the Month --}}
											<td>{{ $forTheMonth }}</td>

											<td>
												<input type="date" name="disbursement_start_date" value="{{ old('disbursement_start_date', $dailypensiondisbursementdata->disbursement_start_date) }}" class="form-control" readonly>
												@error('disbursement_start_date.0')
												<div class="text-danger small mt-1">{{ $message }}</div>
												@enderror
											</td>

											@foreach($fields as $index => $field)
											@php
											$isReadonly = in_array($field, ['no_of_normal_pensioners', 'no_of_ep_pensioners']);
											@endphp
											<td>
												<input 
												type="number" 
												name="{{ $field }}[]" 
												value="{{ old($field.'.0', $dailypensiondisbursementdata->$field) }}" 
												class="form-control {{ $isReadonly ? 'readonly-input' : '' }}" 
												min="0" step="1" 
												placeholder="{{ $isReadonly ? 'Auto Calculated' : 'Enter beneficiary count' }}"
												{{ $isReadonly ? 'readonly' : '' }}>

												@error($field.'.0')
												<div class="text-danger small mt-1">{{ $message }}</div>
												@enderror
											</td>
											@endforeach
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						@php
						$today = \Carbon\Carbon::today();
						@endphp

						@if($today->between(\Carbon\Carbon::parse($startDate), \Carbon\Carbon::parse($endDate)))
						<div class="form-actions mt-3">
							<button type="submit" name="register" class="btn btn-primary text-white from-prevent-multiple-submits">
								<i class="spinner fa fa-spinner fa-spin"></i> Approve
							</button>
						</div>
						@else
						<div class="alert alert-warning mt-3">
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
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	function Validate() {
		let isValid = true;
		let rows = document.querySelectorAll("#example23 tbody tr");

		let atLeastOneRowCompleted = false;

		rows.forEach((row) => {
			let inputs = row.querySelectorAll("input[type='number'], input[type='date']");
			let hasAnyValue = false;
			let rowErrors = [];

			inputs.forEach(input => {
				if (input.value.trim() !== "") {
					hasAnyValue = true;
				}
			});

			if (hasAnyValue) {
				let rowComplete = true;
				inputs.forEach(input => {
					if (input.value.trim() === "") {
						isValid = false;
						rowComplete = false;
						rowErrors.push(input);
						input.classList.add("is-invalid");
					} else {
						input.classList.remove("is-invalid");
					}
				});

				if (rowComplete) {
					atLeastOneRowCompleted = true;
				}
			} else {
				inputs.forEach(input => input.classList.remove("is-invalid"));
			}

			if (rowErrors.length > 0) {
				if (!row.querySelector(".row-error-msg")) {
					let errorMsg = document.createElement("div");
					errorMsg.className = "row-error-msg text-danger small mt-1";
					errorMsg.innerText = "⚠ Please complete all fields in this row.";
					row.appendChild(errorMsg);
				}
			} else {
				let errorMsg = row.querySelector(".row-error-msg");
				if (errorMsg) errorMsg.remove();
			}
		});

		if (!atLeastOneRowCompleted) {
			Swal.fire({
				icon: 'warning',
				title: 'Incomplete Submission',
				text: 'Please fill at least one complete GP/Ward Beneficiary Count before submitting the form.',
				confirmButtonColor: '#3085d6',
				confirmButtonText: 'OK'
			});
			isValid = false;
		}

		return isValid;
	}
</script>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const normalFields = [
			'mbpy_oap_below_80_years', 'mbpy_wp', 'mbpy_dp', 'mbpy_sdp_below_80_percent',
			'mbpy_clp', 'mbpy_wp_aids', 'mbpy_dp_aids', 'mbpy_unmarried_women',
			'mbpy_orphan_due_to_covide', 'mbpy_widow_due_to_covid',
			'mbpy_divorce_or_destitute', 'mbpy_transgender'
		];

		const epFields = [
			'mbpy_oap_above_80_years', 'mbpy_sdp_above_80_percent', 'mbpy_sdoap'
		];

		function recalcRow(row) {
			let normalTotal = 0;
			let epTotal = 0;

			normalFields.forEach(field => {
				const input = row.querySelector(`input[name="${field}[]"]`);
				if (input && input.value.trim() !== "") {
					normalTotal += parseInt(input.value) || 0;
				}
			});

			epFields.forEach(field => {
				const input = row.querySelector(`input[name="${field}[]"]`);
				if (input && input.value.trim() !== "") {
					epTotal += parseInt(input.value) || 0;
				}
			});

			const normalField = row.querySelector(`input[name="no_of_normal_pensioners[]"]`);
			const epField = row.querySelector(`input[name="no_of_ep_pensioners[]"]`);

			if (normalField) normalField.value = normalTotal;
			if (epField) epField.value = epTotal;
		}

		document.querySelectorAll("#example23 tbody tr").forEach(row => {
			const allNumberInputs = row.querySelectorAll('input[type="number"]');
			allNumberInputs.forEach(input => {
				input.addEventListener('input', () => recalcRow(row));
			});
		});
	});
</script>
@endsection
