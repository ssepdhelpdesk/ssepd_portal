@section('title') 
Pension || Pension Disburshing Officer || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection 
@extends('dashboard.layouts.main')
@section('style')
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
            <h4 class="card-title">Block/ULB wise fund requirement under MBPY</h4>
            @include('dashboard.component.message')
            <div class="table-responsive m-t-40">
               <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Block/ULB Name</th>
                        <th>Officer Name</th>
                        <th>Officer Mobile No</th>
                        <th>Officer Designation</th>                        
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tfoot>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Block/ULB Name</th>
                        <th>Officer Name</th>
                        <th>Officer Mobile No</th>
                        <th>Officer Designation</th>                        
                        <th>Action</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @forelse ($pensiondisbursementAuthority as $key => $disbursementAuthority)
                     @php
                     $block = $disbursementAuthority->block->block_name ?? '';
                     $municipality = $disbursementAuthority->municipality->municipality_name ?? '';
                     $addressType = $disbursementAuthority->address_type == 1 ? 'Block' : 'ULB';
                     $unit = $addressType == 'Block' ? $block : $municipality;
                     $blockId = $disbursementAuthority->block->block_id ?? null;
                     $municipalityId = $disbursementAuthority->municipality->municipality_id ?? null;
                     $isSubmitted = false;
                     if ($blockId) {
                        $isSubmitted = DB::table('pension_funds_requirements')->where('block_id', $blockId)->exists();
                     } elseif ($municipalityId) {
                        $isSubmitted = DB::table('pension_funds_requirements')->where('municipality_id', $municipalityId)->exists();
                     }
                     $status = $isSubmitted ? 'Submitted' : 'Not Submitted';

                     $district = 'Not Provided';

                     if ($blockId) {
                       $districtId = DB::table('blocks')->where('block_id', $blockId)->value('district_id');
                       $district = DB::table('districts')->where('district_id', $districtId)->value('district_name') ?? 'Not Provided';
                    } elseif ($municipalityId) {
                       $districtId = DB::table('municipalities')->where('municipality_id', $municipalityId)->value('district_id');
                       $district = DB::table('districts')->where('district_id', $districtId)->value('district_name') ?? 'Not Provided';
                    }

                    $authorityName = $disbursementAuthority->authority_name ?? 'N/A';
                    $authorityMobileNo = $disbursementAuthority->authority_mobile_no ?? 'N/A';
                    $authorityDesignation = $disbursementAuthority->authority_designation ?? 'N/A';                    
                    @endphp
                    <tr>
                     <td>{{ $key + 1 }}</td>
                     <td>{{ $district }}</td>
                     <td>
                        {{ $disbursementAuthority->address_type == 1 ? 'Block: ' . ($block ?: 'Not Provided') : 'ULB: ' . ($municipality ?: 'Not Provided') }}
                     </td>
                     
                     <td>{{ $authorityName }}</td>
                     <td>{{ $authorityMobileNo }}</td>
                    <td>{{ $authorityDesignation }}</td>                     
                     <td>
                        <div class="btn-group">
                           <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Action
                           </button>
                           <div class="dropdown-menu">
                             @if(!empty($disbursementAuthority->id))

                             @can('pension-edit')
                             <a class="dropdown-item" href="{{ route('admin.pension.edit', $disbursementAuthority->id) }}">Edit</a>
                             @endcan

                             @can('pension-delete')
                             <a class="dropdown-item" href="{{ route('admin.pension.delete', $disbursementAuthority->id) }}" id="delete">Delete</a>
                             @endcan
                             @endif
                          </div>
                       </div>
                    </td>
                 </tr>
                 @empty
                 <tr>
                  <td colspan="40" class="text-center">No records found.</td>
               </tr>
               @endforelse
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
         responsive: false,
         ordering: true,
         scrollX: true,
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