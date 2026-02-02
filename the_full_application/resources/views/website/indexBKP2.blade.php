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

<!-- Select2 CSS -->
<link href="{{ asset('dashboard_assets/assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
						<div class="col-md-8">
							<div class="d-flex align-items-center flex-wrap">
								<div class="mb-3">
									<div class="dropdown me-3">
										<a href="javascript:void(0);" class="dropdown-toggle text-gray-6 btn  rounded border d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
											Select District
										</a>
										<ul class="dropdown-menu dropdown-menu-end">
											@forelse ($district as $item)
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1" data-district="{{ $item->district }}"> {{ $item->district }}</a>
											</li>
											@empty
											<li>
												<span class="dropdown-item text-muted">No districts found</span>
											</li>
											@endforelse
										</ul>
									</div>
								</div>
								<div class="mb-3">
									<div class="dropdown me-3">
										<a href="javascript:void(0);" id="areaToggle" class="dropdown-toggle text-gray-6 btn  rounded border d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
											Address Type
										</a>
										@php
										$areaLabels = [
										'R' => 'Rural',
										'U' => 'Urban',
										];
										@endphp
										<ul class="dropdown-menu dropdown-menu-end">
											@forelse ($area as $item)
											@php
											$areaKey = strtoupper(trim($item->area));
											@endphp
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1" data-area="{{ $areaKey }}"> {{ $areaLabels[$areaKey] ?? $areaKey }} </a>
											</li>
											@empty
											<li>
												<span class="dropdown-item text-muted">No area found</span>
											</li>
											@endforelse
										</ul>
									</div>
								</div>
								<div class="mb-3">
									<div class="dropdown me-3">
										<a href="javascript:void(0);" id="blockToggle" class="dropdown-toggle text-gray-6 btn  rounded border d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
											Blocks / ULBs
										</a>
										<ul class="dropdown-menu dropdown-menu-end" id="blockDropdown">
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1"> Select District & Address Type</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="mb-3">
									<div class="dropdown me-3">
										<a href="javascript:void(0);" id="gpToggle" class="dropdown-toggle text-gray-6 btn  rounded border d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
											GPs/Wards
										</a>
										<ul class="dropdown-menu dropdown-menu-end" id="gpDropdown">
											<li>
												<a href="javascript:void(0);" class="dropdown-item rounded-1">Select District, Address Type & Block</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						
						<a href="javascript:void(0)" id="applyFilter" class="btn btn-secondary rounded-pill"> <i class="isax isax-add-circle me-2 fs-10"></i> Add Ticket </a>
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
						<!-- The Button Group -->
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
									<th>Sl.No</th>
									<th>Beneficiary Name</th>
									<th>Care Of</th>
									<th>Scheme</th>
									<th>Sanction From</th>
									<th>Sanction Order No</th>
									<th>Disbursed Mode</th>
									<th>Disbursed Upto</th>
									<th>District</th>
									<th>Address Type</th>
									<th>Block/ULB Name</th>
									<th>GP/Ward Name</th>
									<th>Status</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="ticketTableBody">
								@include('website.nsap_rows', ['nsapDump' => $nsapDump])
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

<script>
	let selectedDistrict = null;
	let selectedArea = null;
	let selectedBlock = null;
	let selectedGp = null;

	const areaToggle  = document.getElementById('areaToggle');
	const blockToggle = document.getElementById('blockToggle');
	const gpToggle    = document.getElementById('gpToggle');

	const blockDropdown = document.getElementById('blockDropdown');
	const gpDropdown    = document.getElementById('gpDropdown');

/* ================= RESETTERS ================= */

	function resetAddressType() {
		selectedArea = null;
		areaToggle.innerText = 'Address Type';
	}

	function resetBlockDropdown() {
		selectedBlock = null;
		blockToggle.innerText = 'Blocks / ULBs';
		blockToggle.classList.add('disabled');

		blockDropdown.innerHTML = `
        <li><span class="dropdown-item text-muted">
            Select District & Address Type
		</span></li>`;
	}

	function resetGpDropdown() {
		selectedGp = null;
		gpToggle.innerText = 'GPs / Wards';
		gpToggle.classList.add('disabled');

		gpDropdown.innerHTML = `
        <li><span class="dropdown-item text-muted">
            Select District, Address Type & Block
		</span></li>`;
	}

/* ================= EVENTS ================= */

	document.addEventListener('click', function (e) {
		const item = e.target.closest('.dropdown-item');
		if (!item) return;

		const dropdown = item.closest('.dropdown');
		const toggle = dropdown.querySelector('.dropdown-toggle');

    /* DISTRICT */
		if (item.dataset.district) {
			selectedDistrict = item.dataset.district;
			toggle.innerText = item.innerText.trim();

			resetAddressType();
			resetBlockDropdown();
			resetGpDropdown();
			return;
		}

    /* AREA */
		if (item.dataset.area) {
			selectedArea = item.dataset.area;
			toggle.innerText = item.innerText.trim();

			resetBlockDropdown();
			resetGpDropdown();
			loadBlocks();
			return;
		}

    /* BLOCK */
		if (item.dataset.block) {
			selectedBlock = item.dataset.block;
			toggle.innerText = item.innerText.trim();

			resetGpDropdown();
			loadGps();
			return;
		}

    /* GP */
		if (item.dataset.gp) {
			selectedGp = item.dataset.gp;
			toggle.innerText = item.innerText.trim();
		}
	});

/* ================= AJAX ================= */

	function loadBlocks() {
		if (!selectedDistrict || !selectedArea) return;

		blockToggle.classList.remove('disabled');
		blockDropdown.innerHTML = `<li><span class="dropdown-item text-muted">Loading...</span></li>`;

		fetch(`{{ route('website.pensioners.blocks.by.district.area') }}?district=${encodeURIComponent(selectedDistrict)}&area=${selectedArea}`)
		.then(r => r.json())
		.then(data => {
			blockDropdown.innerHTML = '';
			if (!data.length) {
				blockDropdown.innerHTML = `<li><span class="dropdown-item text-muted">No Blocks / ULBs found</span></li>`;
				return;
			}
			data.forEach(b => {
				blockDropdown.insertAdjacentHTML('beforeend',
			`<li><a class="dropdown-item" data-block="${b}">${b}</a></li>`);
			});
		});
	}

	function loadGps() {
		if (!selectedDistrict || !selectedArea || !selectedBlock) return;

		gpToggle.classList.remove('disabled');
		gpDropdown.innerHTML = `<li><span class="dropdown-item text-muted">Loading...</span></li>`;

		fetch(`{{ route('website.pensioners.gps.by.district.area.block') }}?district=${encodeURIComponent(selectedDistrict)}&area=${selectedArea}&block=${encodeURIComponent(selectedBlock)}`)
		.then(r => r.json())
		.then(data => {
			gpDropdown.innerHTML = '';
			if (!data.length) {
				gpDropdown.innerHTML = `<li><span class="dropdown-item text-muted">No GP / Ward found</span></li>`;
				return;
			}
			data.forEach(gp => {
				gpDropdown.insertAdjacentHTML('beforeend',
			`<li><a class="dropdown-item" data-gp="${gp}">${gp}</a></li>`);
			});
		});
	}
</script>
<script>
document.getElementById('applyFilter').addEventListener('click', function () {

    if (!selectedDistrict || !selectedArea) {
        alert('Please select District and Address Type');
        return;
    }

    const params = new URLSearchParams({
        district: selectedDistrict,
        area: selectedArea,
        block: selectedBlock ?? '',
        gp: selectedGp ?? ''
    });

    fetch(`{{ route('website.pensioners.filter') }}?${params}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('ticketTableBody').innerHTML = html;
        })
        .catch(() => alert('Failed to load data'));
});
</script>
@endsection