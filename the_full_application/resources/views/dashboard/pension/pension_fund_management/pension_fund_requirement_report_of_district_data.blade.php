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
        <select id="monthFilter" class="select2 form-control form-select">
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
    <select id="districtApprovalStatus" class="select2 form-control form-select">
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

    // ✅ FIX: Excel column naming beyond Z
        function getExcelColumnName(n) {
            let name = '';
            while (n >= 0) {
                name = String.fromCharCode((n % 26) + 65) + name;
                n = Math.floor(n / 26) - 1;
            }
            return name;
        }

    // ✅ STORE TOTALS FROM API
        let exportTotals = {};

        let table = $('#example23').DataTable({
            processing: true,
            serverSide: true,
            deferLoading: 0,
            order: [[1, 'asc']],

            ajax: {
                url: "{{ route('admin.pensionfundrequirementdisbursement.pension_fund_requirement_report_of_district_data') }}",
                data: function (d) {
                    d.for_the_month = $('#monthFilter').val();
                    d.approve_status = $('#districtApprovalStatus').val();
                }
            },

        // ✅ FOOTER TOTALS DISPLAY
            drawCallback: function (settings) {

                let json = settings.json;
                if (!json || !json.totals) return;

                exportTotals = json.totals;

                let t = json.totals;
                let footer = $('#example23 tfoot tr');

                footer.find('th').eq(0).html('');
                footer.find('th').eq(1).html('<b>Total</b>');

                let colIndex = 2;

                function setValue(val, isCurrency = false) {
                    footer.find('th').eq(colIndex).html(
                        isCurrency ? formatCurrency(val) : formatNumber(val)
                        );
                    colIndex++;
                }

                setValue(t.mbpy_oap_below_80_years);
                setValue(t.funds_mbpy_oap_below_80_years, true);

                setValue(t.mbpy_oap_above_80_years);
                setValue(t.funds_mbpy_oap_above_80_years, true);

                setValue(t.mbpy_wp);
                setValue(t.funds_mbpy_wp, true);

                setValue(t.mbpy_dp);
                setValue(t.funds_mbpy_dp, true);

                setValue(t.mbpy_sdp_below_80_percent);
                setValue(t.funds_mbpy_sdp_below_80_percent, true);

                setValue(t.mbpy_sdp_above_80_percent);
                setValue(t.funds_mbpy_sdp_above_80_percent, true);

                setValue(t.mbpy_sdoap);
                setValue(t.funds_mbpy_sdoap, true);

                setValue(t.mbpy_clp);
                setValue(t.funds_mbpy_clp, true);

                setValue(t.mbpy_wp_aids);
                setValue(t.funds_mbpy_wp_aids, true);

                setValue(t.mbpy_dp_aids);
                setValue(t.funds_mbpy_dp_aids, true);

                setValue(t.mbpy_unmarried_women);
                setValue(t.funds_mbpy_unmarried_women, true);

                setValue(t.mbpy_orphan_due_to_covide);
                setValue(t.funds_mbpy_orphan_due_to_covide, true);

                setValue(t.mbpy_widow_due_to_covid);
                setValue(t.funds_mbpy_widow_due_to_covid, true);

                setValue(t.mbpy_divorce_or_destitute);
                setValue(t.funds_mbpy_divorce_or_destitute, true);

                setValue(t.mbpy_transgender);
                setValue(t.funds_mbpy_transgender, true);

                setValue(t.total_beneficiaries);
                setValue(t.total_fund, true);
            },

            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'district_name' },

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

            columnDefs: [
                { targets: '_all', className: 'text-center' },

                { targets: 0, className: 'text-left' },
                { targets: 1, className: 'text-left font-weight-bold' }
            ],

            dom: 'Blfrtip',

            buttons: [

                'copy',

            // ✅ CSV
                {
                    extend: 'csv',
                    customize: function (csv) {
                        let t = exportTotals;

                        let row = [
                            '', 'Total',

                            t.mbpy_oap_below_80_years, t.funds_mbpy_oap_below_80_years,
                            t.mbpy_oap_above_80_years, t.funds_mbpy_oap_above_80_years,
                            t.mbpy_wp, t.funds_mbpy_wp,
                            t.mbpy_dp, t.funds_mbpy_dp,
                            t.mbpy_sdp_below_80_percent, t.funds_mbpy_sdp_below_80_percent,
                            t.mbpy_sdp_above_80_percent, t.funds_mbpy_sdp_above_80_percent,
                            t.mbpy_sdoap, t.funds_mbpy_sdoap,
                            t.mbpy_clp, t.funds_mbpy_clp,
                            t.mbpy_wp_aids, t.funds_mbpy_wp_aids,
                            t.mbpy_dp_aids, t.funds_mbpy_dp_aids,
                            t.mbpy_unmarried_women, t.funds_mbpy_unmarried_women,
                            t.mbpy_orphan_due_to_covide, t.funds_mbpy_orphan_due_to_covide,
                            t.mbpy_widow_due_to_covid, t.funds_mbpy_widow_due_to_covid,
                            t.mbpy_divorce_or_destitute, t.funds_mbpy_divorce_or_destitute,
                            t.mbpy_transgender, t.funds_mbpy_transgender,
                            t.total_beneficiaries, t.total_fund
                        ];

                        return csv + '\n' + row.join(',');
                    }
                },

            // ✅ EXCEL (FIXED)
                {
                    extend: 'excel',
                    customize: function (xlsx) {

                        let sheet = xlsx.xl.worksheets['sheet1.xml'];
                        let newRow = $('row', sheet).length + 1;

                        let t = exportTotals;

                        let rowData = [
                            '', 'Total',

                            t.mbpy_oap_below_80_years, t.funds_mbpy_oap_below_80_years,
                            t.mbpy_oap_above_80_years, t.funds_mbpy_oap_above_80_years,
                            t.mbpy_wp, t.funds_mbpy_wp,
                            t.mbpy_dp, t.funds_mbpy_dp,
                            t.mbpy_sdp_below_80_percent, t.funds_mbpy_sdp_below_80_percent,
                            t.mbpy_sdp_above_80_percent, t.funds_mbpy_sdp_above_80_percent,
                            t.mbpy_sdoap, t.funds_mbpy_sdoap,
                            t.mbpy_clp, t.funds_mbpy_clp,
                            t.mbpy_wp_aids, t.funds_mbpy_wp_aids,
                            t.mbpy_dp_aids, t.funds_mbpy_dp_aids,
                            t.mbpy_unmarried_women, t.funds_mbpy_unmarried_women,
                            t.mbpy_orphan_due_to_covide, t.funds_mbpy_orphan_due_to_covide,
                            t.mbpy_widow_due_to_covid, t.funds_mbpy_widow_due_to_covid,
                            t.mbpy_divorce_or_destitute, t.funds_mbpy_divorce_or_destitute,
                            t.mbpy_transgender, t.funds_mbpy_transgender,
                            t.total_beneficiaries, t.total_fund
                        ];

                        let rowXml = `<row r="${newRow}">`;

                        rowData.forEach((val, i) => {
                            let col = getExcelColumnName(i);
                            rowXml += `<c t="inlineStr" r="${col}${newRow}">
                                    <is><t>${val ?? ''}</t></is>
                        </c>`;
                    });

                        rowXml += '</row>';
                        $('sheetData', sheet).append(rowXml);
                    }
                },

                'pdf',

            // ✅ PRINT
                {
                    extend: 'print',
                    customize: function (win) {
                        let t = exportTotals;

                        let row = `<tr>
                        <td></td><td><b>Total</b></td>
                        ${[
                            t.mbpy_oap_below_80_years, t.funds_mbpy_oap_below_80_years,
                            t.mbpy_oap_above_80_years, t.funds_mbpy_oap_above_80_years,
                            t.mbpy_wp, t.funds_mbpy_wp,
                            t.mbpy_dp, t.funds_mbpy_dp,
                            t.mbpy_sdp_below_80_percent, t.funds_mbpy_sdp_below_80_percent,
                            t.mbpy_sdp_above_80_percent, t.funds_mbpy_sdp_above_80_percent,
                            t.mbpy_sdoap, t.funds_mbpy_sdoap,
                            t.mbpy_clp, t.funds_mbpy_clp,
                            t.mbpy_wp_aids, t.funds_mbpy_wp_aids,
                            t.mbpy_dp_aids, t.funds_mbpy_dp_aids,
                            t.mbpy_unmarried_women, t.funds_mbpy_unmarried_women,
                            t.mbpy_orphan_due_to_covide, t.funds_mbpy_orphan_due_to_covide,
                            t.mbpy_widow_due_to_covid, t.funds_mbpy_widow_due_to_covid,
                            t.mbpy_divorce_or_destitute, t.funds_mbpy_divorce_or_destitute,
                            t.mbpy_transgender, t.funds_mbpy_transgender,
                            t.total_beneficiaries, t.total_fund
                        ].map(v => `<td>${v}</td>`).join('')}
                    </tr>`;

                    $(win.document.body).find('table tbody').append(row);
                }
            }
        ],

        scrollX: true
    });

    // FILTER
$('#filterBtn').click(function () {

    let month = $('#monthFilter').val();
    let status = $('#districtApprovalStatus').val();

    if (!month) return alert('Please select Month');
    if (status === '') return alert('Please select District Approval Status');

    $(this).prop('disabled', true);

    table.ajax.reload(() => {
        $('#filterBtn').prop('disabled', false);
    });
});

});
</script>
@endsection