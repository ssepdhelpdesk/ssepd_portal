@section('title') 
Special School || Toilet Construction Status
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
    .wrap-text {
        white-space: normal !important;
        word-break: break-word;
        max-width: 200px;
    }
    /* Gallery container */
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

/* Each image styling */
.image-gallery img {
    width: 60px;       /* Thumbnail size */
    height: 60px;
    object-fit: cover; /* Crop without distortion */
    border-radius: 5px;
    border: 1px solid #ddd;
    transition: transform 0.2s, box-shadow 0.2s;
}

/* Hover effect */
.image-gallery img:hover {
    transform: scale(1.2);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 10;
}
.wrap-text {
    white-space: normal !important;
    word-wrap: break-word;
    word-break: break-word;
    max-width: 220px; /* adjust as needed */
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
                                <th>Type</th>
                                <th class="wrap-text">Management Name</th>
                                <th class="wrap-text">School Name</th>
                                <th>New/Existing</th>
                                <th>Images</th>
                                <th class="wrap-text">Construction Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl.No</th>
                                <th>District</th>
                                <th>Type</th>
                                <th class="wrap-text">Management Name</th>
                                <th class="wrap-text">School Name</th>
                                <th>New/Existing</th>
                                <th>Images</th>
                                <th class="wrap-text">Construction Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($specialSchoolMapping as $school)
                            <tr>
                                <td>{{ $school->sl_no }}</td>
                                <td>{{ $school->district->district_name ?? 'N/A' }}</td>
                                <td>
                                    @if($school->which_govt == 1)
                                    Govt of Odisha
                                    @elseif($school->which_govt == 2)
                                    Govt of India
                                    @else
                                    Not specified
                                    @endif
                                </td>
                                <td class="wrap-text">{{ substr($school->management_name, 0, 200) }}</td>
                                <td class="wrap-text">{{ substr($school->special_school_name, 0, 200) }}</td>
                                <td>{{ $school->new_or_existing_text }}</td>
                                <td>
                                    @if($school->latest_construction_id)
                                    @php
                                    $images = [
                                    $school->construction()->find($school->latest_construction_id)->file_construction_image_1 ?? null,
                                    $school->construction()->find($school->latest_construction_id)->file_construction_image_2 ?? null,
                                    $school->construction()->find($school->latest_construction_id)->file_construction_image_3 ?? null,
                                    $school->construction()->find($school->latest_construction_id)->file_construction_image_4 ?? null,
                                    $school->construction()->find($school->latest_construction_id)->file_construction_image_5 ?? null,
                                    ];
                                    @endphp

                                    <div class="image-gallery">
                                        @foreach($images as $img)
                                        @if($img)
                                        <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $img) }}" alt="Construction Image">
                                        </a>
                                        @endif
                                        @endforeach
                                    </div>
                                    @else
                                    <span class="text-muted">Not Yet Uploaded</span>
                                    @endif
                                </td>

                                <td class="wrap-text">{{ $school->construction_status }}</td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            @if(auth()->check() && auth()->user()->role_id == 9 && $school->verifier_status === 0)
                                            <a class="dropdown-item btn-dsso-verify"
                                            href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#dssoModal"
                                            data-id="{{ $school->id }}"
                                            data-school="{{ $school->special_school_name }}"
                                            data-management="{{ $school->management_name }}">
                                            DSSO Verification
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="dssoModal" class="modal fade" tabindex="-1" aria-labelledby="dssoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered"> 
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="dssoModalLabel">DSSO Verification</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="dssoForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="school_id" name="school_id">
                                <div class="mb-3">
                                    <label>Management Name</label>
                                    <input type="text" id="school_management_name" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label>School Name</label>
                                    <input type="text" id="school_name" class="form-control" readonly>
                                </div>
                                <div class="row">    
                                    <div class="col-md-4 mb-3">
                                        <label>Status</label>
                                        <select name="verifier_status" class="form-control select">
                                            <option value="">Select</option>
                                            <option value="1">Approve</option>
                                            <option value="2">Reject</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label>Verification Date</label>
                                        <input type="date" name="dsso_verification_date" class="form-control" max="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label>Upload Report (PDF)</label>
                                        <input type="file" name="dsso_verification_report" class="form-control" accept="application/pdf">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Remarks</label>
                                    <textarea name="dsso_verification_remark" class="form-control"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect text-white" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" form="dssoForm" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
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

    $(document).on('click', '.btn-dsso-verify', function () {

        let schoolId = $(this).data('id');
        let schoolName = $(this).data('school');
        let managementName = $(this).data('management');

        $('#school_id').val(schoolId);
        $('#school_name').val(schoolName);
        $('#school_management_name').val(managementName);

        let url = "{{ route('admin.specialschoolconstructions.approve_construction_status_by_dsso_store', ':id') }}";
        url = url.replace(':id', schoolId);

        $('#dssoForm').attr('action', url);
    });
</script>
@endsection