@section('title') 
Pension || MBPY Fund Requirements || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
      @can('user-create')
      <a href="{{ route('admin.users.create') }}"><button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success"><i class="fas fa-plus-square"></i> Add New</button></a>
      @endcan
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
            <h4 class="card-title">Block/ULB wise fund requirement under MBPY for the pension month August 2025</h4>
            @include('dashboard.component.message')
            <div class="table-responsive m-t-40">
               <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Block/ULB Name</th>
                        <th>MBPOAP (Below 80 Years)</th>
                        <th>Fund Requirements</th>
                        <th>MBPOAP (Above 80 Years)</th>
                        <th>Fund Requirements</th>
                        <th>MBPWP</th>
                        <th>Fund Requirements</th>
                        <th>MBPDP</th>
                        <th>Fund Requirements</th>
                        <th>MBPSDP (Below 80%)</th>
                        <th>Fund Requirements</th>
                        <th>MBPSDP (Above 80%)</th>
                        <th>Fund Requirements</th>
                        <th>MBPSDOAP</th>
                        <th>Fund Requirements</th>
                        <th>MBPCLP</th>
                        <th>Fund Requirements</th>
                        <th>MBPWP (Due to Aids)</th>
                        <th>Fund Requirements</th>
                        <th>MBPDP (Due to Aids)</th>
                        <th>Fund Requirements</th>
                        <th>MBPUMW</th>
                        <th>Fund Requirements</th>
                        <th>Orphan due to Covid</th>
                        <th>Fund Requirements</th>
                        <th>Widow due to Covid</th>
                        <th>Fund Requirements</th>
                        <th>Divorcee or Destitute</th>
                        <th>Fund Requirements</th>
                        <th>Transgender</th>
                        <th>Fund Requirements</th>
                        <th>Total Fund Requirement</th>
                        <th>A/C No</th>
                        <th>IFSC Code</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tfoot>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Block/ULB Name</th>
                        <th>MBPOAP (Below 80 Years)</th>
                        <th>Fund Requirements</th>
                        <th>MBPOAP (Above 80 Years)</th>
                        <th>Fund Requirements</th>
                        <th>MBPWP</th>
                        <th>Fund Requirements</th>
                        <th>MBPDP</th>
                        <th>Fund Requirements</th>
                        <th>MBPSDP (Below 80%)</th>
                        <th>Fund Requirements</th>
                        <th>MBPSDP (Above 80%)</th>
                        <th>Fund Requirements</th>
                        <th>MBPSDOAP</th>
                        <th>Fund Requirements</th>
                        <th>MBPCLP</th>
                        <th>Fund Requirements</th>
                        <th>MBPWP (Due to Aids)</th>
                        <th>Fund Requirements</th>
                        <th>MBPDP (Due to Aids)</th>
                        <th>Fund Requirements</th>
                        <th>MBPUMW</th>
                        <th>Fund Requirements</th>
                        <th>Orphan due to Covid</th>
                        <th>Fund Requirements</th>
                        <th>Widow due to Covid</th>
                        <th>Fund Requirements</th>
                        <th>Divorcee or Destitute</th>
                        <th>Fund Requirements</th>
                        <th>Transgender</th>
                        <th>Fund Requirements</th>
                        <th>Total Fund Requirement</th>
                        <th>A/C No</th>
                        <th>IFSC Code</th>
                        <th>Action</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @forelse ($pensionFundsRequirements as $key => $fundsRequirements)
                     @php
                     $accountNumber = $fundsRequirements->mbpy_bank_account_number;
                     $ifscCode = $fundsRequirements->mbpy_bank_ifsc_code;

                     $maskedAccount = $accountNumber ? str_repeat('X', strlen($accountNumber) - 4) . substr($accountNumber, -4) : 'Not Provided';
                     $maskedIFSC = $ifscCode ? str_repeat('X', strlen($ifscCode) - 4) . substr($ifscCode, -4) : 'Not Provided';
                     @endphp
                     <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $fundsRequirements->district->district_name ?? '-' }}</td>
                        <td>
                           @if($fundsRequirements->address_type == 1)
                           Block: {{ $fundsRequirements->block->block_name ?? '-' }}
                           @elseif($fundsRequirements->address_type == 2)
                           ULB: {{ $fundsRequirements->municipality->municipality_name ?? '-' }}
                           @else
                           -
                           @endif
                        </td>

                        {{-- Amount Columns --}}
                        <td>{{ $fundsRequirements->mbpy_oap_below_80_years ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_oap_below_80_years ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_oap_above_80_years ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_oap_above_80_years ?? 0) * 3500 }}</td>

                        <td>{{ $fundsRequirements->mbpy_wp ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_wp ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_dp ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_dp ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_sdp_below_80_percent ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_sdp_below_80_percent ?? 0) * 1200 }}</td>

                        <td>{{ $fundsRequirements->mbpy_sdp_above_80_percent ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_sdp_above_80_percent ?? 0) * 3500 }}</td>

                        <td>{{ $fundsRequirements->mbpy_sdoap ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_sdoap ?? 0) * 3500 }}</td>

                        <td>{{ $fundsRequirements->mbpy_clp ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_clp ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_wp_aids ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_wp_aids ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_dp_aids ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_dp_aids ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_unmarried_women ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_unmarried_women ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_orphan_due_to_covide ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_orphan_due_to_covide ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_widow_due_to_covid ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_widow_due_to_covid ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_divorce_or_destitute ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_divorce_or_destitute ?? 0) * 1000 }}</td>

                        <td>{{ $fundsRequirements->mbpy_transgender ?? 0 }}</td>
                        <td>{{ ($fundsRequirements->mbpy_transgender ?? 0) * 1000 }}</td>

                        @php
    $totalFund = 
        ($fundsRequirements->mbpy_oap_below_80_years ?? 0) * 1000 +
        ($fundsRequirements->mbpy_oap_above_80_years ?? 0) * 3500 +
        ($fundsRequirements->mbpy_wp ?? 0) * 1000 +
        ($fundsRequirements->mbpy_dp ?? 0) * 1000 +
        ($fundsRequirements->mbpy_sdp_below_80_percent ?? 0) * 1200 +
        ($fundsRequirements->mbpy_sdp_above_80_percent ?? 0) * 3500 +
        ($fundsRequirements->mbpy_sdoap ?? 0) * 3500 +
        ($fundsRequirements->mbpy_clp ?? 0) * 1000 +
        ($fundsRequirements->mbpy_wp_aids ?? 0) * 1000 +
        ($fundsRequirements->mbpy_dp_aids ?? 0) * 1000 +
        ($fundsRequirements->mbpy_unmarried_women ?? 0) * 1000 +
        ($fundsRequirements->mbpy_orphan_due_to_covide ?? 0) * 1000 +
        ($fundsRequirements->mbpy_widow_due_to_covid ?? 0) * 1000 +
        ($fundsRequirements->mbpy_divorce_or_destitute ?? 0) * 1000 +
        ($fundsRequirements->mbpy_transgender ?? 0) * 1000;
@endphp

<td>{{ number_format($totalFund) }}</td>
                        <td>{{ $maskedAccount }}</td>
                        <td>{{ $maskedIFSC }}</td>

                        {{-- Action Dropdown --}}
                        <td>
                           <div class="btn-group">
                              <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 Action
                              </button>
                              <div class="dropdown-menu">
                                 @can('pension-edit')
                                 <a class="dropdown-item" href="{{ route('admin.pension.edit', $fundsRequirements->id) }}">Edit</a>
                                 @endcan
                                 @can('pension-delete')
                                 <a class="dropdown-item" href="{{ route('admin.pension.delete', $fundsRequirements->id) }}" id="delete">Delete</a>
                                 @endcan
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