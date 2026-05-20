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
                           <th>Nodal Officer</th>
                           <th>Nodal Officer Mob No</th>
                           <th>User ID</th>
                           <th>Password</th>
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
                           <th>Nodal Officer</th>
                           <th>Nodal Officer Mob No</th>
                           <th>User ID</th>
                           <th>Password</th>
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
                           <td class="wrap-text">{{ ucwords(strtolower($piaInstituteDetails->excel_pia_name ?? 'Not Available')) }}</td>
                           <td class="wrap-text">{{ ucwords(strtolower($piaInstituteDetails->excel_institute_name ?? 'Not Available')) }}</td>
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
                           <td>{{ ucwords(strtolower($piaInstituteDetails->excel_nodal_officer_name ?? 'Not Available')) }}</td>
                           <td>{{ $piaInstituteDetails->excel_nodal_officer_contact_number ?? 'Not Available' }}</td>
                           <td>{{ $piaInstituteDetails->excel_institute_user_id ?? 'Not Available' }}</td>
                           <td>123456</td>
                           <td class="wrap-text">{{ $piaInstituteDetails->excel_institute_address ? ucwords(strtolower($piaInstituteDetails->excel_institute_address)) : 'Address Not Provided' }}</td>
                           <td>
                              {{ $piaInstituteDetails->basic_details_completed == 1 ? '✅' : '❌' }}
                           </td>
                           <td>
                              @if($piaInstituteDetails->beneficiaries_count > 0)
                              <span class="badge bg-info text-white"
                                 data-bs-toggle="tooltip"
                                 title="Inmates: {{ $piaInstituteDetails->beneficiaries_count }}">
                              {{ $piaInstituteDetails->beneficiaries_count }}
                              </span>
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