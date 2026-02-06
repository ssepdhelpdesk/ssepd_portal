@section('title') 
SSEPD-IT
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


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
   .home-five .drop-detail-three {
      width: 150px;
      display: flex;
      border-radius: 50px !important;
      border: 0;
      height: 44px;
      background-color: var(--light);
   }
   .banner-five {
      position: relative;
      background-color: var(--light-900);
      background-image: url("{{ asset('website_assets/assets/img/bg/bg-1.png') }}");
      background-repeat: no-repeat;
      background-position: right bottom;
      padding-top: 15px;
   }

   .lms-page .page-link {
      min-width: 38px;
      text-align: center;
   }

   .lms-page .page-item.disabled .page-link {
      opacity: 0.5;
      pointer-events: none;
   }
   #customPagination {
      display: flex;
      flex-wrap: wrap;  /* <-- allows wrapping */
      gap: 4px;
      padding: 0;
      margin: 0;
   }
   #customPagination li {
      flex: 0 0 auto;
   }
   .dataTables_length select {
      appearance: none;       /* Remove default arrow */
      -webkit-appearance: none;
      -moz-appearance: none;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 6px 30px 6px 10px; /* space for arrow */
      background-color: #fff;
      background-image: url("data:image/svg+xml;utf8,<svg fill='gray' height='12' viewBox='0 0 24 24' width='12' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
      background-repeat: no-repeat;
      background-position: right 10px center;
      font-size: 14px;
      cursor: pointer;
   }
   .dataTables_length label {
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
   }

   .dataTables_length {
      display: none !important;
   }

   @media (min-width: 992px) {
      .banner-three-content {
         max-width: 800px;
         width: 800px;
      }
   }

   .the-main-content {
      padding: 10px 0;
   }
</style>
@endsection 
@section('content')
<section class="banner-five">
   <div class="container">
      <div class="row align-items-center">
         <div class="col-xl-6 col-lg-7 col-md-12 d-flex col-12" data-aos="fade-down">
            <div class="home-five-slide-face flex-fill">
               <div class="home-five-slide-text">
                  <span class="text-white d-inline-block bg-secondary small rounded-pill px-3 py-2 mb-3 mb-sm-4"><span class="badge bg-white text-secondary rounded-pill me-1">New</span>Fast. Safe. Direct – Choose DBT Pension Mode!</span>
                  <h1>One Consent, Lifetime Convenience – Move to <span>DBT!</span></h1>
                  <p>Submit your consent online for receiving pension through Direct Bank Transfer (DBT).</p>
               </div>
               <div class="banner-three-content">
                  <form class="form" id="bannerFilterForm">
                     <div class="form-inner-three">
                        <div class="input-group justify-content-between">
                           <div class="d-flex flex-wrap gap-3">
                              <div class="drop-detail-three">
                                 <select class="form-three-select select" id="districtSelect">
                                    <option value="">Select District</option>
                                    @foreach ($district as $item)
                                    <option value="{{ $item->district }}">{{ $item->district }}</option>
                                    @endforeach
                                 </select>
                              </div>
                              <div class="drop-detail-three">
                                 @php
                                 $areaLabels = [
                                 'R' => 'Rural',
                                 'U' => 'Urban',
                                 ];
                                 @endphp
                                 <select class="form-three-select select" id="areaSelect">
                                    <option value="">Address Type</option>
                                    @foreach ($area as $item)
                                    <option value="{{ $item->area }}">
                                     {{ $areaLabels[$item->area] }}
                                  </option>
                                  @endforeach
                               </select>
                            </div>

                            <div class="drop-detail-three">
                              <select class="form-three-select select" id="blockSelect" disabled>
                                 <option value="">Blocks / ULBs</option>
                              </select>
                           </div>

                           <div class="drop-detail-three">
                              <select class="form-three-select select" id="gpSelect" disabled>
                                 <option value="">GPs / Wards</option>
                              </select>
                           </div>
                           <div class="d-flex align-items-center banner-search5">
                              <div class="search-icon5">
                                 <button class="btn btn-primary sub-btn" type="submit"><i class="fas fa-search"></i></button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <div class="offset-lg-1 col-lg-5 col-12 text-center" data-aos="fade-up">
         <div class="banner-slide-img flex-fill aos">
            <img class="img-fluid ps-lg-5" src="{{ asset('website_assets/assets/img/hero/hero-oldage-dp.png') }}" alt="Img">
         </div>
      </div>
   </div>
</div>
</section>

<section class="section student-course student-course-five">
   <div class="container">
      <div class="course-widget-three">
         <div class="row row-gap-4">
            <div class="col-lg-3 col-md-6 d-flex" data-aos="fade-up">
               <div class="course-details-three">
                  <div class="align-items-center">
                     <div class="course-count-three course-count ms-0">
                        <div class="course-img">
                           <img class="img-fluid" src="{{ asset('website_assets/assets/img/icon/all-beneficiaries.svg') }}" alt="Img">
                        </div>
                        <div class="course-content-three">
                           <h4 class="text-info"><span class="counterUp counterUp-totalActive">0</span></h4>
                           <p>Total Beneficiaries</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex" data-aos="fade-up">
               <div class="course-details-three">
                  <div class="align-items-center">
                     <div class="course-count-three course-count ms-0">
                        <div class="course-img">
                           <img class="img-fluid" src="{{ asset('website_assets/assets/img/icon/oldage.svg') }}" alt="Img">
                        </div>
                        <div class="course-content-three">
                           <h4 class="text-warning"><span class="counterUp counterUp-schemeCountOap">0</span></h4>
                           <p>OldAge Beneficiaries</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex" data-aos="fade-up">
               <div class="course-details-three">
                  <div class="align-items-center">
                     <div class="course-count-three course-count ms-0">
                        <div class="course-img">
                           <img class="img-fluid" src="{{ asset('website_assets/assets/img/icon/disability.svg') }}" alt="Img">
                        </div>
                        <div class="course-content-three">
                           <h4 class="text-skyblue"><span class="counterUp counterUp-schemeCountDp"></span></h4>
                           <p>DP Beneficiaries</p>
                        </div>
                     </div>
                  </div>
               </div>  
            </div>
            <div class="col-lg-3 col-md-6 d-flex" data-aos="fade-up">
               <div class="course-details-three mb-0">
                  <div class="align-items-center">
                     <div class="course-count-three">
                        <div class="course-img">
                           <img class="img-fluid" src="{{ asset('website_assets/assets/img/icon/other-beneficiaries.svg') }}" alt="Img">
                        </div>
                        <div class="course-content-three course-count">
                           <h4 class="text-success"><span class="counterUp counterUp-schemeCountOther"></span></h4>
                           <p>Other Beneficiaries</p> 
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<div class="content the-main-content">
   <div class="container">
      <div class="row">
         <div class="col-lg-12">
            <div class="tickets">
               <div class="row align-items-center mb-2" id="searchContainer" style="display: none;">
                <div class="col-md-8">
                 <div class="input-icon">
                  <span class="input-icon-addon">
                   <i class="isax isax-search-normal-14"></i>
                </span>
                <input type="text" id="customSearch" class="form-control form-control-md" placeholder="Search">
             </div>
             <p>Search by Beneficiary Name, Care Of Name, NSAP Sanction Order No., or Aadhaar Number.</p>
          </div>

          <div class="col-md-4 text-md-end mt-2 mt-md-0">
           <div class="dropdown d-inline-block">
            <a href="javascript:void(0);" 
            class="dropdown-toggle btn rounded border text-gray-6"
            data-bs-toggle="dropdown">
            Show 10
         </a>
         <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" data-length="10">10</a></li>
          <li><a class="dropdown-item" data-length="25">25</a></li>
          <li><a class="dropdown-item" data-length="50">50</a></li>
          <li><a class="dropdown-item" data-length="100">100</a></li>
       </ul>
    </div>
 </div>
</div>
<div class="table-responsive custom-table">
   <table class="table w-100" id="ticketTable">
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
            <th>Block / ULB Name</th>
            <th>GP / Ward Name</th>
            <th>Provide Consent</th>
         </tr>
      </thead>
   </table>
</div>
<div class="row align-items-center mt-4">
   <div class="col-md-2">
      <p class="pagination-text" id="pageInfo"></p>
   </div>
   <div class="col-md-10">
      <ul class="pagination lms-page justify-content-center justify-content-md-end mt-2 mt-md-0"
      id="customPagination"></ul>
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

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
/* ================= GLOBAL STATE ================= */

   let selectedDistrict = null;
   let selectedArea = null;
   let selectedBlock = null;
   let selectedGp = null;

/* ================= BANNER <SELECT> HANDLERS ================= */

   $('#districtSelect').on('change', function () {
      selectedDistrict = this.value || null;

      selectedArea = null;
      selectedBlock = null;
      selectedGp = null;

      $('#areaSelect').val('');
      $('#blockSelect').prop('disabled', true).html('<option value="">Blocks / ULBs</option>');
      $('#gpSelect').prop('disabled', true).html('<option value="">GPs / Wards</option>');
   });

   $('#areaSelect').on('change', function () {
      selectedArea = this.value || null;

      selectedBlock = null;
      selectedGp = null;

      $('#blockSelect').prop('disabled', true).html('<option>Loading...</option>');
      $('#gpSelect').prop('disabled', true).html('<option value="">GPs / Wards</option>');

      if (selectedDistrict && selectedArea) {
         loadBlocks();
      }
   });

   $('#blockSelect').on('change', function () {
      selectedBlock = this.value || null;

      selectedGp = null;
      $('#gpSelect').prop('disabled', true).html('<option>Loading...</option>');

      if (selectedDistrict && selectedArea && selectedBlock) {
         loadGps();
      }
   });

   $('#gpSelect').on('change', function () {
      selectedGp = this.value || null;
   });

/* ================= AJAX LOADERS FOR SELECT ================= */

   function loadBlocks() {
      fetch(`{{ route('website.pensioners.blocks.by.district.area') }}?district=${encodeURIComponent(selectedDistrict)}&area=${selectedArea}`)
      .then(res => res.json())
      .then(data => {
         const blockSelect = $('#blockSelect');
         blockSelect.empty().append('<option value="">Blocks / ULBs</option>');

         if (!data.length) {
            blockSelect.append('<option disabled>No Blocks / ULBs found</option>');
         } else {
            data.forEach(b => {
               blockSelect.append(`<option value="${b}">${b}</option>`);
            });
         }

         blockSelect.prop('disabled', false);
      });
   }

   function loadGps() {
      fetch(`{{ route('website.pensioners.gps.by.district.area.block') }}?district=${encodeURIComponent(selectedDistrict)}&area=${selectedArea}&block=${encodeURIComponent(selectedBlock)}`)
      .then(res => res.json())
      .then(data => {
         const gpSelect = $('#gpSelect');
         gpSelect.empty().append('<option value="">GPs / Wards</option>');

         if (!data.length) {
            gpSelect.append('<option disabled>No GP / Ward found</option>');
         } else {
            data.forEach(gp => {
               gpSelect.append(`<option value="${gp}">${gp}</option>`);
            });
         }

         gpSelect.prop('disabled', false);
      });
   }

/* ================= EXISTING TABLE DROPDOWN SUPPORT (UNCHANGED) ================= */

   document.addEventListener('click', function(e) {
      const item = e.target.closest('.dropdown-item');
      if (!item) return;

      const dropdown = item.closest('.dropdown');
      const toggle = dropdown?.querySelector('.dropdown-toggle');

      if (item.dataset.district) {
         selectedDistrict = item.dataset.district;
         toggle.innerText = item.innerText.trim();
         return;
      }

      if (item.dataset.area) {
         selectedArea = item.dataset.area;
         toggle.innerText = item.innerText.trim();
         return;
      }

      if (item.dataset.block) {
         selectedBlock = item.dataset.block;
         toggle.innerText = item.innerText.trim();
         return;
      }

      if (item.dataset.gp) {
         selectedGp = item.dataset.gp;
         toggle.innerText = item.innerText.trim();
      }
   });

/* ================= DATA TABLE ================= */

   let ticketTable = $('#ticketTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      ordering: true,
      scrollX: true,
      scrollCollapse: true,
      paging: true,
      dom: 'lrt',
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],

      ajax: {
         url: '{{ route("website.pensioners.datatable") }}',
         type: 'GET',
         data: function(d) {
            d.district = selectedDistrict;
            d.area     = selectedArea;
            d.block    = selectedBlock;
            d.gp       = selectedGp;
         },
         dataSrc: function(json) {
            $('.counterUp-totalActive').text(json.counters.totalActive);
            $('.counterUp-schemeCountOap').text(json.counters.schemeCountOap);
            $('.counterUp-schemeCountDp').text(json.counters.schemeCountDp);
            $('.counterUp-schemeCountOther').text(json.counters.schemeCountOther);
            return json.data;
         },
         error: function(xhr) {
            console.error(xhr.responseText);
            alert('Failed to load data');
         }
      },

      columns: [
         { data: 'DT_RowIndex', orderable: false, searchable: false },
         { data: 'applicant_name', name: 'applicant_name' },
         { data: 'father_husband_name', name: 'father_husband_name' },
         { data: 'scheme', name: 'scheme' },
         { data: 'sanction_date', name: 'sanction_date' },
         { data: 'sanction_order_no', name: 'sanction_order_no' },
         { data: 'disbursement_mode', name: 'disbursement_mode' },
         { data: 'disbursement_upto', name: 'disbursement_upto' },
         { data: 'district', name: 'district' },
         { data: 'area', name: 'area' },
         { data: 'sub_district_municipality', name: 'sub_district_municipality' },
         { data: 'gram_panchayat_ward', name: 'gram_panchayat_ward' },
         { data: 'action', orderable: false, searchable: false }
      ],

      drawCallback: function() {
         renderPagination(ticketTable);
      }
   });

   $('#customSearch').on('keyup', function() {
      ticketTable.search(this.value).draw();
   });

/* ================= APPLY FILTER ================= */

   $('#bannerFilterForm').on('submit', function (e) {
      e.preventDefault();

      if (!selectedDistrict || !selectedArea || !selectedBlock) {
         alert('Please Select Your District, Address Type & Block/ULB');
         return;
      }

      $('#searchContainer').fadeIn();
      ticketTable.ajax.reload();
   });

/* ================= CUSTOM PAGINATION ================= */

   function renderPagination(table) {
      const info = table.page.info();
      const pagination = $('#customPagination');
      pagination.empty();

      if (info.pages <= 1) {
         $('#pageInfo').text('');
         return;
      }

      $('#pageInfo').text(`Page ${info.page + 1} of ${info.pages}`);

      const maxPagesToShow = 7;
      let start = Math.max(0, info.page - 3);
      let end = Math.min(info.pages, start + maxPagesToShow);

      pagination.append(`
<li class="page-item ${info.page === 0 ? 'disabled' : ''}">
<a class="page-link" href="#" data-page="${info.page - 1}">
<i class="fas fa-angle-left"></i>
</a>
</li>
      `);

      for (let i = start; i < end; i++) {
         pagination.append(`
<li class="page-item ${i === info.page ? 'active' : ''}">
<a class="page-link" href="#" data-page="${i}">${i + 1}</a>
</li>
         `);
      }

      pagination.append(`
<li class="page-item ${info.page + 1 === info.pages ? 'disabled' : ''}">
<a class="page-link" href="#" data-page="${info.page + 1}">
<i class="fas fa-angle-right"></i>
</a>
</li>
      `);
   }

   $('#customPagination').on('click', '.page-link', function(e) {
      e.preventDefault();
      const page = $(this).data('page');
      if (page !== undefined && !$(this).parent().hasClass('disabled')) {
         ticketTable.page(page).draw('page');
      }
   });

   $('.dropdown-menu a').on('click', function() {
      const length = $(this).data('length');
      ticketTable.page.len(length).draw();
      $(this).closest('.dropdown').find('.dropdown-toggle').text('Show ' + length);
   });
</script>
@endsection