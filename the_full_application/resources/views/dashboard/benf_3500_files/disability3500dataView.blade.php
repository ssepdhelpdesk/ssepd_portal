@section('title') 
EP Pension || Index - Disability
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
                                    <th style="white-space: normal; word-wrap: break-word;">Sl No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Scheme</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Beneficiary Name</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Care of</th>
                                    <th style="white-space: normal; word-wrap: break-word;">DOB</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Age</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Gender</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Aadhaar Verification Status</th>
                                    <th style="white-space: normal; word-wrap: break-word;">UDID No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Category</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Disability %age</th>
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
                                    <th style="white-space: normal; word-wrap: break-word;">Sl No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Scheme</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Beneficiary Name</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Care of</th>
                                    <th style="white-space: normal; word-wrap: break-word;">DOB</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Age</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Gender</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Aadhaar Verification Status</th>
                                    <th style="white-space: normal; word-wrap: break-word;">UDID No</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Category</th>
                                    <th style="white-space: normal; word-wrap: break-word;">Disability %age</th>
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

                        <!-- Modal -->
                        <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius:15px; overflow:hidden; box-shadow:0px 8px 25px rgba(0,0,0,0.25);">

                                    <div class="modal-header" style="background:linear-gradient(90deg,#20c997,#0b5ed7); color:white;">
                                        <h5 class="modal-title" id="actionModalLabel" style="font-weight:600;">
                                            <i class="fas fa-tasks"></i> Select Action
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body" style="background:#f8f9fa; padding:20px;">

                                        <div style="background:#fff3cd; border:1px solid #ffeeba; padding:12px; border-radius:10px; margin-bottom:15px;">
                                            <p style="margin:0; color:#856404; font-weight:600; font-size:14px;">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Please Note: To mark <span style="color:red;">Ineligible</span>, first Verify the Aadhaar.
                                            </p>
                                        </div>

                                        <input type="hidden" id="recordId" value="">
                                        <input type="hidden" id="verifiedAadharStatus" value="">

                                        <div class="form-group mb-3">
                                            <label for="statusSelect" class="form-label" style="font-weight:600;">Choose Status:</label>
                                            <select class="form-control" id="statusSelect" name="status"
                                            style="border-radius:10px; padding:10px;" required>
                                            <option value="Inactive">Discontinue</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="discontinue_date" class="form-label" style="font-weight:600;">Date of Discontinue:</label>
                                        <input type="date" name="discontinue_date" id="discontinue_date"
                                        class="form-control"
                                        style="border-radius:10px; padding:10px;"
                                        max="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="discontinued_reason" class="form-label" style="font-weight:600;">Discontinue Reason:</label>
                                        <select class="form-control" id="discontinued_reason" name="discontinued_reason"
                                        style="border-radius:10px; padding:10px;" required>
                                        <option value=""> -Select- </option>
                                        <option value="Death">Death</option>
                                        <option value="Ineligible" id="ineligibleOption" style="display:none;">Ineligible</option>
                                    </select>
                                </div>

                            </div>

                            <div class="modal-footer" style="background:#ffffff; border-top:1px solid #dee2e6;">
                                <button type="button" class="btn btn-secondary"
                                style="border-radius:10px; padding:8px 18px;"
                                data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="button" class="btn btn-success" id="saveStatus"
                            style="border-radius:10px; padding:8px 18px; font-weight:600;">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>

                </div>
            </div>
        </div>

                        <!-- Modal End -->

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
        $('#oldAgeTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            ajax: "{{ route('admin.disability3500data.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'scheme_name', name: 'scheme_name' },
                { data: 'name_of_the_beneficiary', name: 'name_of_the_beneficiary' },
                { data: 'father_or_husband_name', name: 'father_or_husband_name' },
                { data: 'date_of_birth', name: 'date_of_birth' },
                { data: 'age', name: 'age' },
                { data: 'gender', name: 'gender' },
                { data: 'aadhaar_verification_status', orderable: true, searchable: true },
                { data: 'udid_no', name: 'udid_no' },
                { data: 'disability_category', name: 'disability_category' },
                { data: 'disability_percentage', name: 'disability_percentage' },
                { data: 'district', name: 'district' },
                { data: 'block_or_ulb', name: 'block_or_ulb' },
                { data: 'gp_or_ward', name: 'gp_or_ward' },
                { data: 'village', name: 'village' },
                { data: 'nsap_sanction_order_no', name: 'nsap_sanction_order_no' },
                { data: 'sub_collector_sanction_order_no', name: 'sub_collector_sanction_order_no' },
                { data: 'discontinued_reason', name: 'discontinued_reason' },
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


<script>
    $(document).ready(function() {

        /*Modal open event*/
        $('#actionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var recordId = button.data('id');

            /*Get verified status from controller data attribute*/
            var verified = button.data('verified');

            var modal = $(this);
            modal.find('#recordId').val(recordId);

            /*Reset discontinued reason*/
            $('#discontinued_reason').val('');

            /*Show/Hide Ineligible option based on verified_aadhar*/
            if (verified == 1) {
                $('#ineligibleOption').show();
            } else {
                $('#ineligibleOption').hide();
            }
        });

        /*AJAX Save Status*/
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
@endsection
