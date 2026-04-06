@section('title') 
Pension || District Wise Monthly Fund Requirement - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .th-border-right {
     border-right: 3px solid #000;
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
               <h4 class="card-title">District Wise Monthly Fund Requirement Report - {{$forTheMonth}}</h4>
               @include('dashboard.component.message')
               <form method="GET" class="mb-4">
                <div class="row align-items-end">
                 <div class="col-md-4">
                  <label class="form-label">
                   <strong>Select Disbursement Start Month:</strong>
                </label>

                <select name="for_the_month" class="form-control" required>
                   <option value="">-- Select Month --</option>

                   @foreach($dateConfig as $config)
                   <option value="{{ $config->for_the_month }}"
                     {{ request('for_the_month', $forTheMonth) == $config->for_the_month ? 'selected' : '' }}>
                     {{ \Carbon\Carbon::parse($config->for_the_month)->format('F Y') }}
                  </option>
                  @endforeach
               </select>
            </div>

            <div class="col-md-2">
               <button type="submit" class="btn btn-primary">
                Search
             </button>
          </div>
       </div>
    </form>
    <div class="table-responsive m-t-40">
      <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
         <thead class="table-dark">
            <tr>
               <th>Sl.No</th>
               <th>District</th>
               <th>Status</th>
               <th class="text-end">OAP 60-79 Yrs</th>
               <th class="text-end">OAP ≥80</th>
               <th class="text-end">Widow</th>
               <th class="text-end">DP (40-59)%</th>
               <th class="text-end">SDP (60-79)%</th>
               <th class="text-end">SDP ≥80%</th>
               <th class="text-end">SDOAP</th>
               <th class="text-end">CLP</th>
               <th class="text-end">WP (AIDS)</th>
               <th class="text-end">DP (AIDS)</th>
               <th class="text-end">UMW</th>
               <th class="text-end">Orphan (COVID)</th>
               <th class="text-end">Widow (COVID)</th>
               <th class="text-end">Divorce / Destitute</th>
               <th class="text-end th-border-right">Transgender</th>
               <th class="text-end">Normal Pensioners</th>
               <th class="text-end">EP Pensioners</th>
               <th class="text-end">Total Pensioners</th>
               <th class="text-end">Normal Fund (₹)</th>
               <th class="text-end">EP Fund (₹)</th>
               <th class="text-end">Total Fund (₹)</th>
            </tr>
         </thead>
         <tbody>
            @php
            $totalNormalPensioners = 0;
            $totalEPPensioners = 0;
            $totalNormalFund = 0;
            $totalEPFund = 0;
            @endphp
            @forelse ($data as $index => $row)
            @php
            $rowTotalPensioners =
            $row['no_of_normal_pensioners'] + $row['no_of_ep_pensioners'];
            $rowTotalFund =
            $row['funds_no_of_normal_pensioners'] + $row['funds_no_of_ep_pensioners'];
            $totalNormalPensioners += $row['no_of_normal_pensioners'];
            $totalEPPensioners += $row['no_of_ep_pensioners'];
            $totalNormalFund += $row['funds_no_of_normal_pensioners'];
            $totalEPFund += $row['funds_no_of_ep_pensioners'];
            @endphp
            <tr>
               <td>{{ $index + 1 }}</td>
               <td>{{ $row['district_name'] }}</td>
               <td>
                  @if ($row['status'] === 'Available')
                  <span class="badge bg-success">Available</span>
                  @else
                  <span class="badge bg-danger">Not Available</span>
                  @endif
               </td>
               <td class="text-end">{{ number_format($row['oap_below_80']) }}</td>
               <td class="text-end">{{ number_format($row['oap_above_80']) }}</td>
               <td class="text-end">{{ number_format($row['widow_pension']) }}</td>
               <td class="text-end">{{ number_format($row['disabled_pension']) }}</td>
               <td class="text-end">{{ number_format($row['sdp_below_80']) }}</td>
               <td class="text-end">{{ number_format($row['sdp_above_80']) }}</td>
               <td class="text-end">{{ number_format($row['sdoap']) }}</td>
               <td class="text-end">{{ number_format($row['clp']) }}</td>
               <td class="text-end">{{ number_format($row['wp_aids']) }}</td>
               <td class="text-end">{{ number_format($row['dp_aids']) }}</td>
               <td class="text-end">{{ number_format($row['unmarried_women']) }}</td>
               <td class="text-end">{{ number_format($row['orphan_covid']) }}</td>
               <td class="text-end">{{ number_format($row['widow_covid']) }}</td>
               <td class="text-end">{{ number_format($row['divorce_destitute']) }}</td>
               <td class="text-end th-border-right">{{ number_format($row['transgender']) }}</td>
               <td class="text-end">{{ number_format($row['no_of_normal_pensioners']) }}</td>
               <td class="text-end">{{ number_format($row['no_of_ep_pensioners']) }}</td>
               <td class="text-end fw-bold">{{ number_format($rowTotalPensioners) }}</td>
               <td class="text-end">{{ number_format($row['funds_no_of_normal_pensioners']) }}</td>
               <td class="text-end">{{ number_format($row['funds_no_of_ep_pensioners']) }}</td>
               <td class="text-end fw-bold">{{ number_format($rowTotalFund) }}</td>
            </tr>
            @empty
            <tr>
               <td colspan="11" class="text-center text-danger">
                  No data available for the selected month.
               </td>
            </tr>
            @endforelse
         </tbody>
         @if(count($data) > 0)
         <tfoot class="table-secondary fw-bold">
            <tr>
               <td colspan="19" class="text-end">Grand Total</td>
               <td class="text-end">{{ number_format($totalNormalPensioners) }}</td>
               <td class="text-end">{{ number_format($totalEPPensioners) }}</td>
               <td class="text-end">{{ number_format($totalNormalPensioners + $totalEPPensioners) }}</td>
               <td class="text-end">{{ number_format($totalNormalFund) }}</td>
               <td class="text-end">{{ number_format($totalEPFund) }}</td>
               <td class="text-end">{{ number_format($totalNormalFund + $totalEPFund) }}</td>
            </tr>
         </tfoot>
         @endif
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