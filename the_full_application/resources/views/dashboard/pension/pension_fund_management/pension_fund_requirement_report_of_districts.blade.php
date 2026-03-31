@section('title') 
Pension || District wise Pension Fund Requirement for the Month {{$month}} As on {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection 
@extends('dashboard.layouts.main')
@section('style')
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
         <button onclick="history.back()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info">
            <i class="fas fa-arrow-alt-circle-left"></i> Go Back
         </button>
      </div>
   </div>
   <div class="row mb-3">
      <div class="col-md-4 d-flex align-items-end">
         <div class="w-100">
            <label>Select Month</label>
            <select id="monthFilter" class="form-control">
               @foreach($dateConfig as $date)
               <option value="{{ $date->for_the_month }}" {{ $month == $date->for_the_month ? 'selected' : '' }}>
                  {{ $date->for_the_month }}
               </option>
               @endforeach
            </select>
         </div>
      </div>
      <div class="col-md-4 d-flex align-items-end">
         <div class="w-100">
            <label>District Approval Status</label>
            <select id="districtApprovalStatus" class="form-control">
               <option value="">-Select-</option>
               <option value="1">Approved</option>
               <option value="0">Pending for Approval</option>
            </select>
         </div>
      </div>
      <div class="col-md-2 d-flex align-items-end">
         <button id="filterBtn" class="btn btn-success w-100">
            Submit
         </button>
      </div>
   </div>
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-body">
               <h4 class="card-title"></h4>
               @include('dashboard.component.message')
               <div class="table-responsive m-t-40">
                  <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                     <thead>
                        <tr>
                           <th>Sl No</th>
                           <th>District</th>
                           <th>OAP 60-79 Yrs</th>
                           <th>Fund</th>
                           <th>OAP ≥ 80 Yrs</th>
                           <th>Fund</th>
                           <th>Widow</th>
                           <th>Fund</th>
                           <th>DP (40-59)%</th>
                           <th>Fund</th>
                           <th>SDP (60-79)%</th>
                           <th>Fund</th>
                           <th>SDP ≥ 80%</th>
                           <th>Fund</th>
                           <th>SDOAP</th>
                           <th>Fund</th>
                           <th>CLP</th>
                           <th>Fund</th>
                           <th>WP (AIDS)</th>
                           <th>Fund</th>
                           <th>DP (AIDS)</th>
                           <th>Fund</th>
                           <th>Unmarried Women</th>
                           <th>Fund</th>
                           <th>Orphan (Covid)</th>
                           <th>Fund</th>
                           <th>Widow (Covid)</th>
                           <th>Fund</th>
                           <th>Divorce/Destitute</th>
                           <th>Fund</th>
                           <th>Transgender</th>
                           <th>Fund</th>
                           <th>Total Beneficiaries</th>
                           <th>Total Fund</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>District</th>
                           <th>OAP 60-79 Yrs</th>
                           <th>Fund</th>
                           <th>OAP ≥ 80 Yrs</th>
                           <th>Fund</th>
                           <th>Widow</th>
                           <th>Fund</th>
                           <th>DP (40-59)%</th>
                           <th>Fund</th>
                           <th>SDP (60-79)%</th>
                           <th>Fund</th>
                           <th>SDP ≥ 80%</th>
                           <th>Fund</th>
                           <th>SDOAP</th>
                           <th>Fund</th>
                           <th>CLP</th>
                           <th>Fund</th>
                           <th>WP (AIDS)</th>
                           <th>Fund</th>
                           <th>DP (AIDS)</th>
                           <th>Fund</th>
                           <th>Unmarried Women</th>
                           <th>Fund</th>
                           <th>Orphan (Covid)</th>
                           <th>Fund</th>
                           <th>Widow (Covid)</th>
                           <th>Fund</th>
                           <th>Divorce/Destitute</th>
                           <th>Fund</th>
                           <th>Transgender</th>
                           <th>Fund</th>
                           <th>Total Beneficiaries</th>
                           <th>Total Fund</th>
                        </tr>
                     </tfoot>
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
   $(document).ready(function () {
     function formatCurrency(data) {
       if (data === null || data === undefined || data === '') return '₹ 0';
       return '₹ ' + Number(data).toLocaleString('en-IN');
    }

    function formatNumber(data) {
       if (data === null || data === undefined || data === '') return '0';
       return Number(data).toLocaleString('en-IN');
    }

    let table = $('#example23').DataTable({
       processing: true,
       serverSide: true,
       deferLoading: 0,
       order: [[1, 'asc']],

       ajax: {
         url: "{{ route('admin.pensionfundrequirementdisbursement.pension_fund_requirement_report_of_districts') }}",
         data: function (d) {
           d.for_the_month = $('#monthFilter').val();
           d.approve_status = $('#districtApprovalStatus').val();
        }
     },

     columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'district_name', name: 'district_name' },

      { data: 'mbpy_oap_below_80_years', render: formatNumber },
      { data: 'funds_mbpy_oap_below_80_years', render: formatCurrency },

      { data: 'mbpy_oap_above_80_years', render: formatNumber },
      { data: 'funds_mbpy_oap_above_80_years', render: formatCurrency },

      { data: 'mbpy_wp', render: formatNumber },
      { data: 'funds_mbpy_wp', render: formatCurrency },

      { data: 'mbpy_dp', render: formatNumber },
      { data: 'funds_mbpy_dp', render: formatCurrency },

      { data: 'mbpy_sdp_below_80_percent', render: formatNumber },
      { data: 'funds_mbpy_sdp_below_80_percent', render: formatCurrency },

      { data: 'mbpy_sdp_above_80_percent', render: formatNumber },
      { data: 'funds_mbpy_sdp_above_80_percent', render: formatCurrency },

      { data: 'mbpy_sdoap', render: formatNumber },
      { data: 'funds_mbpy_sdoap', render: formatCurrency },

      { data: 'mbpy_clp', render: formatNumber },
      { data: 'funds_mbpy_clp', render: formatCurrency },

      { data: 'mbpy_wp_aids', render: formatNumber },
      { data: 'funds_mbpy_wp_aids', render: formatCurrency },

      { data: 'mbpy_dp_aids', render: formatNumber },
      { data: 'funds_mbpy_dp_aids', render: formatCurrency },

      { data: 'mbpy_unmarried_women', render: formatNumber },
      { data: 'funds_mbpy_unmarried_women', render: formatCurrency },

      { data: 'mbpy_orphan_due_to_covide', render: formatNumber },
      { data: 'funds_mbpy_orphan_due_to_covide', render: formatCurrency },

      { data: 'mbpy_widow_due_to_covid', render: formatNumber },
      { data: 'funds_mbpy_widow_due_to_covid', render: formatCurrency },

      { data: 'mbpy_divorce_or_destitute', render: formatNumber },
      { data: 'funds_mbpy_divorce_or_destitute', render: formatCurrency },

      { data: 'mbpy_transgender', render: formatNumber },
      { data: 'funds_mbpy_transgender', render: formatCurrency },

      { data: 'total_beneficiaries', render: formatNumber },
      { data: 'total_fund', render: formatCurrency }
   ],
   
   dom: 'Blfrtip',
   buttons: [
     {
       extend: 'copy',
       footer: true
    },
    {
       extend: 'csv',
       footer: true
    },
    {
       extend: 'excel',
       footer: true
    },
    {
       extend: 'pdf',
       footer: true,
       customize: function (doc) {
         doc.styles.tableFooter = {
           bold: true,
           alignment: 'right'
        };
     }
  },
  {
    extend: 'print',
    footer: true
 }
],
lengthMenu: [[30, 50, 100, -1], [30, 50, 100, "All"]],
scrollX: true,

columnDefs: [
   { targets: '_all', className: 'text-center' },
   { targets: 0, className: 'text-start' }
],

footerCallback: function (row, data, start, end, display) {
  let api = this.api();
  let parseValue = function (i) {
    return typeof i === 'string'
    ? i.replace(/₹|,/g, '') * 1
    : typeof i === 'number'
    ? i
    : 0;
 };
 api.columns().every(function (index) {
    if (index <= 1) return;

    let total = this
    .data()
    .reduce(function (a, b) {
     return parseValue(a) + parseValue(b);
  }, 0);
    let formatted = '';
    if (index % 2 === 1 || index === 33) {
      formatted = '₹ ' + total.toLocaleString('en-IN');
} else {
   formatted = total.toLocaleString('en-IN');
}

$(this.footer()).html(formatted);
});
 $(api.column(0).footer()).html('');
 $(api.column(1).footer()).html('<b>Total</b>');
}
});
$('#filterBtn').click(function () {

  let month = $('#monthFilter').val();
  let status = $('#districtApprovalStatus').val();

  if (!month) {
    alert('Please select Month');
    return;
 }

 if (status === '') {
    alert('Please select District Approval Status');
    return;
 }

 $(this).prop('disabled', true);

 table.ajax.reload(function () {
    $('#filterBtn').prop('disabled', false);
 });
});
$('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary btn-sm me-1');

});
</script>
@endsection