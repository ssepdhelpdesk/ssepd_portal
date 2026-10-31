@section('title') 
Add Institute || SSEPD Institute Master
@endsection 
@extends('dashboard.layouts.main')
@section('content')
<div class="container-fluid">
   <!-- ============================================================== -->
   <!-- Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->
   <div class="row page-titles">
      <div class="col-md-7 align-self-center">
         <div class="d-flex align-items-center">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
               <li class="breadcrumb-item"><a href="javascript:void(0)">Field Visit</a></li>
               <li class="breadcrumb-item"><a href="{{ route('admin.field_visit_institutes.index') }}">Institute Master</a></li>
               <li class="breadcrumb-item active">Add Institute</li>
            </ol>
         </div>
      </div>
      <div class="col-md-5 align-self-center text-end">
         <a href="{{ route('admin.field_visit_institutes.index') }}" class="btn waves-effect waves-light btn-rounded text-white btn-xs btn-info">
            <i class="fa fa-arrow-left me-1"></i> Back to Listing
         </a>
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->

   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm border-top border-primary border-3">
            <div class="card-body p-4">
               <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                  <div>
                     <h4 class="card-title mb-1 text-primary"><i class="fas fa-plus-circle me-2"></i> Register New Institute</h4>
                     <p class="text-muted small mb-0">Record a new welfare institution, school, or rehabilitation center into the SSEPD Master catalog.</p>
                  </div>
                  <div>
                     <span class="badge bg-light text-dark border p-2">
                        <i class="fas fa-info-circle text-primary me-1"></i> Mandatory fields marked with <span class="text-danger">*</span>
                     </span>
                  </div>
               </div>

               @include('dashboard.component.message')
               @if (count($errors) > 0)
               <div class="alert alert-danger">
                  <strong>Whoops!</strong> There were some problems with your input.<br><br>
                  <ul class="mb-0">
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <div id="alert-container"></div>

               <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.field_visit_institutes.store') }}" name="vform" id="instituteForm" onsubmit="return Validate()">
                  @csrf

                  <!-- Section 1: Basic Institutional Profile -->
                  <div class="bg-light p-3 rounded mb-4 border">
                     <h5 class="fw-bold text-dark mb-3"><i class="fas fa-id-card text-info me-2"></i> Section 1: Basic Institutional Profile</h5>
                     <div class="row g-3">
                        <div class="col-md-3">
                           <div class="form-group" id="institute_code_div">
                              <label for="institute_code" class="form-label fw-semibold">Institute Code <span class="text-danger">*</span></label>
                              <input type="text" 
                                     name="institute_code" 
                                     id="institute_code" 
                                     value="{{ old('institute_code', $suggestedCode) }}" 
                                     class="form-control font-monospace @error('institute_code') is-invalid @enderror" 
                                     placeholder="e.g. INST-0438" 
                                     required>
                              <small class="text-muted">Unique uppercase identifier.</small>
                              <div id="institute_code_error"></div>
                              @error('institute_code')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group" id="institute_name_div">
                              <label for="institute_name" class="form-label fw-semibold">Institute / Facility Name <span class="text-danger">*</span></label>
                              <input type="text" 
                                     name="institute_name" 
                                     id="institute_name" 
                                     value="{{ old('institute_name') }}" 
                                     class="form-control @error('institute_name') is-invalid @enderror" 
                                     placeholder="e.g. Utkal Special School for Deaf & Blind" 
                                     required>
                              <div id="institute_name_error"></div>
                              @error('institute_name')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-md-3">
                           <div class="form-group" id="category_id_div">
                              <label for="category_id" class="form-label fw-semibold">Category / Classification <span class="text-danger">*</span></label>
                              <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                 <option value="">-- Select Category --</option>
                                 @foreach($categories as $catId => $catName)
                                    <option value="{{ $catId }}" {{ old('category_id') == $catId ? 'selected' : '' }}>
                                       {{ $catName }}
                                    </option>
                                 @endforeach
                              </select>
                              <div id="category_id_error"></div>
                              @error('category_id')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group" id="management_name_div">
                              <label for="management_name" class="form-label fw-semibold">Managing NGO / Trust / Society Name</label>
                              <input type="text" 
                                     name="management_name" 
                                     id="management_name" 
                                     value="{{ old('management_name') }}" 
                                     class="form-control @error('management_name') is-invalid @enderror" 
                                     placeholder="e.g. Association for Social Welfare & Education">
                              <small class="text-muted">Name of the governing registered voluntary organization.</small>
                              <div id="management_name_error"></div>
                              @error('management_name')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-md-3">
                           <div class="form-group" id="which_govt_div">
                              <label class="form-label fw-semibold d-block">Government Scope <span class="text-danger">*</span></label>
                              <div class="d-flex gap-3 pt-1">
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="which_govt" id="govt_odisha" value="1" {{ old('which_govt', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="govt_odisha">Govt. of Odisha</label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="which_govt" id="govt_india" value="2" {{ old('which_govt') == '2' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="govt_india">Govt. of India</label>
                                 </div>
                              </div>
                              <div id="which_govt_error"></div>
                              @error('which_govt')
                                 <div class="text-danger small">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-md-3">
                           <div class="form-group" id="is_active_div">
                              <label class="form-label fw-semibold d-block">Operational Status <span class="text-danger">*</span></label>
                              <div class="d-flex gap-3 pt-1">
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="status_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-bold" for="status_active">Active</label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="status_inactive" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-bold" for="status_inactive">Inactive</label>
                                 </div>
                              </div>
                              <div id="is_active_error"></div>
                              @error('is_active')
                                 <div class="text-danger small">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Section 2: Geographic & Location Hierarchy -->
                  <div class="bg-light p-3 rounded mb-4 border">
                     <h5 class="fw-bold text-dark mb-3"><i class="fas fa-map-marked-alt text-success me-2"></i> Section 2: Geographic &amp; Location Hierarchy</h5>
                     <div class="row">
                        <div class="col-md-4">
                           <div class="form-group" id="address_type_div">
                              <label class="form-label fw-semibold">Address Type <span class="text-danger">*</span></label>
                              <div class="d-flex align-items-center pt-1">
                                 <div class="custom-control custom-radio me-4">
                                    <input type="radio" id="block" name="address_type" value="1" class="form-check-input" {{ old('address_type', old('ngo_address_type')) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold ms-1" for="block">Block (Rural)</label>
                                 </div>
                                 <div class="custom-control custom-radio">
                                    <input type="radio" id="ulb" name="address_type" value="2" class="form-check-input" {{ old('address_type', old('ngo_address_type')) == '2' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold ms-1" for="ulb">ULB (Urban)</label>
                                 </div>
                              </div>
                              <input type="hidden" name="ngo_address_type" id="hidden_ngo_address_type" value="{{ old('address_type', old('ngo_address_type')) }}">
                              <input type="hidden" name="village_id" id="hidden_village_id" value="{{ old('village_id', old('village')) }}">
                              <div id="address_type_error"></div>
                              @error('address_type')
                              <label class="error text-danger small">{{ $message }}</label>
                              @enderror
                              @error('ngo_address_type')
                              <label class="error text-danger small">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                     </div>
                     <!-- Dynamic content container injected through AJAX get-address-type-content/{type} -->
                     <div class="row" id="dynamic-content"></div>
                  </div>

                  <!-- Section 3: Site Liaison & Communication -->
                  <div class="bg-light p-3 rounded mb-4 border">
                     <h5 class="fw-bold text-dark mb-3"><i class="fas fa-address-book text-warning me-2"></i> Section 3: Site Liaison & Contact Details</h5>
                     <div class="row g-3">
                        <div class="col-md-4">
                           <div class="form-group" id="contact_person_div">
                              <label for="contact_person" class="form-label fw-semibold">Contact Person / Superintendent</label>
                              <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" class="form-control @error('contact_person') is-invalid @enderror" placeholder="Name of site head or manager">
                              <div id="contact_person_error"></div>
                              @error('contact_person')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-md-4">
                           <div class="form-group" id="contact_number_div">
                              <label for="contact_number" class="form-label fw-semibold">Contact Phone Number</label>
                              <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" class="form-control @error('contact_number') is-invalid @enderror" placeholder="e.g. 9876543210">
                              <div id="contact_number_error"></div>
                              @error('contact_number')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-md-4">
                           <div class="form-group" id="email_div">
                              <label for="email" class="form-label fw-semibold">Official Email Address</label>
                              <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="e.g. institute@domain.org">
                              <div id="email_error"></div>
                              @error('email')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>

                        <div class="col-12">
                           <div class="form-group" id="remarks_div">
                              <label for="remarks" class="form-label fw-semibold">Administrative Remarks / Notes</label>
                              <textarea name="remarks" id="remarks" rows="2" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any special instructions or historical background...">{{ old('remarks') }}</textarea>
                              <div id="remarks_error"></div>
                              @error('remarks')
                                 <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Dynamic Action Buttons (Populated from AJAX response get-address-type-content) -->
                  <div class="form-actions d-flex justify-content-end gap-2 pt-2 border-top">
                     <a href="{{ route('admin.field_visit_institutes.index') }}" class="btn btn-secondary px-4">
                        <i class="fas fa-times me-1"></i> Cancel
                     </a>
                  </div>
               </form>

            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
   function Validate() {
      let isValid = true;

      function showError(id, message) {
         const errorDiv = document.getElementById(id + '_error');
         if (errorDiv) {
            errorDiv.innerHTML = '<label class="error text-danger small">' + message + '</label>';
         }
         isValid = false;
      }

      function clearError(id) {
         const errorDiv = document.getElementById(id + '_error');
         if (errorDiv) {
            errorDiv.innerHTML = '';
         }
      }

      function getValue(id) {
         const el = document.getElementById(id);
         return el ? el.value.trim() : '';
      }

      const textFields = [
         { id: 'institute_code', message: 'Please enter Institute Code.' },
         { id: 'institute_name', message: 'Please enter Institute / Facility Name.' }
      ];

      textFields.forEach(field => {
         const value = getValue(field.id);
         if (!value) {
            showError(field.id, field.message);
         } else {
            clearError(field.id);
         }
      });

      const categoryEl = document.getElementById('category_id');
      if (!categoryEl || !categoryEl.value) {
         showError('category_id', 'Please select Category / Classification.');
      } else {
         clearError('category_id');
      }

      const radios = document.querySelectorAll('input[name="address_type"]');
      let selected = false;
      radios.forEach(radio => {
         if (radio.checked) selected = true;
      });

      if (!selected) {
         showError('address_type', 'Please select Address Type.');
      } else {
         clearError('address_type');
      }

      return isValid;
   }

   document.addEventListener("DOMContentLoaded", function () {
      const radios = document.querySelectorAll('input[name="address_type"]');
      const dynamicContent = document.getElementById('dynamic-content');
      const formActions = document.querySelector('.form-actions');
      const form = document.forms['vform'];
      const codeInput = document.getElementById('institute_code');

      if (codeInput) {
         codeInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
         });
      }

      function initializeDropdowns() {
         $(".select2").select2();
         $('.selectpicker').selectpicker();

         $('#state-dropdown').on('change', function () {
            var idState = this.value;
            $("#district-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-districts')}}",
               type: "POST",
               data: {
                  state_id: idState,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (result) {
                  $('#district-dropdown').html('<option value="">-- Select District --</option>');
                  $.each(result.districts, function (key, value) {
                     $("#district-dropdown").append('<option value="' + value.district_id + '">' + value.district_name + '</option>');
                  });
                  $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                  $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                  $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
                  $('#ward-dropdown').html('<option value="">-- Select Ward --</option>');
                  $('#district-dropdown').trigger('change.select2');
               }
            });
         });

         $('#district-dropdown').on('change', function () {
            var idDistrict = this.value;
            $("#municipality-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-municipality')}}",
               type: "POST",
               data: {
                  district_id: idDistrict,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
                  $.each(res.municipalities, function (key, value) {
                     $("#municipality-dropdown").append('<option value="' + value.municipality_id + '">' + value.municipality_name + '</option>');
                  });
                  $('#ward-dropdown').html('<option value="">-- Select Ward --</option>');
                  $('#municipality-dropdown').trigger('change.select2');
               }
            });
         });

         $('#district-dropdown').on('change', function () {
            var idDistrict = this.value;
            $("#block-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-block')}}",
               type: "POST",
               data: {
                  district_id: idDistrict,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                  $.each(res.blocks, function (key, value) {
                     $("#block-dropdown").append('<option value="' + value.block_id + '">' + value.block_name + '</option>');
                  });
                  $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                  $('#block-dropdown').trigger('change.select2');
               }
            });
         });

         $('#block-dropdown').on('change', function () {
            var idBlock = this.value;
            $("#grampanchayat-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-grampanchayat')}}",
               type: "POST",
               data: {
                  block_id: idBlock,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                  $.each(res.grampanchayats, function (key, value) {
                     $("#grampanchayat-dropdown").append('<option value="' + value.gp_id + '">' + value.gp_name + '</option>');
                  });
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                  $('#grampanchayat-dropdown').trigger('change.select2');
               }
            });
         });

         $('#grampanchayat-dropdown').on('change', function () {
            var idGrampanchayat = this.value;
            $("#village-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-village')}}",
               type: "POST",
               data: {
                  gp_id: idGrampanchayat,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                  $.each(res.villages, function (key, value) {
                     $("#village-dropdown").append('<option value="' + value.village_id + '">' + value.village_name + '</option>');
                  });
                  $('#village-dropdown').trigger('change.select2');
               }
            });
         });

         $('#municipality-dropdown').on('change', function () {
            var idMunicipality = this.value;
            $("#ward-dropdown").html('');
            $.ajax({
               url: "{{url('dashboard/locations/fetch-ward')}}",
               type: "POST",
               data: {
                  municipality_id: idMunicipality,
                  _token: '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function (res) {
                  $('#ward-dropdown').html('<option value="">-- Select Ward --</option>');
                  $.each(res.wards, function (key, value) {
                     $("#ward-dropdown").append('<option value="' + value.ward_code + '">' + value.ward_name + '</option>');
                  });
                  $('#ward-dropdown').trigger('change.select2');
               }
            });
         });
      }

      function loadAddressContent(value) {
         const hiddenNgo = document.getElementById('hidden_ngo_address_type');
         if (hiddenNgo) hiddenNgo.value = value;
         if (value === '2') {
            const hVil = document.getElementById('hidden_village_id');
            if (hVil) hVil.value = '';
         }

         dynamicContent.innerHTML = '<div class="col-12 text-center py-4 text-primary"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0 fw-semibold">Loading geographical hierarchy...</p></div>';

         fetch(`{{ url('dashboard/get-address-type-content') }}/${value}`)
         .then(response => {
            if (!response.ok) {
               throw new Error('Network response was not ok');
            }
            return response.json();
         })
         .then(data => {
            dynamicContent.innerHTML = data.content;
            formActions.innerHTML = data.buttons;

            // Immediately strip out Postal Address UI (not wanted in Section 2)
            const postalHeading = dynamicContent.querySelector('#postal_address_heading');
            if (postalHeading) {
               let nextNode = postalHeading.nextElementSibling;
               while (nextNode) {
                  let toRemove = nextNode;
                  nextNode = nextNode.nextElementSibling;
                  toRemove.remove();
               }
               postalHeading.remove();
            }

            // Ensure Cancel button directs to Institute Master index
            const cancelBtn = formActions.querySelector('.btn-warning, button[type="button"]');
            if (cancelBtn) {
               cancelBtn.addEventListener('click', function () {
                  window.location.href = "{{ route('admin.field_visit_institutes.index') }}";
               });
            }

            initializeDropdowns();
            bindValidation(value);
         })
         .catch(error => {
            console.error('Error fetching address type content:', error);
            dynamicContent.innerHTML = '<div class="col-12"><div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Failed to load location hierarchy. Please try again.</div></div>';
         });
      }

      radios.forEach(radio => {
         radio.addEventListener('change', function () {
            loadAddressContent(this.value);
         });
      });

      function bindValidation(type) {
         const geoPin = document.getElementById('pin');
         const submitButton = document.getElementById('submitButton');

         function checkPinState() {
            if (!submitButton) return;
            const gPin = geoPin ? geoPin.value.trim() : '';
            if (gPin.length === 6 && /^\d+$/.test(gPin)) {
               submitButton.style.display = 'inline-block';
            } else {
               submitButton.style.display = 'inline-block';
            }
         }

         if (geoPin) {
            geoPin.addEventListener('input', checkPinState);
         }

         checkPinState();

         $(document).on('change', '#village-dropdown', function () {
            $('#hidden_village_id').val(this.value);
         });

         if (submitButton) {
            submitButton.addEventListener('click', function (e) {
               e.preventDefault();

               const vDrop = document.getElementById('village-dropdown');
               if (vDrop && vDrop.value) {
                  $('#hidden_village_id').val(vDrop.value);
               }

               if (type === '1') {
                  if (validateVillageFields()) {
                     form.submit();
                  }
               } else if (type === '2') {
                  $('#hidden_village_id').val('');
                  if (validateMunicipalityFields()) {
                     form.submit();
                  }
               }
            });
         }
      }

      function showAlert(message, focusElement = null) {
         const alertContainer = document.getElementById('alert-container') || createAlertContainer();
         const alertDiv = document.createElement('div');

         alertDiv.classList.add('alert', 'alert-warning', 'alert-rounded', 'alert-dismissible', 'fade', 'show');
         alertDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> ${message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

         alertContainer.appendChild(alertDiv);

         if (focusElement) {
            focusElement.focus();
            if ($(focusElement).hasClass('select2-hidden-accessible')) {
               $(focusElement).select2('open');
            }
         }

         setTimeout(() => {
            alertDiv.remove();
         }, 4000);
      }

      function createAlertContainer() {
         const newContainer = document.createElement('div');
         newContainer.id = 'alert-container';
         if (form && form.parentNode) {
            form.parentNode.insertBefore(newContainer, form);
         } else {
            document.body.appendChild(newContainer);
         }
         return newContainer;
      }

      function validateFields(fields) {
         for (let field of fields) {
            const element = document.getElementById(field.id);
            if (!element || element.value.trim() === '') {
               showAlert(field.message, element);
               return false;
            }
         }
         return true;
      }

      function validateVillageFields() {
         const fields = [
            { id: 'state-dropdown', message: 'Please select State.' },
            { id: 'district-dropdown', message: 'Please select District.' },
            { id: 'block-dropdown', message: 'Please select Block.' },
            { id: 'grampanchayat-dropdown', message: 'Please select Grampanchayat.' },
            { id: 'village-dropdown', message: 'Please select Village.' },
            { id: 'pin', message: 'Please provide Geographic PIN.' }
         ];

         return validateFields(fields) && Validate();
      }

      function validateMunicipalityFields() {
         const fields = [
            { id: 'state-dropdown', message: 'Please select State.' },
            { id: 'district-dropdown', message: 'Please select District.' },
            { id: 'municipality-dropdown', message: 'Please select Municipality.' },
            { id: 'ward-dropdown', message: 'Please select Ward.' },
            { id: 'pin', message: 'Please provide Geographic PIN.' }
         ];

         return validateFields(fields) && Validate();
      }

      // Check if address type is pre-selected (e.g. following validation error redirect)
      const checkedRadio = document.querySelector('input[name="address_type"]:checked')
         || document.querySelector('input[name="ngo_address_type"]:checked');
      if (checkedRadio) {
         loadAddressContent(checkedRadio.value);
      }
   });
</script>
@endsection
