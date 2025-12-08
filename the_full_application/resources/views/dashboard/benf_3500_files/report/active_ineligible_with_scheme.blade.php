@section('title') 
EP Pensiners || Abstract (Age 80+ & ≥80% Disability) || {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }}
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
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Total Oldage</th>
                        <th>Total Oldage MBPOAP</th>
                        <th>Total Oldage IGNOAP</th>
                        <th>Oldage Death</th>
                        <th>Oldage Death MBPOAP</th>
                        <th>Oldage Death IGNOAP</th>
                        <th>Oldage Ineligible</th>
                        <th>Oldage Ineligible MBPOAP</th>
                        <th>Oldage Ineligible IGNOAP</th>
                        <th>Total Oldage Discontinued</th>
                        <th>Total Oldage Discontinued MBPOAP</th>
                        <th>Total Oldage Discontinued IGNOAP</th>
                        <th>Oldage Active</th>
                        <th>Total Disability</th>
                        <th>Total Disability MBPSDP</th>
                        <th>Total Disability IGNDP</th>
                        <th>Disability Death</th>
                        <th>Disability Death MBPSDP</th>
                        <th>Disability Death IGNDP</th>
                        <th>Disability Ineligible</th>
                        <th>Disability Ineligible MBPSDP</th>
                        <th>Disability Ineligible IGNDP</th>
                        <th>Total Disability Discontinued</th>
                        <th>Disability Active</th>
                        <th>Total Sanction</th>
                        <th>Total Discontinued</th>
                        <th>Total Active</th>
                     </tr>
                  </thead>

                  <tfoot>
                     <tr>
                        <th>Sl No</th>
                        <th>District</th>
                        <th>Total Oldage</th>
                        <th>Total Oldage MBPOAP</th>
                        <th>Total Oldage IGNOAP</th>
                        <th>Oldage Death</th>
                        <th>Oldage Death MBPOAP</th>
                        <th>Oldage Death IGNOAP</th>
                        <th>Oldage Ineligible</th>
                        <th>Oldage Ineligible MBPOAP</th>
                        <th>Oldage Ineligible IGNOAP</th>
                        <th>Total Oldage Discontinued</th>
                        <th>Total Oldage Discontinued MBPOAP</th>
                        <th>Total Oldage Discontinued IGNOAP</th>
                        <th>Oldage Active</th>
                        <th>Total Disability</th>
                        <th>Total Disability MBPSDP</th>
                        <th>Total Disability IGNDP</th>
                        <th>Disability Death</th>
                        <th>Disability Death MBPSDP</th>
                        <th>Disability Death IGNDP</th>
                        <th>Disability Ineligible</th>
                        <th>Disability Ineligible MBPSDP</th>
                        <th>Disability Ineligible IGNDP</th>
                        <th>Total Disability Discontinued</th>
                        <th>Disability Active</th>
                        <th>Total Sanction</th>
                        <th>Total Discontinued</th>
                        <th>Total Active</th>
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
                        <td>{{ $row['DisabilityActive'] }}</td>
                        <td>{{ $row['TotalSanction'] }}</td>
                        <td>{{ $row['TotalDiscontinued'] }}</td>
                        <td>{{ $row['TotalActive'] }}</td>
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