@section('title') 
EP Pensiners || Scheme Wise Abstract (Age 80+ & ≥80% Disability) || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
               <div>
                  <form method="GET" class="row g-3 mb-4">
                     <div class="col-md-3">
                        <label for="from_date">From Date</label>
                        <input type="date" name="from_date" id="from_date" value="{{ $from_date }}" class="form-control">
                     </div>
                     <div class="col-md-3">
                        <label for="to_date">To Date</label>
                        <input type="date" name="to_date" id="to_date" value="{{ $to_date }}" class="form-control">
                     </div>
                     <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                     </div>
                  </form>
               </div>
               <div class="table-responsive m-t-40">
                  <div id="columnToggleContainer" class="mb-3 p-2 border rounded bg-light"></div>
                  <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                     <thead>
                        <tr>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Sl No</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">District</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage (A = B + C)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage MBPOAP (B)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage IGNOAP (C)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death (D = E + F)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death MBPOAP (E)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death IGNOAP (F)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible (G = H + I)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible MBPOAP (H)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible IGNOAP (I)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued (J = K + L)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued MBPOAP (K)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued IGNOAP (L)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active (M = N + O)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active MBPOAP (N)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active IGNOAP (O)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability (P = Q + R)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability MBPSDP (Q)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability IGNDP (R)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death (S = T + U)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death MBPSDP (T)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death IGNDP (U)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible (V = W + X)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible MBPSDP (W)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible IGNDP (X)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued (Y = Z + AA)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued MBPOAP (Z)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued IGNOAP (AA)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Active (AB = AC + AD)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active MBPSDP (AC)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active IGNDP (AD)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction (AE = AF + AG)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction MBPY (AF)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction NSAP (AG)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued (AH = AI + AJ)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued MBPY (AI)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued NSAP (AJ)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active (AK = AL + AM)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active MBPY (AL)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active NSAP (AM)</th>
                        </tr>
                     </thead>
                     <tfoot>
                        <tr>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Sl No</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">District</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage (A = B + C)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage MBPOAP (B)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage IGNOAP (C)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death (D = E + F)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death MBPOAP (E)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death IGNOAP (F)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible (G = H + I)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible MBPOAP (H)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible IGNOAP (I)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued (J = K + L)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued MBPOAP (K)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued IGNOAP (L)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active (M = N + O)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active MBPOAP (N)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active IGNOAP (O)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability (P = Q + R)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability MBPSDP (Q)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability IGNDP (R)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death (S = T + U)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death MBPSDP (T)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death IGNDP (U)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible (V = W + X)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible MBPSDP (W)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible IGNDP (X)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued (Y = Z + AA)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued MBPOAP (Z)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued IGNOAP (AA)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Active (AB = AC + AD)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active MBPSDP (AC)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active IGNDP (AD)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction (AE = AF + AG)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction MBPY (AF)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction NSAP (AG)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued (AH = AI + AJ)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued MBPY (AI)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued NSAP (AJ)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active (AK = AL + AM)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active MBPY (AL)</th>
                           <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active NSAP (AM)</th>
                        </tr>
                     </tfoot>
                     <tbody>
                        @foreach($final_data as $row)
                        <tr>
                           <td>{{ $row['SlNo'] }}</td>
                           <td>{{ $row['District'] }}</td>
                           {{-- ================= OLD AGE ================= --}}
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','status'=>'total','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['TotalOldage'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'MBPOAP','status'=>'total','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['TotalOldageMbpoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'IGNOAP','status'=>'total','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['TotalOldageIgnoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','status'=>'death','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageDeath'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'MBPOAP','status'=>'death','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageDeathMbpoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'IGNOAP','status'=>'death','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageDeathIgnoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','status'=>'ineligible','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageIneligible'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'MBPOAP','status'=>'ineligible','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageIneligibleMbpoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'IGNOAP','status'=>'ineligible','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageIneligibleIgnoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['TotalOldageDiscontinued'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'MBPOAP','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'IGNOAP','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','status'=>'active']) }}">
                              {{ $row['OldageActive'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'MBPOAP','status'=>'active']) }}">
                              {{ $row['TotalOldageMbpoap'] - ($row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap']) }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'oldage','scheme'=>'IGNOAP','status'=>'active']) }}">
                              {{ $row['TotalOldageIgnoap'] - ($row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap']) }}
                              </a>
                           </td>
                           {{-- ================= DISABILITY ================= --}}
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','status'=>'total']) }}">
                              {{ $row['TotalDisability'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'MBPSDP','status'=>'total']) }}">
                              {{ $row['TotalDisabilityMbpsdp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'IGNDP','status'=>'total']) }}">
                              {{ $row['TotalDisabilityIgndp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','status'=>'death','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityDeath'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'MBPSDP','status'=>'death','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityDeathMbpsdp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'IGNDP','status'=>'death','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityDeathIgndp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','status'=>'ineligible','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityIneligible'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'MBPSDP','status'=>'ineligible','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityIneligibleMbpsdp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'IGNDP','status'=>'ineligible','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityIneligibleIgndp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['TotalDisabilityDiscontinued'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'MBPSDP','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'IGNDP','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','status'=>'active']) }}">
                              {{ $row['DisabilityActive'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'MBPSDP','status'=>'active']) }}">
                              {{ $row['TotalDisabilityMbpsdp'] - ($row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp']) }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'disability','scheme'=>'IGNDP','status'=>'active']) }}">
                              {{ $row['TotalDisabilityIgndp'] - ($row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp']) }}
                              </a>
                           </td>
                           {{-- ================= COMBINED ================= --}}
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','status'=>'total']) }}">
                              {{ $row['TotalSanction'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','scheme'=>'MBPY','status'=>'total']) }}">
                              {{ $row['TotalOldageMbpoap'] + $row['TotalDisabilityMbpsdp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','scheme'=>'NSAP','status'=>'total']) }}">
                              {{ $row['TotalOldageIgnoap'] + $row['TotalDisabilityIgndp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['TotalDiscontinued'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','scheme'=>'MBPY','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap'] + $row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','scheme'=>'NSAP','status'=>'discontinued','from_date'=>$from_date,'to_date'=>$to_date]) }}">
                              {{ $row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap'] + $row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','status'=>'active']) }}">
                              {{ $row['TotalActive'] }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','scheme'=>'MBPY','status'=>'active']) }}">
                              {{ ($row['TotalOldageMbpoap'] + $row['TotalDisabilityMbpsdp']) - ($row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap'] + $row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp']) }}
                              </a>
                           </td>
                           <td>
                              <a target="_blank" href="{{ route('admin.reportof3500data.scheme_wise_list', ['district'=>$row['District'],'category'=>'all','scheme'=>'NSAP','status'=>'active']) }}">
                              {{ ($row['TotalOldageIgnoap'] + $row['TotalDisabilityIgndp']) - ($row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap'] + $row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp']) }}
                              </a>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
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
   $(document).ready(function () {
   
       var table = $('#example23').DataTable({
           scrollX: true,
           pageLength: 30,
           ordering: true,
           dom: 'Bfrtip',
           buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
       });
   
       var toggleContainer = $("#columnToggleContainer");
       toggleContainer.append("<strong>Show / Hide Columns:</strong><br><br>");
   
       table.columns().every(function (index) {
           var column = this;
           var columnName = $(column.header()).text().trim();
           var checkbox = $(`
               <label class="me-3">
                   <input type="checkbox" class="col-toggle me-1" data-column="${index}" checked>
                   ${columnName}
               </label>
           `);
           toggleContainer.append(checkbox);
           checkbox.find("input").on("change", function () {
               var colIndex = $(this).data("column");
               var col = table.column(colIndex);
   
               col.visible(!col.visible());
               $(this).prop("checked", col.visible());
           });
       });
   });
</script>
@endsection