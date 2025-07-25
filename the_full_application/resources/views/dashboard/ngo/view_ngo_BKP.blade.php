@section('title') 
NGO || View Details 
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
   .fixed-table {
   table-layout: fixed;
   width: 100%;
   }
   .fixed-table th,
   .fixed-table td {
   word-break: break-word;
   white-space: normal;
   vertical-align: top;
   text-align: left;
   padding: 8px;
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
         <button onclick="exportToPDF()" class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-info"><i class="fa-solid fa-file-pdf"></i> Export to PDF</button>
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
                  <div class="card">
                     <div id="ngoDetailsToExport">
                        <div class="card-body">
                           <button onclick="printCardBody()" class="btn btn-primary mb-3">Print</button>
                           <h5 class="card-title">Basic Details of {{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</h5>
                           <div class="table-responsive">
                              <table class="table table-bordered fixed-table">
                                 <tbody>
                                    <tr>
                                       <td><strong>NGO Category</strong></td>
                                       <td>
                                          <p>{{ $NgoRegistration->ngo_category == 1 ? 'RPwD Act' : ($NgoRegistration->ngo_category == 2 ? 'Senior Citizen Act' : '') }}</p>
                                       </td>
                                       <td><strong>NGO Name</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>NGO Pan No</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_org_pan) ? $NgoRegistration->ngo_org_pan : 'N/A' }}</p>
                                       </td>
                                       <td><strong>Pan Document</strong></td>
                                       <td>
                                          <p><label class="badge bg-info"><a href="{{ url('storage/' . $NgoRegistration->ngo_org_pan_file) }}" target="_blank" style="cursor: pointer;" class=" text-white">View Pan Document</a></label></p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>NGO Email</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_org_email) ? $NgoRegistration->ngo_org_email : 'N/A' }}</p>
                                       </td>
                                       <td><strong>NGO Phone No</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_org_phone) ? $NgoRegistration->ngo_org_phone : 'N/A' }}</p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>NGO Website</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_org_website) ? $NgoRegistration->ngo_org_website : 'N/A' }}</p>
                                       </td>
                                       <td><strong>Registered With</strong></td>
                                       <td>
                                          @php
                                          $registrationOptions = [
                                          1 => 'Societies Registration Act, 1860',
                                          2 => 'Indian Trusts Act, 1882',
                                          3 => 'Section 8 of the Companies Act, 2013',
                                          4 => 'Registrar of Companies',
                                          5 => 'Registrar of Societies',
                                          6 => 'Registrar of Cooperative Societies',
                                          7 => 'Charity Commissioner',
                                          8 => 'International Organisation',
                                          9 => 'Sub Registrar',
                                          10 => 'Any Other (Specify)',
                                          ];
                                          @endphp
                                          <p>@if($NgoRegistration->ngo_registered_with == 10)
                                             {{ !empty($NgoRegistration->ngo_other_reg_act) ? $NgoRegistration->ngo_other_reg_act : 'N/A' }}
                                             @else
                                             {{ $registrationOptions[$NgoRegistration->ngo_registered_with] ?? 'N/A' }}
                                             @endif
                                          </p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>Type of VO/NGO</strong></td>
                                       <td>
                                          @php
                                          $ngoTypes = [
                                          1 => 'Private Sector Companies (Sec 8/25)',
                                          2 => 'Registered Societies (Non Government)',
                                          3 => 'Trust (Non Government)',
                                          4 => 'Other Registered Entities (Non Government)',
                                          5 => 'Academic Institutions (Govt)',
                                          6 => 'Academic Institutions (Private)',
                                          ];
                                          @endphp
                                          <p>{{ $ngoTypes[$NgoRegistration->ngo_type_of_vo_or_ngo] ?? 'N/A' }}</p>
                                       </td>
                                       <td><strong>Date of Registration</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_date_of_registration) ? \Carbon\Carbon::parse($NgoRegistration->ngo_date_of_registration)->format('d-M-Y') : 'N/A' }}</p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>NGO Registration No</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_reg_no) ? $NgoRegistration->ngo_reg_no : 'N/A' }}</p>
                                       </td>
                                       <td><strong>View Registration Certificate</strong></td>
                                       <td>
                                          <p><label class="badge bg-info"><a href="{{ url('storage/' . $NgoRegistration->ngo_file_rc) }}" target="_blank" style="cursor: pointer;" class=" text-white">View Registration Certificate</a></label></p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>New NGO Registration No</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_system_gen_reg_no) ? $NgoRegistration->ngo_system_gen_reg_no : 'N/A' }}</p>
                                       </td>
                                       <td><strong>Nature of Organization</strong></td>
                                       <td>
                                          @php
                                          $natureOptions = [
                                          1 => 'Persons with Disabilities',
                                          2 => 'Senior Citizens',
                                          3 => 'Transgender Person',
                                          4 => 'Beggars & Destitute',
                                          5 => 'Victims of Substance Abusers',
                                          6 => 'Others (Specify)',
                                          ];
                                          $selectedNatureIds = !empty($NgoRegistration->nature_of_organisation) 
                                          ? explode(',', $NgoRegistration->nature_of_organisation) 
                                          : [];
                                          $selectedNatureLabels = [];
                                          foreach ($selectedNatureIds as $id) {
                                          $id = trim($id);
                                          if ($id == 6 && !empty($NgoRegistration->nature_of_organisation_other)) {
                                          $selectedNatureLabels[] = $natureOptions[6] . ': ' . $NgoRegistration->nature_of_organisation_other;
                                          } elseif (isset($natureOptions[$id])) {
                                          $selectedNatureLabels[] = $natureOptions[$id];
                                          }
                                          }
                                          @endphp
                                          <p>{{ !empty($selectedNatureLabels) ? implode(', ', $selectedNatureLabels) : 'N/A' }}</p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>Organisation Type</strong></td>
                                       <td>
                                          @php
                                          $orgTypes = [
                                          1 => 'Independent Organisation',
                                          2 => 'Branch of Other Organisation',
                                          ];
                                          @endphp
                                          <p>{{ $orgTypes[$NgoRegistration->ngo_organisation_type] ?? 'N/A' }}</p>
                                       </td>
                                       <td><strong>View Byelaws</strong></td>
                                       <td>
                                          <p><label class="badge bg-info"><a href="{{ url('storage/' . $NgoRegistration->ngo_file_byelaws) }}" target="_blank" style="cursor: pointer;" class=" text-white">View Byelaws</a></label></p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>Parent Organisation</strong></td>
                                       <td>
                                          <p>{{ !empty($NgoRegistration->ngo_parent_organisation) ? $NgoRegistration->ngo_parent_organisation : 'N/A' }}</p>
                                       </td>
                                       <td><strong>NGO Reg Validity Available?</strong></td>
                                       <td>
                                          @php
                                          $ngoRegAvailability = [
                                          1 => 'Yes',
                                          2 => 'No',
                                          ];
                                          @endphp
                                          <p>{{ $ngoRegAvailability[$NgoRegistration->ngo_reg_velidity_available] ?? 'N/A' }}</p>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td><strong>NGO Address</strong></td>
                                       <td>
                                          <p>
                                             @if($NgoRegistration->ngo_address_type == 1)
                                             Village: {{ $NgoRegistration->village->village_name ?? 'N/A' }},
                                             GP: {{ $NgoRegistration->grampanchayat->gp_name ?? 'N/A' }},
                                             Block: {{ $NgoRegistration->block->block_name ?? 'N/A' }},
                                             District: {{ $NgoRegistration->district->district_name ?? 'N/A' }},
                                             State: {{ $NgoRegistration->state->state_name ?? 'N/A' }},
                                             PIN: {{ $NgoRegistration->pin ?? 'N/A' }}
                                             @elseif($NgoRegistration->ngo_address_type == 2)
                                             Municiaplity: {{ $NgoRegistration->municipality->municipality_name ?? 'N/A' }},
                                             District: {{ $NgoRegistration->district->district_name ?? 'N/A' }},
                                             State: {{ $NgoRegistration->state->state_name ?? 'N/A' }},
                                             PIN: {{ $NgoRegistration->pin ?? 'N/A' }}
                                             @else
                                             N/A
                                             @endif
                                          </p>
                                       </td>
                                       <td><strong>NGO Postal Address</strong></td>
                                       <td>
                                          <p>AT: {{ $NgoRegistration->ngo_postal_address_at ?? 'N/A' }}, Post: {{ $NgoRegistration->ngo_postal_address_ps ?? 'N/A' }}, PS: {{ $NgoRegistration->ngo_postal_address_ps ?? 'N/A' }}, PIN: {{ $NgoRegistration->ngo_postal_address_pin ?? 'N/A' }}</p>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                              <h5 class="card-title">Office Bearer Details of {{ $NgoRegistration->ngo_org_name ?? 'N/A' }}</h5>
                              @php
                              $designations = [
                              1 => 'Chairman',
                              2 => 'President',
                              3 => 'Vice Chairman',
                              4 => 'Secretary',
                              5 => 'Director',
                              6 => 'Addl. Director',
                              7 => 'Joint Secretary',
                              8 => 'Treasurer',
                              9 => 'Executive Member',
                              10 => 'Board Member',
                              11 => 'Academic Adviser',
                              12 => 'Academic Administrator',
                              13 => 'Accountant',
                              14 => 'Coordinator',
                              15 => 'Other',
                              ];
                              @endphp
                              <table class="table color-table info-table table-bordered fixed-table">
                                 <thead>
                                    <tr>
                                       <th>Name</th>
                                       <th>Email</th>
                                       <th>Mobile</th>
                                       <th>Designation</th>
                                       <th>PAN</th>
                                       <th>PAN Document</th>
                                       <th>Aadhar</th>
                                       <th>Aadhar Document</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @forelse($NgoPartTwoOfficeBearer as $officeBearer)
                                    <tr>
                                       <td>{{ $officeBearer->office_bearer_name ?? 'N/A' }}</td>
                                       <td>{{ $officeBearer->office_bearer_email ?? 'N/A' }}</td>
                                       <td>{{ $officeBearer->office_bearer_phone ?? 'N/A' }}</td>
                                       <td>{{ $designations[$officeBearer->office_bearer_designation] ?? 'N/A' }}</td>
                                       <td>{{ $officeBearer->office_bearer_pan ?? 'N/A' }}</td>
                                       <td>
                                          @if(!empty($officeBearer->office_bearer_pan_file))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $officeBearer->office_bearer_pan_file) }}" target="_blank" class="text-white">View PAN</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                       <td>{{ $officeBearer->office_bearer_aadhar ?? 'N/A' }}</td>
                                       <td>
                                          @if(!empty($officeBearer->office_bearer_aadhar_file))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $officeBearer->office_bearer_aadhar_file) }}" target="_blank" class="text-white">View Aadhar</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    @empty
                                    <tr>
                                       <td colspan="8" class="text-center">No Office Bearers Found</td>
                                    </tr>
                                    @endforelse
                                 </tbody>
                              </table>
                              <h5 class="card-title">Other ACT Registration Details of {{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</h5>
                              <table class="table color-table info-table table-bordered fixed-table">
                                 <thead>
                                    <tr>
                                       <th>Act</th>
                                       <th>Authority</th>
                                       <th>Regd. No.</th>
                                       <th>Date of Regn.</th>
                                       <th>Validity Till</th>
                                       <th>Certificates</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr>
                                       <td>Income Tax Act 1961 (Sec. 12AA)</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_one ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_one ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_one ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_one ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_one))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_one_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Income Tax Act 1961 (Sec. 80G)</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_two ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_two ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_two ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_two ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_two))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_two_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Registration ID under the NGO Darpan Portal of Government of India</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_three ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_three ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_three ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_three ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_three))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_three_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Foreign Contribution (Regulation) Act, 2010</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_four ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_four ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_four ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_four ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_four))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_four_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Section 50 of the RPwD Act, 2016</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_five ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_five ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_five ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_five ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_five))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_five_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Section 65 of the Mental Health Care Act, 2017</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_six ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_six ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_six ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_six ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_six))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_six_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Section 12 of the National Trust Act, 1999</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_seven ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_seven ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_seven ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_seven ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_seven))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_seven_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>MWPSC Act, 2007</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_eight ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_eight ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_eight ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_eight ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_eight))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_eight_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Juvenile Justice Act 2015</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_nine ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_nine ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_nine ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_nine ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_nine))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_nine_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>Any Other (Specify): {{ $NgoPartThreeActRegistration->authority_other_act ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->authority_other ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_no_other ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->regd_date_other ?? 'N/A' }}</td>
                                       <td>{{ $NgoPartThreeActRegistration->validity_date_other ?? 'N/A' }}</td>
                                       <td>@if(!empty($NgoPartThreeActRegistration->regd_certificate_file_other))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoPartThreeActRegistration->regd_certificate_file_other_path) }}" target="_blank" class="text-white">View Regd Certificate</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                              <h5 class="card-title">Other Recognitions/Affiliations under Govt. of Odisha Details of {{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</h5>
                              <table class="table color-table info-table table-bordered fixed-table">
                                 <thead>
                                    <tr>
                                       <th>Project Title</th>
                                       <th>Approving Authority</th>
                                       <th>Date of Approval</th>
                                       <th>Project Location</th>
                                       <th>No. of Beneficiaries</th>
                                       <th>Project Cost</th>
                                       <th>Current Status</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach($NgoPartFourOtherRecognition as $recognition)
                                    <tr>
                                       <td>{{ $recognition->project_title ?? 'N/A' }}</td>
                                       <td>{{ $recognition->approving_authority ?? 'N/A' }}</td>
                                       <td>{{ $recognition->date_of_approval ?? 'N/A' }}</td>
                                       <td>{{ $recognition->project_location ?? 'N/A' }}</td>
                                       <td>{{ $recognition->no_of_beneficiaries ?? 'N/A' }}</td>
                                       <td>{{ $recognition->project_cost ?? 'N/A' }}</td>
                                       <td>{{ $recognition->current_status ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                              <h5 class="card-title">Trained Staff Details of {{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</h5>
                              <table class="table color-table info-table table-bordered fixed-table">
                                 <thead>
                                    <tr>
                                       <th>Staff Name</th>
                                       <th>Designation</th>
                                       <th>Role</th>
                                       <th>Category</th>
                                       <th>Category Type</th>
                                       <th>Qualification</th>
                                       <th>Date of Joining</th>
                                       <th>Aadhar No</th>
                                       <th>Mobile No</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach($NgoPartFourTrainedStaff as $staff)
                                    <tr>
                                       <td>{{ $staff->staff_name ?? 'N/A' }}</td>
                                       <td>{{ $staff->staff_designation ?? 'N/A' }}</td>
                                       <td>{{ $staff->staff_role ?? 'N/A' }}</td>
                                       {{-- Convert staff_category number to readable text --}}
                                       <td>
                                          @php
                                          $categoryOptions = [1 => 'Professional/ Technical', 2 => 'Assisting/ Attending', 3 => 'Community Workers', 4 => 'Others(Specify)'];
                                          @endphp
                                          {{ $categoryOptions[$staff->staff_category] ?? 'N/A' }}
                                       </td>
                                       {{-- Convert staff_category_type number to readable text --}}
                                       <td>
                                          @php
                                          $typeOptions = [1 => 'Fulltime', 2 => 'Parttime'];
                                          @endphp
                                          {{ $typeOptions[$staff->staff_category_type] ?? 'N/A' }}
                                       </td>
                                       <td>{{ $staff->staff_qualification ?? 'N/A' }}</td>
                                       <td>{{ $staff->staff_date_of_joining ?? 'N/A' }}</td>
                                       <td>{{ $staff->staff_aadhar_number ?? 'N/A' }}</td>
                                       <td>{{ $staff->staff_mob_no ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                              <h5 class="card-title">Assets of the Organization Details of {{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</h5>
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
                                    @php
                                    $unit = $NgoPartSixAssetOrganization[$key . '_no_of_unit'] ?? 'N/A';
                                    $permRental = $NgoPartSixAssetOrganization[$key . '_permanent_or_rental'] ?? '';
                                    $permRentalText = $permRental == '1' ? 'Permanent' : ($permRental == '2' ? 'Rental' : 'N/A');
                                    $filePath = $NgoPartSixAssetOrganization[$key . '_no_of_unit_file'] ?? null;
                                    @endphp
                                    <tr>
                                       <td>{{ $label }}</td>
                                       <td>{{ $unit }}</td>
                                       <td>{{ $permRentalText }}</td>
                                       <td>
                                          @if(!empty($filePath))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $filePath) }}" target="_blank" class="text-white">View {{ $label }} Document</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                              <h5 class="card-title">Financial Status of {{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</h5>
                              @php
                              $financialYears = [
                              '2016-17', '2017-18', '2018-19', '2019-20',
                              '2020-21', '2021-22', '2022-23', '2023-24',
                              '2024-25', '2025-26'
                              ];
                              @endphp
                              <table class="table color-table info-table financial-table">
                                 <thead>
                                    <tr>
                                       <th>Financial Year</th>
                                       <th>Receipt Price</th>
                                       <th>Payment</th>
                                       <th>Surplus/Deficit</th>
                                       <th>Audit Report</th>
                                       <th>IT Return</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @for($i = 1; $i <= 3; $i++)
                                    @php
                                    $yearIndex = $NgoPartSixFinancialStatus["financial_status_financial_year_$i"] ?? null;
                                    $year = ($yearIndex && isset($financialYears[$yearIndex - 1])) ? $financialYears[$yearIndex - 1] : 'N/A';
                                    $receipt = $NgoPartSixFinancialStatus["financial_status_receipt_price_$i"] ?? 'N/A';
                                    $payment = $NgoPartSixFinancialStatus["financial_status_payment_$i"] ?? 'N/A';
                                    $surplus = $NgoPartSixFinancialStatus["financial_status_surplus_$i"] ?? 'N/A';
                                    $auditFile = $NgoPartSixFinancialStatus["financial_status_audit_file_$i"] ?? null;
                                    $itFile = $NgoPartSixFinancialStatus["financial_status_it_return_file_$i"] ?? null;
                                    @endphp
                                    <tr>
                                       <td>{{ $year }}</td>
                                       <td>{{ $receipt }}</td>
                                       <td>{{ $payment }}</td>
                                       <td>{{ $surplus }}</td>
                                       <td>
                                          @if(!empty($auditFile))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $auditFile) }}" target="_blank" class="text-white">View Audit Report</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                       <td>
                                          @if(!empty($itFile))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $itFile) }}" target="_blank" class="text-white">View IT Return</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                    @endfor
                                 </tbody>
                              </table>
                              <h5 class="card-title">Bank Account Details of {{ !empty($NgoRegistration->ngo_org_name) ? $NgoRegistration->ngo_org_name : 'N/A' }}</h5>
                              <table class="table color-table info-table financial-table">
                                 <thead>
                                    <tr>
                                       <th>Account Type</th>
                                       <th>Account Holder</th>
                                       <th>Additional Holder</th>
                                       <th>Account Number</th>
                                       <th>IFSC Code</th>
                                       <th>Passbook (Front Page)</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr>
                                       <td>{{ $NgoRegistration->bank_account_type_1 == 1 ? 'Single Account' : ($NgoRegistration->bank_account_type_1 == 2 ? 'Joint Account' : '') }}</td>
                                       <td>{{ $NgoRegistration->bank_account_holder_name_1 ?? 'N/A' }}</td>
                                       <td>{{ $NgoRegistration->bank_account_holder_name_2 ?? 'N/A' }}</td>
                                       <td>{{ $NgoRegistration->bank_account_number ?? 'N/A' }}</td>
                                       <td>{{ $NgoRegistration->ifsc_code ?? 'N/A' }}</td>
                                       <td>
                                          @if(!empty($NgoRegistration->bank_account_file))
                                          <label class="badge bg-info">
                                          <a href="{{ url('storage/' . $NgoRegistration->bank_account_file) }}" target="_blank" class="text-white">View Passbook</a>
                                          </label>
                                          @else
                                          N/A
                                          @endif
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
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
<script>
   $(function () {
      $('#example23').DataTable({
         processing: true,
         responsive: true,
         ordering: true,
         lengthMenu: [[10, 500, 1000, -1], [10, 500, 1000, "All"]],
         dom: 'Blfrtip',
         buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
         ]
      });
      $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
   });   
</script>
<script>
   function printCardBody() {
      const content = document.querySelector('.card-body').cloneNode(true);
   
      const printWindow = window.open('', '', 'width=900,height=1000');
   
      if (printWindow) {
         printWindow.document.write(`
   <html>
   <head>
   <title>Print NGO Details</title>
   <style>
   @media print {
   @page {
   size: A4;
   margin: 1in;
   }
   body {
   font-family: Arial, sans-serif;
   margin: 0;
   padding: 0;
   }
   table {
   width: 100%;
   border-collapse: collapse;
   }
   th, td {
   border: 1px solid #000;
   padding: 8px;
   text-align: left;
   }
   h4 {
   text-align: center;
   margin-bottom: 20px;
   }
   .badge {
   padding: 4px 8px;
   background-color: #f0ad4e;
   color: white;
   border-radius: 4px;
   text-decoration: none;
   }
   a {
   text-decoration: none;
   color: black;
   }
   }
   </style>
   </head>
   <body></body>
   </html>
         `);
   
         printWindow.document.body.appendChild(content);
         printWindow.document.close();
   
         printWindow.focus();
   // Wait a short moment to ensure rendering is complete
         setTimeout(() => {
            printWindow.print();
            printWindow.close();
         }, 500);
      } else {
         alert("Popup blocked! Please allow popups for this site.");
      }
   }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
   async function exportToPDF() {
      const { jsPDF } = window.jspdf;
   
   /*Select the content to export*/
      const element = document.getElementById('ngoDetailsToExport');
   
   /*Temporarily add header and footer*/
      const header = document.createElement('div');
      header.innerHTML = `
   <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
   <img src="https://w1.pngwing.com/pngs/998/654/png-transparent-india-symbol-ganjam-district-boudh-district-koraput-district-government-of-india-government-of-odisha-college-bhubaneswar-thumbnail.png" style="height:50px;" />
   <span style="font-size: 14px;">${new Date().toLocaleDateString()}</span>
   </div>
      `;
      element.prepend(header);
   
      const footer = document.createElement('div');
      footer.innerHTML = `
   <div style="margin-top: 30px; text-align: center; font-size: 12px;">
   © ${new Date().getFullYear()} Your Organization Name
   </div>
      `;
      element.appendChild(footer);
   
   /*Render the content to canvas*/
      const canvas = await html2canvas(element, { scale: 2, useCORS: true });
   
   /*Remove temp header/footer to restore original view*/
      header.remove();
      footer.remove();
   
   /*Get image data*/
      const imgData = canvas.toDataURL('image/png');
   
   /*Create PDF*/
      const pdf = new jsPDF('p', 'pt', 'a4');
      const pageWidth = 595.28;
      const pageHeight = 841.89;
   
      const imgProps = pdf.getImageProperties(imgData);
      const imgHeight = (imgProps.height * pageWidth) / imgProps.width;
   
      if (imgHeight <= pageHeight) {
         pdf.addImage(imgData, 'PNG', 0, 0, pageWidth, imgHeight);
      } else {
   /*Handle multipage*/
         let position = 0;
         let heightLeft = imgHeight;
         while (heightLeft > 0) {
            pdf.addImage(imgData, 'PNG', 0, position, pageWidth, imgHeight);
            heightLeft -= pageHeight;
            position -= pageHeight;
            if (heightLeft > 0) pdf.addPage();
         }
      }
   
   /*Save the PDF*/
      pdf.save('NGO_Details.pdf');
   }
</script>
@endsection