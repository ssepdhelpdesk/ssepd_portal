<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Orbiter is a bootstrap minimal & clean admin template" />
    <meta name="keywords" content="admin, admin panel, admin template, admin dashboard, responsive, bootstrap 4, ui kits, ecommerce, web app, crm, cms, html, sass support, scss" />
    <meta name="author" content="Themesbox" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="ngo-id" content="{{ $id ?? '' }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://ssepd.odisha.gov.in/themes/swfssedp/images/logo-odisha.png" />
    <title>@yield('title')</title>
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="{{ asset('dashboard_assets/assets/node_modules/morrisjs/morris.css') }}" rel="stylesheet" />
    <!--Toaster Popup message CSS -->
    <link href="{{ asset('dashboard_assets/assets/node_modules/toast-master/css/jquery.toast.css') }}" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="{{ asset('dashboard_assets/dist/css/style.min.css') }}" rel="stylesheet" />
    <!-- Dashboard 1 Page CSS -->
    <link href="{{ asset('dashboard_assets/dist/css/pages/dashboard1.css') }}" rel="stylesheet" />
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
    />
    <!-- Datatable -->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard_assets/assets/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard_assets/assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <!-- Dropify CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard_assets/assets/node_modules/dropify/dist/css/dropify.min.css') }}" />
    <!-- Select Two -->
    <link href="{{ asset('dashboard_assets/assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard_assets/assets/node_modules/switchery/dist/switchery.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dashboard_assets/assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('dashboard_assets/assets/node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dashboard_assets/assets/node_modules/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <!-- Date Picker -->
    <link href="{{ asset('dashboard_assets/assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('dashboard_assets/assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard_assets/dist/css/development_css.css') }}" rel="stylesheet" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') }}"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') }}"></script>
<![endif]-->
    <style>
    .itsrequired {
      color: red;
      font-weight: bold;
    }
  </style>
    @yield('style')
</head>
<body class="skin-blue fixed-layout">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
<!-- <div class="preloader">
<div class="loader">
<div class="loader__figure"></div>
<p class="loader__label">SSEPD PORTAL</p>
</div>
</div> -->
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->
<div id="main-wrapper">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <header class="topbar">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <!-- ============================================================== -->
            <!-- Logo -->
            <!-- ============================================================== -->
            <div class="navbar-header">
                <a class="navbar-brand" href="{{url('/dashboard')}}">
                    <!-- Logo icon -->
                    <b>
                        <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                        <!-- Dark Logo icon -->
                        <img src="https://ssepd.odisha.gov.in/themes/swfssedp/images/logo-odisha.png" alt="homepage" class="dark-logo" />
                        <!-- Light Logo icon -->
                        <img src="https://ssepd.odisha.gov.in/themes/swfssedp/images/logo-odisha.png" alt="homepage" class="light-logo" />
                    </b>
                    <!--End Logo icon -->
                    <!-- Logo text -->
                    <!-- <span>
                        <img src="{{ asset('dashboard_assets/assets/images/logo-text.png') }}" alt="homepage" class="dark-logo" />
                        <img src="{{ asset('dashboard_assets/assets/images/logo-light-text.png') }}" class="light-logo" alt="homepage" />
                    </span> -->
                </a>
            </div>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <div class="navbar-collapse">
                <!-- ============================================================== -->
                <!-- toggle and nav items -->
                <!-- ============================================================== -->
                <ul class="navbar-nav me-auto">
                    <!-- This is  -->
                    <li class="nav-item">
                        <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a>
                    </li>
                    <!-- ============================================================== -->
                    <!-- Search -->
                    <!-- ============================================================== -->
                    <!-- @include('dashboard.layouts.search') -->
                </ul>
                <!-- ============================================================== -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
                <ul class="navbar-nav my-lg-0">
                    <!-- ============================================================== -->
                    <!-- Comment -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- End Comment -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Messages -->
                    <!-- ============================================================== -->
                    <!-- @include('dashboard.layouts.navdropdown') -->
                    <!-- ============================================================== -->
                    <!-- End Messages -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- mega menu -->
                    <!-- ============================================================== -->
                    <!-- @include('dashboard.layouts.navbarmegamenu') -->
                    <!-- ============================================================== -->
                    <!-- End mega menu -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- User Profile -->
                    <!-- ============================================================== -->
                    <li class="nav-item dropdown u-pro">
                        <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ (!empty(Auth::user()->profile_photo))? url(Auth::user()->profile_photo_path):url('profile-pic/no_image.jpg') }}" alt="user" class="" />
                            <span class="hidden-md-down">{{Auth::user()->name}} &nbsp;<i class="fa fa-angle-down"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end animated flipInY">
                            <!-- text-->
                            @can('my-profile-access')
                            <a href="{{route('admin.myprofile.index')}}" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                            @endcan
                            <!-- text-->
                            <!-- text-->
                            @can('my-profile-edit')
                            <a href="{{route('admin.myprofile.changePassword')}}" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
                            @endcan
                            <!-- text-->
                            <!-- text-->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                            </form>
                            <!-- text-->
                        </div>
                    </li>
                    <!-- ============================================================== -->
                    <!-- End User Profile -->
                    <!-- ============================================================== -->
                    <li class="nav-item right-side-toggle">
                        <a class="nav-link waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- ============================================================== -->
    <!-- End Topbar header -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    @include('dashboard.layouts.leftbar')
    <!-- ============================================================== -->
    <!-- End Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        @yield('content') @include('dashboard.layouts.servicepanel')
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- footer -->
    <!-- ============================================================== -->
    @include('dashboard.layouts.footer')
    <!-- ============================================================== -->
    <!-- End footer -->
    <!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{ asset('dashboard_assets/assets/node_modules/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="{{ asset('dashboard_assets/dist/js/perfect-scrollbar.jquery.min.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('dashboard_assets/dist/js/waves.js') }}"></script>
<!--Menu sidebar -->
<script src="{{ asset('dashboard_assets/dist/js/sidebarmenu.js') }}"></script>
<!--Custom JavaScript -->
<script src="{{ asset('dashboard_assets/dist/js/custom.min.js') }}"></script>
<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<!--morris JavaScript -->
<script src="{{ asset('dashboard_assets/assets/node_modules/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/morrisjs/morris.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<!-- Popup message jquery -->
<script src="{{ asset('dashboard_assets/assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>
<!-- Chart JS -->
<script src="{{ asset('dashboard_assets/dist/js/dashboard1.js') }}"></script>
<!-- This is data table -->
<script src="{{ asset('dashboard_assets/assets/node_modules/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js') }}"></script>
<!-- start - This is for export functionality only -->
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<!-- end - This is for export functionality only -->
<!-- jQuery file upload -->
<script src="{{ asset('dashboard_assets/assets/node_modules/dropify/dist/js/dropify.min.js') }}"></script>
<!-- Select 2 -->
<script src="{{ asset('dashboard_assets/assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/dff/dff.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('dashboard_assets/assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<!-- Date Picker -->
<script src="{{ asset('dashboard_assets/assets/node_modules/moment/moment.js') }}"></script>
<script src="{{ asset('dashboard_assets/assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
<script type="text/javascript">
    $("#ts_date").bootstrapMaterialDatePicker({ weekStart: 0, time: false });
    $("#te_date").bootstrapMaterialDatePicker({ weekStart: 0, time: false });
    $("#registration_date").bootstrapMaterialDatePicker({ weekStart: 0, time: false });
</script>
<script>
    (function () {
        $(".spinner").hide();
        $(".from-prevent-multiple-submits").on("submit", function () {
            $(".from-prevent-multiple-submits").attr("disabled", "true");
            setTimeout(function() {
                $('[type="submit"]').prop('disabled', false);
            }, 2000);
            $(".spinner").show();
        });
    })();
    const currentYear = new Date().getFullYear();
    document.getElementById('currentYearInFooter').textContent = currentYear;
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            alert.classList.add('fade-out');
            alert.style.display = 'none';
        });
    }, 2000);
</script>
<script>
    $(function () {
        var elems = Array.prototype.slice.call(document.querySelectorAll(".js-switch"));
        $(".js-switch").each(function () {
            new Switchery($(this)[0], $(this).data());
        });
        $(".select2").select2();
        $(".selectpicker").selectpicker();
        $(".vertical-spin").TouchSpin({
            verticalbuttons: true,
        });
        var vspinTrue = $(".vertical-spin").TouchSpin({
            verticalbuttons: true,
        });
        if (vspinTrue) {
            $(".vertical-spin").prev(".bootstrap-touchspin-prefix").remove();
        }
        $("input[name='tch1']").TouchSpin({
            min: 0,
            max: 100,
            step: 0.1,
            decimals: 2,
            boostat: 5,
            maxboostedstep: 10,
            postfix: "%",
        });
        $("input[name='tch2']").TouchSpin({
            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: "$",
        });
        $("input[name='tch3']").TouchSpin();
        $("input[name='tch3_22']").TouchSpin({
            initval: 40,
        });
        $("input[name='tch5']").TouchSpin({
            prefix: "pre",
            postfix: "post",
        });
        $("#pre-selected-options").multiSelect();
        $("#optgroup").multiSelect({
            selectableOptgroup: true,
        });
        $("#public-methods").multiSelect();
        $("#select-all").click(function () {
            $("#public-methods").multiSelect("select_all");
            return false;
        });
        $("#deselect-all").click(function () {
            $("#public-methods").multiSelect("deselect_all");
            return false;
        });
        $("#refresh").on("click", function () {
            $("#public-methods").multiSelect("refresh");
            return false;
        });
        $(".ajax").select2({
            ajax: {
                url: "https://api.github.com/search/repositories",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page,
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: params.page * 30 < data.total_count,
                        },
                    };
                },
                cache: true,
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
        });
    });
</script>
<script>
    $(document).ready(function () {
        $(".dropify").dropify();
        $(".dropify-fr").dropify({
            messages: {
            default: "Glissez-déposez un fichier ici ou cliquez",
                replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
                remove: "Supprimer",
                error: "Désolé, le fichier trop volumineux",
            },
        });

        var drEvent = $("#input-file-events").dropify();

        drEvent.on("dropify.beforeClear", function (event, element) {
            return confirm('Do you really want to delete "' + element.file.name + '" ?');
        });

        drEvent.on("dropify.afterClear", function (event, element) {
            alert("File deleted");
        });

        drEvent.on("dropify.errors", function (event, element) {
            console.log("Has Errors");
        });

        var drDestroy = $("#input-file-to-destroy").dropify();
        drDestroy = drDestroy.data("dropify");
        $("#toggleDropify").on("click", function (e) {
            e.preventDefault();
            if (drDestroy.isDropified()) {
                drDestroy.destroy();
            } else {
                drDestroy.init();
            }
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    @if(Session::has('message'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
    toastr.success("{{ session('message') }}");
    @endif

    @if(Session::has('error'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
    toastr.error("{{ session('error') }}");
    @endif

    @if(Session::has('info'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
    toastr.info("{{ session('info') }}");
    @endif

    @if(Session::has('warning'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
    toastr.warning("{{ session('warning') }}");
    @endif
</script>
<!-- For SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(function () {
        $(document).on("click", "#delete", function (e) {
            e.preventDefault();
            var link = $(this).attr("href");
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                    Swal.fire("Deleted!", "Your file has been deleted.", "success");
                }
            });
        });
    });
</script>
@yield('script')
<script>
    $(function () {
        $("#myTable").DataTable();
        var table = $("#example").DataTable({
            columnDefs: [
            {
                visible: false,
                targets: 2,
            },
            ],
            order: [[2, "asc"]],
            displayLength: 25,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api
                .rows({
                    page: "current",
                })
                .nodes();
                var last = null;
                api.column(2, {
                    page: "current",
                })
                .data()
                .each(function (group, i) {
                    if (last !== group) {
                        $(rows)
                        .eq(i)
                        .before('<tr class="group"><td colspan="5">' + group + "</td></tr>");
                        last = group;
                    }
                });
            },
        });
        $("#example tbody").on("click", "tr.group", function () {
            var currentOrder = table.order()[0];
            if (currentOrder[0] === 2 && currentOrder[1] === "asc") {
                table.order([2, "desc"]).draw();
            } else {
                table.order([2, "asc"]).draw();
            }
        });
        $("#config-table").DataTable({
            responsive: true,
        });
        $(".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel").addClass("btn btn-primary me-1");
    });
</script>
</body>
</html>