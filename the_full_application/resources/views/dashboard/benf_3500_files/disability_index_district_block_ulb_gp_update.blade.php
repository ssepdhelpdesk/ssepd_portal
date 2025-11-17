@section('title') 
EP Pension || Disability GP Update
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
                           <th>Sceheme</th>
                           <th>Benf Name</th>
                           <th>Care of</th>
                           <th>DOB</th>
                           <th>Age</th>
                           <th>Gender</th>
                           <th>District</th>
                           <th>Block</th>
                           <!-- <th>Complete Address</th> -->
                           <th>NSAP Sanction Or No</th>
                           <th>Sub-Col Sign Or No</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th>Sl No</th>
                           <th>Sceheme</th>
                           <th>Benf Name</th>
                           <th>Care of</th>
                           <th>DOB</th>
                           <th>Age</th>
                           <th>Gender</th>
                           <th>District</th>
                           <th>Block</th>
                           <!-- <th>Complete Address</th> -->
                           <th>NSAP Sanction Or No</th>
                           <th>Sub-Col Sign Or No</th>
                           <th>Action</th>
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
     $('#example23').DataTable({
       processing: true,
       serverSide: true,
       ajax: "{{ route('admin.disability3500data.disability_index_district_block_ulb_gp_update') }}",
       columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'scheme_name', name: 'scheme_name' },
        { data: 'name_of_the_beneficiary', name: 'name_of_the_beneficiary' },
        { data: 'father_or_husband_name', name: 'father_or_husband_name' },
        { data: 'date_of_birth', name: 'date_of_birth' },
        { data: 'age', name: 'age'},
        { data: 'gender', name: 'gender' },
        { data: 'district', name: 'district' },
        { data: 'block_or_ulb', name: 'block_or_ulb' },
        /*{ data: 'complete_address', name: 'complete_address' },*/
        { data: 'nsap_sanction_order_no', name: 'nsap_sanction_order_no' },
        { data: 'sub_collector_sanction_order_no', name: 'sub_collector_sanction_order_no' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
     ],
     dom: 'Blfrtip',
     buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
     lengthMenu: [[10, 500, 1000, -1], [10, 500, 1000, "All"]],
  });

     $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel')
     .addClass('btn btn-primary me-1');
  });
</script>
@endsection