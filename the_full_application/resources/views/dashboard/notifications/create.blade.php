@section('title') 
SSEPD || Notification Create
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .readonly-input {
      pointer-events: none;
      background-color: #f8f9fa;
      cursor: default;
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
               <div id="alert-container"></div>
               <div class="col-sm-12 col-xs-12">
                  <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ssepdnotification.store')}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                     @csrf
                     @method('post')
                     <div class="form-body">
                        <h5 class="card-title">Notification Details</h5>
                        <hr>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group" id="title_div">
                                 <label class="form-label">Notification Name<span class="itsrequired"> *</span></label>
                                 <input type="text" id="title" name="title" value="{{old('title')}}" class="form-control" placeholder="Notification Name">
                                 <div id="title_error"></div>
                                 @error('title')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="start_date_div">
                                 <label class="form-label">Start Date<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="start_date" name="start_date" value="{{old('start_date')}}">
                                 <div id="start_date_error"></div>
                                 @error('start_date')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="start_time_div">
                                 <label class="form-label">Start Time<span class="itsrequired"> *</span></label>
                                 <input type="time" id="start_time" name="start_time" value="{{old('start_time')}}" class="form-control" placeholder="Time">
                                 <div id="start_time_error"></div>
                                 @error('start_time')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="end_date_div">
                                 <label class="form-label">End Date<span class="itsrequired"> *</span></label>
                                 <input type="date" class="form-control" id="end_date" name="end_date" value="{{old('end_date')}}">
                                 <div id="end_date_error"></div>
                                 @error('end_date')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="end_time_div">
                                 <label class="form-label">End Time<span class="itsrequired"> *</span></label>
                                 <input type="time" id="end_time" name="end_time" value="{{old('end_time')}}" class="form-control" placeholder="Time">
                                 <div id="end_time_error"></div>
                                 @error('end_time')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="type_div">
                                 <label class="form-label">Notification Type<span class="itsrequired"> *</span></label>
                                 <input type="text" id="type" name="type" value="{{old('type')}}" class="form-control" placeholder="Notification Type">
                                 <div id="type_error"></div>
                                 @error('type')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group" id="priority_div">
                                 <label for="priority" class="form-label">Priority</label>
                                 <select class="form-control" name="priority" id="priority" required>
                                    <option value="">--Select--</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                 </select>
                                 <div class="invalid-feedback">Please select an action (New or Existing).</div>
                                 @error('priority')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-md-12">
                              <div class="form-group" id="message_div">
                                 <label class="form-label">Message<span class="itsrequired"> *</span></label>
                                 <textarea id="message" name="message" class="form-control" rows="3"></textarea>
                                 <div id="message_error"></div>
                                 @error('message')
                                 <label class="error">{{ $message }}</label>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <!--/row-->
                        <div class="row" id="dynamic-content"></div>
                     </div>
                     <div class="form-actions">
                        <button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
                     </div>
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
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script>
   document.addEventListener('DOMContentLoaded', () => {
     const form = document.forms['vform'];
     const submitButton = document.getElementById('submitButton');

     form.addEventListener('submit', (e) => {
       document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
       document.querySelectorAll('.text-danger').forEach(el => el.remove());

       let isValid = true;
       let firstInvalid = null;

       const requiredFields = [
         { id: 'title', name: 'Notification Name' },
         { id: 'start_date', name: 'Start Date' },
         { id: 'start_time', name: 'Start Time' },
         { id: 'end_date', name: 'End Date' },
         { id: 'end_time', name: 'End Time' },
         { id: 'type', name: 'Notification Type' },
         { id: 'priority', name: 'Priority' },
         { id: 'message', name: 'Message' },
      ];

      requiredFields.forEach(field => {
         const el = document.getElementById(field.id);
         const container = document.getElementById(field.id + '_div');
         if (!el || !el.value.trim()) {
           isValid = false;
           el.classList.add('is-invalid');
           container.insertAdjacentHTML('beforeend', `<small class="text-danger">${field.name} is required.</small>`);
           if (!firstInvalid) firstInvalid = el;
        }
     });

      const startDate = document.getElementById('start_date').value;
      const startTime = document.getElementById('start_time').value;
      const endDate = document.getElementById('end_date').value;
      const endTime = document.getElementById('end_time').value;

      if (startDate && startTime && endDate && endTime) {
         const start = new Date(`${startDate}T${startTime}`);
         const end = new Date(`${endDate}T${endTime}`);
         if (end <= start) {
           isValid = false;
           const endDateDiv = document.getElementById('end_date_div');
           document.getElementById('end_date').classList.add('is-invalid');
           document.getElementById('end_time').classList.add('is-invalid');
           endDateDiv.insertAdjacentHTML('beforeend', `<small class="text-danger">End date/time must be after start date/time.</small>`);
           if (!firstInvalid) firstInvalid = document.getElementById('end_date');
        }
     }

     if (!isValid) {
      e.preventDefault();
      if (firstInvalid) firstInvalid.focus();
      return false;
   }

   submitButton.disabled = true;
   submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Please wait...';
});
  });
</script>
@endsection