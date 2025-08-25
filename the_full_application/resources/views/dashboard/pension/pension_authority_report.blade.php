@section('title') 
Pension || Block/ULB wise Pension Disbursing Office Details || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
@endsection 
@extends('dashboard.layouts.main')
@section('style')
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
            <h4 class="card-title">Block/ULB wise Pension Disbursing Office Details</h4>
            @include('dashboard.component.message')
            <div class="table-responsive m-t-40">
               <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Block/ULB</th>
                        <th>Gp/Ward</th>
                        <th>Provided/Not Provided</th>
                        <th>Officer Name</th>
                        <th>Mobile No</th>
                        <th>Designation</th>                        
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tfoot>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Block/ULB</th>
                        <th>Gp/Ward</th>
                        <th>Provided/Not Provided</th>
                        <th>Officer Name</th>
                        <th>Mobile No</th>
                        <th>Designation</th>                        
                        <th>Action</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @forelse($pensiondisbursementAuthority as $index => $disbursementAuthority)
                     <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $disbursementAuthority->district->district_name ?? 'Not Provided' }}</td>
                        <td>
                           @if($disbursementAuthority->staff_address_type == 1)
                           Block: {{ $disbursementAuthority->block->block_name ?? 'Not Provided' }}
                           @elseif($disbursementAuthority->staff_address_type == 2)
                           ULB: {{ $disbursementAuthority->municipality->municipality_name ?? 'Not Provided' }}
                           @else
                           Not Provided
                           @endif
                        </td>
                        <td>
                           @if($disbursementAuthority->staff_address_type == 1)
                           {{ $disbursementAuthority->grampanchayat->gp_name ?? 'Not Provided' }}
                           @elseif($disbursementAuthority->staff_address_type == 2)
                           {{ $disbursementAuthority->ward->ward_name ?? 'Not Provided' }}
                           @else
                           Not Provided
                           @endif
                        </td>
                        <td>
                           @if(isset($disbursementAuthority->id))
                           @if($disbursementAuthority->staff_address_type == 1)
                           <span class="badge bg-success">Submitted GP</span>
                           @elseif($disbursementAuthority->staff_address_type == 2)
                           <span class="badge bg-success">Submitted Ward</span>
                           @endif
                           @else
                           @if($disbursementAuthority->staff_address_type == 1)
                           <span class="badge bg-danger">Pending GP</span>
                           @elseif($disbursementAuthority->staff_address_type == 2)
                           <span class="badge bg-danger">Pending Ward</span>
                           @endif
                           @endif
                        </td>
                        <td>{{ $disbursementAuthority->authority_name ?? '-' }}</td>
                        <td>{{ $disbursementAuthority->authority_mobile_no ?? '-' }}</td>
                        <td>
                           @switch($disbursementAuthority->authority_designation)
                           @case(1) PEO @break
                           @case(2) CO @break
                           @case(3) Tax Collector @break
                           @case(4) JA @break
                           @case(5) PA @break
                           @case(6) ADEO @break
                           @case(7) GRS @break
                           @case(8) Other @break
                           @case(9) Jogana Sahayak (JS) @break
                           @default  -
                           @endswitch
                        </td>
                        <td>
                           <div class="btn-group">
                              <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 Action
                              </button>
                              <div class="dropdown-menu">
                                 @if(!empty($disbursementAuthority->id))
                                 @can('pension-edit')
                                 <a class="dropdown-item" href="">Edit</a>
                                 @endcan
                                 @can('pension-delete')
                                 <a class="dropdown-item" href="{{ route('admin.pension.pension_authority_delete', $disbursementAuthority->id) }}" id="delete">Delete</a>
                                 @endcan
                                 @endif
                              </div>
                           </div>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="8" class="text-center text-muted">No Records Found Yet</td>
                     </tr>
                     @endforelse
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