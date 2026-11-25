@section('title') 
Disability Day 2026 || Participant Registrations
@endsection 
@extends('dashboard.layouts.main')

@section('style')
<style>
    .font-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    .table td {
        vertical-align: middle;
    }
    .stat-card {
        border-radius: 10px;
        transition: transform 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .bg-pink {
        background-color: #d63384 !important;
    }
</style>
@endsection 

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row page-titles">
        <div class="col-md-7 align-self-center">
            <div class="d-flex align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Disability Day 2026</a></li>
                    <li class="breadcrumb-item active">Participant Registrations</li>
                </ol>
            </div>
        </div>
        <div class="col-md-5 align-self-center text-end">
            <div class="d-flex justify-content-end gap-2">
                @can('disability-day-edit')
                <a href="{{ route('admin.disability_day_participant.create') }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-success">
                    <i class="fas fa-plus me-1"></i> Add Participant
                </a>
                @endcan
                <a href="{{ route('disability_day.participant_registration') }}" target="_blank" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-primary">
                    <i class="fas fa-external-link-alt me-1"></i> Public Registration Form
                </a>
                <a href="{{ route('admin.disability_day.index') }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-info">
                    <i class="fas fa-store me-1"></i> Stall Registrations
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @include('dashboard.component.message')

    <!-- Metrics Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm border-0 bg-primary text-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-white-50">Total Participants</div>
                        <h3 class="mb-0 fw-bold text-white">{{ number_format($totalParticipants) }}</h3>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm border-0 bg-success text-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-white-50">Accompanying Escorts</div>
                        <h3 class="mb-0 fw-bold text-white">{{ number_format($totalEscorts) }}</h3>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-user-friends"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card shadow-sm border-0 bg-purple text-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-white-50">Cultural</div>
                        <h3 class="mb-0 fw-bold text-white">{{ number_format($culturalCount) }}</h3>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-music"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card shadow-sm border-0 bg-info text-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-white-50">Sports</div>
                        <h3 class="mb-0 fw-bold text-white">{{ number_format($sportsCount) }}</h3>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-running"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-muted">Others</div>
                        <h3 class="mb-0 fw-bold text-dark">{{ number_format($othersCount) }}</h3>
                    </div>
                    <div class="fs-1 text-muted"><i class="fas fa-trophy"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h4 class="card-title mb-1">Disability Day 2026 — Participant Registrations</h4>
                            <p class="text-muted small mb-0">Venue: S'O'A University &bull; Dates: 01 Dec 2026 – 03 Dec 2026</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.disability_day_participant.export_participants_escorts') }}"
                               id="btnExportParticipantsEscorts"
                               class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i> Export Participants &amp; Escorts to Excel
                            </a>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body py-3">
                            <form id="filterForm">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label for="search_keyword" class="form-label small fw-semibold mb-1">Keyword Search</label>
                                        <input type="text" class="form-control form-control-sm" id="search_keyword" placeholder="Reg No, Name, Escort, Mobile...">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="institute_id" class="form-label small fw-semibold mb-1">Institute Name</label>
                                        <select class="form-control form-control-sm select2" id="institute_id" style="width: 100%;">
                                            <option value="">-- All Institutes --</option>
                                            @foreach($institutes as $inst)
                                                <option value="{{ $inst->id }}">{{ $inst->institute_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="gender" class="form-label small fw-semibold mb-1">Gender</label>
                                        <select class="form-control form-control-sm" id="gender">
                                            <option value="">-- All Genders --</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="participation_in" class="form-label small fw-semibold mb-1">Category</label>
                                        <select class="form-control form-control-sm" id="participation_in">
                                            <option value="">-- All Categories --</option>
                                            <option value="Walkathon">Walkathon</option>
                                            <option value="Cultural">Cultural</option>
                                            <option value="Sports">Sports</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="tshirt_size" class="form-label small fw-semibold mb-1">T-Shirt Size</label>
                                        <select class="form-control form-control-sm" id="tshirt_size">
                                            <option value="">-- All Sizes --</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                            <option value="XXXL">XXXL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="attending_date" class="form-label small fw-semibold mb-1">Attendance Date</label>
                                        <select class="form-control form-control-sm" id="attending_date">
                                            <option value="">-- All Dates --</option>
                                            @foreach(\App\Models\DisabilityDayParticipantRegistration::EVENT_DATES as $isoDate => $dateLabel)
                                                <option value="{{ $isoDate }}">{{ $dateLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="transport_mode" class="form-label small fw-semibold mb-1">Transport Mode</label>
                                        <select class="form-control form-control-sm" id="transport_mode">
                                            <option value="">-- All Modes --</option>
                                            @foreach(\App\Models\DisabilityDayParticipantRegistration::TRANSPORT_MODES as $mode)
                                                <option value="{{ $mode }}">{{ $mode }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="expected_arrival_date" class="form-label small fw-semibold mb-1">Arrival Date</label>
                                        <input type="date" class="form-control form-control-sm" id="expected_arrival_date">
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="date_from" class="form-label small fw-semibold mb-1">Reg. Date From</label>
                                        <input type="date" class="form-control form-control-sm" id="date_from">
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="date_to" class="form-label small fw-semibold mb-1">Reg. Date To</label>
                                        <input type="date" class="form-control form-control-sm" id="date_to">
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="status" class="form-label small fw-semibold mb-1">Status</label>
                                        <select class="form-control form-control-sm" id="status">
                                            <option value="">-- All Status --</option>
                                            <option value="1">Active / Registered</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                            <i class="fas fa-filter me-1"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="btnResetFilter" title="Reset Filters">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="participantRegistrationsTable" class="table table-bordered table-striped table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>Registration No.</th>
                                    <th>Participant Name</th>
                                    <th>Institute Name</th>
                                    <th>District</th>
                                    <th>Complete Address</th>
                                    <th>Gender</th>
                                    <th>T-Shirt</th>
                                    <th>Escorts</th>
                                    <th>Participation</th>
                                    <th>Dates of Participation</th>
                                    <th>Transport Mode</th>
                                    <th>Arrival Date</th>
                                    <th>Arrival Time</th>
                                    <th>Registered Date</th>
                                    <th>Status</th>
                                    <th style="width: 110px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables Server-Side AJAX -->
                            </tbody>
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
let table = null;

$(document).ready(function () {
    // Initialize Select2 filter if available
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: "-- All Institutes --",
            allowClear: true
        });
    }

    // Initialize Yajra DataTables
    table = $('#participantRegistrationsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.disability_day_participant.index') }}",
            data: function (d) {
                d.search_keyword        = $('#search_keyword').val();
                d.institute_id          = $('#institute_id').val();
                d.gender                = $('#gender').val();
                d.participation_in      = $('#participation_in').val();
                d.attending_date        = $('#attending_date').val();
                d.transport_mode        = $('#transport_mode').val();
                d.expected_arrival_date = $('#expected_arrival_date').val();
                d.tshirt_size           = $('#tshirt_size').val();
                d.status                = $('#status').val();
                d.date_from             = $('#date_from').val();
                d.date_to               = $('#date_to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
            { data: 'registration_no', name: 'registration_no' },
            { data: 'participant_name', name: 'participant_name' },
            { data: 'school_name', name: 'school_name' },
            { data: 'district', name: 'district.district_name' },
            { data: 'complete_address', name: 'complete_address', orderable: false, searchable: false },
            { data: 'gender', name: 'gender', className: 'text-center' },
            { data: 'tshirt_size', name: 'tshirt_size', className: 'text-center' },
            { data: 'number_of_escorts', name: 'number_of_escorts', className: 'text-center' },
            { data: 'participation_in', name: 'participation_in', className: 'text-center' },
            { data: 'attending_dates', name: 'attending_dates', orderable: false },
            { data: 'transport_mode', name: 'transport_mode' },
            { data: 'expected_arrival_date', name: 'expected_arrival_date', className: 'text-center' },
            { data: 'expected_arrival_time', name: 'expected_arrival_time', className: 'text-center' },
            { data: 'applied_at', name: 'applied_at' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[14, 'desc']],
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'print'],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    });

    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-excel')
        .addClass('btn btn-primary me-1');

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterForm')[0].reset();
        if ($.fn.select2) {
            $('.select2').val('').trigger('change');
        }
        table.ajax.reload();
    });

    $('#btnExportParticipantsEscorts').on('click', function (e) {
        e.preventDefault();
        const baseUrl = $(this).attr('href').split('?')[0];
        const params = {
            search_keyword: $('#search_keyword').val(),
            institute_id: $('#institute_id').val(),
            gender: $('#gender').val(),
            participation_in: $('#participation_in').val(),
            attending_date: $('#attending_date').val(),
            transport_mode: $('#transport_mode').val(),
            expected_arrival_date: $('#expected_arrival_date').val(),
            tshirt_size: $('#tshirt_size').val(),
            status: $('#status').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val()
        };
        const cleanParams = {};
        Object.keys(params).forEach(function (k) {
            if (params[k] !== null && params[k] !== undefined && String(params[k]).trim() !== '') {
                cleanParams[k] = params[k];
            }
        });
        const qs = $.param(cleanParams);
        window.location.href = qs ? (baseUrl + '?' + qs) : baseUrl;
    });
});

function confirmDelete(id, regNo) {
    Swal.fire({
        title: 'Delete Participant Record?',
        text: 'Are you sure you want to delete participant ' + regNo + '? This will also delete any associated escort records.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const deleteUrl = "{{ url('/dashboard/disability-day-participants') }}/" + id + "/delete";
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function (res) {
                    if (table) table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: res.message || 'Participant registration deleted.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to delete record.'
                    });
                }
            });
        }
    });
}
</script>
@endsection
