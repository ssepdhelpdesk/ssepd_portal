<div class="card shadow-sm mb-4 border-0">
   <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0 text-dark fw-bold"><i class="fas fa-filter text-primary me-2"></i>Filter Report Data</h6>
      <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#reportFiltersCollapse" aria-expanded="true">
         <i class="fas fa-chevron-down"></i>
      </button>
   </div>
   <div class="collapse show" id="reportFiltersCollapse">
      <div class="card-body p-3">
         <form method="GET" action="{{ url()->current() }}" id="reportFilterForm">
            {{-- Retain drill-down state if present --}}
            @if(request()->filled('drill_district_id')) <input type="hidden" name="drill_district_id" value="{{ request('drill_district_id') }}"> @endif
            @if(request()->filled('drill_subdivision_id')) <input type="hidden" name="drill_subdivision_id" value="{{ request('drill_subdivision_id') }}"> @endif
            @if(request()->filled('drill_block_id')) <input type="hidden" name="drill_block_id" value="{{ request('drill_block_id') }}"> @endif
            @if(request()->filled('drill_municipality_id')) <input type="hidden" name="drill_municipality_id" value="{{ request('drill_municipality_id') }}"> @endif
            @if(request()->filled('drill_gp_id')) <input type="hidden" name="drill_gp_id" value="{{ request('drill_gp_id') }}"> @endif
            @if(request()->filled('drill_ward_id')) <input type="hidden" name="drill_ward_id" value="{{ request('drill_ward_id') }}"> @endif

            <div class="row g-2">
               <!-- Date From -->
               <div class="col-md-2 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                  <input type="date" name="date_from" id="filter_date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
               </div>

               <!-- Date To -->
               <div class="col-md-2 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                  <input type="date" name="date_to" id="filter_date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
               </div>

               <!-- Area Type -->
               <div class="col-md-2 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Area Type</label>
                  <select name="address_type" id="filter_address_type" class="form-select form-select-sm">
                     <option value="">-- All (Rural & Urban) --</option>
                     <option value="1" {{ request('address_type') == '1' ? 'selected' : '' }}>Rural</option>
                     <option value="2" {{ request('address_type') == '2' ? 'selected' : '' }}>Urban</option>
                  </select>
               </div>

               <!-- Visit Type -->
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Visit Type</label>
                  <select name="field_visit_type_id" id="filter_visit_type_id" class="form-select form-select-sm">
                     <option value="">-- All Visit Types --</option>
                     @foreach($filterOptions['visitTypes'] as $vt)
                        <option value="{{ $vt->id }}" {{ request('field_visit_type_id') == $vt->id ? 'selected' : '' }}>{{ $vt->name }}</option>
                     @endforeach
                  </select>
               </div>

               <!-- Category -->
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Institution Category</label>
                  <select name="category_id" id="filter_category_id" class="form-select form-select-sm">
                     <option value="">-- All Categories --</option>
                     @foreach($filterOptions['categories'] as $cat)
                        <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                     @endforeach
                  </select>
               </div>

               <!-- Designation -->
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Visiting Designation</label>
                  <select name="visiting_officer_designation" id="filter_designation" class="form-select form-select-sm">
                     <option value="">-- All Designations --</option>
                     @foreach($filterOptions['designations'] as $desig)
                        <option value="{{ $desig }}" {{ request('visiting_officer_designation') == $desig ? 'selected' : '' }}>{{ $desig }}</option>
                     @endforeach
                  </select>
               </div>

               <!-- District (Only permitted roles can change) -->
               @php
                  $userLevel = $filterOptions['jurisdiction']['level'] ?? 'state';
               @endphp
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">District</label>
                  @if($userLevel === 'state')
                     <select name="district_id" id="filter_district_id" class="form-select form-select-sm">
                        <option value="">-- All Districts --</option>
                        @foreach($filterOptions['districts'] as $dist)
                           <option value="{{ $dist->district_id }}" {{ request('district_id') == $dist->district_id ? 'selected' : '' }}>{{ $dist->district_name }}</option>
                        @endforeach
                     </select>
                  @else
                     <select name="district_id" id="filter_district_id" class="form-select form-select-sm" disabled>
                        @foreach($filterOptions['districts'] as $dist)
                           <option value="{{ $dist->district_id }}" selected>{{ $dist->district_name }} (Assigned)</option>
                        @endforeach
                     </select>
                  @endif
               </div>

               <!-- Subdivision -->
               @if(in_array($userLevel, ['state', 'district', 'subdivision']))
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Subdivision</label>
                  @if($userLevel === 'subdivision')
                     <select name="subdivision_id" id="filter_subdivision_id" class="form-select form-select-sm" disabled>
                        @foreach($filterOptions['subdivisions'] as $sub)
                           <option value="{{ $sub->subdivision_id }}" selected>{{ $sub->subdivision_name }} (Assigned)</option>
                        @endforeach
                     </select>
                  @else
                     <select name="subdivision_id" id="filter_subdivision_id" class="form-select form-select-sm">
                        <option value="">-- All Subdivisions --</option>
                        @foreach($filterOptions['subdivisions'] as $sub)
                           <option value="{{ $sub->subdivision_id }}" {{ request('subdivision_id') == $sub->subdivision_id ? 'selected' : '' }}>{{ $sub->subdivision_name }}</option>
                        @endforeach
                     </select>
                  @endif
               </div>
               @endif

               <!-- Block (Rural) -->
               @if(in_array($userLevel, ['state', 'district', 'subdivision', 'block']))
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Block (Rural)</label>
                  @if($userLevel === 'block')
                     <select name="block_id" id="filter_block_id" class="form-select form-select-sm" disabled>
                        @foreach($filterOptions['blocks'] as $blk)
                           <option value="{{ $blk->block_id }}" selected>{{ $blk->block_name }} (Assigned)</option>
                        @endforeach
                     </select>
                  @else
                     <select name="block_id" id="filter_block_id" class="form-select form-select-sm">
                        <option value="">-- All Blocks --</option>
                        @foreach($filterOptions['blocks'] as $blk)
                           <option value="{{ $blk->block_id }}" {{ request('block_id') == $blk->block_id ? 'selected' : '' }}>{{ $blk->block_name }}</option>
                        @endforeach
                     </select>
                  @endif
               </div>
               @endif

               <!-- Municipality (Urban) -->
               @if(in_array($userLevel, ['state', 'district', 'subdivision', 'ulb']))
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Municipality / ULB (Urban)</label>
                  @if($userLevel === 'ulb')
                     <select name="municipality_id" id="filter_municipality_id" class="form-select form-select-sm" disabled>
                        @foreach($filterOptions['municipalities'] as $muni)
                           <option value="{{ $muni->municipality_id }}" selected>{{ $muni->municipality_name }} (Assigned)</option>
                        @endforeach
                     </select>
                  @else
                     <select name="municipality_id" id="filter_municipality_id" class="form-select form-select-sm">
                        <option value="">-- All Municipalities --</option>
                        @foreach($filterOptions['municipalities'] as $muni)
                           <option value="{{ $muni->municipality_id }}" {{ request('municipality_id') == $muni->municipality_id ? 'selected' : '' }}>{{ $muni->municipality_name }}</option>
                        @endforeach
                     </select>
                  @endif
               </div>
               @endif

               <!-- Search keyword -->
               <div class="col-md-3 col-sm-6">
                  <label class="form-label small fw-semibold text-muted mb-1">Keyword Search</label>
                  <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ref no, institution, officer..." value="{{ request('search') }}">
               </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top flex-wrap gap-2">
               <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-sm btn-primary">
                     <i class="fas fa-search me-1"></i> Apply Filters
                  </button>
                  <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">
                     <i class="fas fa-undo me-1"></i> Reset Filters
                  </a>
               </div>

               @if(isset($exportRoute))
               <div>
                  <a href="{{ route($exportRoute, request()->query()) }}" class="btn btn-sm btn-success text-white shadow-sm" title="Export current filtered data to Excel">
                     <i class="fas fa-file-excel me-1"></i> Export Excel
                  </a>
               </div>
               @endif
            </div>
         </form>
      </div>
   </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
   const districtSelect = document.getElementById('filter_district_id');
   const subdivSelect = document.getElementById('filter_subdivision_id');
   const blockSelect = document.getElementById('filter_block_id');
   const muniSelect = document.getElementById('filter_municipality_id');

   // Cascading District -> Subdivisions, Blocks, Municipalities
   if (districtSelect && !districtSelect.disabled) {
      districtSelect.addEventListener('change', function () {
         const distId = this.value;

         if (subdivSelect && !subdivSelect.disabled) {
            subdivSelect.innerHTML = '<option value="">Loading subdivisions...</option>';
            fetch(`{{ route('admin.field_visit_reports.ajax.subdivisions') }}?district_id=${distId}`)
               .then(res => res.json())
               .then(data => {
                  let options = '<option value="">-- All Subdivisions --</option>';
                  data.forEach(item => {
                     options += `<option value="${item.subdivision_id}">${item.subdivision_name}</option>`;
                  });
                  subdivSelect.innerHTML = options;
               }).catch(() => { subdivSelect.innerHTML = '<option value="">-- All Subdivisions --</option>'; });
         }

         if (blockSelect && !blockSelect.disabled) {
            blockSelect.innerHTML = '<option value="">Loading blocks...</option>';
            fetch(`{{ route('admin.field_visit_reports.ajax.blocks') }}?district_id=${distId}`)
               .then(res => res.json())
               .then(data => {
                  let options = '<option value="">-- All Blocks --</option>';
                  data.forEach(item => {
                     options += `<option value="${item.block_id}">${item.block_name}</option>`;
                  });
                  blockSelect.innerHTML = options;
               }).catch(() => { blockSelect.innerHTML = '<option value="">-- All Blocks --</option>'; });
         }

         if (muniSelect && !muniSelect.disabled) {
            muniSelect.innerHTML = '<option value="">Loading ULBs...</option>';
            fetch(`{{ route('admin.field_visit_reports.ajax.municipalities') }}?district_id=${distId}`)
               .then(res => res.json())
               .then(data => {
                  let options = '<option value="">-- All Municipalities --</option>';
                  data.forEach(item => {
                     options += `<option value="${item.municipality_id}">${item.municipality_name}</option>`;
                  });
                  muniSelect.innerHTML = options;
               }).catch(() => { muniSelect.innerHTML = '<option value="">-- All Municipalities --</option>'; });
         }
      });
   }

   // Cascading Subdivision -> Blocks & Municipalities
   if (subdivSelect && !subdivSelect.disabled) {
      subdivSelect.addEventListener('change', function () {
         const subdivId = this.value;

         if (blockSelect && !blockSelect.disabled) {
            blockSelect.innerHTML = '<option value="">Loading blocks...</option>';
            fetch(`{{ route('admin.field_visit_reports.ajax.blocks') }}?subdivision_id=${subdivId}`)
               .then(res => res.json())
               .then(data => {
                  let options = '<option value="">-- All Blocks --</option>';
                  data.forEach(item => {
                     options += `<option value="${item.block_id}">${item.block_name}</option>`;
                  });
                  blockSelect.innerHTML = options;
               }).catch(() => { blockSelect.innerHTML = '<option value="">-- All Blocks --</option>'; });
         }

         if (muniSelect && !muniSelect.disabled) {
            muniSelect.innerHTML = '<option value="">Loading ULBs...</option>';
            fetch(`{{ route('admin.field_visit_reports.ajax.municipalities') }}?subdivision_id=${subdivId}`)
               .then(res => res.json())
               .then(data => {
                  let options = '<option value="">-- All Municipalities --</option>';
                  data.forEach(item => {
                     options += `<option value="${item.municipality_id}">${item.municipality_name}</option>`;
                  });
                  muniSelect.innerHTML = options;
               }).catch(() => { muniSelect.innerHTML = '<option value="">-- All Municipalities --</option>'; });
         }
      });
   }
});
</script>

