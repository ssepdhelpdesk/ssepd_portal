@section('title') 
EP Pensiners || OldAge Bulk Aadhaar Verification || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
               @if (count($errors) > 0)
               <div class="alert alert-danger">
                  <strong>Whoops!</strong> There were some problems with your input.<br><br>
                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <div class="alert alert-info">
                 <strong>Pending Aadhaar Verification:</strong> {{ $pendingCount }}
              </div>
              <div id="alert-container"></div>
              <div class="col-sm-12 col-xs-12">
               <form id="bulkAadharForm" class="from-prevent-multiple-submits">
                 @csrf
                 <div class="row mb-3">
                   <div class="col-md-4">
                     <label class="form-label">Records to process</label>
                     <input type="number" name="limit" class="form-control" value="10000" min="1" max="50000" required>
                  </div>
               </div>

               <button type="submit" class="btn btn-primary" id="startBtn">
                Start Bulk Aadhaar Verification
             </button>
          </form>

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
     $('#bulkAadharForm').on('submit', function(e) {
       e.preventDefault();

       $('#startBtn').prop('disabled', true).text('Processing...');
       $('#resultBox').hide().html('');

       $.ajax({
         url: "{{ route('admin.oldage3500data.oldage_bulk_aadhar_verification_process') }}",
         type: "POST",
         data: $(this).serialize(),
         success: function(res) {
           $('#resultBox')
           .removeClass()
           .addClass('alert alert-success')
           .html('<strong>' + res.message + '</strong><br>Processed Records: ' + res.processed_records)
           .show();
        },
        error: function(xhr) {
           let msg = 'Something went wrong';
           if (xhr.responseJSON && xhr.responseJSON.message) {
             msg = xhr.responseJSON.message;
          }
          $('#resultBox').removeClass().addClass('alert alert-danger').html(msg).show();
       },
       complete: function() {
        $('#startBtn').prop('disabled', false).text('Start Bulk Aadhaar Verification');
     }
  });

    });
  });
</script>
@endsection