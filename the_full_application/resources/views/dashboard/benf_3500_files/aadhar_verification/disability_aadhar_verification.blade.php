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
            <h4>Test Aadhaar Verification API</h4>
        </div>

        <div class="card-body">
            <form id="aadharVerificationForm">
    @csrf

    <div class="mb-3">
        <label class="form-label">Name of the Beneficiary</label>
        <input type="text" name="name_of_the_beneficiary" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Aadhaar Number</label>
        <input type="text" name="aadhaar_no" class="form-control" maxlength="12" required>
    </div>

    <button type="submit" class="btn btn-primary" id="verifyBtn">
        Verify Aadhaar
    </button>
</form>

<div id="responseBox" class="mt-4" style="display:none;"></div>

            @if(session('response'))
                <div class="alert alert-info mt-4">
                    <strong>API Response:</strong><br>
                    {{ session('response') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mt-4">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function () {

    $('#aadharVerificationForm').on('submit', function (e) {
        e.preventDefault();

        $('#verifyBtn').prop('disabled', true).text('Verifying...');
        $('#responseBox').hide().html('');

        $.ajax({
            url: "{{ route('admin.disability3500data.disability_aadhar_verification_process') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (response) {
                $('#responseBox')
                    .removeClass()
                    .addClass('alert alert-info')
                    .html('<strong>API Response:</strong><br>' + response.data)
                    .show();
            },
            error: function (xhr) {
                let msg = 'Something went wrong';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }

                $('#responseBox')
                    .removeClass()
                    .addClass('alert alert-danger')
                    .html(msg)
                    .show();
            },
            complete: function () {
                $('#verifyBtn').prop('disabled', false).text('Verify Aadhaar');
            }
        });
    });

});
</script>
@endsection
