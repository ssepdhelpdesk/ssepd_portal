@section('title') 
EP Pensiners || Abstract (Age 80+ & ≥80% Disability) || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
    <div class="card shadow">
        <div class="card-header">
            <h4>Bulk Aadhaar Verification (Disability 3500)</h4>
        </div>

        <div class="card-body">

            <div class="alert alert-info">
                <strong>Pending Aadhaar Verification:</strong> {{ $pendingCount }}
            </div>

            <form id="bulkAadharForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Records per Batch</label>
                        <input type="number"
                               name="limit"
                               class="form-control"
                               value="100"
                               min="1"
                               max="500"
                               required>
                        <small class="text-muted">
                            Recommended: 50–100
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="startBtn">
                    Start Bulk Aadhaar Verification
                </button>
            </form>

            <div id="resultBox" class="mt-4" style="display:none;"></div>

        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(function () {

    $('#bulkAadharForm').on('submit', function (e) {
        e.preventDefault();

        $('#startBtn')
            .prop('disabled', true)
            .text('Queuing Verification...');

        $('#resultBox').hide().html('');

        $.ajax({
            url: "{{ route('admin.disability3500data.disability_bulk_aadhar_verification_process') }}",
            type: "POST",
            data: $(this).serialize(),

            success: function (res) {
                $('#resultBox')
                    .removeClass()
                    .addClass('alert alert-success')
                    .html(
                        '<strong>' + res.message + '</strong><br>' +
                        'Queued Records: ' + res.queued_records
                    )
                    .show();
            },

            error: function (xhr) {
                let msg = 'Something went wrong';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                $('#resultBox')
                    .removeClass()
                    .addClass('alert alert-danger')
                    .html(msg)
                    .show();
            },

            complete: function () {
                $('#startBtn')
                    .prop('disabled', false)
                    .text('Start Bulk Aadhaar Verification');
            }
        });
    });

});
</script>
@endsection
