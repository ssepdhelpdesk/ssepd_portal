@section('title') 
SSEPD WEBSITE
@endsection 
@extends('website.layout.mainlayout')
@section('style')
<!-- Summernote JS -->
<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/summernote/summernote-lite.min.css') }}">

<!-- Datetimepicker CSS -->
<link rel="stylesheet" href="{{ asset('website_assets/assets/css/bootstrap-datetimepicker.min.css') }}">

<!-- Daterangepicker CSS -->
<link rel="stylesheet" href="{{ asset('website_assets/assets/plugins/daterangepicker/daterangepicker.css') }}">
@endsection 
@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-bar text-center">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-12">
				<h2 class="breadcrumb-title mb-2">Tickets</h2>
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb justify-content-center mb-0">
						<li class="breadcrumb-item"><a href="index.html">Home</a></li>
						<li class="breadcrumb-item active" aria-current="page">Tickets</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
</div>
<!-- /Breadcrumb -->

<div class="content">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="tickets">
					<div class="d-flex align-items-center justify-content-between flex-wrap page-title">
						<h5>Support Tickets</h5>
						<a href="#" class="btn btn-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#add_ticket"><i class="isax isax-add-circle me-2 fs-10"></i>Add Ticket</a>
					</div> 
					<div class="row">
						<div class="col-md-6 col-xl-4">
							<div class="card">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<span class="icon-box bg-primary-transparent me-3 flex-shrink-0">
											<img src="{{ asset('website_assets/assets/img/icon/graduation.svg') }}" alt="">
										</span>
										<div>
											<p class="mb-1">Total Tickets</p>
											<h4 class="fw-bold">50</h4>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-xl-4">
							<div class="card">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<span class="icon-box bg-secondary-transparent me-3 flex-shrink-0">
											<img src="{{ asset('website_assets/assets/img/icon/book.svg') }}" alt="">
										</span>
										<div>
											<p class="mb-1">Opened Tickets</p>
											<h4 class="fw-bold">30</h4>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-xl-4">
							<div class="student-info">
								<div class="d-flex align-items-center">
									<span class="icon-box bg-success-transparent me-3 flex-shrink-0">
										<img src="{{ asset('website_assets/assets/img/icon/bookmark.svg') }}" alt="">
									</span>
									<div>
										<span class="d-block">Closed Tickets</span>
										<h4 class="fs-24 mt-1">25</h4>
									</div>
								</div>
							</div>
						</div>
					</div> 
					<div class="row align-items-center mb-2">
						<div class="col-md-8">
							<div class="d-flex align-items-center flex-wrap">
								<div class="mb-3">
									<div class="dropdown me-3">
										<a href="javascript:void(0);" class="dropdown-toggle text-gray-6 btn  rounded border d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
											Category
										</a>
										<ul class="dropdown-menu dropdown-menu-end">
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Mailing Issues</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Language Issues</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Installation Error</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="mb-3">
									<div class="dropdown me-3">
										<a href="javascript:void(0);" class="dropdown-toggle text-gray-6 btn  rounded border d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
											Priority
										</a>
										<ul class="dropdown-menu dropdown-menu-end">
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">High</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Low</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Medium</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="mb-3">
									<div class="dropdown">
										<a href="javascript:void(0);" class="dropdown-toggle text-gray-6 btn  rounded border d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
											Status
										</a>
										<ul class="dropdown-menu dropdown-menu-end">
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Opened</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Inprogress</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Closed</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="input-icon mb-3">
								<span class="input-icon-addon">
									<i class="isax isax-search-normal-14"></i>
								</span>
								<input type="email" class="form-control form-control-md" placeholder="Search">
							</div>
						</div>
					</div>  
					<div class="table-responsive custom-table">
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th>Ticket ID</th>
									<th>Date</th>
									<th>Subject</th>
									<th>Priority</th>
									<th>Category</th>
									<th>Status</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#ticket_details">#TIC010</a></td>
									<td>22 Aug 2025</td>
									<td>Issue with Course Notification Emails</td>
									<td><span class="badge badge-sm bg-soft-danger d-inline-flex align-items-center"><i class="fa-solid fa-circle fs-5 me-1"></i>High</span></td>
									<td>Mailing Issues</td>
									<td><span class="badge badge-sm bg-purple d-inline-flex align-items-center"><i class="fa-solid fa-circle fs-5 me-1"></i>Opened</span></td>
									<td>
										<div class="d-flex align-items-center">
											<a href="#" class="d-inline-flex fs-14 me-1 action-icon" data-bs-toggle="modal" data-bs-target="#ticket_details"><i class="isax isax-eye"></i></a>
											<a href="#" class="d-inline-flex fs-14 me-2 action-icon" data-bs-toggle="modal" data-bs-target="#edit_ticket"><i class="isax isax-edit-2"></i></a>
											<a href="#" class="d-inline-flex fs-14 action-icon" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash"></i></a>
										</div>
									</td>
								</tr>								
								<tr>
									<td><a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#ticket_details">#TIC001</a></td>
									<td>15 May 2025</td>
									<td>Account Recovery Assistance Need</td>
									<td><span class="badge badge-sm bg-soft-danger d-inline-flex align-items-center"><i class="fa-solid fa-circle fs-5 me-1"></i>High</span></td>
									<td>Recovery Issues</td>
									<td><span class="badge badge-sm bg-success d-inline-flex align-items-center"><i class="fa-solid fa-circle fs-5 me-1"></i>Closed</span></td>
									<td>
										<div class="d-flex align-items-center">
											<a href="#" class="d-inline-flex fs-14 me-1 action-icon" data-bs-toggle="modal" data-bs-target="#ticket_details"><i class="isax isax-eye"></i></a>
											<a href="#" class="d-inline-flex fs-14 me-2 action-icon" data-bs-toggle="modal" data-bs-target="#edit_ticket"><i class="isax isax-edit-2"></i></a>
											<a href="#" class="d-inline-flex fs-14 action-icon" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash"></i></a>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="row align-items-center mt-4">
						<div class="col-md-2">
							<p class="pagination-text">Page 1 of 2</p>
						</div>
						<div class="col-md-10">
							<ul class="pagination lms-page justify-content-center justify-content-md-end mt-2 mt-md-0">
								<li class="page-item prev">
									<a class="page-link" href="javascript:void(0)" tabindex="-1"><i class="fas fa-angle-left"></i></a>
								</li>
								<li class="page-item first-page active">
									<a class="page-link" href="javascript:void(0)">1</a>
								</li>
								<li class="page-item">
									<a class="page-link" href="javascript:void(0)">2</a>
								</li>
								<li class="page-item">
									<a class="page-link" href="javascript:void(0)">3</a>
								</li>
								<li class="page-item next">
									<a class="page-link" href="javascript:void(0)"><i class="fas fa-angle-right"></i></a>
								</li>
							</ul>
						</div>
					</div>                   
				</div>
			</div>
		</div>
	</div>
</div>
@endsection 
@section('script')
<!-- Datepicker Core JS -->
<script src="{{ asset('website_assets/assets/js/moment.min.js') }}"></script>
<script src="{{ asset('website_assets/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('website_assets/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

<!-- Summernote JS -->
<script src="{{ asset('website_assets/assets/plugins/summernote/summernote-lite.min.js') }}"></script>

<!-- Sticky Sidebar JS -->
<script src="{{ asset('website_assets/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('website_assets/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
@endsection