@section('title')
Pension || Block/ULB Wise Daily Pension Disbursement - {{$forTheMonth}} || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection

@extends('dashboard.layouts.main')

@section('style')
@endsection

@section('content')
<div class="container-fluid">

   <div class="row page-titles">
      <div class="col-md-7 align-self-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active">@yield('title')</li>
         </ol>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <button onclick="history.back()" class="btn btn-info btn-xs text-white">
            <i class="fas fa-arrow-alt-circle-left"></i> Go Back
         </button>
      </div>
   </div>

   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-body">

               <h4 class="card-title">
                  Block / ULB Wise Daily Pension Disbursement Report - {{$forTheMonth}}
               </h4>

               @include('dashboard.component.message')

               <form class="mb-4">
                  <div class="row">
                     <div class="col-md-3">
                        <label class="form-label">
                           Select Month <span class="itsrequired">*</span>
                        </label>
                        <select class="select2 form-control form-select"
                                id="for_the_month"
                                name="for_the_month">
                           <option value="">--Select--</option>
                           @foreach($dateConfig as $config)
                              <option value="{{ $config->for_the_month }}"
                                 {{ $forTheMonth == $config->for_the_month ? 'selected' : '' }}>
                                 {{ $config->for_the_month }}
                              </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
               </form>

               <div class="table-responsive m-t-40">
                  <table id="example23" class="display nowrap table table-hover table-striped border" width="100%">
                     <thead class="table-dark">
                        <tr>
                           <th>Sl.No</th>
                           <th>District</th>
                           <th>Type</th>
                           <th>Block / ULB Name</th>
                           <th>Status</th>
                           <th class="text-end">OAP &lt;80</th>
                           <th class="text-end">OAP ≥80</th>
                           <th class="text-end">Widow</th>
                           <th class="text-end">Disabled</th>
                           <th class="text-end">SDP &lt;80%</th>
                           <th class="text-end">SDP ≥80%</th>
                           <th class="text-end">SDOAP</th>
                           <th class="text-end">CLP</th>
                           <th class="text-end">WP (AIDS)</th>
                           <th class="text-end">DP (AIDS)</th>
                           <th class="text-end">Unmarried Women</th>
                           <th class="text-end">Orphan (COVID)</th>
                           <th class="text-end">Widow (COVID)</th>
                           <th class="text-end">Divorce / Destitute</th>
                           <th class="text-end">Transgender</th>
                           <th class="text-end">Normal Pensioners</th>
                           <th class="text-end">EP Pensioners</th>
                           <th class="text-end">Total Pensioners</th>
                           <th class="text-end">Normal Fund (₹)</th>
                           <th class="text-end">EP Fund (₹)</th>
                           <th class="text-end">Total Fund (₹)</th>
                        </tr>
                     </thead>
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
$(function () {

    let table = $('#example23').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('admin.dailypensiondisbursement.block_ulb_wise_monthly_report') }}",
            type: "POST",
            data: function (d) {
                d.for_the_month = $('#for_the_month').val();
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            {data: 'DT_RowIndex'},
            {data: 'district_name'},
            {data: 'staff_address_type'},
            {data: 'block_ulb_name'},
            {data: 'status'},
            {data: 'oap_below_80', className: 'text-end'},
            {data: 'oap_above_80', className: 'text-end'},
            {data: 'widow_pension', className: 'text-end'},
            {data: 'disabled_pension', className: 'text-end'},
            {data: 'sdp_below_80', className: 'text-end'},
            {data: 'sdp_above_80', className: 'text-end'},
            {data: 'sdoap', className: 'text-end'},
            {data: 'clp', className: 'text-end'},
            {data: 'wp_aids', className: 'text-end'},
            {data: 'dp_aids', className: 'text-end'},
            {data: 'unmarried_women', className: 'text-end'},
            {data: 'orphan_covid', className: 'text-end'},
            {data: 'widow_covid', className: 'text-end'},
            {data: 'divorce_destitute', className: 'text-end'},
            {data: 'transgender', className: 'text-end'},
            {data: 'no_of_normal_pensioners', className: 'text-end'},
            {data: 'no_of_ep_pensioners', className: 'text-end'},
            {data: 'total_pensioners', className: 'text-end'},
            {data: 'funds_no_of_normal_pensioners', className: 'text-end'},
            {data: 'funds_no_of_ep_pensioners', className: 'text-end'},
            {data: 'total_funds', className: 'text-end'}
        ],
        scrollX: true,
        lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[1, 'asc']]
    });

    $('#for_the_month').on('change', function () {
        if ($(this).val()) {
            table.ajax.reload();
        }
    });

});
</script>
@endsection
