@section('title') 
SSEPD || Stall Application
@endsection 

@extends('frontend.layouts.main')

@section('style')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection 

@section('content')
<!-- Inner Banner -->
<div class="inner-banner inner-banner-bg13">
	<div class="container">
		<div class="inner-title text-center">
			<h3>Stall Application – International Day of Persons with Disabilities 2025</h3>
			<ul>
				<li>
					<a href="{{route('frontend.disabilitydaystallregistration.index')}}">Home</a>
				</li>
				<li>Stall Application</li>
			</ul>
		</div>
	</div>
</div>
<!-- Inner Banner End -->

<!-- Contact Widget Area -->
<div class="contact-widget-area pb-70">
	<div class="container">

		<div class="section-title text-center mb-45 pt-30">
			@if(session('success'))
			<div class="alert alert-success">{!! session('success') !!}</div>
			@endif

			@if(session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
			@endif
		</div>

		<div class="contact-form">

			<div class="table-responsive mt-4">
				<table id="stallTable" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Sl No</th>
							<th>Registration No</th>
							<th>Organization Name</th>
							<th>Contact Person</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Purpose</th>
							<th>Address</th>
							<th>Applied Date</th>
							<th>Applied Time</th>
						</tr>
					</thead>

					<tbody>
						@foreach($disabilityDayStallRegistrationData as $key => $item)
						<tr>
							<td>{{ $key + 1 }}</td>

							<td><strong style="color:red;">{{ $item->registration_number }}</strong></td>

							<td>{{ $item->name_of_the_organization }}</td>
							<td>{{ $item->contact_person_name }}</td>
							<td>{{ $item->email }}</td>
							<td>{{ $item->phone_number }}</td>
							<td>{{ $item->purpose_of_requirement_of_stall }}</td>
							<td>{{ $item->organization_address }}</td>

							<td>{{ \Carbon\Carbon::parse($item->created_date)->format('d M Y') }}</td>
							<td>{{ \Carbon\Carbon::parse($item->created_time)->format('h:i A') }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

		</div>
	</div>
</div>
<!-- Contact Widget Area End -->
@endsection 

@section('script')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#stallTable').DataTable({
        "pageLength": 30,
        "ordering": true,
        "searching": true,
        "lengthMenu": [30, 50, 500, 1000]
    });
});
</script>
@endsection
