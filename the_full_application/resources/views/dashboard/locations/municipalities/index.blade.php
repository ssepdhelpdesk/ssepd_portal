@section('title') 
Municipality || Master
@endsection 
@extends('dashboard.layouts.main')
@section('style')
    <link href="{{ asset('dashboard_assets/assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
         @can('user-create')
         <a href="{{ route('admin.permissions.create') }}"><button class="btn waves-effect waves-light btn-rounded m-l-15 text-white btn-xs btn-success"><i class="fas fa-plus-square"></i> Add New</button></a>
         @endcan
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
               <div class="row">
                  <div class="col-md-4">
                     <h5 class="m-t-30">State</h5>
                     <select class="select2 form-control form-select" id="state-dropdown" style="width: 100%; height:36px;">
                        <option value="">-- Select State --</option>
                        @foreach ($states as $data)
                        <option value="{{$data->state_id}}">
                           {{$data->state_name}}
                        </option>
                        @endforeach
                     </select>
                  </div>
                  <div class="col-md-4">
                     <h5 class="m-t-30">District</h5>
                     <select class="select2 form-control form-select" id="district-dropdown" style="width: 100%; height:36px;">
                     </select>
                  </div>
                  <div class="col-md-4">
                     <h5 class="m-t-30">Municipality</h5>
                     <select class="select2 form-control form-select" id="municipality-dropdown" style="width: 100%; height:36px;">
                     </select>
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
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            // For select 2
            $(".select2").select2();
            $('.selectpicker').selectpicker();
        });
        $(document).ready(function () {

        /*------------------------------------------
        --------------------------------------------
        State Dropdown Change Event
        --------------------------------------------
        --------------------------------------------*/
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
                        $("#district-dropdown").append('<option value="' + value
                            .district_id + '">' + value.district_name + '</option>');
                    });
                    $('#municipality-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                }
            });
        });

        /*------------------------------------------
        --------------------------------------------
        District Dropdown Change Event
        --------------------------------------------
        --------------------------------------------*/
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
                        $("#municipality-dropdown").append('<option value="' + value
                            .municipality_id + '">' + value.municipality_name + '</option>');
                    });
                }
            });
        });
    });
    </script>
@endsection