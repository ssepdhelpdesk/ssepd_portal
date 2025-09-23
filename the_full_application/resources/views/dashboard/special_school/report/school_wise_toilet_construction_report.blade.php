@section('title') 
Special School || No of Staffs in Schools
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
    .wrap-text {
        white-space: normal !important;
        word-break: break-word;
        max-width: 200px;
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
                <h4 class="card-title"></h4>
                @include('dashboard.component.message')
                <div class="table-responsive m-t-40">
                    <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Sl.No</th>
                                <th>District</th>
                                <th>Management Name</th>
                                <th>School Name</th>
                                <th>New/Existing</th>
                                <th>Images</th>
                                <th>Construction Status</th>
                                <th>Approve Status</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl.No</th>
                                <th>District</th>
                                <th>Management Name</th>
                                <th>School Name</th>
                                <th>New/Existing</th>
                                <th>Images</th>                                
                                <th>Construction Status</th>
                                <th>Approve Status</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($specialSchoolMapping as $school)
                            <tr>
                                <td>{{ $school->sl_no }}</td>
                                <td>{{ $school->district->district_name ?? 'N/A' }}</td>
                                <td>{{ substr($school->management_name, 0, 55) }}</td>
                                <td>{{ substr($school->special_school_name, 0, 35) }}</td>
                                <td>{{ $school->new_or_existing_text }}</td>
                                <td>
    @if($school->latest_construction_id)
        @php $images = [
            $school->construction()->find($school->latest_construction_id)->file_construction_image_1 ?? null,
            $school->construction()->find($school->latest_construction_id)->file_construction_image_2 ?? null,
            $school->construction()->find($school->latest_construction_id)->file_construction_image_3 ?? null,
            $school->construction()->find($school->latest_construction_id)->file_construction_image_4 ?? null,
            $school->construction()->find($school->latest_construction_id)->file_construction_image_5 ?? null,
        ]; @endphp

        @foreach($images as $img)
            @if($img)
                <a href="{{ asset('storage/' . $img) }}" target="_blank">
                    <img src="{{ asset('storage/' . $img) }}" alt="Construction Image" style="width:50px;height:50px;margin-right:5px;">
                </a>
            @endif
        @endforeach
    @else
        N/A
    @endif
</td>

                                <td>{{ $school->construction_status }}</td>
                                <td><a href="{{ $school->latest_construction_school_id ? route('admin.specialschoolconstructions.index', $school->latest_construction_school_id) : '#' }}" target="_blank">
                                    {{ $school->approve_status_text }}
                                </a></td>
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