@section('title') 
EP Pension || Index - Disability ReInitiate Application
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
                        <table id="oldAgeTable" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th style="white-space: normal; word-wrap: break-word;">Sl No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Scheme</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Beneficiary Name</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Care of</th>
                                    <th style="white-space: normal; word-wrap: break-word;">DOB</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Age</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Gender</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Aadhaar Verification Status</th>
                                    <th style="white-space: normal; word-wrap: break-word;">District</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Block/ULB</th>
                                    <th style="white-space: normal; word-wrap: break-word;">GP/Ward</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Village</th>
                                    <th style="white-space: normal; word-wrap: break-word;">NSAP Sanction Order No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Sub-Collector Sanction Order No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Status</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th style="white-space: normal; word-wrap: break-word;">Sl No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Scheme</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Beneficiary Name</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Care of</th>
                                    <th style="white-space: normal; word-wrap: break-word;">DOB</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Age</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Gender</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Aadhaar Verification Status</th>
                                    <th style="white-space: normal; word-wrap: break-word;">District</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Block/ULB</th>
                                    <th style="white-space: normal; word-wrap: break-word;">GP/Ward</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Village</th>
                                    <th style="white-space: normal; word-wrap: break-word;">NSAP Sanction Order No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Sub-Collector Sanction Order No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Status</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Action</th>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="mb-3 text-end">
                            <button type="button" id="btnReInitiate" class="btn btn-success"> ReInstead the Marked Applications!</button>
                        </div>
                        <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="actionModalLabel">Select Action</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="recordId" value="">
                                        <div class="form-group mb-3">
                                            <label for="statusSelect" class="form-label">Choose Status:</label>
                                            <select class="form-control" id="statusSelect" name="status" required>
                                                <option value="Inactive">Discontinue</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="discontinue_date" class="form-label">Date of Discontinue:</label>
                                            <input type="date" name="discontinue_date" id="discontinue_date" class="form-control" max="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="discontinued_reason" class="form-label">Discontinue Reason:</label>
                                            <select class="form-control" id="discontinued_reason" name="discontinued_reason" required>
                                                <option value=""> -Select- </option>
                                                <option value="Death">Death</option>
                                                <option value="Ineligible">Ineligible</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-success" id="saveStatus">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="reInitiateModal" tabindex="-1">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Re-Instead Beneficiaries</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label class="form-label">Upload Approval PDF <span class="text-danger">*</span></label>
                                            <input type="file" name="reinitiated_sub_col_files" id="reinitiated_sub_col_files" class="form-control" accept="application/pdf">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Sub-Col Signature Date <span class="text-danger">*</span></label>
                                            <input type="date" name="sub_col_signature_date" id="sub_col_signature_date" class="form-control" max="{{ date('Y-m-d') }}" placeholder="Sub COl Signature Date">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">PDF Sanction Order No <span class="text-danger">*</span></label>
                                            <input type="text" name="sub_collector_sanction_order_no" id="sub_collector_sanction_order_no" class="form-control" placeholder="Sanction Order No">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Remarks / Order Reference <span class="text-danger">*</span></label>
                                            <input type="text" name="reinitiate_remark" id="reinitiate_remark" class="form-control" placeholder="Enter remarks">
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success" id="confirmReInitiate">
                                            Confirm ReInstead
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 

@section('script')
<script>
    let selectedIds = new Set();
</script>
<script>
    $(function () {

        let table = $('#oldAgeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.disability3500data.disability_ineligible_to_eligible_reinstead') }}",

            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'scheme_name' },
                { data: 'name_of_the_beneficiary' },
                { data: 'father_or_husband_name' },
                { data: 'date_of_birth' },
                { data: 'age' },
                { data: 'gender' },
                { data: 'aadhaar_verification_status' },
                { data: 'district' },
                { data: 'block_or_ulb' },
                { data: 'gp_or_ward' },
                { data: 'village' },
                { data: 'nsap_sanction_order_no' },
                { data: 'sub_collector_sanction_order_no' },
                { data: 'discontinued_reason' },
                { data: 'action', orderable: false, searchable: false }
            ],

            drawCallback: function () {

                $('.row-checkbox').each(function () {
                    let id = $(this).val();
                    if (selectedIds.has(id)) {
                        $(this).prop('checked', true);
                    }
                });

                let total = $('.row-checkbox').length;
                let checked = $('.row-checkbox:checked').length;
                $('#selectAll').prop('checked', total > 0 && total === checked);
            },

            dom: 'Blfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
        });

        $('.buttons-copy, .buttons-csv, .buttons-excel, .buttons-pdf, .buttons-print')
        .addClass('btn btn-primary me-1');
    });
</script>
<script>
    $(document).on('change', '.row-checkbox', function () {

        let id = $(this).val();

        if (this.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
            $('#selectAll').prop('checked', false);
        }
    });
</script>
<script>
    $(document).on('change', '#selectAll', function () {

        let checked = this.checked;

        $('.row-checkbox').each(function () {
            let id = $(this).val();
            $(this).prop('checked', checked);

            if (checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
        });
    });
</script>
<script>
    function getSelectedIds() {
        return Array.from(selectedIds);
    }
</script>
<script>
    $(document).ready(function() {
        $('#actionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var recordId = button.data('id');
            var modal = $(this);
            modal.find('#recordId').val(recordId);
        });

        $('#saveStatus').on('click', function() {
            var recordId = $('#recordId').val();
            var status = $('#statusSelect').val();
            var discontinue_date = $('#discontinue_date').val();
            var discontinued_reason = $('#discontinued_reason').val();

            if(!status || !discontinue_date || !discontinued_reason) {
                alert('Please fill all required fields.');
                return;
            }

            $.ajax({
                url: "{{ route('admin.disability3500data.update_status') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: recordId,
                    status: status,
                    discontinue_date: discontinue_date,
                    discontinued_reason: discontinued_reason
                },
                beforeSend: function() {
                    $('#saveStatus').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if(response.success) {
                        $('#actionModal').modal('hide');
                        $('#oldAgeTable').DataTable().ajax.reload(null, false);
                        alert(response.message);
                    } else {
                        alert(response.message || 'Something went wrong.');
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                },
                complete: function() {
                    $('#saveStatus').prop('disabled', false).text('Save');
                }
            });
        });

    });
</script>
<script>
    $(document).on('click', '#btnReInitiate', function () {

    let ids = getSelectedIds();

    if (ids.length === 0) {
        alert('Please select at least one beneficiary.');
        return;
    }

    $('#reInitiateModal').modal('show');
});
</script>
<script>
    $(document).on('click', '#confirmReInitiate', function () {

    let ids = getSelectedIds();
    let pdf = $('#reinitiated_sub_col_files')[0].files[0];
    let sub_col_signature_date = $('#sub_col_signature_date').val().trim();
    let sub_collector_sanction_order_no = $('#sub_collector_sanction_order_no').val().trim();
    let remark = $('#reinitiate_remark').val().trim();

    if (!pdf) {
        alert('Please upload PDF document.');
        return;
    }

    if (!sub_col_signature_date) {
        alert('Please Provide Sub Collector Signature Date.');
        return;
    }

    if (!sub_collector_sanction_order_no) {
        alert('Please Provide Sub Collector Sanction Order No.');
        return;
    }

    if (!remark) {
        alert('Please enter remarks.');
        return;
    }

    let formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('pdf', pdf);
    formData.append('sub_col_signature_date', sub_col_signature_date);
    formData.append('sub_collector_sanction_order_no', sub_collector_sanction_order_no);
    formData.append('remark', remark);

    ids.forEach(id => {
        formData.append('ids[]', id);
    });

    $.ajax({
        url: "{{ route('admin.disability3500data.disability_ineligible_to_eligible_reinstead_process') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,

        beforeSend: function () {
            $('#confirmReInitiate').prop('disabled', true).text('Processing...');
        },

        success: function (response) {

            if (response.success) {

                alert(response.message);

                $('#reInitiateModal').modal('hide');
                $('#reinitiated_sub_col_files').val('');
                $('#sub_col_signature_date').val('');
                $('#sub_collector_sanction_order_no').val('');
                $('#reinitiate_remark').val('');

                selectedIds.clear();
                $('#selectAll').prop('checked', false);

                $('#oldAgeTable').DataTable().ajax.reload(null, false);

            } else {
                alert(response.message || 'Re-initiation failed.');
            }
        },

        error: function (xhr) {
            alert('Server Error: ' + xhr.responseText);
        },

        complete: function () {
            $('#confirmReInitiate').prop('disabled', false).text('Confirm ReInitiate');
        }
    });
});
</script>
<script>
document.getElementById('sub_col_signature_date').addEventListener('change', function () {
    const selectedDate = new Date(this.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate > today) {
        alert('Sub Collector Signature Date cannot be greater than today.');
        this.value = '';
    }
});
</script>
@endsection
