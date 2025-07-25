@section('title') 
Staff Details || Special School
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
			@can('user-create')
			<a href="{{ route('admin.users.create') }}"><button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success"><i class="fas fa-plus-square"></i> Add New</button></a>
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
					<div class="table-responsive m-t-40">
						<table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>Sl No</th>
									<th>School Name</th>
									<th>Name</th>
									<th>Engagement Date</th>
									<th>Designation</th>
									<th>Employment Type</th>
									<th>Basic Remuneration</th>
									<th>Mobile No</th>
									<th>Address</th>
									<th>Action</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Sl No</th>
									<th>School Name</th>
									<th>Name</th>
									<th>Engagement Date</th>
									<th>Designation</th>
									<th>Employment Type</th>
									<th>Basic Remuneration</th>
									<th>Mobile No</th>
									<th>Address</th>
									<th>Action</th>
								</tr>
							</tfoot>
							<tbody>
								@php $i = 1; @endphp
								@if ($specialSchoolStaff->isNotEmpty())
								@foreach($specialSchoolStaff as $key => $staffDetails)
								<tr>
									<td>{{ $i++ }}</td>
									<td>{{ $staffDetails->special_school_name }}</td>
									<td>{{ $staffDetails->special_school_staff_name }}</td>
									<td>{{ \Carbon\Carbon::parse($staffDetails->staff_engagement_date)->format('jS F, Y') }}</td>
									<td>
										@php
										$designations = [
										1 => 'HM / Principal / Sr. SES',
										2 => 'Trained Graduate',
										3 => 'Trained Intermediate',
										4 => 'Trained Matric',
										5 => 'MCD / DHLS',
										6 => 'Craft Teacher',
										7 => 'Art Teacher',
										8 => 'PET',
										9 => 'Music Teacher',
										10 => 'Mobility Teacher',
										11 => 'Matron / Warden',
										12 => 'Clerk / Accountant',
										13 => 'Cook',
										14 => 'Attendant',
										15 => 'Sweeper / Watchman',
										];
										@endphp

										{{ $designations[$staffDetails->staff_designation] ?? 'N/A' }}
									</td>
									<td>
										@php
										$employmentTypes = [
										1 => 'Regular',
										2 => 'Contractual',
										3 => 'Part-Time',
										];
										@endphp

										{{ $employmentTypes[$staffDetails->staff_employment_type] ?? 'N/A' }}
									</td>
									<td>{{ $staffDetails->basic_remuneration }}</td>
									<td>{{ $staffDetails->staff_mob_no }}</td>
									<td>{{ $staffDetails->full_address }}</td>
									<td>
										<div class="btn-group">
											<button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Action
											</button>
                                 <!-- <div class="dropdown-menu">
                                    @can('special-school-show')
                                    <a class="dropdown-item" href="{{route('admin.specialschool.create')}}">Add Staff Details</a>
                                    @endcan
                                 </div> -->
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