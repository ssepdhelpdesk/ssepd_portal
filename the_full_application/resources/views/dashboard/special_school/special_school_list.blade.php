@extends('dashboard.layouts.main')

@section('title') 
Special School || List
@endsection 

@section('style')
<style>
	table.dataTable thead th.wrap-text,
	table.dataTable tbody td.wrap-text{
		white-space:normal!important;
		word-break:break-word;
		line-height:1.2;
	}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-7 align-self-center">
			<div class="d-flex align-items-center">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
					<li class="breadcrumb-item active">@yield('title')</li>
				</ol>
			</div>
		</div>
		<div class="col-md-5 align-self-center text-end">
			<button onclick="history.back()" class="btn btn-info btn-xs text-white">
				<i class="fas fa-arrow-alt-circle-left"></i> Go Back
			</button>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">

					@include('dashboard.component.message')

					<div class="table-responsive m-t-40">
						<table id="example23" class="display table table-hover table-striped border" width="100%">
							<thead>
								<tr>
									<th data-priority="1">Sl No</th>
									<th data-priority="2">District</th>
									<th data-priority="9">Management ID</th>
									<th data-priority="4">Management Name</th>
									<th data-priority="9">School ID</th>
									<th data-priority="3">School Name</th>
									<th data-priority="2">Approved Staff</th>									
									<th data-priority="2">Current Staff</th>
									<th data-priority="2">Staff Status</th>
									<th data-priority="2">Toilet Construction</th>
									<th data-priority="2">Toilet Construction Approval</th>
									<th data-priority="6">Grant</th>
									<th data-priority="6">Establishment</th>
									<th data-priority="7">Category</th>
									<th data-priority="7">Type</th>
									<th data-priority="8">ACT No</th>
									<th data-priority="8" class="no-export">ACT File</th>
									<th data-priority="9">Address</th>
									<th data-priority="1" class="no-export">Action</th>
								</tr>
							</thead>

							<tbody>
								@php $i=1; @endphp
								@forelse($specialSchool as $schoolDetails)

								@php
								$hasSchool=!empty($schoolDetails->special_school_id);
								$hasStaff=$schoolDetails->staff_count>0;
								$hasConstruction=$schoolDetails->construction_count>0;
								@endphp

								<tr>
									<td>{{ $i++ }}</td>

									<td>
										@if($schoolDetails->district_name)
										{{ $schoolDetails->district_name }}
										@else
										<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
										@endif
									</td>

									<td class="wrap-text">
										@if($schoolDetails->management_id)
										{{ $schoolDetails->management_id }}
										@else
										<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
										@endif
									</td>

									<td class="wrap-text">
										@if($schoolDetails->management_name)
										{{ $schoolDetails->management_name }}
										@else
										<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
										@endif
									</td>

									<td class="wrap-text">
										@if($schoolDetails->special_school_id)
										{{ $schoolDetails->special_school_id }}
										@else
										<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
										@endif
									</td>

									<td class="wrap-text">
										@if($schoolDetails->special_school_name)
										{{ $schoolDetails->special_school_name }}
										@else
										<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
										@endif
									</td>

									<td>
										@if($schoolDetails->approved_staff_total > 0)
										<span class="badge bg-info text-white"
										data-bs-toggle="tooltip"
										title="Teaching: {{ $schoolDetails->teaching_approved_staff_strength }} || Non-Teaching: {{ $schoolDetails->non_teaching_approved_staff_strength }}">
										{{ $schoolDetails->approved_staff_total }}
									</span>
									@else
									<i class="fas fa-times-circle text-danger"
									data-bs-toggle="tooltip"
									title="No Approved Staff"></i>
									@endif
								</td>

								<td>
									@if($hasStaff)
									<a href="{{ route('admin.specialschool.view_staff_details_by_state_office',$schoolDetails->special_school_id) }}"
										target="_blank"
										class="badge bg-success text-white"
										data-bs-toggle="tooltip"
										title="View Staff Details ({{ $schoolDetails->staff_count }})">
										{{ $schoolDetails->staff_count }}
										<i class="fas fa-users ms-1"></i>
									</a>
									@else
									<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="No Staff Data"></i>
									@endif
								</td>

								<td class="wrap-text">
    @php
        $approved = $schoolDetails->approved_staff_total ?? 0;
        $actual = $schoolDetails->staff_count ?? 0;
        $gap = $schoolDetails->staff_gap ?? 0;
        $util = $schoolDetails->staff_utilization ?? 0;
    @endphp

    @if($approved > 0)

        @if($actual > $approved)
            <span class="badge bg-primary"
                  data-bs-toggle="tooltip"
                  title="Overstaffed (Extra: {{ abs($gap) }})">
                {{ $actual }}/{{ $approved }} ({{ $util }}%)
            </span>

        @elseif($actual == $approved)
            <span class="badge bg-success"
                  data-bs-toggle="tooltip"
                  title="Fully Staffed">
                {{ $actual }}/{{ $approved }} (100%)
            </span>

        @elseif($actual > 0)
            <span class="badge bg-warning text-dark"
                  data-bs-toggle="tooltip"
                  title="Shortage: {{ $gap }}">
                {{ $actual }}/{{ $approved }} ({{ $util }}%)
            </span>

        @else
            <span class="badge bg-danger"
                  data-bs-toggle="tooltip"
                  title="No Staff Available">
                0/{{ $approved }} (0%)
            </span>
        @endif

    @else
        <i class="fas fa-times-circle text-danger"
           data-bs-toggle="tooltip"
           title="No Approved Strength"></i>
    @endif
</td>

								<td>
									@if($hasConstruction)
									<a href="{{ route('admin.specialschoolconstructions.index',$schoolDetails->special_school_id) }}"
										target="_blank"
										class="badge bg-warning text-white"
										data-bs-toggle="tooltip"
										title="View Construction ({{ $schoolDetails->construction_count }} Phase)">
										{{ $schoolDetails->construction_count }}
										<i class="fas fa-eye ms-1"></i>
									</a>
									@else
									<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="No Construction Data"></i>
									@endif
								</td>

								<td class="wrap-text">
									@if($schoolDetails->phase_approval_details)
									{{ $schoolDetails->phase_approval_details }}
									@else
									<i class="fas fa-times-circle text-danger" 
									data-bs-toggle="tooltip" 
									title="No Approval Data"></i>
									@endif
								</td>

								<td>
									@if($schoolDetails->which_govt==1)
									Govt of Odisha
									@elseif($schoolDetails->which_govt==2)
									Govt of India
									@else
									<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
									@endif
								</td>

								<td>
									@if($schoolDetails->school_establishment_date)
									{{ \Carbon\Carbon::parse($schoolDetails->school_establishment_date)->format('d M Y') }}
									@else
									<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
									@endif
								</td>

								<td>
									@php $categories=[1=>'VI',2=>'HI',3=>'MR/ID',4=>'CP',5=>'ASD']; @endphp
									{{ $categories[$schoolDetails->school_category] ?? '' }}
								</td>

								<td>
									@php $types=[1=>'Residential',2=>'Non Residential']; @endphp
									{{ $types[$schoolDetails->school_type] ?? '' }}
								</td>

								<td>
									@if($schoolDetails->act_reg_no)
									{{ $schoolDetails->act_reg_no }}
									@else
									<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
									@endif
								</td>

								<td>
									@if($schoolDetails->file_act_reg)
									<a href="{{ url('storage/'.$schoolDetails->file_act_reg) }}"
										target="_blank"
										class="badge bg-warning text-white"
										data-bs-toggle="tooltip"
										title="View File">
										<i class="fas fa-eye"></i>
									</a>
									@else
									<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="No File"></i>
									@endif
								</td>

								<td>
									@if($schoolDetails->full_address)
									{{ $schoolDetails->full_address }}
									@else
									<i class="fas fa-times-circle text-danger" data-bs-toggle="tooltip" title="Not Provided"></i>
									@endif
								</td>

								<td>
									@can('special-school-show')
									@if($hasSchool && ($hasStaff || $hasConstruction))
									<div class="btn-group">
										<button type="button" class="btn btn-danger btn-xs dropdown-toggle" data-bs-toggle="dropdown">Action</button>
										<div class="dropdown-menu">
											@if($hasStaff)
											<a class="dropdown-item" href="{{ route('admin.specialschool.view_staff_details_by_state_office',$schoolDetails->special_school_id) }}">View Staff Details</a>
											@endif
											@if($hasConstruction)
											<a class="dropdown-item" href="{{ route('admin.specialschoolconstructions.index',$schoolDetails->special_school_id) }}">Construction Status</a>
											@endif
										</div>
									</div>
									@endif
									@endcan
								</td>

							</tr>

							@empty
							<tr>
								<td colspan="14" class="text-center">No Records Found</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
</div>
@endsection

@section('script')
<script>
	$(function(){

// Tooltip init
		var tooltipTriggerList=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el){return new bootstrap.Tooltip(el);});

// DataTable
		$('#example23').DataTable({
			processing:true,
			responsive:true,
			autoWidth:false,
			ordering:true,
			lengthMenu:[[10,500,1000,-1],[10,500,1000,"All"]],
			dom:'Blfrtip',
			buttons:[
				{extend:'copy',exportOptions:{columns:':not(.no-export)',format:{body:exportFormatter}}},
				{extend:'csv',exportOptions:{columns:':not(.no-export)',format:{body:exportFormatter}}},
				{extend:'excel',exportOptions:{columns:':not(.no-export)',format:{body:exportFormatter}}},
				{extend:'pdf',exportOptions:{columns:':not(.no-export)',format:{body:exportFormatter}}},
				{extend:'print',exportOptions:{columns:':not(.no-export)',format:{body:exportFormatter}}}
			]
		});

		function exportFormatter(data, row, column, node) {

			let text = $('<div>').html(data).text().trim();

    // ✅ If numeric or meaningful text exists → return it
			if (text && text !== '') {
				return text;
			}

    // ❌ No text → then check icons
			if ($(node).find('.fa-users').length) {
        return '0'; // no staff
    }

    if ($(node).find('.fa-eye').length) {
        return '0'; // no construction count visible
    }

    if ($(node).find('.fa-times-circle').length) {
    	return 'Not Provided';
    }

    return 'NA';
}

});
</script>
@endsection