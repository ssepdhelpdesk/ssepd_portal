@section('title') 
NGO || Create
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
               <div id="alert-container"></div>
               <div class="col-sm-12 col-xs-12">
                  <div class="table-responsive">
                     <form class="from-prevent-multiple-submits" method="POST" action="{{ route('admin.ngo.part_six_store', $id)}}" onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="form-body">
                           <h5 class="card-title">Assets of the Organization</h5>
                           <hr>
                           @php
                           $items = [
                           'land' => 'Land',
                           'building' => 'Building',
                           'vehicles' => 'Vehicles',
                           'equipment' => 'Equipment',
                           'others' => 'Others'
                           ];
                           @endphp
                           <table class="table color-table info-table financial-table">
                              <thead>
                                 <tr>
                                    <th>Items</th>
                                    <th>No. of Units</th>
                                    <th>Permanent/Rental</th>
                                    <th>Documents Regarding Items</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @foreach($items as $key => $label)
                                 <tr>
                                    <td>{{ $label }}</td>
                                    {{-- No. of Units --}}
                                    <td>
                                       <div class="form-group" id="{{ $key }}_no_of_unit_div">
                                          <input type="number" id="{{ $key }}_no_of_unit" name="{{ $key }}_no_of_unit" value="{{ old($key . '_no_of_unit') }}" class="form-control" data-required="true">
                                          <div class="{{ $key }}_no_of_unit_error"></div>
                                          @error($key . '_no_of_unit')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                    </td>
                                    {{-- Permanent or Rental --}}
                                    <td>
                                       <div class="form-group" id="{{ $key }}_permanent_or_rental_div">
                                          <select class="select2 form-control form-select" name="{{ $key }}_permanent_or_rental" style="width: 100%; height:36px;" data-placeholder="Choose a Category" data-required="true">
                                             <option value="">--Select--</option>
                                             <option value="1" {{ old($key . '_permanent_or_rental') == '1' ? 'selected' : '' }}>Permanent</option>
                                             <option value="2" {{ old($key . '_permanent_or_rental') == '2' ? 'selected' : '' }}>Rental</option>
                                          </select>
                                          <div class="{{ $key }}_permanent_or_rental_error"></div>
                                          @error($key . '_permanent_or_rental')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                    </td>
                                    {{-- File Upload --}}
                                    <td>
                                       <div class="form-group" id="{{ $key }}_no_of_unit_file_div">
                                          <input type="file" class="form-control" id="{{ $key }}_no_of_unit_file" name="{{ $key }}_no_of_unit_file" accept=".pdf">
                                          <div class="{{ $key }}_no_of_unit_file_error"></div>
                                          @error($key . '_no_of_unit_file')
                                          <label class="error">{{ $message }}</label>
                                          @enderror
                                       </div>
                                    </td>
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                           <h5 class="card-title">Financial Status: <a href="javascript:void(0);">(Upload financial report of last 3Years)</a></h5>
                           <hr>
                           <table class="table color-table info-table financial-table" id="financialTable">
                              <thead>
                                 <tr>
                                    <th>Financial Year</th>
                                    <th>Receipt Price</th>
                                    <th>Payment</th>
                                    <th>Surplus/ Deficit</th>
                                    <th>Upload Audit Report</th>
                                    <th>Upload IT Return</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 use Carbon\Carbon;
                                 $currentYear = Carbon::now()->year;
                                 $currentMonth = Carbon::now()->month;
                                 $currentFYStart = $currentMonth <= 3 ? $currentYear - 1 : $currentYear;
                                 $startYear = $currentFYStart - 1;
                                 $financialYears = [];
                                 for ($i = 2; $i >= 0; $i--) {
                                   $fyStart = $startYear - $i;
                                   $fyEnd = substr($fyStart + 1, -2);
                                   $financialYears[] = "$fyStart-$fyEnd";
                                }
                                @endphp
                                @for($i = 1; $i <= 3; $i++)
                                <tr>
                                 <td>
                                    <div class="form-group" id="financial_status_financial_year_{{ $i }}_div">
                                       <select name="financial_status_financial_year_{{ $i }}" class="select2 form-control" style="width:100%">
                                          <option value="">--Select--</option>
                                          @foreach($financialYears as $year)
                                          <option value="{{ $year }}" {{ old("financial_status_financial_year_$i") == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                         </option>
                                         @endforeach
                                      </select>
                                      <div class="financial_status_financial_year_{{ $i }}_error"></div>
                                      @error("financial_status_financial_year_$i")
                                      <label class="error">{{ $message }}</label>
                                      @enderror
                                   </div>
                                </td>
                                <td>
                                 <div class="form-group" id="financial_status_receipt_price_{{ $i }}_div">
                                    <input type="number" name="financial_status_receipt_price_{{ $i }}" value="{{ old("financial_status_receipt_price_$i") }}" class="form-control">
                                    <div class="financial_status_receipt_price_{{ $i }}_error"></div>
                                    @error("financial_status_receipt_price_$i")
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="financial_status_payment_{{ $i }}_div">
                                    <input type="number" name="financial_status_payment_{{ $i }}" value="{{ old("financial_status_payment_$i") }}" class="form-control">
                                    <div class="financial_status_payment_{{ $i }}_error"></div>
                                    @error("financial_status_payment_$i")
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="financial_status_surplus_{{ $i }}_div">
                                    <input type="number" name="financial_status_surplus_{{ $i }}" value="{{ old("financial_status_surplus_$i") }}" class="form-control">
                                    <div class="financial_status_surplus_{{ $i }}_error"></div>
                                    @error("financial_status_surplus_$i")
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="financial_status_audit_file_{{ $i }}_div">
                                    <input type="file" name="financial_status_audit_file_{{ $i }}" class="form-control" accept=".pdf">
                                    <div class="financial_status_audit_file_{{ $i }}_error"></div>
                                    @error("financial_status_audit_file_$i")
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="financial_status_it_return_file_{{ $i }}_div">
                                    <input type="file" name="financial_status_it_return_file_{{ $i }}" class="form-control" accept=".pdf">
                                    <div class="financial_status_it_return_file_{{ $i }}_error"></div>
                                    @error("financial_status_it_return_file_$i")
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                           @endfor
                        </tbody>
                     </table>
                     <h5 class="card-title">Bank A/C Details: </h5>
                     <hr>
                     <table class="table color-table info-table financial-table" id="bankAccountTable">
                        <thead>
                           <tr>
                              <th>Bank Account Type</th>
                              <th>Account Holder Name</th>
                              <th>Additional Account Holder Name</th>
                              <th>Account Number</th>
                              <th>Choose IFSC Code</th>
                              <th>Upload Passbook Front Page</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>
                                 <div class="form-group" id="bank_account_type_1_div">
                                    <select class="select2 form-control form-select select2-hidden-accessible" style="width: 100%; height:36px;" data-placeholder="Choose a Category" tabindex="-1" name="bank_account_type_1" data-required="true" data-select2-id="1" aria-hidden="true">
                                       <option value="" data-select2-id="3">--Select--</option>
                                       <option value="1">Single Account</option>
                                       <option value="2">Joint Account</option>
                                    </select>
                                    <div class="bank_account_type_1_error"></div>
                                    @error('bank_account_type_1')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="bank_account_holder_name_1_div">
                                    <input type="text" id="bank_account_holder_name_1" name="bank_account_holder_name_1" value="{{ old('bank_account_holder_name_1') ?? '' }}" class="form-control" data-required="true">
                                    <div class="bank_account_holder_name_1_error"></div>
                                    @error('bank_account_holder_name_1')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="bank_account_holder_name_2_div">
                                    <input type="text" id="bank_account_holder_name_2" name="bank_account_holder_name_2" value="{{ old('bank_account_holder_name_2') ?? '' }}" class="form-control" data-required="true">
                                    <div class="bank_account_holder_name_2_error"></div>
                                    @error('bank_account_holder_name_2')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="bank_account_number_div">
                                    <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') ?? '' }}" class="form-control" maxlength="25" placeholder="Enter bank account number" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <div class="bank_account_number_error"></div>
                                    @error('bank_account_number')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="ifsc_code_div">
                                    <select class="select2 form-control form-select select2-hidden-accessible" style="width: 100%; height:36px;" data-placeholder="Choose a Category" tabindex="-1" name="ifsc_code" data-required="true" data-select2-id="1" aria-hidden="true">
                                       <option value="" data-select2-id="3">--Select--</option>
                                       @foreach($ifsc_codes as $ifsc_code)
                                       <option value="{{$ifsc_code->bank_id}}">{{$ifsc_code->bank_ifsc}}</option>
                                       @endforeach
                                    </select>
                                    <div class="ifsc_code_error"></div>
                                    @error('ifsc_code')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="bank_account_file_div">
                                    <input type="file" class="form-control" id="bank_account_file" value="{{ old('bank_account_file') ?? '' }}" name="bank_account_file" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                                    <div class="bank_account_file_error"></div>
                                    @error('bank_account_file')
                                    <label class="error">{{ $message }}</label>
                                    @enderror
                                 </div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <hr>
                     <div class="row">
                        <div class="col-md-4">
                           <div class="form-group" id="ngo_additional_docs_file_div">
                              <label class="form-label">Additional Document(If Any):<span class="itsrequired"> *</span></label>
                              <input type="file" class="form-control" id="ngo_additional_docs_file" value="{{ old('ngo_additional_docs_file') ?? '' }}" name="ngo_additional_docs_file" accept=".pdf" aria-describedby="inputGroupFileAddon01">
                              <div id="ngo_additional_docs_file_error"></div>
                              @error('ngo_additional_docs_file')
                              <label class="error">{{ $message }}</label>
                              @enderror
                           </div>
                        </div>
                     </div>
                     <div class="form-actions submit-container" id="submitContainer" style="display: none;">
                        <button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i> Submit</button>
                     </div>
                  </div>
               </form>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
   document.addEventListener("DOMContentLoaded", function () {
   	const form = document.forms['vform'];
   	const submitButton = document.getElementById('submitButton');
      
   	function validateRow(row) {
   		let isAnyFieldFilled = false;
   		const inputs = row.querySelectorAll("input, select");
   		const errors = [];
         
   		inputs.forEach(input => {
   			if (input.value.trim() !== "") {
   				isAnyFieldFilled = true;
   			}
   		});
         
   		if (isAnyFieldFilled) {
   			inputs.forEach(input => {
   				const errorDiv = input.parentElement.querySelector('.error');
   				if (input.value.trim() === "") {
   					errors.push(input);
   					if (errorDiv) {
   						errorDiv.textContent = "This field is required.";
   						errorDiv.style.color = "red";
   					} else {
   						const errorLabel = document.createElement('label');
   						errorLabel.classList.add('error');
   						errorLabel.style.color = "red";
   						errorLabel.textContent = "This field is required.";
   						input.parentElement.appendChild(errorLabel);
   					}
   				} else {
   					if (errorDiv) {
   						errorDiv.textContent = "";
   					}
   				}
   			});
   		}
         
   		return errors.length === 0;
   	}
      
   	submitButton.addEventListener("click", function (event) {
   		let isValid = true;
   		const rows = form.querySelectorAll("tr");
         
   		const isAnyRowFilled = Array.from(rows).some(row => {
   			return Array.from(row.querySelectorAll("input, select")).some(input => input.value.trim() !== "");
   		});
         
   		if (!isAnyRowFilled) {
   			Swal.fire({
   				icon: "warning",
   				title: "Oops...",
   				text: "Please fill all required fields before submitting!",
   				confirmButtonColor: "#d33"
   			});
   			event.preventDefault();
   			return;
   		}
         
   		rows.forEach(row => {
   			if (!validateRow(row)) {
   				isValid = false;
   			}
   		});
         
   		if (!isValid) {
   			event.preventDefault();
   		}
   	});
      
   	const maxFileSize = 3 * 1024 * 1024;
   	const allowedFileType = 'application/pdf';
      
   	function validateFileInput(input) {
   		const file = input.files[0];
   		const fieldName = input.name;
   		let errorDiv = document.querySelector(`.${fieldName}_error`);
         
   		if (!errorDiv) {
   			errorDiv = document.createElement("div");
   			errorDiv.classList.add("error");
   			errorDiv.classList.add(`${fieldName}_error`);
   			input.parentElement.appendChild(errorDiv);
   		}
   		errorDiv.innerHTML = "";
         
   		if (!file) return;
         
   		if (file.type !== allowedFileType) {
   			Swal.fire({
   				icon: "error",
   				title: "Invalid File Type",
   				text: "Only PDF files are allowed!",
   				confirmButtonColor: "#d33"
   			});
   			input.value = "";
   			return;
   		}
         
   		if (file.size > maxFileSize) {
   			Swal.fire({
   				icon: "error",
   				title: "File Too Large",
   				text: "File size must be less than 3MB!",
   				confirmButtonColor: "#d33"
   			});
   			input.value = "";
   			return;
   		}
   	}
      
   	const fileInputs = document.querySelectorAll('input[type="file"]');
   	fileInputs.forEach(function (input) {
   		input.addEventListener('change', function () {
   			validateFileInput(input);
   		});
   	});
   });
</script>
<script>
   document.addEventListener('DOMContentLoaded', function () {
   	const tables = document.querySelectorAll('.financial-table');
   	const submitContainer = document.getElementById('submitContainer');
      
   	const checkAllFieldsFilled = () => {
   		for (let table of tables) {
   			const rows = table.querySelectorAll('tr');
            
   			for (let row of rows) {
   				const isOthersRow = Array.from(row.cells).some(cell =>
   					cell.textContent.trim().toLowerCase() === 'others'
   					);
               
   				if (isOthersRow) {
                 continue;
              }
              
              const inputs = row.querySelectorAll('input[type="text"], select');
              const fileInputs = row.querySelectorAll('input[type="file"]');
              
              for (let input of inputs) {
                 if (!input.value.trim()) {
                   submitContainer.style.display = "none";
                   return;
                }
             }
             
             for (let file of fileInputs) {
              if (!file.files || file.files.length === 0) {
                submitContainer.style.display = "none";
                return;
             }
          }
       }
    }
    submitContainer.style.display = "block";
 };
 
 tables.forEach(table => {
  const allInputs = table.querySelectorAll('input[type="text"], input[type="file"], select');
  allInputs.forEach(input => {
    input.addEventListener('change', checkAllFieldsFilled);
    input.addEventListener('blur', checkAllFieldsFilled);
 });
});
 
 checkAllFieldsFilled();
});
   
</script>
<script>
   document.addEventListener("DOMContentLoaded", function () {
   	const accountTypeSelect = document.querySelector('select[name="bank_account_type_1"]');
   	const additionalHolderInput = document.getElementById("bank_account_holder_name_2");
      
   	accountTypeSelect.addEventListener("change", function () {
   		if (this.value === "1") {
   			additionalHolderInput.value = "SINGLE BANK ACCOUNT";
   			//additionalHolderInput.setAttribute("readonly", true);
   		} else {
   			additionalHolderInput.value = "";
   			//additionalHolderInput.removeAttribute("readonly");
   		}
   	});
   });
</script>
<script>
   document.getElementById('bank_account_number').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, ''); // Only allow digits
 });
</script>
@endsection