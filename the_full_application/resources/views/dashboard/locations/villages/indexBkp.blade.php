<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel AJAX Dependent Country State Block Dropdown Example - ItSolutionStuff.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container mt-4" >
    <div class="card mt-5">
        <h3 class="card-header p-3">Laravel 11 AJAX Dependent Country State Block Dropdown Example - ItSolutionStuff.com</h3>
        <div class="card-body">
            <form>
                <div class="form-group mb-3">
                    <select  id="state-dropdown" class="form-control">
                        <option value="">-- Select State --</option>
                        @foreach ($states as $data)
                        <option value="{{$data->state_id}}">
                            {{$data->state_name}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-3">
                    <select id="district-dropdown" class="form-control">
                    </select>
                </div>
                <div class="form-group">
                    <select id="block-dropdown" class="form-control">
                    </select>
                </div>
                <div class="form-group">
                    <select id="grampanchayat-dropdown" class="form-control">
                    </select>
                </div>
                <div class="form-group">
                    <select id="village-dropdown" class="form-control">
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
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
                url: "{{url('dashboard/fetch-districts')}}",
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
                    $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                    $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                    $('#village-dropdown').html('<option value="">-- Select Village --</option>');
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
            $("#block-dropdown").html('');
            $.ajax({
                url: "{{url('dashboard/fetch-block')}}",
                type: "POST",
                data: {
                    district_id: idDistrict,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#block-dropdown').html('<option value="">-- Select Block --</option>');
                    $.each(res.blocks, function (key, value) {
                        $("#block-dropdown").append('<option value="' + value
                            .block_id + '">' + value.block_name + '</option>');
                    });
                    $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                    $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                }
            });
        });

        /*------------------------------------------
        --------------------------------------------
        Block Dropdown Change Event
        --------------------------------------------
        --------------------------------------------*/
        $('#block-dropdown').on('change', function () {
            var idBlock = this.value;
            $("#grampanchayat-dropdown").html('');
            $.ajax({
                url: "{{url('dashboard/fetch-grampanchayat')}}",
                type: "POST",
                data: {
                    block_id: idBlock,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
                    $.each(res.grampanchayats, function (key, value) {
                        $("#grampanchayat-dropdown").append('<option value="' + value
                            .gp_id + '">' + value.gp_name + '</option>');
                    });
                    $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                }
            });
        });

        /*------------------------------------------
        --------------------------------------------
        Grampanchayat Dropdown Change Event
        --------------------------------------------
        --------------------------------------------*/
        $('#grampanchayat-dropdown').on('change', function () {
            var idGrampanchayat = this.value;
            $("#village-dropdown").html('');
            $.ajax({
                url: "{{url('dashboard/fetch-village')}}",
                type: "POST",
                data: {
                    gp_id: idGrampanchayat,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#village-dropdown').html('<option value="">-- Select Village --</option>');
                    $.each(res.villages, function (key, value) {
                        $("#village-dropdown").append('<option value="' + value
                            .village_id + '">' + value.village_name + '</option>');
                    });
                }
            });
        });

    });
</script>
</body>
</html>
