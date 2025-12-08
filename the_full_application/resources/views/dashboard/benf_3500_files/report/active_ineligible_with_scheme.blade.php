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
               <table id="example23" class="display nowrap table table-hover table-striped border" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Sl No</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">District</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage MBPOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage IGNOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death MBPOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death IGNOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible MBPOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible IGNOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued MBPOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued IGNOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active MBPOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active IGNOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability MBPSDP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability IGNDP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death MBPSDP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death IGNDP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible MBPSDP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible IGNDP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued MBPOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued IGNOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Active</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active MBPOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active IGNOAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction MBPY</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction NSAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued MBPY</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued NSAP</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active MBPY</th>
                      <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active NSAP</th>
                   </tr>
                </thead>

                <tfoot>
                  <tr>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Sl No</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">District</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage MBPOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage IGNOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death MBPOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Death IGNOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible MBPOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Oldage Ineligible IGNOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued MBPOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Discontinued IGNOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active MBPOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Oldage Active IGNOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability MBPSDP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability IGNDP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death MBPSDP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Death IGNDP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible MBPSDP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Ineligible IGNDP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued MBPOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Discontinued IGNOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Disability Active</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active MBPOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Disability Active IGNOAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction MBPY</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Sanction NSAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued MBPY</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Discontinued NSAP</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active MBPY</th>
                   <th style="white-space: normal; word-wrap: break-word; max-width:120px;">Total Active NSAP</th>
                </tr>
             </tfoot>
             <tbody>
               @foreach($final_data as $row)
               <tr>
                  <td>{{ $row['SlNo'] }}</td>
                  <td>{{ $row['District'] }}</td>
                  <td>{{ $row['TotalOldage'] }}</td>
                  <td>{{ $row['TotalOldageMbpoap'] }}</td>
                  <td>{{ $row['TotalOldageIgnoap'] }}</td>
                  <td>{{ $row['OldageDeath'] }}</td>
                  <td>{{ $row['OldageDeathMbpoap'] }}</td>
                  <td>{{ $row['OldageDeathIgnoap'] }}</td>
                  <td>{{ $row['OldageIneligible'] }}</td>
                  <td>{{ $row['OldageIneligibleMbpoap'] }}</td>
                  <td>{{ $row['OldageIneligibleIgnoap'] }}</td>
                  <td>{{ $row['TotalOldageDiscontinued'] }}</td>
                  <td>{{ $row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap'] }}</td>
                  <td>{{ $row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap'] }}</td>
                  <td>{{ $row['OldageActive'] }}</td>
                  <td>{{ $row['TotalOldageMbpoap'] - ($row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap']) }}</td>
                  <td>{{ $row['TotalOldageIgnoap'] - ($row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap']) }}</td>
                  
                  <td>{{ $row['TotalDisability'] }}</td>
                  <td>{{ $row['TotalDisabilityMbpsdp'] }}</td>
                  <td>{{ $row['TotalDisabilityIgndp'] }}</td>
                  <td>{{ $row['DisabilityDeath'] }}</td>
                  <td>{{ $row['DisabilityDeathMbpsdp'] }}</td>
                  <td>{{ $row['DisabilityDeathIgndp'] }}</td>
                  <td>{{ $row['DisabilityIneligible'] }}</td>
                  <td>{{ $row['DisabilityIneligibleMbpsdp'] }}</td>
                  <td>{{ $row['DisabilityIneligibleIgndp'] }}</td>
                  <td>{{ $row['TotalDisabilityDiscontinued'] }}</td>
                  <td>{{ $row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp'] }}</td>
                  <td>{{ $row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp'] }}</td>
                  <td>{{ $row['DisabilityActive'] }}</td>
                  <td>{{ $row['TotalDisabilityMbpsdp'] - ($row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp']) }}</td>
                  <td>{{ $row['TotalDisabilityIgndp'] - ($row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp']) }}</td>
                  <td>{{ $row['TotalSanction'] }}</td>
                  <td>{{ $row['TotalOldageMbpoap'] + $row['TotalDisabilityMbpsdp'] }}</td>
                  <td>{{ $row['TotalOldageIgnoap'] + $row['TotalDisabilityIgndp'] }}</td>
                  <td>{{ $row['TotalDiscontinued'] }}</td>
                  <td>{{ $row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap'] + $row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp']}}</td>
                  <td>{{ $row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap'] + $row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp']}}</td>
                  <td>{{ $row['TotalActive'] }}</td>
                  <td>{{ ($row['TotalOldageMbpoap'] + $row['TotalDisabilityMbpsdp']) - ($row['OldageDeathMbpoap'] + $row['OldageIneligibleMbpoap'] + $row['DisabilityDeathMbpsdp'] + $row['DisabilityIneligibleMbpsdp'])}}</td>
                  <td>{{ ($row['TotalOldageIgnoap'] + $row['TotalDisabilityIgndp']) - ($row['OldageDeathIgnoap'] + $row['OldageIneligibleIgnoap'] + $row['DisabilityDeathIgndp'] + $row['DisabilityIneligibleIgndp'])}}</td>
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
   $(function () {
      $('#example23').DataTable({
         processing: true,
         responsive: false,
         ordering: true,
         scrollX: true,
         lengthMenu: [[30, 500, 1000, -1], [30, 500, 1000, "All"]],
         dom: 'Blfrtip',
         buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
         ]
      });
      $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
   });   
</script>
@endsection