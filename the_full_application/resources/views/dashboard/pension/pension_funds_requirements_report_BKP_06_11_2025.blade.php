@section('title') 
Pension || MBPY Fund Requirements for the month - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
            <h4 class="card-title">Block/ULB wise fund requirement under MBPY for the month - {{$forTheMonth}}</h4>
            @include('dashboard.component.message')
            <div class="table-responsive m-t-40">
               <div class="card-header bg-light mb-3">
                     <h5 class="mb-2">📅 Filter by Month</h5>
                     <form method="GET" action="{{ route('admin.pension.report_without_ajax') }}" class="d-flex align-items-center">
                        <label class="me-2 fw-bold">Select Month:</label>
                        <select name="for_the_month" class="form-select w-auto me-2" onchange="this.form.submit()">
                           @foreach($dateConfig as $config)
                           <option value="{{ $config->for_the_month }}" {{ $month == $config->for_the_month ? 'selected' : '' }}>
                              {{ $config->for_the_month }}
                           </option>
                           @endforeach
                        </select>
                     </form>
                  </div>
               <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>For the Month</th>
                        <th>Block/ULB Name</th>
                        <th>Provided/Not Provided</th>
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
                        <th>Total Normal Beneficiaries</th>
                        <th>Total EP Beneficiaries</th>
                        <th>Total Beneficiaries</th>
                        <th>Total Normal Fund</th>
                        <th>Total EP Fund</th>
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
                        <th>For the Month</th>
                        <th>Block/ULB Name</th>
                        <th>Provided/Not Provided</th>
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
                        <th>Total Normal Beneficiaries</th>
                        <th>Total EP Beneficiaries</th>
                        <th>Total Beneficiaries</th>
                        <th>Total Normal Fund</th>
                        <th>Total EP Fund</th>
                        <th>Total Fund Requirement</th>
                        <th>A/C No</th>
                        <th>IFSC Code</th>
                        <th>Action</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @forelse ($pensionFundsRequirements as $key => $fundsRequirements)
                     @php
                     $block = $fundsRequirements->block->block_name ?? '';
                     $municipality = $fundsRequirements->municipality->municipality_name ?? '';
                     $addressType = $fundsRequirements->address_type == 1 ? 'Block' : 'ULB';
                     $unit = $addressType == 'Block' ? $block : $municipality;
                     $blockId = $fundsRequirements->block->block_id ?? null;
                     $municipalityId = $fundsRequirements->municipality->municipality_id ?? null;

                     $isSubmitted = false;
                     if ($blockId) {
                        $isSubmitted = DB::table('pension_funds_requirements')
                        ->whereBetween('created_date', [$startDate, $endDate])
                        ->where('block_id', $blockId)->exists();
                     } elseif ($municipalityId) {
                        $isSubmitted = DB::table('pension_funds_requirements')
                        ->whereBetween('created_date', [$startDate, $endDate])
                        ->where('municipality_id', $municipalityId)->exists();
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

                     $oapBelow80 = $fundsRequirements->mbpy_oap_below_80_years ?? 0;
                     $oapAbove80 = $fundsRequirements->mbpy_oap_above_80_years ?? 0;
                     $wp = $fundsRequirements->mbpy_wp ?? 0;
                     $dp = $fundsRequirements->mbpy_dp ?? 0;
                     $sdpBelow80 = $fundsRequirements->mbpy_sdp_below_80_percent ?? 0;
                     $sdpAbove80 = $fundsRequirements->mbpy_sdp_above_80_percent ?? 0;
                     $sdoap = $fundsRequirements->mbpy_sdoap ?? 0;
                     $clp = $fundsRequirements->mbpy_clp ?? 0;
                     $wpAids = $fundsRequirements->mbpy_wp_aids ?? 0;
                     $dpAids = $fundsRequirements->mbpy_dp_aids ?? 0;
                     $unmarried = $fundsRequirements->mbpy_unmarried_women ?? 0;
                     $orphan = $fundsRequirements->mbpy_orphan_due_to_covide ?? 0;
                     $widow = $fundsRequirements->mbpy_widow_due_to_covid ?? 0;
                     $divorce = $fundsRequirements->mbpy_divorce_or_destitute ?? 0;
                     $transgender = $fundsRequirements->mbpy_transgender ?? 0;

                     $totalNormalBeneficiaries = $oapBelow80 + $wp + $dp + $sdpBelow80 + $clp + $wpAids + $dpAids + $unmarried + $orphan + $widow + $divorce + $transgender;
                     $totalEPBeneficiaries = $oapAbove80 + $sdpAbove80 + $sdoap;
                     $totalBeneficiaries = $totalNormalBeneficiaries + $totalEPBeneficiaries;

                     $totalNormalFund = 
                     ($oapBelow80 * 1000) +
                     ($wp * 1000) +
                     ($dp * 1000) +
                     ($sdpBelow80 * 1200) +
                     ($clp * 1000) +
                     ($wpAids * 1000) +
                     ($dpAids * 1000) +
                     ($unmarried * 1000) +
                     ($orphan * 1000) +
                     ($widow * 1000) +
                     ($divorce * 1000) +
                     ($transgender * 1000);

                     $totalEPFund =
                     ($oapAbove80 * 3500) +
                     ($sdpAbove80 * 3500) +
                     ($sdoap * 3500);

                     $totalFund = $totalNormalFund + $totalEPFund;

                     $accountNumber = $fundsRequirements->mbpy_bank_account_number ?? null;
                     $ifscCode = $fundsRequirements->mbpy_bank_ifsc_code ?? null;
                     $maskedAccount = $accountNumber && trim($accountNumber) !== '' 
                     ? str_repeat('X', strlen($accountNumber) - 4) . substr($accountNumber, -4) 
                     : 'Not Provided';
                     $maskedIFSC = $ifscCode && trim($ifscCode) !== '' 
                     ? str_repeat('X', strlen($ifscCode) - 4) . substr($ifscCode, -4) 
                     : 'Not Provided';
                     @endphp

                     <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $district }}</td>
                        <td>{{ $forTheMonth }}</td>
                        <td>{{ $fundsRequirements->address_type == 1 ? 'Block: ' . ($block ?: 'Not Provided') : 'ULB: ' . ($municipality ?: 'Not Provided') }}</td>
                        <td><span class="badge {{ $status == 'Submitted' ? 'bg-success' : 'bg-danger' }}">{{ $status }}</span></td>

                        <td>{{ $oapBelow80 }}</td><td>{{ number_format($oapBelow80 * 1000) }}</td>
                        <td>{{ $oapAbove80 }}</td><td>{{ number_format($oapAbove80 * 3500) }}</td>
                        <td>{{ $wp }}</td><td>{{ number_format($wp * 1000) }}</td>
                        <td>{{ $dp }}</td><td>{{ number_format($dp * 1000) }}</td>
                        <td>{{ $sdpBelow80 }}</td><td>{{ number_format($sdpBelow80 * 1200) }}</td>
                        <td>{{ $sdpAbove80 }}</td><td>{{ number_format($sdpAbove80 * 3500) }}</td>
                        <td>{{ $sdoap }}</td><td>{{ number_format($sdoap * 3500) }}</td>
                        <td>{{ $clp }}</td><td>{{ number_format($clp * 1000) }}</td>
                        <td>{{ $wpAids }}</td><td>{{ number_format($wpAids * 1000) }}</td>
                        <td>{{ $dpAids }}</td><td>{{ number_format($dpAids * 1000) }}</td>
                        <td>{{ $unmarried }}</td><td>{{ number_format($unmarried * 1000) }}</td>
                        <td>{{ $orphan }}</td><td>{{ number_format($orphan * 1000) }}</td>
                        <td>{{ $widow }}</td><td>{{ number_format($widow * 1000) }}</td>
                        <td>{{ $divorce }}</td><td>{{ number_format($divorce * 1000) }}</td>
                        <td>{{ $transgender }}</td><td>{{ number_format($transgender * 1000) }}</td>

                        <td><strong>{{ number_format($totalNormalBeneficiaries) }}</strong></td>
                        <td><strong>{{ number_format($totalEPBeneficiaries) }}</strong></td>
                        <td><strong>{{ number_format($totalBeneficiaries) }}</strong></td>
                        <td><strong>{{ number_format($totalNormalFund) }}</strong></td>
                        <td><strong>{{ number_format($totalEPFund) }}</strong></td>
                        <td><strong>{{ number_format($totalFund) }}</strong></td>

                        <td>{{ $maskedAccount }}</td>
                        <td>{{ $maskedIFSC }}</td>
                        <td>
                           <div class="btn-group">
                              <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 Action
                              </button>
                              <div class="dropdown-menu">
                                 @if(!empty($fundsRequirements->id) && isset($fundsRequirements->created_date) && $fundsRequirements->created_date >= $startDate && $fundsRequirements->created_date <= $endDate)
                                 @can('pension-edit')
                                 <a class="dropdown-item" href="{{ route('admin.pension.edit', $fundsRequirements->id) }}">Edit</a>
                                 @endcan
                                 @can('pension-delete')
                                 <a class="dropdown-item" href="{{ route('admin.pension.delete', $fundsRequirements->id) }}" id="delete">Delete</a>
                                 @endcan
                                 @endif
                              </div>
                           </div>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="45" class="text-center">No records found.</td>
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