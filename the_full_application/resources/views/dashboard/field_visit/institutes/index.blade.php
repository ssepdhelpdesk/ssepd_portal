@section('title') 
SSEPD Institute Master || List
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .badge-code {
      font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size: 0.85rem;
   }
   .table td {
      vertical-align: middle;
   }
   .stat-card {
      border-left: 4px solid #009efb;
      transition: all 0.2s ease-in-out;
   }
   .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.08);
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
               <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
               <li class="breadcrumb-item"><a href="javascript:void(0)">Field Visit</a></li>
               <li class="breadcrumb-item active">Institute Master</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         @can('field-visit-institute-create')
         <a href="{{ route('admin.field_visit_institutes.create') }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success">
            <i class="fa fa-plus-circle"></i> Add Institute
         </a>
         @endcan
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <!-- Statistics Overview Cards -->
   <div class="row g-3 mb-3">
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle btn-info text-white"><i class="fas fa-university"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Total Master Institutes</h6>
                     <h4 class="mb-0 fw-bold">{{ $institutes->total() }}</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0" style="border-left-color: #26c6da;">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle btn-success text-white"><i class="fas fa-landmark"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Govt. of Odisha Funded</h6>
                     <h4 class="mb-0 fw-bold">294</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0" style="border-left-color: #745af2;">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle text-white" style="background:#745af2;"><i class="fas fa-flag"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Govt. of India Funded</h6>
                     <h4 class="mb-0 fw-bold">143</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6">
         <div class="card stat-card shadow-sm mb-0" style="border-left-color: #55ce63;">
            <div class="card-body p-3">
               <div class="d-flex align-items-center">
                  <div class="m-r-10"><span class="btn btn-circle btn-success text-white"><i class="fas fa-check-circle"></i></span></div>
                  <div>
                     <h6 class="text-muted mb-0 small">Active Facilities</h6>
                     <h4 class="mb-0 fw-bold">433</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Start Page Content -->
   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <div>
                     <h4 class="card-title mb-1">SSEPD Institute Master</h4>
                     <p class="text-muted small mb-0">Centralized departmental registry of special schools, rehabilitation centers, old age homes, and social welfare institutions.</p>
                  </div>
                  <div>
                     <span class="badge bg-primary fs-6 px-3 py-2">
                        Displaying {{ $institutes->count() }} of {{ $institutes->total() }} Institutes
                     </span>
                  </div>
               </div>

               @include('dashboard.component.message')

               <!-- Filter & Search Toolbar -->
               <form method="GET" action="{{ route('admin.field_visit_institutes.index') }}" class="row g-2 mb-4 align-items-end p-3 bg-light rounded border">
                  <div class="col-lg-3 col-md-6 col-sm-12">
                     <label for="search" class="form-label small fw-semibold text-muted mb-1">Search Keyword</label>
                     <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}" 
                               class="form-control" 
                               placeholder="Code, name, or NGO...">
                     </div>
                  </div>

                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <label for="district_id" class="form-label small fw-semibold text-muted mb-1">District</label>
                     <select name="district_id" id="district_id" class="form-select form-select-sm">
                        <option value="">-- All Districts --</option>
                        @foreach($districts as $dist)
                           <option value="{{ $dist->district_id }}" {{ request('district_id') == $dist->district_id ? 'selected' : '' }}>
                              {{ $dist->district_name }}
                           </option>
                        @endforeach
                     </select>
                  </div>

                  <div class="col-lg-3 col-md-4 col-sm-6">
                     <label for="category_id" class="form-label small fw-semibold text-muted mb-1">Institute Category</label>
                     <select name="category_id" id="category_id" class="form-select form-select-sm">
                        <option value="">-- All Categories --</option>
                        @foreach($categories as $catId => $catName)
                           <option value="{{ $catId }}" {{ request('category_id') == $catId ? 'selected' : '' }}>
                              {{ $catName }}
                           </option>
                        @endforeach
                     </select>
                  </div>

                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <label for="which_govt" class="form-label small fw-semibold text-muted mb-1">Govt. Scope</label>
                     <select name="which_govt" id="which_govt" class="form-select form-select-sm">
                        <option value="">-- All Schemes --</option>
                        <option value="1" {{ request('which_govt') == '1' ? 'selected' : '' }}>Govt. of Odisha</option>
                        <option value="2" {{ request('which_govt') == '2' ? 'selected' : '' }}>Govt. of India</option>
                     </select>
                  </div>

                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <label for="status" class="form-label small fw-semibold text-muted mb-1">Status</label>
                     <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">-- All Statuses --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                     </select>
                  </div>

                  <div class="col-12 text-end mt-2">
                     <button type="submit" class="btn btn-sm btn-primary px-3">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                     </button>
                     <a href="{{ route('admin.field_visit_institutes.index') }}" class="btn btn-sm btn-outline-secondary px-3 ms-1">
                        <i class="fas fa-undo me-1"></i> Reset
                     </a>
                  </div>
               </form>

               <!-- Institutes Table -->
               <div class="table-responsive">
                  <table class="table table-hover table-striped align-middle border">
                     <thead class="table-dark">
                        <tr>
                           <th style="width: 50px;" class="text-center">#</th>
                           <th style="width: 120px;">Code</th>
                           <th>Institute Name & Managing Body</th>
                           <th>Category</th>
                           <th>District / Sub-District</th>
                           <th style="width: 130px;" class="text-center">Govt. Scope</th>
                           <th style="width: 90px;" class="text-center">Status</th>
                           <th style="width: 90px;" class="text-center">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($institutes as $index => $inst)
                        <tr>
                           <td class="text-center text-muted fw-bold">
                              {{ $institutes->firstItem() + $index }}
                           </td>
                           <td>
                              <span class="badge bg-primary font-monospace px-2 py-1">
                                 {{ $inst->institute_code }}
                              </span>
                           </td>
                           <td>
                              <div class="fw-bold text-dark">
                                 <a href="{{ route('admin.field_visit_institutes.show', $inst->id) }}" class="text-decoration-none text-dark">
                                    {{ $inst->institute_name }}
                                 </a>
                              </div>
                              @if(!empty($inst->management_name))
                                 <small class="text-muted d-block mt-1">
                                    <i class="fas fa-building text-info me-1"></i> {{ $inst->management_name }}
                                 </small>
                              @endif
                           </td>
                           <td>
                              <span class="badge bg-info text-dark px-2 py-1">
                                 {{ $inst->category_name }}
                              </span>
                           </td>
                           <td>
                              <div class="fw-semibold text-secondary">
                                 <i class="fas fa-map-marker-alt text-danger me-1"></i> <span class="text-dark">{{ $inst->district_name ?? 'N/A' }}</span>
                                 @if($inst->address_type == 1)
                                    <span class="badge bg-light text-success border ms-1">Rural</span>
                                 @elseif($inst->address_type == 2)
                                    <span class="badge bg-light text-primary border ms-1">Urban</span>
                                 @endif
                              </div>
                              @if(!empty($inst->block_name))
                                 <small class="text-muted">{{ $inst->block_name }} (Block)</small>
                                 @if(!empty($inst->village_name))
                                    <small class="text-muted d-block"><i class="fas fa-home me-1"></i> {{ $inst->village_name }} (Village)</small>
                                 @endif
                              @elseif(!empty($inst->municipality_name))
                                 <small class="text-muted">{{ $inst->municipality_name }} (ULB)</small>
                              @endif
                           </td>
                           <td class="text-center">
                              @if($inst->which_govt == 2)
                                 <span class="badge bg-info px-2 py-1">Govt. of India</span>
                              @else
                                 <span class="badge bg-success px-2 py-1">Govt. of Odisha</span>
                              @endif
                           </td>
                           <td class="text-center">
                              @if($inst->is_active)
                                 <span class="badge bg-success px-2 py-1">
                                    <i class="fas fa-check-circle me-1"></i> Active
                                 </span>
                              @else
                                 <span class="badge bg-danger px-2 py-1">
                                    <i class="fas fa-times-circle me-1"></i> Inactive
                                 </span>
                              @endif
                           </td>
                           <td class="text-center">
                              <div class="btn-group">
                                 <button type="button" class="btn btn-danger dropdown-toggle btn-xs text-white" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                 </button>
                                 <div class="dropdown-menu dropdown-menu-end shadow">
                                    @can('field-visit-institute-show')
                                    <a class="dropdown-item" href="{{ route('admin.field_visit_institutes.show', $inst->id) }}">
                                       <i class="fas fa-eye text-info me-2"></i> View Details
                                    </a>
                                    @endcan

                                    @can('field-visit-institute-edit')
                                    <a class="dropdown-item" href="{{ route('admin.field_visit_institutes.edit', $inst->id) }}">
                                       <i class="fas fa-edit text-warning me-2"></i> Edit Institute
                                    </a>
                                    @endcan

                                    @can('field-visit-institute-delete')
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('admin.field_visit_institutes.destroy', $inst->id) }}" id="delete">
                                       <i class="fas fa-trash-alt text-danger me-2"></i> Delete
                                    </a>
                                    @endcan
                                 </div>
                              </div>
                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="8" class="text-center py-5 text-muted">
                              <div class="py-3">
                                 <i class="fas fa-university fa-3x text-muted mb-3 d-block opacity-50"></i>
                                 <h5>No Institutes Found</h5>
                                 <p class="small text-muted mb-0">No institute records matched your filter criteria.</p>
                              </div>
                           </td>
                        </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>

               <!-- Pagination -->
               <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                  <div class="small text-muted">
                     Showing <strong>{{ $institutes->firstItem() ?? 0 }}</strong> to <strong>{{ $institutes->lastItem() ?? 0 }}</strong> of <strong>{{ $institutes->total() }}</strong> entries
                  </div>
                  <div>
                     {{ $institutes->links('pagination::bootstrap-5') }}
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</div>
@endsection
