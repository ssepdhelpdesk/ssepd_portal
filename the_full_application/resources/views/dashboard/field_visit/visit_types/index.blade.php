@section('title') 
Visit Type Master || List
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
               <li class="breadcrumb-item active">Visit Types</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         @can('field-visit-type-create')
         <a href="{{ route('admin.field_visit_types.create') }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success">
            <i class="fa fa-plus-circle"></i> Add Visit Type
         </a>
         @endcan
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <!-- Start Page Content -->
   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <div>
                     <h4 class="card-title mb-1">Field Visit Types</h4>
                     <p class="text-muted small mb-0">Master catalog of standardized departmental visit and inspection categories.</p>
                  </div>
                  <div>
                     <span class="badge bg-primary fs-6 px-3 py-2">
                        Total Records: {{ $visitTypes->total() }}
                     </span>
                  </div>
               </div>

               @include('dashboard.component.message')

               <!-- Filter & Search Toolbar -->
               <form method="GET" action="{{ route('admin.field_visit_types.index') }}" class="row g-2 mb-4 align-items-end">
                  <div class="col-md-5 col-sm-12">
                     <label for="search" class="form-label small text-muted mb-1">Search Keywords</label>
                     <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}" 
                               class="form-control" 
                               placeholder="Search by name, code, or description...">
                     </div>
                  </div>

                  <div class="col-md-3 col-sm-6">
                     <label for="status" class="form-label small text-muted mb-1">Status Filter</label>
                     <select name="status" id="status" class="form-select">
                        <option value="">-- All Statuses --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
                     </select>
                  </div>

                  <div class="col-md-4 col-sm-6 d-flex gap-2">
                     <button type="submit" class="btn btn-info text-white flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                     </button>
                     @if(request()->hasAny(['search', 'status']))
                     <a href="{{ route('admin.field_visit_types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> Reset
                     </a>
                     @endif
                  </div>
               </form>

               <!-- Table Data -->
               <div class="table-responsive">
                  <table class="table table-bordered table-hover align-middle">
                     <thead class="table-dark">
                        <tr>
                           <th style="width: 50px;" class="text-center">#</th>
                           <th>Visit Type Name</th>
                           <th style="width: 140px;">Code</th>
                           <th>Description</th>
                           <th style="width: 90px;" class="text-center">Sort Order</th>
                           <th style="width: 100px;" class="text-center">Status</th>
                           <th style="width: 140px;">Created At</th>
                           <th style="width: 90px;" class="text-center">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($visitTypes as $index => $type)
                        <tr>
                           <td class="text-center text-muted">
                              {{ $visitTypes->firstItem() + $index }}
                           </td>
                           <td>
                              <span class="fw-bold text-dark">{{ $type->name }}</span>
                           </td>
                           <td>
                              <span class="badge bg-secondary badge-code text-dark">{{ $type->code }}</span>
                           </td>
                           <td class="small text-muted">
                              {{ $type->description ? \Illuminate\Support\Str::limit($type->description, 70) : '—' }}
                           </td>
                           <td class="text-center">
                              <span class="badge bg-light text-dark border">{{ $type->sort_order }}</span>
                           </td>
                           <td class="text-center">
                              @if($type->is_active)
                              <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                              @else
                              <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Inactive</span>
                              @endif
                           </td>
                           <td class="small text-muted">
                              {{ $type->created_at ? $type->created_at->format('d-M-Y') : '—' }}
                           </td>
                           <td class="text-center">
                              <div class="btn-group">
                                 <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                 </button>
                                 <div class="dropdown-menu dropdown-menu-end">
                                    @can('field-visit-type-show')
                                    <a class="dropdown-item" href="{{ route('admin.field_visit_types.show', $type->id) }}">
                                       <i class="fas fa-eye text-info me-2"></i> View
                                    </a>
                                    @endcan
                                    @can('field-visit-type-edit')
                                    <a class="dropdown-item" href="{{ route('admin.field_visit_types.edit', $type->id) }}">
                                       <i class="fas fa-edit text-warning me-2"></i> Edit
                                    </a>
                                    @endcan
                                    @can('field-visit-type-delete')
                                    <a class="dropdown-item text-danger" href="{{ route('admin.field_visit_types.destroy', $type->id) }}" id="delete">
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
                              <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
                              <h5>No Visit Types Found</h5>
                              <p class="small mb-3">No field visit type records match the current filter criteria.</p>
                              @can('field-visit-type-create')
                              <a href="{{ route('admin.field_visit_types.create') }}" class="btn btn-success btn-sm">
                                 <i class="fa fa-plus-circle me-1"></i> Add First Visit Type
                              </a>
                              @endcan
                           </td>
                        </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>

               <!-- Pagination -->
               <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                  <div class="small text-muted mb-2">
                     Showing {{ $visitTypes->firstItem() ?? 0 }} to {{ $visitTypes->lastItem() ?? 0 }} of {{ $visitTypes->total() }} entries
                  </div>
                  <div>
                     {{ $visitTypes->links('pagination::bootstrap-5') }}
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</div>
@endsection
