@section('title') 
EP Pension || Scheme Wise Beneficiary Details of District: {{ $district }}, Category: {{ strtoupper($category) }}, Status: {{ strtoupper($status) }} @if($scheme) ,Scheme: {{ $scheme }} @endif @if($from_date && $to_date) ,Period: {{ $from_date }} to {{ $to_date }} @endif
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
                    <h4 class="card-title"></h4>
                    @include('dashboard.component.message')
                    <small class="text-muted">
                     District:
                     <strong>{{ $district }}</strong>
                     |
                     Category:
                     <strong>{{ strtoupper($category) }}</strong>
                     |
                     Status:
                     <strong>{{ strtoupper($status) }}</strong>
                     @if($scheme)
                     | Scheme:
                     <strong>{{ $scheme }}</strong>
                     @endif
                     @if($from_date && $to_date)
                     | Period:
                     <strong>{{ $from_date }} to {{ $to_date }}</strong>
                     @endif
                 </small>
                 <div class="table-responsive m-t-40">
                    <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Beneficiary Name</th>
                                <th>Care Of</th>
                                <th>DOB</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Category</th>
                                <th>Scheme</th>
                                <th>UDID No</th>
                                <th>Disability Type</th>
                                <th>District</th>
                                <th>Block</th>
                                <th>GP / Ward</th>
                                <th>Village / Locality</th>
                                <th>Aadhaar No</th>
                                <th>Sanction Order No</th>
                                <th>Discontinued Reason</th>
                                <th>Discontinued Date</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl No</th>
                                <th>Beneficiary Name</th>
                                <th>Care Of</th>
                                <th>DOB</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Category</th>
                                <th>Scheme</th>
                                <th>UDID No</th>
                                <th>Disability Type</th>
                                <th>District</th>
                                <th>Block</th>
                                <th>GP / Ward</th>
                                <th>Village / Locality</th>
                                <th>Aadhaar No</th>
                                <th>Sanction Order No</th>
                                <th>Discontinued Reason</th>
                                <th>Discontinued Date</th>
                            </tr>
                        </tfoot>
                        <tbody>
                          @foreach($records as $index => $row)
                          <tr>
                           <td>{{ $index + 1 }}</td>
                           <td>{{ $row->name_of_the_beneficiary ?? $row->name_of_the_beneficiary }}</td>
                           <td>{{ $row->father_or_husband_name }}</td>
                           <td>
                               @php
                               $dob = $row->date_of_birth;
                               @endphp

                               @if($dob && strtotime($dob) !== false)
                               {{ \Carbon\Carbon::parse($dob)->format('d M Y') }}
                               @else
                               -
                               @endif
                           </td>
                           <td>{{ $row->age }}</td>
                           <td>{{ $row->gender }}</td>
                           <td>
                            {{ $row instanceof \App\Models\OldAge3500Pensioner ? 'Old Age' : 'Disability' }}
                        </td>
                        <td>{{ $row->scheme_name }}</td>
                        <td>{{ $row->udid_no ?? '-' }}</td>
                        <td>{{ $row->disability_category ?? '-' }}</td>
                        <td>{{ $row->district }}</td>
                        <td>{{ $row->block_or_ulb ?? '-' }}</td>
                        <td>{{ $row->gp_or_ward ?? $row->gp_or_ward ?? '-' }}</td>
                        <td>{{ $row->village ?? '-' }}</td>
                        <td>
                            @if(!empty($row->aadhaar_no))
                            {{ substr($row->aadhaar_no, 0, 4) }}-XXXX-{{ substr($row->aadhaar_no, -4) }}
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $row->nsap_sanction_order_no ?? '-' }}</td>
                        <td>{{ $row->discontinued_reason ?? '-' }}</td>
                        <td>{{ $row->discontinued_date ?? '-' }}</td>                     
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