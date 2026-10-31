@section('title') 
Visit Type Master || View Details
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .detail-label {
      font-weight: 600;
      color: #495057;
      width: 200px;
   }
   .detail-value {
      color: #212529;
   }
   .badge-code {
      font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size: 0.95rem;
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
               <li class="breadcrumb-item"><a href="{{ route('admin.field_visit_types.index') }}">Field Visit</a></li>
               <li class="breadcrumb-item active">Visit Type Details</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <a href="{{ route('admin.field_visit_types.index') }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info">
            <i class="fas fa-arrow-alt-circle-left"></i> Back to List
         </a>
         @can('field-visit-type-edit')
         <a href="{{ route('admin.field_visit_types.edit', $visitType->id) }}" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-warning">
            <i class="fas fa-edit"></i> Edit Visit Type
         </a>
         @endcan
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <!-- Start Page Content -->
   <div class="row">
      <div class="col-lg-10 col-md-12 mx-auto">
         <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
               <h5 class="mb-0 text-white"><i class="fas fa-info-circle me-2"></i> Visit Type Details: {{ $visitType->name }}</h5>
               <div>
                  @if($visitType->is_active)
                  <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                  @else
                  <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Inactive</span>
                  @endif
               </div>
            </div>
            <div class="card-body">
               <table class="table table-bordered table-striped mb-0">
                  <tbody>
                     <tr>
                        <td class="detail-label">Record ID</td>
                        <td class="detail-value font-monospace">#{{ $visitType->id }}</td>
                     </tr>
                     <tr>
                        <td class="detail-label">Visit Type Name</td>
                        <td class="detail-value fs-5 fw-bold text-primary">{{ $visitType->name }}</td>
                     </tr>
                     <tr>
                        <td class="detail-label">System Code</td>
                        <td class="detail-value">
                           <span class="badge bg-info badge-code">{{ $visitType->code }}</span>
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Display Sort Order</td>
                        <td class="detail-value">
                           <span class="badge bg-light text-dark border">{{ $visitType->sort_order }}</span>
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Operational Status</td>
                        <td class="detail-value">
                           @if($visitType->is_active)
                           <span class="badge bg-success"><i class="fas fa-check me-1"></i> Active (Available for surveys)</span>
                           @else
                           <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Inactive (Hidden from new surveys)</span>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Description / Purpose</td>
                        <td class="detail-value text-wrap">
                           {{ $visitType->description ?: 'No detailed description provided.' }}
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Created At</td>
                        <td class="detail-value">
                           {{ $visitType->created_at ? $visitType->created_at->format('d F Y, h:i:s A') : 'N/A' }}
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Created By</td>
                        <td class="detail-value">
                           @if($visitType->creator)
                              {{ $visitType->creator->name }} ({{ $visitType->creator->email }})
                              <span class="badge bg-light text-muted border ms-2">user_table_id: #{{ $visitType->created_by }}</span>
                           @elseif($visitType->created_by)
                              <span class="badge bg-light text-muted border">user_table_id: #{{ $visitType->created_by }}</span>
                           @else
                              <span class="text-muted">System / Seeded</span>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Last Updated At</td>
                        <td class="detail-value">
                           {{ $visitType->updated_at ? $visitType->updated_at->format('d F Y, h:i:s A') : 'N/A' }}
                        </td>
                     </tr>
                     <tr>
                        <td class="detail-label">Last Updated By</td>
                        <td class="detail-value">
                           @if($visitType->updater)
                              {{ $visitType->updater->name }} ({{ $visitType->updater->email }})
                              <span class="badge bg-light text-muted border ms-2">user_table_id: #{{ $visitType->updated_by }}</span>
                           @elseif($visitType->updated_by)
                              <span class="badge bg-light text-muted border">user_table_id: #{{ $visitType->updated_by }}</span>
                           @else
                              <span class="text-muted">Not updated yet</span>
                           @endif
                        </td>
                     </tr>
                  </tbody>
               </table>

               <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                  <a href="{{ route('admin.field_visit_types.index') }}" class="btn btn-secondary">
                     <i class="fas fa-arrow-left me-1"></i> Back to List
                  </a>
                  <div class="d-flex gap-2">
                     @can('field-visit-type-edit')
                     <a href="{{ route('admin.field_visit_types.edit', $visitType->id) }}" class="btn btn-warning text-dark fw-bold">
                        <i class="fas fa-edit me-1"></i> Edit
                     </a>
                     @endcan

                     @can('field-visit-type-delete')
                     <a href="{{ route('admin.field_visit_types.destroy', $visitType->id) }}" class="btn btn-danger text-white" id="delete">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                     </a>
                     @endcan
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</div>
@endsection
