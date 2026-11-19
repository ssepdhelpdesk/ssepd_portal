@section('title') 
Disability Day 2026 || Stall Registrations
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
                    <li class="breadcrumb-item active">Stall Registrations</li>
                </ol>
            </div>
        </div>
        <div class="col-md-5 align-self-center text-end">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('disability_day.registration') }}" target="_blank" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-primary">
                    <i class="fas fa-external-link-alt me-1"></i> Public Form
                </a>
                <a href="{{ route('disability_day.track') }}" target="_blank" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-info">
                    <i class="fas fa-search me-1"></i> Public Tracking
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @include('dashboard.component.message')

    <!-- Metrics Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-primary text-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-white-50">Total Applications</div>
                        <h3 class="mb-0 fw-bold text-white">{{ number_format($totalCount) }}</h3>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-clipboard-list"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-success text-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-white-50">Stalls Allotted</div>
                        <h3 class="mb-0 fw-bold text-white">{{ number_format($allottedCount) }}</h3>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-store"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold text-muted">Pending Allotment</div>
                        <h3 class="mb-0 fw-bold">{{ number_format($unallottedCount) }}</h3>
                    </div>
                    <div class="fs-1 text-muted"><i class="fas fa-hourglass-half"></i></div>
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
                            <h4 class="card-title mb-1">Disability Day 2026 — Stall Registrations</h4>
                            <p class="text-muted small mb-0">Venue: S'O'A University &bull; Dates: 01 Dec 2026 – 03 Dec 2026</p>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body p-3">
                            <form id="filterForm" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold mb-1">Search Keyword</label>
                                    <input type="text" class="form-control form-control-sm" id="search_keyword" placeholder="Reg No, Org, Person, Email, Phone...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold mb-1">District</label>
                                    <select class="form-select form-select-sm" id="district_id">
                                        <option value="">All Districts</option>
                                        @foreach($districts as $d)
                                        <option value="{{ $d->id }}">{{ $d->district_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold mb-1">Financial Year</label>
                                    <select class="form-select form-select-sm" id="financial_year">
                                        <option value="">All FY</option>
                                        @foreach($financialYears as $fy)
                                        <option value="{{ $fy }}">{{ $fy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold mb-1">Allotment Status</label>
                                    <select class="form-select form-select-sm" id="allotment_status">
                                        <option value="">All Statuses</option>
                                        <option value="allotted">Allotted Only</option>
                                        <option value="not_allotted">Not Yet Allotted</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label small fw-semibold mb-1">From</label>
                                    <input type="date" class="form-control form-control-sm" id="date_from">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label small fw-semibold mb-1">To</label>
                                    <input type="date" class="form-control form-control-sm" id="date_to">
                                </div>
                                <div class="col-md-1 d-flex gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100" title="Apply Filters">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" id="btnResetFilter" title="Reset Filters">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive m-t-15">
                        <table id="stallRegistrationsTable" class="display nowrap table table-hover table-striped border align-middle" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th style="width: 190px;">Registration No</th>
                                    <th>Organization Name</th>
                                    <th style="width: 140px;">Contact Person</th>
                                    <th style="width: 160px;">Email</th>
                                    <th style="width: 120px;">Phone</th>
                                    <th style="width: 110px;">District</th>
                                    <th style="width: 130px;">Applied At</th>
                                    <th style="width: 140px;" class="text-center">Allotted Stalls</th>
                                    <th style="width: 250px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Registration No</th>
                                    <th>Organization Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>District</th>
                                    <th>Applied At</th>
                                    <th class="text-center">Allotted Stalls</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Assign / Update Stall Allotment -->
<div class="modal fade" id="allotStallModal" tabindex="-1" aria-labelledby="allotStallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="allotStallForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="allotStallModalLabel">
                        <i class="fas fa-store-alt me-2"></i> Assign Stall Allotment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="small text-muted">Registration Number</div>
                                    <div class="fw-bold font-monospace text-primary" id="modalRegNo">—</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="small text-muted">Organization</div>
                                    <div class="fw-bold text-dark text-truncate" id="modalOrgName">—</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="modalLoadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading available stalls...</span>
                        </div>
                        <div class="small text-muted mt-2">Checking stall availability (1 to 100)...</div>
                    </div>

                    <!-- Full Capacity Alert -->
                    <div id="modalCapacityAlert" class="alert alert-danger d-none py-2 px-3 small mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> <strong>All 100 stalls are assigned!</strong> No unassigned stalls are currently available.
                    </div>

                    <!-- Stall Selection Container -->
                    <div id="modalStallFieldContainer" class="d-none">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="modalAllottedStall" class="form-label fw-semibold mb-0">
                                    Allot Stalls (1 to 100) — Multi-select
                                </label>
                                <span id="modalAvailabilityCount" class="badge bg-info text-dark"></span>
                            </div>
                            <select class="form-select" id="modalAllottedStall" name="stalls[]" multiple="multiple" style="width: 100%;">
                                <!-- Dynamically populated via AJAX -->
                            </select>
                            <div class="form-text small mt-1">
                                <i class="fas fa-info-circle me-1"></i> You can select multiple available stalls. Existing allotments remain selected. Deselect to unassign.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" id="modalClearAllBtn" title="Clear all allotments for this organization">
                                <i class="fas fa-times me-1"></i> Unassign All Stalls
                            </button>
                        </div>

                        <div id="modalCurrentStatus" class="p-2 rounded bg-light border small text-muted"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="modalSubmitBtn">
                        <i class="fas fa-save me-1"></i> Save Allotments
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Clear All Form -->
<form id="clearAllStallForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="clear_all" value="1">
</form>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('script')
<script>
let table = null;
let currentModalRegId = null;
let isSendingAllotmentEmail = false;

$(function () {
    table = $('#stallRegistrationsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.disability_day.index') }}",
            data: function (d) {
                d.search_keyword = $('#search_keyword').val();
                d.district_id = $('#district_id').val();
                d.financial_year = $('#financial_year').val();
                d.allotment_status = $('#allotment_status').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
            { data: 'registration_no', name: 'registration_no' },
            { data: 'organization_name', name: 'organization_name' },
            { data: 'contact_person', name: 'contact_person' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'district', name: 'district.district_name' },
            { data: 'applied_at', name: 'applied_at' },
            { data: 'allotted_stall', name: 'allotted_stall', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[7, 'desc']],
        dom: 'Blfrtip',
        buttons: ['copy', 'csv', 'excel', 'print'],
        lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
    });

    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-excel')
        .addClass('btn btn-primary me-1');

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterForm')[0].reset();
        table.ajax.reload();
    });

    // Clear all stalls button handler inside modal
    $('#modalClearAllBtn').on('click', function () {
        if (!currentModalRegId) return;
        if (confirm('Are you sure you want to remove ALL stall allotments for this organization?')) {
            const clearUrl = "{{ url('/dashboard/disability-day') }}/" + currentModalRegId + "/allot-stall";
            $.ajax({
                url: clearUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    clear_all: 1
                },
                success: function (res) {
                    const modalEl = document.getElementById('allotStallModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    if (table) table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Allotments Cleared',
                        text: res.message || 'All stalls unassigned successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to clear stall allotments.'
                    });
                }
            });
        }
    });
});

function openAllotModal(id, regNo, currentStallsStr, orgName) {
    currentModalRegId = id;
    $('#modalRegNo').text(regNo);
    $('#modalOrgName').text(orgName || '—');
    const allotUrl = "{{ url('/dashboard/disability-day') }}/" + id + "/allot-stall";
    $('#allotStallForm').attr('action', allotUrl);

    // Reset UI state
    $('#modalLoadingSpinner').removeClass('d-none');
    $('#modalStallFieldContainer').addClass('d-none');
    $('#modalCapacityAlert').addClass('d-none');
    $('#modalSubmitBtn').prop('disabled', true);

    const modalEl = document.getElementById('allotStallModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }
    modal.show();

    // Fetch dynamic available stalls for this registration
    const availableUrl = "{{ url('/dashboard/disability-day') }}/" + id + "/available-stalls";
    $.ajax({
        url: availableUrl,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $('#modalLoadingSpinner').addClass('d-none');
            $('#modalStallFieldContainer').removeClass('d-none');

            const $select = $('#modalAllottedStall');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.empty();

            const currentStalls = (data.current_stalls || []).map(Number);
            const availableStalls = (data.available_stalls || []).map(Number);
            const combined = Array.from(new Set([...currentStalls, ...availableStalls])).sort((a, b) => a - b);

            combined.forEach(function (num) {
                const isSelected = currentStalls.includes(num);
                const label = 'Stall #' + num + (isSelected ? ' (Currently Assigned)' : '');
                $select.append(new Option(label, num, false, isSelected));
            });

            // Initialize Select2 with multi-select support
            $select.select2({
                dropdownParent: $('#allotStallModal'),
                width: '100%',
                placeholder: '-- Select One or More Stalls (1 - 100) --',
                allowClear: true,
                closeOnSelect: false
            });

            // Capacity indicator
            if (availableStalls.length === 0 && currentStalls.length === 0) {
                $('#modalCapacityAlert').removeClass('d-none');
                $select.prop('disabled', true);
                $('#modalSubmitBtn').prop('disabled', true);
                $('#modalAvailabilityCount').text('0 Available').removeClass('bg-info text-dark').addClass('bg-danger text-white');
            } else {
                $('#modalCapacityAlert').addClass('d-none');
                $select.prop('disabled', false);
                $('#modalSubmitBtn').prop('disabled', false);
                $('#modalAvailabilityCount').text(availableStalls.length + ' Available (1-100)').removeClass('bg-danger text-white').addClass('bg-info text-dark');
            }

            if (currentStalls.length > 0) {
                const badgeList = currentStalls.map(s => '<span class="badge bg-success font-monospace me-1">Stall #' + s + '</span>').join(' ');
                $('#modalCurrentStatus').html('<div class="mb-1 text-dark fw-semibold"><i class="fas fa-check-circle text-success me-1"></i> Current Allotments (' + currentStalls.length + '):</div>' + badgeList);
            } else {
                $('#modalCurrentStatus').html('<i class="fas fa-info-circle text-muted me-1"></i> Currently not allotted any stall.');
            }
        },
        error: function () {
            $('#modalLoadingSpinner').addClass('d-none');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load stall availability. Please try again.'
            });
        }
    });
}

// Clean up Select2 when modal closes
$('#allotStallModal').on('hidden.bs.modal', function () {
    const $select = $('#modalAllottedStall');
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }
    $select.empty();
    currentModalRegId = null;
});

// Handle Allotment Form AJAX Submission with SweetAlert2
$('#allotStallForm').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);
    const $btn = $('#modalSubmitBtn');
    const originalBtnHtml = $btn.html();
    const actionUrl = $form.attr('action');

    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...');

    $.ajax({
        url: actionUrl,
        type: 'POST',
        data: $form.serialize(),
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (res) {
            $btn.prop('disabled', false).html(originalBtnHtml);
            const modalEl = document.getElementById('allotStallModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            } else {
                $('#allotStallModal').modal('hide');
            }

            if (table) {
                table.ajax.reload(null, false);
            }

            Swal.fire({
                icon: 'success',
                title: 'Allotment Updated',
                text: res.message || 'Stall allotment updated successfully.',
                timer: 2500,
                showConfirmButton: false
            });
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(originalBtnHtml);
            let errorMsg = 'Failed to update stall allotment.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const firstErr = Object.values(xhr.responseJSON.errors)[0];
                errorMsg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
            }

            Swal.fire({
                icon: 'error',
                title: 'Allotment Conflict / Error',
                text: errorMsg
            });
        }
    });
});

// Function to send stall allotment confirmation email
function sendAllotmentEmail(id, regNo, email, hasStall) {
    if (!hasStall) {
        Swal.fire({
            icon: 'warning',
            title: 'Stall Not Allotted',
            text: 'Cannot send allotment email: No stall has been allotted to ' + regNo + ' yet. Please assign at least one stall first.',
            confirmButtonColor: '#087e8b'
        });
        return;
    }

    if (isSendingAllotmentEmail) {
        return;
    }

    Swal.fire({
        title: 'Send Allotment Email?',
        html: `Are you sure you want to send the official Stall Allotment Confirmation email to <strong>${email}</strong> for registration <code>${regNo}</code>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#087e8b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Yes, Send Email',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            isSendingAllotmentEmail = true;

            Swal.fire({
                title: 'Sending Email...',
                text: 'Please wait while the allotment confirmation email is being dispatched.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('/dashboard/disability-day') }}/" + id + "/send-allotment-email",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (res) {
                    isSendingAllotmentEmail = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Sent',
                        text: res.message || 'Allotment email dispatched successfully.',
                        confirmButtonColor: '#087e8b'
                    });
                },
                error: function (xhr) {
                    isSendingAllotmentEmail = false;
                    let msg = 'Failed to dispatch allotment email.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Delivery Failed',
                        text: msg,
                        confirmButtonColor: '#087e8b'
                    });
                }
            });
        }
    });
}

function confirmDelete(id, regNo) {
    if (confirm('Are you sure you want to delete registration ' + regNo + '? This action will soft-delete the record.')) {
        const form = document.getElementById('deleteForm');
        form.action = "{{ url('/dashboard/disability-day') }}/" + id + "/delete";
        form.submit();
    }
}
</script>
@endsection

