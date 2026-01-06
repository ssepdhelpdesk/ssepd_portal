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
        <label>Name of the Beneficiary</label>
        <input type="text" name="name_of_the_beneficiary" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Aadhaar Number</label>
        <input type="text" name="aadhaar_no" maxlength="12" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary" id="verifyBtn">
        Verify Aadhaar
    </button>
</form>

<div id="responseBox" class="mt-4" style="display:none;"></div>


        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$('#aadharVerificationForm').on('submit', function (e) {
    e.preventDefault();

    $('#verifyBtn').prop('disabled', true).text('Verifying...');
    $('#responseBox').hide().removeClass().html('');

    $.ajax({
        url: "{{ route('admin.disability3500data.disability_aadhar_verification_process') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function (res) {
            $('#responseBox')
                .addClass('alert alert-info')
                .html('<strong>API Response:</strong><br>' + res.data)
                .show();
        },
        error: function (xhr) {
            let msg = xhr.responseJSON?.error ?? 'Request failed';
            $('#responseBox')
                .addClass('alert alert-danger')
                .html(msg)
                .show();
        },
        complete: function () {
            $('#verifyBtn').prop('disabled', false).text('Verify Aadhaar');
        }
    });
});
</script>
@endsection
