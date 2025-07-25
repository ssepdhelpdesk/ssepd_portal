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
<h3 class="box-title">Postal Address</h3>
<hr class="m-t-0 m-b-10">
<div class="col-md-2">
   <div class="form-group">
      <label class="form-label">At<span class="itsrequired"> *</span></label>
      <input type="text" id="ngo_postal_address_at" name="ngo_postal_address_at" class="form-control" placeholder="At">
      @error('ngo_postal_address_at')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-2">
   <div class="form-group">
      <label class="form-label">Post<span class="itsrequired"> *</span></label>
      <input type="text" id="ngo_postal_address_post" name="ngo_postal_address_post" class="form-control" placeholder="Post">
      @error('ngo_postal_address_post')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-2">
   <div class="form-group">
      <label class="form-label">Via<span class="itsrequired"> *</span></label>
      <input type="text" id="ngo_postal_address_via" name="ngo_postal_address_via" class="form-control" placeholder="Via">
      @error('ngo_postal_address_via')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-2">
   <div class="form-group">
      <label class="form-label">Police Station<span class="itsrequired"> *</span></label>
      <input type="text" id="ngo_postal_address_ps" name="ngo_postal_address_ps" class="form-control" placeholder="Police Station">
      @error('ngo_postal_address_ps')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-2">
   <div class="form-group">
      <label class="form-label">District<span class="itsrequired"> *</span></label>
      <input type="text" id="ngo_postal_address_district" name="ngo_postal_address_district" class="form-control" placeholder="District">
      @error('ngo_postal_address_district')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<div class="col-md-2">
   <div class="form-group">
      <label class="form-label">Pin Code<span class="itsrequired"> *</span></label>
      <input type="text" id="ngo_postal_address_pin" name="ngo_postal_address_pin" maxlength="6" minlength="6" class="form-control" placeholder="Pin Code">
      @error('ngo_postal_address_pin')
      <label class="error">{{ $message }}</label>
      @enderror
   </div>
</div>
<!-- row -->
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->