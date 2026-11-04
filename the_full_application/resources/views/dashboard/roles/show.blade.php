@section('title') 
Role & Permission Management || Role Details: {{ $role->name }}
@endsection 
@extends('dashboard.layouts.main')

@section('style')
<style>
.role-info-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.sticky-summary-toolbar {
    position: sticky;
    top: 65px;
    z-index: 1010;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 4px 14px rgba(0,0,0,0.07);
    transition: all 0.2s ease;
}
.module-card {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    margin-bottom: 20px;
}
.module-card:hover {
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
}
.module-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 18px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}
.permission-box {
    display: flex;
    align-items: flex-start;
    padding: 10px 12px;
    border-radius: 6px;
    transition: all 0.15s ease-in-out;
    height: 100%;
}
.permission-box.is-assigned {
    background-color: #f0fdf4 !important;
    border: 1px solid #86efac !important;
    box-shadow: 0 1px 3px rgba(34, 197, 94, 0.12);
}
.permission-box.is-unassigned {
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    opacity: 0.65;
}
.permission-box.is-unassigned:hover {
    opacity: 0.9;
    background-color: #ffffff !important;
    border-color: #cbd5e1 !important;
}
.permission-icon-indicator {
    font-size: 1.05rem;
    margin-top: 1px;
    flex-shrink: 0;
}
.permission-content-wrap {
    margin-left: 10px;
    width: 100%;
}
.permission-name-code {
    font-size: 0.82rem;
    font-weight: 600;
    word-break: break-all;
    display: block;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.permission-box.is-assigned .permission-name-code {
    color: #15803d;
}
.permission-box.is-unassigned .permission-name-code {
    color: #64748b;
}
.permission-action-badge {
    font-size: 0.70rem;
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 600;
    display: inline-block;
    margin-top: 4px;
}
.stat-pill {
    padding: 6px 14px;
    border-radius: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.stat-pill .stat-num {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
}
.stat-pill .stat-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 600;
}
.filter-btn-pill {
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.78rem;
    cursor: pointer;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    font-weight: 500;
}
.filter-btn-pill.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.sub-group-divider {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 4px;
    margin-bottom: 12px;
    margin-top: 10px;
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
               <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
               <li class="breadcrumb-item active">Role Details: {{ $role->name }}</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end text-right">
         @can('role-edit')
         <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-primary me-2 mr-2">
            <i class="fas fa-edit me-1 mr-1"></i> Edit Role
         </a>
         @endcan
         <a href="{{ route('admin.roles.index') }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-info">
            <i class="fas fa-arrow-alt-circle-left me-1 mr-1"></i> Back to Roles
         </a>
      </div>
   </div>

   @php
      // 1. Definition of Known Modules with logical icons and descriptions
      $knownModuleDefinitions = [
         'User Management' => [
            'icon' => 'fas fa-users',
            'description' => 'User accounts, listings, and user management operations',
            'prefix' => ['user-'],
         ],
         'Role & Permission Management' => [
            'icon' => 'fas fa-user-shield',
            'description' => 'Roles and permission administration operations',
            'prefix' => ['role-', 'permission-'],
         ],
         'Location Management' => [
            'icon' => 'fas fa-map-marked-alt',
            'description' => 'Geographical hierarchy: block-wise and municipality-wise',
            'prefix' => ['location-'],
         ],
         'Pension Management' => [
            'icon' => 'fas fa-hand-holding-usd',
            'description' => 'Fund requirements, disbursing officer, and daily pension disbursements',
            'prefix' => ['pension-'],
            'exclude' => ['pension-3500-'],
         ],
         'Enhanced Pension' => [
            'icon' => 'fas fa-file-invoice-dollar',
            'description' => 'Enhanced pension scheme (EP 3500) pensioner lists, data entry, and updates',
            'prefix' => ['pension-3500-'],
         ],
         'Special School' => [
            'icon' => 'fas fa-school',
            'description' => 'Special schools, staff details entry, and toilet construction monitoring',
            'prefix' => ['special-school-'],
         ],
         'DDRC' => [
            'icon' => 'fas fa-hospital-alt',
            'description' => 'District Disability Rehabilitation Centres staff details and approvals',
            'prefix' => ['DDRC-'],
         ],
         'ARC' => [
            'icon' => 'fas fa-wheelchair',
            'description' => 'Advanced Rehabilitation Centres operations and approvals',
            'prefix' => ['ARC-'],
         ],
         'PIA/NGO Institutes' => [
            'icon' => 'fas fa-landmark',
            'description' => 'PIA & NGO institutes beneficiary details and management',
            'prefix' => ['Institute-'],
         ],
         'NGO Management' => [
            'icon' => 'fas fa-hands-helping',
            'description' => 'NGO access, listings, and form approvals',
            'prefix' => ['ngo-'],
         ],
         'Field Visit Management' => [
            'icon' => 'fas fa-clipboard-check',
            'description' => 'Field visits, institute master, visit types, evidence, and exports',
            'prefix' => ['field-visit-'],
         ],
         'My Profile' => [
            'icon' => 'fas fa-id-card',
            'description' => 'Personal profile viewing, editing, and password management',
            'prefix' => ['my-profile-'],
         ],
         'Product Management' => [
            'icon' => 'fas fa-boxes',
            'description' => 'Product catalog access and operations',
            'prefix' => ['product-'],
         ],
      ];

      // 2. Action badge & label resolver with dynamic fallback
      $getActionInfo = function ($permName) {
         $knownMap = [
            'approve-form' => ['label' => 'Approve Form', 'badge' => 'badge-primary', 'icon' => 'fas fa-check-circle'],
            'fund-requirement-approval' => ['label' => 'Approve PFR', 'badge' => 'badge-primary', 'icon' => 'fas fa-stamp'],
            'evidence-upload' => ['label' => 'Evidence Upload', 'badge' => 'badge-success', 'icon' => 'fas fa-file-upload'],
            'evidence-delete' => ['label' => 'Evidence Delete', 'badge' => 'badge-danger', 'icon' => 'fas fa-trash-alt'],
            'evidence-view' => ['label' => 'Evidence View', 'badge' => 'badge-info', 'icon' => 'fas fa-images'],
            'access' => ['label' => 'Access', 'badge' => 'badge-info', 'icon' => 'fas fa-key'],
            'list' => ['label' => 'List', 'badge' => 'badge-secondary', 'icon' => 'fas fa-list'],
            'show' => ['label' => 'View Details', 'badge' => 'badge-secondary', 'icon' => 'fas fa-eye'],
            'create' => ['label' => 'Create', 'badge' => 'badge-success', 'icon' => 'fas fa-plus-circle'],
            'edit' => ['label' => 'Edit', 'badge' => 'badge-warning text-dark', 'icon' => 'fas fa-edit'],
            'delete' => ['label' => 'Delete', 'badge' => 'badge-danger', 'icon' => 'fas fa-trash'],
            'submit' => ['label' => 'Submit', 'badge' => 'badge-primary', 'icon' => 'fas fa-paper-plane'],
            'export' => ['label' => 'Export', 'badge' => 'badge-dark', 'icon' => 'fas fa-file-export'],
            'import' => ['label' => 'Import', 'badge' => 'badge-dark', 'icon' => 'fas fa-file-import'],
            'download' => ['label' => 'Download', 'badge' => 'badge-info', 'icon' => 'fas fa-download'],
            'upload' => ['label' => 'Upload', 'badge' => 'badge-success', 'icon' => 'fas fa-upload'],
            'view' => ['label' => 'View', 'badge' => 'badge-secondary', 'icon' => 'fas fa-eye'],
            'print' => ['label' => 'Print', 'badge' => 'badge-secondary', 'icon' => 'fas fa-print'],
         ];

         foreach ($knownMap as $key => $info) {
            if (str_ends_with($permName, '-' . $key) || $permName === $key) {
               return $info;
            }
         }

         // Dynamic fallback for any newly added actions
         $lastDash = strrpos($permName, '-');
         $rawAction = ($lastDash !== false) ? substr($permName, $lastDash + 1) : $permName;
         $label = ucwords(str_replace('-', ' ', $rawAction));
         return ['label' => $label, 'badge' => 'badge-light text-dark', 'icon' => 'fas fa-tag'];
      };

      // 3. Map all permissions dynamically into modules with zero hardcoded omissions
      $groupedPermissions = [];
      foreach ($knownModuleDefinitions as $modName => $def) {
         $groupedPermissions[$modName] = [
            'info' => $def,
            'permissions' => collect(),
         ];
      }

      foreach ($permission as $perm) {
         $matched = false;
         foreach ($knownModuleDefinitions as $modName => $def) {
            $isMatch = false;
            foreach ($def['prefix'] as $pfx) {
               if (str_starts_with($perm->name, $pfx)) {
                  $isMatch = true;
                  break;
               }
            }
            if ($isMatch && !empty($def['exclude'])) {
               foreach ($def['exclude'] as $ex) {
                  if (str_starts_with($perm->name, $ex)) {
                     $isMatch = false;
                     break;
                  }
               }
            }
            if ($isMatch) {
               $groupedPermissions[$modName]['permissions']->push($perm);
               $matched = true;
               break;
            }
         }
         if (!$matched) {
            // Automatically derive module name from prefix before the last hyphen
            $lastDash = strrpos($perm->name, '-');
            $dynamicModSlug = ($lastDash !== false) ? substr($perm->name, 0, $lastDash) : 'Other';
            $dynamicModName = ucwords(str_replace('-', ' ', $dynamicModSlug));
            if (!isset($groupedPermissions[$dynamicModName])) {
               $groupedPermissions[$dynamicModName] = [
                  'info' => [
                     'icon' => 'fas fa-folder-open',
                     'description' => 'Permissions for ' . $dynamicModName,
                  ],
                  'permissions' => collect(),
               ];
            }
            $groupedPermissions[$dynamicModName]['permissions']->push($perm);
         }
      }

      // Automatically filter out any module that does not exist in the database
      $groupedPermissions = array_filter($groupedPermissions, function($g) {
         return $g['permissions']->isNotEmpty();
      });

      // 4. Calculate dynamic database-driven metrics
      $totalPermissionsCount = $permission->count();
      $assignedCount = count($rolePermissions);
      $unassignedCount = $totalPermissionsCount - $assignedCount;

      $initialActiveModules = 0;
      foreach ($groupedPermissions as $g) {
         $selectedInGroup = $g['permissions']->filter(fn($p) => in_array($p->id, $rolePermissions))->count();
         if ($selectedInGroup > 0) {
            $initialActiveModules++;
         }
      }
      $totalModulesCount = count($groupedPermissions);
   @endphp

   <!-- 1. Role Information Section (Read-only) -->
   <div class="card role-info-card mb-4">
      <div class="card-header bg-light d-flex align-items-center justify-content-between flex-wrap py-3">
         <h5 class="mb-0 fw-bold text-dark">
            <i class="fas fa-id-badge text-primary me-2 mr-2"></i> Role Information
         </h5>
         <div class="d-flex align-items-center gap-2">
            <span class="badge badge-primary px-3 py-2">Role ID: #{{ $role->id }}</span>
            <span class="badge badge-secondary px-3 py-2">Guard: {{ $role->guard_name }}</span>
            <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle me-1 mr-1"></i> {{ $assignedCount }} Assigned</span>
         </div>
      </div>
      <div class="card-body">
         <div class="row align-items-center">
            <div class="col-md-6 col-lg-5">
               <div class="d-flex align-items-center">
                  <div class="avatar-box bg-light-primary text-primary rounded-circle p-3 me-3 mr-3">
                     <i class="fas fa-shield-alt fa-2x"></i>
                  </div>
                  <div>
                     <h3 class="mb-0 fw-bold text-dark">{{ $role->name }}</h3>
                     <small class="text-muted">Standard portal role definition with configured permissions.</small>
                  </div>
               </div>
            </div>
            <div class="col-md-6 col-lg-7 d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
               <div class="text-muted small">
                  <p class="mb-1"><i class="fas fa-calendar-alt text-info me-1 mr-1"></i> <strong>Created:</strong> {{ $role->created_at ? $role->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                  <p class="mb-0"><i class="fas fa-history text-success me-1 mr-1"></i> <strong>Last Updated:</strong> {{ $role->updated_at ? $role->updated_at->format('d M Y, h:i A') : 'N/A' }}</p>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- 2. Sticky Summary & Search Toolbar (Read-only) -->
   <div class="sticky-summary-toolbar p-3 mb-4">
      <div class="row align-items-center">
         <!-- Summary Stats -->
         <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
            <div class="d-flex align-items-center flex-wrap gap-2">
               <div class="stat-pill">
                  <i class="fas fa-list-check text-primary"></i>
                  <div>
                     <div class="stat-num">{{ $totalPermissionsCount }}</div>
                     <div class="stat-label">Total Permissions</div>
                  </div>
               </div>
               <div class="stat-pill border-primary bg-light-primary">
                  <i class="fas fa-check-circle text-success"></i>
                  <div>
                     <div class="stat-num text-primary">{{ $assignedCount }}</div>
                     <div class="stat-label">Assigned Permissions</div>
                  </div>
               </div>
               <div class="stat-pill">
                  <i class="fas fa-layer-group text-info"></i>
                  <div>
                     <div class="stat-num" id="active_modules_count">{{ $initialActiveModules }} / {{ $totalModulesCount }}</div>
                     <div class="stat-label">Active Modules</div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Search Field -->
         <div class="col-lg-4 col-md-7 mb-2 mb-md-0">
            <div class="input-group">
               <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
               <input type="text" id="permission_search" class="form-control border-left-0" placeholder="Search by permission or module name..." autocomplete="off">
               <button type="button" id="clear_search_btn" class="btn btn-outline-secondary border-left-0 d-none" title="Clear search">
                  <i class="fas fa-times"></i>
               </button>
            </div>
         </div>

         <!-- Action Buttons -->
         <div class="col-lg-3 col-md-5 text-end text-right">
            @can('role-edit')
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary text-white me-2 mr-2">
               <i class="fas fa-edit me-1 mr-1"></i> Edit Permissions
            </a>
            @endcan
            <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
               <i class="fas fa-arrow-left me-1 mr-1"></i> Back
            </a>
         </div>
      </div>

      <!-- Quick Filter Pills & Match Status -->
      <div class="d-flex align-items-center justify-content-between flex-wrap mt-2 pt-2 border-top">
         <div class="d-flex align-items-center gap-1">
            <span class="text-muted small me-2 mr-2">Filter View:</span>
            <button type="button" class="filter-btn-pill active" data-filter="all">All ({{ $totalPermissionsCount }})</button>
            <button type="button" class="filter-btn-pill" data-filter="assigned">Assigned ({{ $assignedCount }})</button>
            <button type="button" class="filter-btn-pill" data-filter="unassigned">Unassigned ({{ $unassignedCount }})</button>
         </div>
         <div id="search_feedback" class="small text-muted d-none">
            <i class="fas fa-filter text-info me-1 mr-1"></i> Showing <span id="search_match_count" class="fw-bold text-dark">0</span> matching permissions
         </div>
      </div>
   </div>

   <!-- 3. Module-wise Permission Sections (Read-Only) -->
   <div id="modules_container">
      @foreach($groupedPermissions as $moduleName => $group)
         @php
            $modTotal = $group['permissions']->count();
            $modAssigned = $group['permissions']->filter(function($p) use ($rolePermissions) {
               return in_array($p->id, $rolePermissions);
            })->count();
         @endphp
         <div class="card module-card" data-module-name="{{ strtolower($moduleName) }}" id="module_card_{{ Str::slug($moduleName) }}">
            <div class="module-header d-flex align-items-center justify-content-between flex-wrap">
               <div class="d-flex align-items-center">
                  <div class="me-3 mr-3">
                     <i class="{{ $group['info']['icon'] }} fa-2x text-primary"></i>
                  </div>
                  <div>
                     <h5 class="mb-0 fw-bold text-dark d-flex align-items-center flex-wrap">
                        <span class="module-title-text">{{ $moduleName }}</span>
                        <span class="badge module-selection-badge ms-2 ml-2 {{ $modAssigned === $modTotal ? 'badge-success bg-success' : ($modAssigned > 0 ? 'badge-primary bg-primary' : 'badge-secondary bg-secondary') }}">
                           {{ $modAssigned }} / {{ $modTotal }} assigned
                        </span>
                     </h5>
                     <small class="text-muted">{{ $group['info']['description'] }}</small>
                  </div>
               </div>
               <div class="mt-2 mt-sm-0 text-muted small">
                  @if($modAssigned === $modTotal)
                     <span class="text-success fw-bold"><i class="fas fa-check-double me-1 mr-1"></i> Full Access</span>
                  @elseif($modAssigned > 0)
                     <span class="text-primary fw-bold"><i class="fas fa-check me-1 mr-1"></i> Partial Access</span>
                  @else
                     <span class="text-muted"><i class="fas fa-ban me-1 mr-1"></i> No Access</span>
                  @endif
               </div>
            </div>

            <div class="card-body">
               @if($moduleName === 'Role & Permission Management')
                  {{-- Group into Sub-sections: Roles vs Permissions --}}
                  @php
                     $roleItems = $group['permissions']->filter(fn($p) => str_starts_with($p->name, 'role-'));
                     $permItems = $group['permissions']->filter(fn($p) => str_starts_with($p->name, 'permission-'));
                     $otherRolePermItems = $group['permissions']->filter(fn($p) => !str_starts_with($p->name, 'role-') && !str_starts_with($p->name, 'permission-'));
                  @endphp
                  @if($roleItems->isNotEmpty())
                  <div class="sub-group-divider"><i class="fas fa-shield-alt text-primary me-1 mr-1"></i> Role Permissions ({{ $roleItems->count() }})</div>
                  <div class="row mb-3">
                     @foreach($roleItems as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
                  @endif

                  @if($permItems->isNotEmpty())
                  <div class="sub-group-divider"><i class="fas fa-key text-info me-1 mr-1"></i> Permission Master Permissions ({{ $permItems->count() }})</div>
                  <div class="row mb-3">
                     @foreach($permItems as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
                  @endif

                  @if($otherRolePermItems->isNotEmpty())
                  <div class="row">
                     @foreach($otherRolePermItems as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
                  @endif

               @elseif($moduleName === 'Field Visit Management')
                  {{-- Group into Sub-sections: Field Visits, Institute Master, Visit Types --}}
                  @php
                     $visitItems = $group['permissions']->filter(fn($p) => str_starts_with($p->name, 'field-visit-') && !str_starts_with($p->name, 'field-visit-institute-') && !str_starts_with($p->name, 'field-visit-type-'));
                     $instItems = $group['permissions']->filter(fn($p) => str_starts_with($p->name, 'field-visit-institute-'));
                     $typeItems = $group['permissions']->filter(fn($p) => str_starts_with($p->name, 'field-visit-type-'));
                     $otherVisitItems = $group['permissions']->filter(fn($p) => !str_starts_with($p->name, 'field-visit-'));
                  @endphp
                  @if($visitItems->isNotEmpty())
                  <div class="sub-group-divider"><i class="fas fa-walking text-primary me-1 mr-1"></i> Field Visits, Submissions & Evidence ({{ $visitItems->count() }})</div>
                  <div class="row mb-3">
                     @foreach($visitItems as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
                  @endif

                  @if($instItems->isNotEmpty())
                  <div class="sub-group-divider"><i class="fas fa-university text-info me-1 mr-1"></i> Field Visit Institute Master ({{ $instItems->count() }})</div>
                  <div class="row mb-3">
                     @foreach($instItems as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
                  @endif

                  @if($typeItems->isNotEmpty())
                  <div class="sub-group-divider"><i class="fas fa-tags text-success me-1 mr-1"></i> Field Visit Types ({{ $typeItems->count() }})</div>
                  <div class="row mb-3">
                     @foreach($typeItems as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
                  @endif

                  @if($otherVisitItems->isNotEmpty())
                  <div class="row">
                     @foreach($otherVisitItems as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
                  @endif

               @else
                  <div class="row">
                     @foreach($group['permissions'] as $p)
                        @php
                           $isAssigned = in_array($p->id, $rolePermissions);
                           $action = $getActionInfo($p->name);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 permission-col" data-permission-name="{{ strtolower($p->name) }}" data-action-name="{{ strtolower($action['label']) }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                           <div class="permission-box {{ $isAssigned ? 'is-assigned' : 'is-unassigned' }}">
                              <div class="permission-icon-indicator">
                                 @if($isAssigned)
                                    <i class="fas fa-check-circle text-success"></i>
                                 @else
                                    <i class="far fa-circle text-muted"></i>
                                 @endif
                              </div>
                              <div class="permission-content-wrap">
                                 <span class="permission-name-code">{{ $p->name }}</span>
                                 <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="badge permission-action-badge {{ $action['badge'] }}">
                                       <i class="{{ $action['icon'] }}"></i> {{ $action['label'] }}
                                    </span>
                                    <span class="badge {{ $isAssigned ? 'badge-success bg-success' : 'badge-light text-muted' }}" style="font-size: 0.65rem;">
                                       {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
               @endif

               <div class="module-no-match text-center text-muted py-3 d-none">
                  <i class="fas fa-search me-1 mr-1"></i> No permissions in this module match your search filter.
               </div>
            </div>
         </div>
      @endforeach
   </div>

   <!-- No Search Results Found Global Placeholder -->
   <div id="no_global_matches" class="alert alert-warning text-center py-4 d-none">
      <h5 class="fw-bold mb-1"><i class="fas fa-search me-2 mr-2"></i> No permissions found</h5>
      <p class="mb-0 text-muted">No permissions match your current search query. Try searching for a different keyword or click Clear.</p>
   </div>

   <!-- Bottom Action Bar (Read-only) -->
   <div class="card bg-light border p-3 mb-5">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
         <div>
            <h6 class="mb-0 fw-bold text-dark">Role "{{ $role->name }}" currently has {{ $assignedCount }} active permissions.</h6>
            <small class="text-muted">To modify assignments, click "Edit Role Permissions" below.</small>
         </div>
         <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
               <i class="fas fa-arrow-left me-1 mr-1"></i> Back to Roles
            </a>
            @can('role-edit')
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-primary text-white px-4">
               <i class="fas fa-edit me-1 mr-1"></i> Edit Role Permissions
            </a>
            @endcan
         </div>
      </div>
   </div>
</div>
@endsection 

@section('script')
<script type="text/javascript">
   // State variables
   var currentFilter = 'all';
   var currentSearchQuery = '';

   // Initialize on DOMContentLoaded
   document.addEventListener('DOMContentLoaded', function() {
      countActiveModules();
      setupEventListeners();
   });

   // Count active modules dynamically from live DOM
   function countActiveModules() {
      var activeModules = 0;
      var moduleCards = document.querySelectorAll('.module-card');
      moduleCards.forEach(function(card) {
         var assignedInModule = card.querySelectorAll('[data-assigned="true"]').length;
         if (assignedInModule > 0) {
            activeModules++;
         }
      });
      var activeModulesEl = document.getElementById('active_modules_count');
      if (activeModulesEl) {
         activeModulesEl.textContent = activeModules + ' / ' + moduleCards.length;
      }
   }

   // Set up event listeners
   function setupEventListeners() {
      var searchInput = document.getElementById('permission_search');
      var clearBtn = document.getElementById('clear_search_btn');

      if (searchInput) {
         searchInput.addEventListener('input', function() {
            currentSearchQuery = this.value.trim().toLowerCase();
            if (clearBtn) {
               if (currentSearchQuery.length > 0) {
                  clearBtn.classList.remove('d-none');
               } else {
                  clearBtn.classList.add('d-none');
               }
            }
            applyFilters();
         });
      }

      if (clearBtn) {
         clearBtn.addEventListener('click', function() {
            if (searchInput) {
               searchInput.value = '';
               currentSearchQuery = '';
               this.classList.add('d-none');
               searchInput.focus();
               applyFilters();
            }
         });
      }

      // Filter pills
      var filterPills = document.querySelectorAll('.filter-btn-pill');
      filterPills.forEach(function(btn) {
         btn.addEventListener('click', function() {
            filterPills.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            applyFilters();
         });
      });
   }

   // Filtering function: handles search query and assigned/unassigned view filter
   function applyFilters() {
      var query = currentSearchQuery;
      var filter = currentFilter;
      var moduleCards = document.querySelectorAll('.module-card');
      var totalMatches = 0;

      moduleCards.forEach(function(card) {
         var moduleName = card.getAttribute('data-module-name') || '';
         var moduleMatchesQuery = (query.length > 0 && moduleName.indexOf(query) !== -1);
         var items = card.querySelectorAll('.permission-col');
         var moduleVisibleItems = 0;

         items.forEach(function(item) {
            var permName = item.getAttribute('data-permission-name') || '';
            var actionName = item.getAttribute('data-action-name') || '';
            var isAssigned = item.getAttribute('data-assigned') === 'true';

            // Check view filter: all, assigned, unassigned
            var matchesFilter = true;
            if (filter === 'assigned' && !isAssigned) matchesFilter = false;
            if (filter === 'unassigned' && isAssigned) matchesFilter = false;

            // Check search query
            var matchesSearch = true;
            if (query.length > 0 && !moduleMatchesQuery) {
               if (permName.indexOf(query) === -1 && actionName.indexOf(query) === -1) {
                  matchesSearch = false;
               }
            }

            if (matchesFilter && matchesSearch) {
               item.style.display = '';
               moduleVisibleItems++;
               totalMatches++;
            } else {
               item.style.display = 'none';
            }
         });

         var noMatchEl = card.querySelector('.module-no-match');
         if (moduleVisibleItems === 0) {
            if (query.length > 0 || filter !== 'all') {
               card.style.display = 'none';
            } else {
               card.style.display = '';
               if (noMatchEl) noMatchEl.classList.remove('d-none');
            }
         } else {
            card.style.display = '';
            if (noMatchEl) noMatchEl.classList.add('d-none');
         }
      });

      // Update match count indicator
      var feedbackEl = document.getElementById('search_feedback');
      var matchCountEl = document.getElementById('search_match_count');
      var noGlobalMatches = document.getElementById('no_global_matches');

      if (query.length > 0 || filter !== 'all') {
         if (feedbackEl) feedbackEl.classList.remove('d-none');
         if (matchCountEl) matchCountEl.textContent = totalMatches;
         if (noGlobalMatches) {
            if (totalMatches === 0) {
               noGlobalMatches.classList.remove('d-none');
            } else {
               noGlobalMatches.classList.add('d-none');
            }
         }
      } else {
         if (feedbackEl) feedbackEl.classList.add('d-none');
         if (noGlobalMatches) noGlobalMatches.classList.add('d-none');
      }
   }
</script>
@endsection
