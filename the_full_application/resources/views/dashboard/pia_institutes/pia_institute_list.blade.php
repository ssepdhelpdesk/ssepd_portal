@section('title') 
PIA || Institute List
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
               <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
               <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <button onclick="history.back()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info"><i class="fas fa-arrow-alt-circle-left"></i> Go Back</button>
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->
   <!-- Start Page Content -->
   <!-- ============================================================== -->
   <!-- row -->
   <div class="row">
      <div class="col-12">
         <div class="card mb-3 shadow-sm">
          <div class="card-header bg-primary text-white">
           <h5 class="mb-0">
            <i class="fas fa-filter"></i> Filter Institutes
         </h5>
      </div>

      <div class="card-body">
        <form method="GET" action="{{ route('admin.piainstitutes.pia_institute_list') }}">

          <div class="row align-items-end">

            <!-- District -->
            <div class="col-md-3">
              <label class="form-label fw-bold">District</label>

              <select name="district_id" class="form-select">
                <option value="">-- All Districts --</option>

                @foreach($districts as $district)
                <option value="{{ $district->district_id }}"
                 {{ request('district_id') == $district->district_id ? 'selected' : '' }}>
                 {{ $district->district_name }}
              </option>
              @endforeach
           </select>
        </div>

        <!-- Institute Type -->
        <div class="col-md-3">
           <label class="form-label fw-bold">Institute Type</label>

           <select name="institute_type_id" class="form-select">
             <option value="">-- All Institute Types --</option>

             <option value="1" {{ request('institute_type_id') == 1 ? 'selected' : '' }}>
               Geriatric Center
            </option>

            <option value="2" {{ request('institute_type_id') == 2 ? 'selected' : '' }}>
               Disha Center
            </option>

            <option value="3" {{ request('institute_type_id') == 3 ? 'selected' : '' }}>
               Sahaya Center
            </option>

            <option value="4" {{ request('institute_type_id') == 4 ? 'selected' : '' }}>
               Old Age Home
            </option>

            <option value="5" {{ request('institute_type_id') == 5 ? 'selected' : '' }}>
               Half Way Home
            </option>

            <option value="6" {{ request('institute_type_id') == 6 ? 'selected' : '' }}>
               Therapeutic Center
            </option>
         </select>
      </div>

      <!-- Basic Details Status -->
      <div class="col-md-3">
        <label class="form-label fw-bold">Basic Details Status</label>

        <select name="basic_details_completed" class="form-select">
          <option value="">-- All --</option>

          <option value="1" {{ request('basic_details_completed') == '1' ? 'selected' : '' }}>
            Completed
         </option>

         <option value="0" {{ request('basic_details_completed') == '0' ? 'selected' : '' }}>
            Not Completed
         </option>
      </select>
   </div>

   <!-- Buttons -->
   <div class="col-md-3">
     <label class="form-label d-block">&nbsp;</label>

     <div class="d-flex gap-2">
       <button type="submit" class="btn btn-primary flex-fill">
         <i class="fas fa-search"></i> Search
      </button>

      <a href="{{ route('admin.piainstitutes.pia_institute_list') }}"
      class="btn btn-danger">
      <i class="fas fa-sync-alt"></i>
   </a>
</div>
</div>

</div>

</form>
</div>
</div>
<div class="card">
   <div class="card-body">
      <h4 class="card-title"></h4>
      @include('dashboard.component.message')
      <div class="table-responsive m-t-40">
         <table id="example23" class="display table table-hover table-striped border" cellspacing="0" width="100%">
            <thead>
               <tr>
                  <th>Sl No</th>
                  <th>District</th>
                  <th>PIA/NGO Name</th>
                  <th>Institute Name</th>
                  <th>Institute Type</th>
                  <th>Institute Type (Updated By Center)</th>
                  <th>Nodal Officer</th>
                  <th>Nodal Officer Mob No</th>
                  <th>User ID</th>
                  <th>Address</th>
                  <th>Basic Details Completed</th>
                  <th>No of Inmates</th>
               </tr>
            </thead>
            <tfoot>
               <tr>
                  <th>Sl No</th>
                  <th>District</th>
                  <th>PIA/NGO Name</th>
                  <th>Institute Name</th>
                  <th>Institute Type</th>
                  <th>Institute Type (Updated By Center)</th>
                  <th>Nodal Officer</th>
                  <th>Nodal Officer Mob No</th>
                  <th>User ID</th>
                  <th>Address</th>
                  <th>Basic Details Completed</th>
                  <th>No of Inmates</th>
               </tr>
            </tfoot>
            <tbody>
               @php $i = 1; @endphp
               @if ($piainstitutemaster->isNotEmpty())
               @foreach($piainstitutemaster as $key => $piaInstituteDetails)
               <tr>
                  <td>{{ $i++ }}</td>
                  <td>{{ $piaInstituteDetails->district->district_name ?? 'Not Available' }}</td>
                  <td class="wrap-text">
                     @if($piaInstituteDetails->basic_details_completed == 1)
                     {{ ucwords(strtolower($piaInstituteDetails->pia_name ?? 'Not Available')) }}
                     @elseif($piaInstituteDetails->basic_details_completed == 0)
                     {{ ucwords(strtolower($piaInstituteDetails->excel_pia_name ?? 'Not Available')) }}
                     @endif
                  </td>
                  <td class="wrap-text">
                     @if($piaInstituteDetails->basic_details_completed == 1)
                     {{ ucwords(strtolower($piaInstituteDetails->institute_name ?? 'Not Available')) }}
                     @elseif($piaInstituteDetails->basic_details_completed == 0)
                     {{ ucwords(strtolower($piaInstituteDetails->excel_institute_name ?? 'Not Available')) }}
                     @endif
                  </td>
                  <td>
                     @php
                     $categories = [
                     1 => 'Geriatric Center',
                     2 => 'Disha Center',
                     3 => 'Sahaya Center',
                     4 => 'Old Age Home',
                     5 => 'Half Way Home',
                     6 => 'Therapeutic Center'
                     ];
                     @endphp
                     {{ $categories[$piaInstituteDetails->excel_institute_type_id] ?? 'Not Specified' }}
                  </td>
                  <td>
                     @php
                     $categories = [
                     1 => 'Geriatric Center',
                     2 => 'Disha Center',
                     3 => 'Sahaya Center',
                     4 => 'Old Age Home',
                     5 => 'Half Way Home',
                     6 => 'Therapeutic Center'
                     ];
                     @endphp
                     {{ $categories[$piaInstituteDetails->institute_type_id] ?? 'Not Specified' }}
                  </td>
                  <td>
                     @if($piaInstituteDetails->basic_details_completed == 1)
                     {{ ucwords(strtolower($piaInstituteDetails->nodal_officer_name ?? 'Not Available')) }}
                     @elseif($piaInstituteDetails->basic_details_completed == 0)
                     {{ ucwords(strtolower($piaInstituteDetails->excel_nodal_officer_name ?? 'Not Available')) }}
                     @endif
                  </td>
                  <td>
                     @if($piaInstituteDetails->basic_details_completed == 1)
                     {{ $piaInstituteDetails->nodal_officer_contact_number ?? 'Not Available' }}
                     @elseif($piaInstituteDetails->basic_details_completed == 0)
                     {{ $piaInstituteDetails->excel_nodal_officer_contact_number ?? 'Not Available' }}
                     @endif
                  </td>
                  <td>{{ $piaInstituteDetails->excel_institute_user_id ?? 'Not Available' }}</td>
                  <td class="wrap-text">
                     @if($piaInstituteDetails->basic_details_completed == 1)
                     @if($piaInstituteDetails->address_type ==1)
                     AT: {{ $piaInstituteDetails->village->village_name ?? 'Not Available' }}, 
                     GP: {{ $piaInstituteDetails->grampanchayat->gp_name ?? 'Not Available' }}, 
                     Block: {{ $piaInstituteDetails->block->block_name ?? 'Not Available' }}, 
                     District: {{ $piaInstituteDetails->district->district_name ?? 'Not Available' }}
                     @elseif($piaInstituteDetails->address_type ==2)
                     WARD: {{ $piaInstituteDetails->ward->ward_name ?? 'Not Available' }}, 
                     ULB: {{ $piaInstituteDetails->municipality->municipality_name ?? 'Not Available' }}, 
                     District: {{ $piaInstituteDetails->district->district_name ?? 'Not Available' }}
                     @endif
                     @elseif($piaInstituteDetails->basic_details_completed == 0)
                     {{ $piaInstituteDetails->excel_institute_address ? ucwords(strtolower($piaInstituteDetails->excel_institute_address)) : 'Address Not Provided' }}
                     @endif                              
                  </td>
                  <td>
                     {{ $piaInstituteDetails->basic_details_completed == 1 ? '✅' : '❌' }}
                  </td>
                  <td>
                     @if($piaInstituteDetails->beneficiaries_count > 0)
                     <a href="{{ route('admin.piainstitutes.pia_institute_benf_details_admin', $piaInstituteDetails->institute_id) }}"><span class="badge bg-info text-white"
                     data-bs-toggle="tooltip"
                     title="Inmates: {{ $piaInstituteDetails->beneficiaries_count }}">
                     {{ $piaInstituteDetails->beneficiaries_count }}
                  </span></a>
                  @else
                  <i class="fas fa-times-circle text-danger"
                  data-bs-toggle="tooltip"
                  title="Not Provided"></i>
                  @endif
               </td>
            </tr>
            @endforeach
            @endif
         </tbody>
      </table>
   </div>
</div>
</div>
</div>
</div>
<!-- row -->
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
</div>
@endsection 
@section('script')
<script>
   $(function () {
     $('#example23').DataTable({
       processing: true,
       responsive: true,
       ordering: true,
       lengthMenu: [[10, 500, 1000, -1], [10, 500, 1000, "All"]],
       dom: 'Blfrtip',
       buttons: [
         'copy', 'csv', 'excel', 'pdf', 'print'
      ]
   });
     $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
  });   
</script>
@endsection