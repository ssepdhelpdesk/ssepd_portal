@section('title') 
SSEPD-IT
@endsection 
@extends('website.layout.mainlayout')
@section('style')
@endsection 
@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-bar text-center">
   <div class="container">
      <div class="row">
         <div class="col-md-12 col-12">
            <h2 class="breadcrumb-title mb-2">DBT Consent</h2>
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb justify-content-center mb-0">
                  <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">DBT Consent</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
<!-- /Breadcrumb -->
<div class="content">
   <div class="container">
      <div class="row">
         <div class="col-lg-3 theiaStickySidebar">
            <div class="settings-sidebar mb-lg-0">
               <div>
                  <h6 class="mb-3">Basic Information</h6>
                  <ul class="mb-3 pb-1">
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Name: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->applicant_name}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Care of: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->father_husband_name}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Scheme: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->scheme}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Gender: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->gender}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Sanction Order No: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->sanction_order_no}}</span></a>
                     </li>
                     <hr>
                     <li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Disbursement Mode: </b><span class="ms-2"> {{$nsapPortal27Jan2026CsvData->disbursement_mode}}</span></a>
                     </li>
                     <hr>
                     <li>
                     </li>
                        <a href="" class="d-inline-flex align-items-center"> <b>Address: </b><span class="ms-2"> District: {{$nsapPortal27Jan2026CsvData->district}}, Block/ULB: {{$nsapPortal27Jan2026CsvData->sub_district_municipality}}, GP/Ward: {{$nsapPortal27Jan2026CsvData->gram_panchayat_ward}}</span></a>
                  </ul>
                  <hr>
               </div>
            </div>
         </div>
         <div class="col-lg-9">
            <div class="card mb-0">
               <div class="card-body">
                  <h6 class="fs-18 page-title fw-bold">Basic Information</h6>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>First Name</h6>
                           <span>Ronald</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>Last Name</h6>
                           <span>Richard</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>Registration Date</h6>
                           <span>16 Jan 2024, 11:15 AM</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>User Name</h6>
                           <span>studentdemo</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>Phone Number</h6>
                           <span>90154-91036</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>Email</h6>
                           <span>studentdemo@example.com</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>Gender</h6>
                           <span>Male</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>DOB</h6>
                           <span>16 Jan 2020</span>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <h6>Age</h6>
                           <span>24</span>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div>
                           <h6>Bio</h6>
                           <span>Hello! I'm Ronald Richard. I'm passionate about developing innovative software solutions, analyzing classic literature. I aspire to become a software developer, work as an editor. In my free time, I enjoy coding, reading, hiking etc.
                           </span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection 
@section('script')
@endsection