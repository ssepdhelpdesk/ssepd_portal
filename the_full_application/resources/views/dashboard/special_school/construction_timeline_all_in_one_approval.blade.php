@section('title') 
Special School || Construction Progress Details
@endsection 
@extends('dashboard.layouts.main')
@section('style')
<style>
   .readonly-input {
      pointer-events: none;
      background-color: #f8f9fa;
      cursor: default;
   }
   .custom-shadow {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
   }
   .card.custom-shadow:hover {
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
      transition: box-shadow 0.3s ease-in-out;
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
                  <div class="col-lg-8 col-xlg-9 col-md-7">
                     <button onclick="printDiv('printSection')" class="btn btn-primary mb-3">
   <i class="fa fa-print"></i> Print Construction Status
</button>
                     <div id="printSection" class="card">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs profile-tab" role="tablist">
                           <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">Special School Toilet Construction Status</a> </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                           <div class="tab-pane active" id="home" role="tabpanel">
                              <div class="card-body">
                                 <div class="profiletimeline">
                                    @if($special_school_construction && $special_school_construction->count() > 0)
                                    @foreach($special_school_construction as $data)
                                    <div class="sl-item">
                                       <div class="sl-left">
                                          <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" 
                                          alt="user" class="img-circle" />
                                       </div>
                                       <div class="sl-right">
                                          <div>
                                             <a href="javascript:void(0)" class="link">
                                                @php
                                                $n = $data->phase_no;
                                                echo $n . ((($n % 100) >= 11 && ($n % 100) <= 13) ? 'th' : (['th','st','nd','rd','th','th','th','th','th','th'][$n % 10]));
                                                @endphp Phase Updated On</a>
                                             <span class="sl-date">{{ \Carbon\Carbon::parse($data->created_date)->format('d F Y') }}</span>
                                             <p>
                                                Management Name: 
                                                <a href="javascript:void(0)">{{ $data->special_school_management_name }},</a>
                                                School Name: 
                                                <a href="javascript:void(0)">{{ $data->special_school_name }},</a>
                                                 Address:
                                                @if($data->school_address_type == 1)
                                                <a href="javascript:void(0)">{{$data->village->village_name}}, {{$data->grampanchayat->gp_name}}, {{$data->district->district_name}}</a>
                                                @elseif($data->school_address_type == 2)
                                                <a href="javascript:void(0)">{{ optional($data->ward)->ward_name }}, {{$data->municipality->municipalities}}, {{$data->district->district_name}}</a>
                                                @endif
                                             </p>
                                             <div class="row">
                                                @for ($i = 1; $i <= 5; $i++)
                                                @php
                                                $file = "file_construction_image_{$i}";
                                                $lat = "latitude_{$i}";
                                                $long = "longitude_{$i}";
                                                @endphp
                                                @if (!empty($data->$file))
                                                <div class="col-md-4 mb-4">
                                                   <div class="card shadow-lg custom-shadow h-100">
                                                      <div style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                         <img class="card-img-top img-fluid"
                                                         src="{{ url('storage/' . $data->$file) }}"
                                                         alt="Construction Image {{ $i }}"
                                                         style="width: 100%; height: 100%; object-fit: contain;">
                                                      </div>
                                                      <div class="card-body">
                                                         <ul class="list-inline font-14 mb-0">
                                                            <li class="p-l-0 d-inline-block me-3">
                                                               Uploaded On: {{ \Carbon\Carbon::parse($data->created_date)->format('d F Y') }}
                                                            </li>
                                                            @if (!empty($data->$lat))
                                                            <li class="d-inline-block">
                                                               <a href="javascript:void(0)" class="link">Latitude: {{ $data->$lat }}</a>
                                                            </li>
                                                            @endif
                                                            @if (!empty($data->$long))
                                                            <li class="d-inline-block">
                                                               <a href="javascript:void(0)" class="link">Longitude: {{ $data->$long }}</a>
                                                            </li>
                                                            @endif
                                                         </ul>
                                                      </div>
                                                   </div>
                                                </div>
                                                @endif
                                                @endfor
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                    @else
                                    <p class="text-danger">No construction data found for any phase.</p>
                                    @endif
                                    <hr>
                                    @can('special-school-create')
                                    <div class="sl-item">
                                       <div class="sl-left"> <img src="https://www.shutterstock.com/image-illustration/hand-car-logodisabled-care-logoillness-600nw-2301166719.jpg" alt="user" class="img-circle" /> </div>
                                       <div class="sl-right">
                                          
                                       </div>
                                    </div>
                                    
                                    @endcan
                                 </div>
                              </div>
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
   function printDiv(divId) {
      var printContents = document.getElementById(divId).innerHTML;
      var originalContents = document.body.innerHTML;

      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
      location.reload(); // reload to restore JS & styles
   }
</script>
@endsection