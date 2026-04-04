@section('title') 
Pension || Block/ULB wise Pension Fund Requirement for the Month {{$month}} As on {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
               <option value="3">Data Not Provided</option>
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
                           <th>Area (Block / ULB)</th>
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
                           <th>Area (Block / ULB)</th>
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

    // =============================
    // FORMATTERS
    // =============================
    function formatCurrency(data) {
        if (!data) return '₹ 0';
        return '₹ ' + Number(data).toLocaleString('en-IN');
    }

    function formatNumber(data) {
        if (!data) return '0';
        return Number(data).toLocaleString('en-IN');
    }

    // =============================
    // FIELD DEFINITIONS (CORE)
    // =============================
    const fields = [
        'mbpy_oap_below_80_years',
        'mbpy_oap_above_80_years',
        'mbpy_wp',
        'mbpy_dp',
        'mbpy_sdp_below_80_percent',
        'mbpy_sdp_above_80_percent',
        'mbpy_sdoap',
        'mbpy_clp',
        'mbpy_wp_aids',
        'mbpy_dp_aids',
        'mbpy_unmarried_women',
        'mbpy_orphan_due_to_covide',
        'mbpy_widow_due_to_covid',
        'mbpy_divorce_or_destitute',
        'mbpy_transgender'
    ];

    // =============================
    // DYNAMIC COLUMN BUILDING
    // =============================
    let columns = [
        { data: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'district_name', name: 'district_name' },
        { data: 'area_name', name: 'area_name' }
    ];

    fields.forEach(field => {
        columns.push({ data: field, render: formatNumber });
        columns.push({ data: 'funds_' + field, render: formatCurrency });
    });

    columns.push({ data: 'total_beneficiaries', render: formatNumber });
    columns.push({ data: 'total_fund', render: formatCurrency });

    // =============================
    // DATATABLE INITIALIZATION
    // =============================
    let table = $('#example23').DataTable({
        processing: true,
        serverSide: true,
        deferLoading: 0,
        stateSave: true,
        order: [[1, 'asc']],
        scrollX: true,

        ajax: {
            url: "{{ route('admin.pensionfundrequirementdisbursement.pension_fund_requirement_report_of_block_ulb') }}",
            type: 'POST',
            data: function (d) {
                d.for_the_month = $('#monthFilter').val();
                d.approve_status = $('#districtApprovalStatus').val();
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },

        columns: columns,

        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            .map(btn => ({ extend: btn, footer: true })),

        lengthMenu: [[30, 50, 100, -1], [30, 50, 100, "All"]],

        columnDefs: [
            { targets: '_all', className: 'text-center' },
            { targets: 0, className: 'text-start' }
        ],

        // =============================
        // FOOTER TOTALS (OPTIMIZED)
        // =============================
        drawCallback: function (settings) {

            let json = settings.json;
            if (!json || !json.totals) return;

            let t = json.totals;
            let api = this.api();

            $(api.column(0).footer()).html('');
            $(api.column(1).footer()).html('<b>Total</b>');
            $(api.column(2).footer()).html('');

            let colIndex = 3;

            function setValue(val, isCurrency = false) {
                $(api.column(colIndex).footer()).html(
                    isCurrency ? formatCurrency(val) : formatNumber(val)
                );
                colIndex++;
            }

            // Dynamic footer mapping
            fields.forEach(field => {
                setValue(t[field]);
                setValue(t['funds_' + field], true);
            });

            setValue(t.total_beneficiaries);
            setValue(t.total_fund, true);
        }
    });

    // =============================
    // FILTER BUTTON
    // =============================
    $('#filterBtn').click(function () {

        let month = $('#monthFilter').val();
        let status = $('#districtApprovalStatus').val();

        if (!month) {
            toastr.error('Please select Month');
            return;
        }

        if (status === '') {
            toastr.error('Please select District Approval Status');
            return;
        }

        $(this).prop('disabled', true);

        table.ajax.reload(() => {
            $('#filterBtn').prop('disabled', false);
        });
    });

    // =============================
    // BUTTON STYLING
    // =============================
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
        .addClass('btn btn-primary btn-sm me-1');

});
</script>
@endsection