@php
$fieldMap = [
'mbpy_oap_below_80_years' => ['label' => 'OAP 60-79 Yrs'],
'mbpy_oap_above_80_years' => ['label' => 'OAP ≥ 80 Yrs'],
'mbpy_wp' => ['label' => 'Widow'],
'mbpy_dp' => ['label' => 'DP (40-59)%'],
'mbpy_sdp_below_80_percent' => ['label' => 'SDP (60-79)%'],
'mbpy_sdp_above_80_percent' => ['label' => 'SDP ≥ 80%'],
'mbpy_sdoap' => ['label' => 'SDOAP'],
'mbpy_clp' => ['label' => 'CLP'],
'mbpy_wp_aids' => ['label' => 'WP (AIDS)'],
'mbpy_dp_aids' => ['label' => 'DP (AIDS)'],
'mbpy_unmarried_women' => ['label' => 'Unmarried Women'],
'mbpy_orphan_due_to_covide' => ['label' => 'Orphan (Covid)'],
'mbpy_widow_due_to_covid' => ['label' => 'Widow (Covid)'],
'mbpy_divorce_or_destitute' => ['label' => 'Divorce/Destitute'],
'mbpy_transgender' => ['label' => 'Transgender'],
];
@endphp

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
                           <th>Area</th>
                           <th>Bank Account No</th>
                           <th>IFSC Code</th>

                           @foreach($fieldMap as $f)
                           <th>{{ $f['label'] }}</th>
                           <th>Fund</th>
                           @endforeach

                           <th>Total Beneficiaries</th>
                           <th>Total Fund</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th></th>
                           <th>Total</th>
                           <th></th>
                           <th></th>
                           <th></th>

                           @foreach($fieldMap as $f)
                           <th></th>
                           <th></th>
                           @endforeach

                           <th></th>
                           <th></th>
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
   $(function () {

    // =============================
    // SINGLE SOURCE FROM BLADE
    // =============================
     const fields = @json(array_keys($fieldMap));

     function formatCurrency(x) {
       return '₹ ' + Number(x || 0).toLocaleString('en-IN');
    }

    function formatNumber(x) {
       return Number(x || 0).toLocaleString('en-IN');
    }

    // =============================
    // COLUMNS
    // =============================
    let columns = [
       {data:'DT_RowIndex', orderable:false},
       {data:'district_name'},
       {data:'area_name'},
       {data:'mbpy_bank_account_number', defaultContent:'-'},
       {data:'mbpy_bank_ifsc_code', defaultContent:'-'}
    ];

    fields.forEach(f => {
       columns.push({data:f, render:formatNumber});
       columns.push({data:'funds_'+f, render:formatCurrency});
    });

    columns.push({data:'total_beneficiaries', render:formatNumber});
    columns.push({data:'total_fund', render:formatCurrency});

    // =============================
    // DATATABLE
    // =============================
    let table = $('#example23').DataTable({
     processing:true,
     serverSide:true,
     ordering:true,
     scrollX:true,
     pageLength:10,
     lengthMenu:[[10,500,1000,-1],[10,500,1000,"All"]],

     dom:'Blfrtip',

     buttons:[
       {extend:'copy',footer:true},
       {extend:'csv',footer:true},
       {extend:'excel',footer:true},
       {extend:'pdf',footer:true},
       {extend:'print',footer:true}
    ],

    ajax:{
       url:"{{ route('admin.pensionfundrequirementdisbursement.pension_fund_requirement_report_of_block_ulb') }}",
       type:'POST',
       data:d=>{
         d.for_the_month=$('#monthFilter').val();
         d.approve_status=$('#districtApprovalStatus').val();
      },
      headers:{
         'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
      }
   },

   columns:columns,

   drawCallback:function(settings){

    let t=settings.json?.totals;
    if(!t) return;

    let api=this.api();
    let col=5;

    function set(val,isMoney=false){
      $(api.column(col).footer())
      .html(isMoney?formatCurrency(val):formatNumber(val));
      col++;
   }

   fields.forEach(f=>{
      set(t[f]);
      set(t['funds_'+f],true);
   });

   set(t.total_beneficiaries);
   set(t.total_fund,true);
}
});
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');

    // =============================
    // FILTER
    // =============================
    $('#filterBtn').click(function(){

       if(!$('#monthFilter').val()) return alert('Select Month');
       if($('#districtApprovalStatus').val()==='') return alert('Select Status');

       table.ajax.reload();
    });

 });
</script>
@endsection