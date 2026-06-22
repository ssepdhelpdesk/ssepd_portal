@section('title') 
PIA || Institute Beneficiary List
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
						<table id="example23" class="display table table-hover table-striped border" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>Sl No</th>
									<th>PIA/NGO Name</th>
									<th>Institute Name</th>
									<th>Imgage</th>
									<th>Beneficiary Name</th>
									<th>Benf F/H Name</th>
									<th>Mobile No</th>
									<th>DOB</th>
									<th>Age</th>
									<th>Disability Category</th>
									<th>Date of Joining</th>
									<th>Address</th>
									<th>Action</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Sl No</th>
									<th>PIA/NGO Name</th>
									<th>Institute Name</th>
									<th>Imgage</th>
									<th>Beneficiary Name</th>
									<th>Benf F/H Name</th>
									<th>Mobile No</th>
									<th>DOB</th>
									<th>Age</th>
									<th>Disability Category</th>
									<th>Date of Joining</th>
									<th>Address</th>
									<th>Action</th>
								</tr>
							</tfoot>
							<tbody>
								@php $i = 1; @endphp
								@if ($beneficiary_details->isNotEmpty())
								@foreach($beneficiary_details as $key => $beneficiary)
								<tr>
									<td>{{ $i++ }}</td>
									<td class="wrap-text">{{ ucwords(strtolower($beneficiary->institute->pia_name ?? 'Not Available')) }}</td>
									<td class="wrap-text">{{ ucwords(strtolower($beneficiary->institute->institute_name ?? 'Not Available')) }}</td>
									<td>
										@if($beneficiary->beneficiary_file)
										<img src="{{ asset('storage/'.$beneficiary->beneficiary_file) }}" alt="Beneficiary Image" class="img-circle" style="height: 50px; width: 50px;">
										@else
										Not Available
										@endif
									</td>
									<td>{{ ucwords(strtolower($beneficiary->name_of_the_beneficiary ?? 'Not Available')) }}</td>
									<td>{{ $beneficiary->father_or_husband_name ?? 'Not Available' }}</td>
									<td>{{ $beneficiary->beneficiary_mobile ?? 'Not Available' }}</td>
									<td>{{ $beneficiary->date_of_birth 
										? \Carbon\Carbon::parse($beneficiary->date_of_birth)->format('d M Y') 
										: 'Not Available' 
									}}</td>
									<td>{{ $beneficiary->age ?? 'Not Available' }}</td>
									<td>{{ $beneficiary->disability_category ?? 'Not Available' }}</td>
									<td>{{ $beneficiary->date_of_birth 
										? \Carbon\Carbon::parse($beneficiary->date_of_birth)->format('d M Y') 
										: 'Not Available' 
									}}</td>
									<td class="wrap-text">
										@if($beneficiary->benf_address_type == 1)

										At: {{ $beneficiary->village->village_name ?? 'Not Available' }},
										GP: {{ $beneficiary->grampanchayat->gp_name ?? 'Not Available' }},
										Block: {{ $beneficiary->block->block_name ?? 'Not Available' }},
										District: {{ $beneficiary->district->district_name ?? 'Not Available' }}

										@elseif($beneficiary->benf_address_type == 2)

										Ward: {{ $beneficiary->ward->ward_name ?? 'Not Available' }},
										Municipality: {{ $beneficiary->municipality->municipality_name ?? 'Not Available' }},
										District: {{ $beneficiary->district->district_name ?? 'Not Available' }}

										@else

										Not Available

										@endif
									</td>
									<td>
										<div class="btn-group">
											<button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Action1
											</button>
											<div class="dropdown-menu">
												<!-- @can('special-school-delete')
												<a class="dropdown-item" href="" id="delete">Delete Staff Details</a>
												@endcan -->
											</div>
										</div>
									</td>
								</tr>
								@endforeach
								@endif
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
			responsive: true,
			ordering: true,
			lengthMenu: [[10, 500, 1000, -1], [10, 500, 1000, "All"]],
			dom: 'Blfrtip',
			buttons: [
				'copy', 'csv', 'excel', 'pdf', 'print'
			]
		});
		$('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
	});   
</script>
@endsection