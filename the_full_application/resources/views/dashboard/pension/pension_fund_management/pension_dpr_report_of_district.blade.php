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
'death_reported' => ['label' => 'Death Reported'],
];
@endphp

@section('title') 
Pension || District wise Daily Pension Disbursement <!-- for the Month {{$month}} --> As on {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection
@extends('dashboard.layouts.main')
@section('content')
<div class="container-fluid">
   <div class="row page-titles">
      <div class="col-md-7 align-self-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active">@yield('title')</li>
         </ol>
      </div>
      <div class="col-md-5 text-end">
         <button onclick="history.back()" class="btn btn-info btn-xs text-white">
            <i class="fas fa-arrow-alt-circle-left"></i> Go Back
         </button>
      </div>
   </div>
   <div class="row mb-3">
      <div class="col-md-4">
         <label>Select Month</label>
         <select id="monthFilter" class="select2 form-control form-select">
            @foreach($dateConfig as $date)
            <option value="{{ $date->for_the_month }}" {{ $month==$date->for_the_month?'selected':'' }}>
               {{ $date->for_the_month }}
            </option>
            @endforeach
         </select>
      </div>
      <div class="col-md-4">
         <label>District Approval Status</label>
         <select id="districtApprovalStatus" class="select2 form-control form-select">
            <option value="">-Select-</option>
            <option value="1">Approved</option>
            <option value="0">Pending for Approval</option>
            <option value="3">Data Not Provided</option>
         </select>
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
               @include('dashboard.component.message')
               <div class="table-responsive m-t-40">
                  <table id="example23" class="display nowrap table table-hover table-striped border" width="100%">
                     <thead>
                        <tr>
                           <th>Sl No</th>
                           <th>District</th>
                           @foreach($fieldMap as $f)
                           <th>{{$f['label']}}</th>
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
   $(document).ready(function(){
      $.fn.dataTable.ext.errMode = 'none';
      $('#example23').on('error.dt', function(e, settings, techNote, message){
         console.log("DataTables error:", message);
      });

      const fields = @json(array_keys($fieldMap));

      function formatCurrency(x){
         return '₹ ' + Number(x || 0).toLocaleString('en-IN');
      }

      function formatNumber(x){
         return Number(x || 0).toLocaleString('en-IN');
      }

      let columns = [
         {data:'DT_RowIndex',orderable:false,searchable:false},
         {data:'district_name'}
      ];

      fields.forEach(f=>{
         columns.push({data:f,render:formatNumber});
         columns.push({data:'funds_'+f,render:formatCurrency});
      });

      columns.push({data:'total_beneficiaries',render:formatNumber});
      columns.push({data:'total_fund',render:formatCurrency});

      let table = $('#example23').DataTable({
         processing:true,
         serverSide:true,
         scrollX:true,
         deferLoading:0,
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
            url:"{{ route('admin.pensionfundrequirementdisbursement.pension_dpr_report_of_district') }}",
            type:'POST',
            data:function(d){
               d.for_the_month = $('#monthFilter').val();
               d.approve_status = $('#districtApprovalStatus').val();
            },
            headers:{
               'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            error:function(xhr){
               console.log("AJAX ERROR",xhr.responseText);
            }
         },
         columns:columns,
         drawCallback:function(settings){
            let totals=settings.json?.totals;
            if(!totals) return;
            let api=this.api();
            let col=2;
            function set(val,isMoney=false){
               $(api.column(col).footer()).html(
                  isMoney ? formatCurrency(val) : formatNumber(val)
                  );
               col++;
            }
            fields.forEach(f=>{
               set(totals[f]);
               set(totals['funds_'+f],true);
            });
            set(totals.total_beneficiaries);
            set(totals.total_fund,true);
         }
      });
      $('#filterBtn').click(function(){
         let month=$('#monthFilter').val();
         let status=$('#districtApprovalStatus').val();
         if(!month){
            alert('Select Month');
            return;
         }
         if(status===''){
            alert('Select Status');
            return;
         }
         table.ajax.reload();
      });
   });
</script>
@endsection