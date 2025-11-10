@section('title') 
Pension || Daily Pension Disbursement vs Funds Requirements
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
                     <form method="GET" action="{{ route('admin.dailypensiondisbursement.daily_pension_disbursement_vs_funds_requirements_beneficiaries') }}" class="d-flex align-items-center">
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

                          <th class="text-center">OAP Above 80 Requirement</th>
                          <th class="text-center">OAP Above 80 Disbursement</th>
                          <th class="text-center">OAP Above 80 Difference</th>

                          <th class="text-center">Widow Pension Requirement</th>
                          <th class="text-center">Widow Pension Disbursement</th>
                          <th class="text-center">Widow Pension Difference</th>

                          <th class="text-center">Disabled Pension Requirement</th>
                          <th class="text-center">Disabled Pension Disbursement</th>
                          <th class="text-center">Disabled Pension Difference</th>

                          <th class="text-center">SDP Below 80% Requirement</th>
                          <th class="text-center">SDP Below 80% Disbursement</th>
                          <th class="text-center">SDP Below 80% Difference</th>

                          <th class="text-center">SDP Above 80% Requirement</th>
                          <th class="text-center">SDP Above 80% Disbursement</th>
                          <th class="text-center">SDP Above 80% Difference</th>

                          <th class="text-center">SDOAP Requirement</th>
                          <th class="text-center">SDOAP Disbursement</th>
                          <th class="text-center">SDOAP Difference</th>

                          <th class="text-center">CLP Requirement</th>
                          <th class="text-center">CLP Disbursement</th>
                          <th class="text-center">CLP Difference</th>

                          <th class="text-center">WP AIDS Requirement</th>
                          <th class="text-center">WP AIDS Disbursement</th>
                          <th class="text-center">WP AIDS Difference</th>

                          <th class="text-center">DP AIDS Requirement</th>
                          <th class="text-center">DP AIDS Disbursement</th>
                          <th class="text-center">DP AIDS Difference</th>

                          <th class="text-center">Unmarried Women Requirement</th>
                          <th class="text-center">Unmarried Women Disbursement</th>
                          <th class="text-center">Unmarried Women Difference</th>

                          <th class="text-center">Orphan COVID Requirement</th>
                          <th class="text-center">Orphan COVID Disbursement</th>
                          <th class="text-center">Orphan COVID Difference</th>

                          <th class="text-center">Widow COVID Requirement</th>
                          <th class="text-center">Widow COVID Disbursement</th>
                          <th class="text-center">Widow COVID Difference</th>

                          <th class="text-center">Divorce / Destitute Requirement</th>
                          <th class="text-center">Divorce / Destitute Disbursement</th>
                          <th class="text-center">Divorce / Destitute Difference</th>

                          <th class="text-center">Transgender Requirement</th>
                          <th class="text-center">Transgender Disbursement</th>
                          <th class="text-center">Transgender Difference</th>

                          <!-- Summary -->
                          <th class="text-center">Normal Pension Requirement</th>
                          <th class="text-center">Normal Pension Disbursement</th>
                          <th class="text-center">Normal Pension Difference</th>

                          <th class="text-center">EP Pension Requirement</th>
                          <th class="text-center">EP Pension Disbursement</th>
                          <th class="text-center">EP Pension Difference</th>

                          <th class="text-center">Total Beneficiaries Requirement</th>
                          <th class="text-center">Total Beneficiaries Disbursement</th>
                          <th class="text-center">Total Beneficiaries Difference</th>
                       </tr>
                    </thead>

                    <tbody>
                      @foreach($finalReport as $row)
                      @php
                      $normalRequirement = $row['oap_below_80_requirement'] + 
                      $row['widow_pension_requirement'] +
                      $row['disabled_pension_requirement'] +
                      $row['sdp_below_80_requirement'] +
                      $row['clp_requirement'] +
                      $row['wp_aids_requirement'] +
                      $row['dp_aids_requirement'] +
                      $row['unmarried_women_requirement'] +
                      $row['orphan_covid_requirement'] +
                      $row['widow_covid_requirement'] +
                      $row['divorce_destitute_requirement'] +
                      $row['transgender_requirement'];

                      $normalDisbursement = $row['oap_below_80_disbursement'] + 
                      $row['widow_pension_disbursement'] +
                      $row['disabled_pension_disbursement'] +
                      $row['sdp_below_80_disbursement'] +
                      $row['clp_disbursement'] +
                      $row['wp_aids_disbursement'] +
                      $row['dp_aids_disbursement'] +
                      $row['unmarried_women_disbursement'] +
                      $row['orphan_covid_disbursement'] +
                      $row['widow_covid_disbursement'] +
                      $row['divorce_destitute_disbursement'] +
                      $row['transgender_disbursement'];

                      $normalDiff = $normalRequirement - $normalDisbursement;

                      $epRequirement = $row['oap_above_80_requirement'] +
                      $row['sdp_above_80_requirement'] +
                      $row['sdoap_requirement'];

                      $epDisbursement = $row['oap_above_80_disbursement'] +
                      $row['sdp_above_80_disbursement'] +
                      $row['sdoap_disbursement'];

                      $epDiff = $epRequirement - $epDisbursement;

                      $totalRequirement = $normalRequirement + $epRequirement;
                      $totalDisbursement = $normalDisbursement + $epDisbursement;
                      $totalDiff = $totalRequirement - $totalDisbursement;
                      @endphp
                      <tr>
                       <td class="text-center">{{ $row['sl_no'] }}</td>
                       <td class="border px-4 py-2 text-center">{{ $row['area_type'] }}<!--  - {{ $row['area_id'] }} --></td>
                       <td class="border px-4 py-2 text-center">{{ $row['district_name'] }} : {{ $row['area_name'] }}</td>

                       {{-- Categories --}}
                       @foreach([
                       'oap_below_80','oap_above_80','widow_pension','disabled_pension',
                       'sdp_below_80','sdp_above_80','sdoap','clp',
                       'wp_aids','dp_aids','unmarried_women','orphan_covid',
                       'widow_covid','divorce_destitute','transgender'
                       ] as $key)
                       <td class="border px-4 py-2 text-center">{{ $row[$key.'_requirement'] }}</td>
                       <td class="border px-4 py-2 text-center">{{ $row[$key.'_disbursement'] }}</td>
                       <td class="border px-4 py-2 text-center font-semibold {{ ($row[$key.'_requirement'] - $row[$key.'_disbursement']) < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $row[$key.'_requirement'] - $row[$key.'_disbursement'] }}
                     </td>
                     @endforeach

                     {{-- Normal Pension Summary --}}
                     <td class="border px-4 py-2 text-center font-semibold">{{ $normalRequirement }}</td>
                     <td class="border px-4 py-2 text-center font-semibold">{{ $normalDisbursement }}</td>
                     <td class="border px-4 py-2 text-center font-semibold {{ $normalDiff < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $normalDiff }}</td>

                     {{-- EP Pension Summary --}}
                     <td class="border px-4 py-2 text-center font-semibold">{{ $epRequirement }}</td>
                     <td class="border px-4 py-2 text-center font-semibold">{{ $epDisbursement }}</td>
                     <td class="border px-4 py-2 text-center font-semibold {{ $epDiff < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $epDiff }}</td>

                     {{-- Total Beneficiaries --}}
                     <td class="border px-4 py-2 text-center font-semibold">{{ $totalRequirement }}</td>
                     <td class="border px-4 py-2 text-center font-semibold">{{ $totalDisbursement }}</td>
                     <td class="border px-4 py-2 text-center font-semibold {{ $totalDiff < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $totalDiff }}</td>
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