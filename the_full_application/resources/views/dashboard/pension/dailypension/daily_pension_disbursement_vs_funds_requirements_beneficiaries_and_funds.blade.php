@section('title') 
Pension || Funds Requirements vs Daily Disbursement
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .wrap-text {
     white-space: normal !important;
     word-break: break-word;
     max-width: 200px;
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
                  <div class="card-header bg-light mb-3">
                     <h5 class="mb-2">📅 Filter by Month</h5>
                     <form method="GET" action="{{ route('admin.dailypensiondisbursement.daily_pension_disbursement_vs_funds_requirements_beneficiaries_and_funds') }}" class="d-flex align-items-center">
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
                          <th class="text-center">Sl.No</th>
                          <th class="text-center">Block / ULB</th>
                          <th class="text-center">Address</th>

                          <th class="text-center">OAP Below 80 Requirement</th>
                          <th class="text-center">OAP Below 80 Disbursement</th>
                          <th class="text-center">OAP Below 80 Difference</th>

                          <th class="text-center">Funds OAP Below 80 Requirement</th>
                          <th class="text-center">Funds OAP Below 80 Disbursement</th>
                          <th class="text-center">Funds OAP Below 80 Difference</th>

                          <th class="text-center">OAP Above 80 Requirement</th>
                          <th class="text-center">OAP Above 80 Disbursement</th>
                          <th class="text-center">OAP Above 80 Difference</th>

                          <th class="text-center">Funds OAP Above 80 Requirement</th>
                          <th class="text-center">Funds OAP Above 80 Disbursement</th>
                          <th class="text-center">Funds OAP Above 80 Difference</th>

                          <th class="text-center">Widow Pension Requirement</th>
                          <th class="text-center">Widow Pension Disbursement</th>
                          <th class="text-center">Widow Pension Difference</th>

                          <th class="text-center">Funds Widow Pension Requirement</th>
                          <th class="text-center">Funds Widow Pension Disbursement</th>
                          <th class="text-center">Funds Widow Pension Difference</th>

                          <th class="text-center">Disabled Pension Requirement</th>
                          <th class="text-center">Disabled Pension Disbursement</th>
                          <th class="text-center">Disabled Pension Difference</th>

                          <th class="text-center">Funds Disabled Pension Requirement</th>
                          <th class="text-center">Funds Disabled Pension Disbursement</th>
                          <th class="text-center">Funds Disabled Pension Difference</th>

                          <th class="text-center">SDP Below 80% Requirement</th>
                          <th class="text-center">SDP Below 80% Disbursement</th>
                          <th class="text-center">SDP Below 80% Difference</th>

                          <th class="text-center">Funds SDP Below 80% Requirement</th>
                          <th class="text-center">Funds SDP Below 80% Disbursement</th>
                          <th class="text-center">Funds SDP Below 80% Difference</th>

                          <th class="text-center">SDP Above 80% Requirement</th>
                          <th class="text-center">SDP Above 80% Disbursement</th>
                          <th class="text-center">SDP Above 80% Difference</th>

                          <th class="text-center">Funds SDP Above 80% Requirement</th>
                          <th class="text-center">Funds SDP Above 80% Disbursement</th>
                          <th class="text-center">Funds SDP Above 80% Difference</th>

                          <th class="text-center">SDOAP Requirement</th>
                          <th class="text-center">SDOAP Disbursement</th>
                          <th class="text-center">SDOAP Difference</th>

                          <th class="text-center">Funds SDOAP Requirement</th>
                          <th class="text-center">Funds SDOAP Disbursement</th>
                          <th class="text-center">Funds SDOAP Difference</th>

                          <th class="text-center">CLP Requirement</th>
                          <th class="text-center">CLP Disbursement</th>
                          <th class="text-center">CLP Difference</th>

                          <th class="text-center">Funds CLP Requirement</th>
                          <th class="text-center">Funds CLP Disbursement</th>
                          <th class="text-center">Funds CLP Difference</th>

                          <th class="text-center">WP AIDS Requirement</th>
                          <th class="text-center">WP AIDS Disbursement</th>
                          <th class="text-center">WP AIDS Difference</th>

                          <th class="text-center">Funds WP AIDS Requirement</th>
                          <th class="text-center">Funds WP AIDS Disbursement</th>
                          <th class="text-center">Funds WP AIDS Difference</th>

                          <th class="text-center">DP AIDS Requirement</th>
                          <th class="text-center">DP AIDS Disbursement</th>
                          <th class="text-center">DP AIDS Difference</th>

                          <th class="text-center">Funds DP AIDS Requirement</th>
                          <th class="text-center">Funds DP AIDS Disbursement</th>
                          <th class="text-center">Funds DP AIDS Difference</th>

                          <th class="text-center">Unmarried Women Requirement</th>
                          <th class="text-center">Unmarried Women Disbursement</th>
                          <th class="text-center">Unmarried Women Difference</th>

                          <th class="text-center">Funds Unmarried Women Requirement</th>
                          <th class="text-center">Funds Unmarried Women Disbursement</th>
                          <th class="text-center">Funds Unmarried Women Difference</th>

                          <th class="text-center">Orphan COVID Requirement</th>
                          <th class="text-center">Orphan COVID Disbursement</th>
                          <th class="text-center">Orphan COVID Difference</th>

                          <th class="text-center">Funds Orphan COVID Requirement</th>
                          <th class="text-center">Funds Orphan COVID Disbursement</th>
                          <th class="text-center">Funds Orphan COVID Difference</th>

                          <th class="text-center">Widow COVID Requirement</th>
                          <th class="text-center">Widow COVID Disbursement</th>
                          <th class="text-center">Widow COVID Difference</th>

                          <th class="text-center">Funds Widow COVID Requirement</th>
                          <th class="text-center">Funds Widow COVID Disbursement</th>
                          <th class="text-center">Funds Widow COVID Difference</th>

                          <th class="text-center">Divorce / Destitute Requirement</th>
                          <th class="text-center">Divorce / Destitute Disbursement</th>
                          <th class="text-center">Divorce / Destitute Difference</th>

                          <th class="text-center">Funds Divorce / Destitute Requirement</th>
                          <th class="text-center">Funds Divorce / Destitute Disbursement</th>
                          <th class="text-center">Funds Divorce / Destitute Difference</th>

                          <th class="text-center">Transgender Requirement</th>
                          <th class="text-center">Transgender Disbursement</th>
                          <th class="text-center">Transgender Difference</th>

                          <th class="text-center">Funds Transgender Requirement</th>
                          <th class="text-center">Funds Transgender Disbursement</th>
                          <th class="text-center">Funds Transgender Difference</th>

                          <!-- Summary -->
                          <th class="text-center">Normal Pension Requirement</th>
                          <th class="text-center">Normal Pension Disbursement</th>
                          <th class="text-center">Normal Pension Difference</th>

                          <th class="text-center">Funds Normal Pension Requirement</th>
                          <th class="text-center">Funds Normal Pension Disbursement</th>
                          <th class="text-center">Funds Normal Pension Difference</th>

                          <th class="text-center">EP Pension Requirement</th>
                          <th class="text-center">EP Pension Disbursement</th>
                          <th class="text-center">EP Pension Difference</th>

                          <th class="text-center">Funds EP Pension Requirement</th>
                          <th class="text-center">Funds EP Pension Disbursement</th>
                          <th class="text-center">Funds EP Pension Difference</th>

                          <th class="text-center">Total Beneficiaries Requirement</th>
                          <th class="text-center">Total Beneficiaries Disbursement</th>
                          <th class="text-center">Total Beneficiaries Difference</th>

                          <th class="text-center">Funds Total Beneficiaries Requirement</th>
                          <th class="text-center">Funds Total Beneficiaries Disbursement</th>
                          <th class="text-center">Funds Total Beneficiaries Difference</th>
                       </tr>
                    </thead>
                    <tbody>
                     @foreach($finalReport as $row)
                     @php
                     $normalKeys = [
                     'oap_below_80','widow_pension','disabled_pension','sdp_below_80','clp',
                     'wp_aids','dp_aids','unmarried_women','orphan_covid','widow_covid','divorce_destitute','transgender'
                     ];

                     $epKeys = ['oap_above_80','sdp_above_80','sdoap'];

                     $normalRequirement = collect($normalKeys)->sum(fn($key) => $row[$key.'_requirement']);
                     $normalDisbursement = collect($normalKeys)->sum(fn($key) => $row[$key.'_disbursement']);
                     $normalDiff = $normalRequirement - $normalDisbursement;

                     $epRequirement = collect($epKeys)->sum(fn($key) => $row[$key.'_requirement']);
                     $epDisbursement = collect($epKeys)->sum(fn($key) => $row[$key.'_disbursement']);
                     $epDiff = $epRequirement - $epDisbursement;

                     $totalRequirement = $normalRequirement + $epRequirement;
                     $totalDisbursement = $normalDisbursement + $epDisbursement;
                     $totalDiff = $totalRequirement - $totalDisbursement;

                     // FUND TOTALS
                     $normalFundReq = collect($normalKeys)->sum(fn($key) => $row['funds_'.$key.'_requirement']);
                     $normalFundDis = collect($normalKeys)->sum(fn($key) => $row['funds_'.$key.'_disbursement']);
                     $normalFundDiff = $normalFundReq - $normalFundDis;

                     $epFundReq = collect($epKeys)->sum(fn($key) => $row['funds_'.$key.'_requirement']);
                     $epFundDis = collect($epKeys)->sum(fn($key) => $row['funds_'.$key.'_disbursement']);
                     $epFundDiff = $epFundReq - $epFundDis;

                     $totalFundReq = $normalFundReq + $epFundReq;
                     $totalFundDis = $normalFundDis + $epFundDis;
                     $totalFundDiff = $totalFundReq - $totalFundDis;
                     @endphp

                     <tr>
                      <td class="text-center">{{ $row['sl_no'] }}</td>
                      <td class="border text-center">{{ $row['area_type'] }} - {{ $row['area_id'] }}</td>
                      <td class="border text-center">{{ $row['district_name'] }} : {{ $row['area_name'] }}</td>

                      {{-- Category-wise data --}}
                      @foreach([
                      'oap_below_80','oap_above_80','widow_pension','disabled_pension',
                      'sdp_below_80','sdp_above_80','sdoap','clp',
                      'wp_aids','dp_aids','unmarried_women','orphan_covid',
                      'widow_covid','divorce_destitute','transgender'
                      ] as $key)
                      {{-- Counts --}}
                      <td class="text-center">{{ $row[$key.'_requirement'] }}</td>
                      <td class="text-center">{{ $row[$key.'_disbursement'] }}</td>
                      <td class="text-center {{ ($row[$key.'_requirement'] - $row[$key.'_disbursement']) < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $row[$key.'_requirement'] - $row[$key.'_disbursement'] }}
                     </td>

                     {{-- Fund Values --}}
                     <td class="text-center">₹{{ number_format($row['funds_'.$key.'_requirement']) }}</td>
                     <td class="text-center">₹{{ number_format($row['funds_'.$key.'_disbursement']) }}</td>
                     <td class="text-center {{ ($row['funds_'.$key.'_diff']) < 0 ? 'text-danger' : 'text-success' }}">
                        ₹{{ number_format($row['funds_'.$key.'_diff']) }}
                     </td>
                     @endforeach

                     {{-- Normal Pension --}}
                     <td class="text-center">{{ $normalRequirement }}</td>
                     <td class="text-center">{{ $normalDisbursement }}</td>
                     <td class="text-center {{ $normalDiff < 0 ? 'text-danger' : 'text-success' }}">{{ $normalDiff }}</td>
                     <td class="text-center">₹{{ number_format($normalFundReq) }}</td>
                     <td class="text-center">₹{{ number_format($normalFundDis) }}</td>
                     <td class="text-center {{ $normalFundDiff < 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($normalFundDiff) }}</td>

                     {{-- EP Pension --}}
                     <td class="text-center">{{ $epRequirement }}</td>
                     <td class="text-center">{{ $epDisbursement }}</td>
                     <td class="text-center {{ $epDiff < 0 ? 'text-danger' : 'text-success' }}">{{ $epDiff }}</td>
                     <td class="text-center">₹{{ number_format($epFundReq) }}</td>
                     <td class="text-center">₹{{ number_format($epFundDis) }}</td>
                     <td class="text-center {{ $epFundDiff < 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($epFundDiff) }}</td>

                     {{-- Total --}}
                     <td class="text-center">{{ $totalRequirement }}</td>
                     <td class="text-center">{{ $totalDisbursement }}</td>
                     <td class="text-center {{ $totalDiff < 0 ? 'text-danger' : 'text-success' }}">{{ $totalDiff }}</td>
                     <td class="text-center">₹{{ number_format($totalFundReq) }}</td>
                     <td class="text-center">₹{{ number_format($totalFundDis) }}</td>
                     <td class="text-center {{ $totalFundDiff < 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($totalFundDiff) }}</td>
                  </tr>
                  @endforeach
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