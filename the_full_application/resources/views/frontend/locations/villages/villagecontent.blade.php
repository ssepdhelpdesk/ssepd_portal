<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<!-- row -->
<div class="col-md-4">
   <div class="form-group">
      <label class="form-label">State<span class="itsrequired"> *</span></label>
      <select class="select2 form-control form-select" id="state-dropdown" name="state" style="width: 100%; height:36px;">
         <option value="">-- Select State --</option>
         @foreach (\App\Models\State::all() as $data)
         <option value="{{$data->state_id}}">
            {{$data->state_name}}
         </option>
         @endforeach
      </select>
      @error('state')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-4">
   <div class="form-group">
      <label class="form-label">District<span class="itsrequired"> *</span></label>
      <select class="select2 form-control form-select" id="district-dropdown" name="district" style="width: 100%; height:36px;">
      </select>
      @error('district')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-4">
   <div class="form-group">
      <label class="form-label">Block<span class="itsrequired"> *</span></label>
      <select class="select2 form-control form-select" id="block-dropdown" name="block" style="width: 100%; height:36px;">
      </select>
      @error('block')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-4">
   <div class="form-group">
      <label class="form-label">Grampanchayat<span class="itsrequired"> *</span></label>
      <select class="select2 form-control form-select" id="grampanchayat-dropdown" name="grampanchayat" style="width: 100%; height:36px;">
      </select>
      @error('grampanchayat')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-4">
   <div class="form-group">
      <label class="form-label">Village<span class="itsrequired"> *</span></label>
      <select class="select2 form-control form-select" id="village-dropdown" name="village" style="width: 100%; height:36px;">
      </select>
      @error('village')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-4">
   <div class="form-group">
      <label class="form-label">Pin<span class="itsrequired"> *</span></label>
      <input type="text" id="pin" name="pin" maxlength="6" minlength="6" class="form-control" placeholder="Pin">
      @error('pin')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<!-- row -->
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->