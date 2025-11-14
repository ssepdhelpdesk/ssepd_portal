@section('title') 
SSEPD || Stall Application
@endsection 

@extends('frontend.layouts.main')

@section('style')
<!-- DataTables Core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- Buttons Extension CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- Responsive Extension CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endsection 

@section('content')
<!-- Inner Banner -->
<div class="inner-banner inner-banner-bg13">
	<div class="container">
		<div class="inner-title text-center">
			<h3>Stall Application – International Day of Persons with Disabilities 2025</h3>
			<ul>
				<li><a href="{{ route('frontend.disabilitydaystallregistration.index') }}">Home</a></li>
				<li>Stall Application</li>
			</ul>
		</div>
	</div>
</div>

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
				<table id="stallTable" class="table table-bordered table-striped nowrap" style="width:100%;">
					<thead>
						<tr>
							<th>Sl No</th>							
							<th>Organization Name</th>
							<th>Contact Person</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Registration No</th>
							<th>Purpose</th>
							<th>Address</th>
							<th>Applied Date & Time</th>
							<th>Allotted Stall</th>
						</tr>
					</thead>

					<tbody>
						@foreach($disabilityDayStallRegistrationData as $key => $item)
						<tr>
							<td>{{ $key + 1 }}</td>							
							<td>{{ $item->name_of_the_organization }}</td>
							<td>{{ $item->contact_person_name }}</td>
							<td>{{ $item->email }}</td>
							<td>{{ $item->phone_number }}</td>
							<td><strong style="color:red;">{{ $item->registration_number }}</strong></td>
							<td>{{ $item->purpose_of_requirement_of_stall }}</td>
							<td>{{ $item->organization_address }}</td>
							<td>{{ \Carbon\Carbon::parse($item->created_date)->format('d M Y') }} {{ \Carbon\Carbon::parse($item->created_time)->format('h:i A') }}</td>
							<td>{{ $item->allotted_stall_no }}</td>
						</tr>
						@endforeach
					</tbody>

				</table>
			</div>

		</div>
	</div>
</div>
@endsection 

@section('script')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables Core JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Buttons Extension JS -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Responsive Extension JS -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function () {
    $('#stallTable').DataTable({
        responsive: true,
        pageLength: 30,
        ordering: true,
        searching: true,
        lengthMenu: [30, 50, 500, 1000],

        dom: 'Bfrtip',  // Buttons + search + pagination

        buttons: [
            { extend: 'copy', text: 'Copy' },
            { extend: 'csv', text: 'CSV Export' },
            { extend: 'excel', text: 'Excel Export' },
            { extend: 'pdf', text: 'PDF Export' },
            { extend: 'print', text: 'Print Table' }
        ]
    });
});
</script>
@endsection
