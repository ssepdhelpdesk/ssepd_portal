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
        .goog-te-gadget {
          color: transparent !important;
      }
      .goog-te-gadget .goog-te-combo {
          color: #000 !important;
          background: #f8f9fa !important;
          border: 1px solid #ccc !important;
          padding: 6px 10px;
          border-radius: 8px;
          font-size: 14px;
          width: 100%;
      }
      .goog-logo-link,
      .goog-te-banner-frame.skiptranslate {
          display: none !important;
      }

      .lang-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
          gap: 8px;
          justify-items: stretch;
          align-items: center;
          max-height: 340px;
          overflow-y: auto;
          padding-right: 8px;
      }

      .lang-btn {
          border-radius: 8px;
          padding: 6px 12px;
          font-size: 14px;
          font-weight: 500;
          border: 1px solid #dee2e6;
          background-color: #f8f9fa;
          color: #212529;
          width: 100%;
          text-align: left;
          transition: all 0.3s ease;
          cursor: pointer;
      }

      .lang-btn:hover {
          background-color: #007bff;
          color: #fff;
          border-color: #007bff;
          box-shadow: 0 2px 6px rgba(0, 123, 255, 0.2);
      }

      .lang-btn.active {
          background-color: #007bff !important;
          color: #fff !important;
          border-color: #007bff !important;
          box-shadow: 0 2px 6px rgba(0, 123, 255, 0.25) !important;
      }

      .lang-grid::-webkit-scrollbar {
          width: 6px;
      }
      .lang-grid::-webkit-scrollbar-thumb {
          background: #c5c5c5;
          border-radius: 6px;
      }
      .lang-grid::-webkit-scrollbar-thumb:hover {
          background: #888;
      }

      @media (max-width: 576px) {
          .lang-grid {
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 6px;
        }
        .lang-btn {
            font-size: 13px;
            padding: 5px 8px;
        }
    }
</style>
@yield('style')
</head>
<body class="horizontal-nav skin-megna fixed-layout">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Elite admin</p>
        </div>
    </div>
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
                    <a class="navbar-brand" href="index.html">
                        <!-- Logo icon --><b>
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <img src="../assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            <!-- Light Logo icon -->
                            <img src="../assets/images/logo-light-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text --><span class="hidden-sm-down">
                         <!-- dark Logo text -->
                         <img src="../assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                         <!-- Light Logo text -->    
                         <img src="../assets/images/logo-light-text.png" class="light-logo" alt="homepage" /></span> </a>
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
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        <li class="nav-item">
                            <form class="app-search d-none d-md-block d-lg-block">
                                <input type="text" class="form-control" placeholder="Search & enter">
                            </form>
                        </li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="ti-email"></i>
                                <div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end mailbox animated bounceInDown">
                                <ul>
                                    <li>
                                        <div class="drop-title">Notifications</div>
                                    </li>
                                    <li>
                                        <div class="message-center">
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="btn btn-danger btn-circle text-white"><i class="fa fa-link"></i></div>
                                                <div class="mail-contnet">
                                                    <h5>Luanch Admin</h5> <span class="mail-desc">Just see the my new admin!</span> <span class="time">9:30 AM</span> </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="btn btn-success btn-circle text-white"><i class="ti-calendar"></i></div>
                                                <div class="mail-contnet">
                                                    <h5>Event today</h5> <span class="mail-desc">Just a reminder that you have event</span> <span class="time">9:10 AM</span> </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="btn btn-info btn-circle text-white"><i class="ti-settings"></i></div>
                                                <div class="mail-contnet">
                                                    <h5>Settings</h5> <span class="mail-desc">You can customize this template as you want</span> <span class="time">9:08 AM</span> </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="btn btn-primary btn-circle"><i class="ti-user"></i></div>
                                                <div class="mail-contnet">
                                                    <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="nav-link text-center link" href="javascript:void(0);"> <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Comment -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- Messages -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" id="2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="icon-note"></i>
                                <div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                            </a>
                            <div class="dropdown-menu mailbox dropdown-menu-end animated bounceInDown" aria-labelledby="2">
                                <ul>
                                    <li>
                                        <div class="drop-title">You have 4 new messages</div>
                                    </li>
                                    <li>
                                        <div class="message-center">
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="user-img"> <img src="../assets/images/users/1.jpg" alt="user" class="img-circle"> <span class="profile-status online pull-right"></span> </div>
                                                <div class="mail-contnet">
                                                    <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:30 AM</span> </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="user-img"> <img src="../assets/images/users/2.jpg" alt="user" class="img-circle"> <span class="profile-status busy pull-right"></span> </div>
                                                <div class="mail-contnet">
                                                    <h5>Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="user-img"> <img src="../assets/images/users/3.jpg" alt="user" class="img-circle"> <span class="profile-status away pull-right"></span> </div>
                                                <div class="mail-contnet">
                                                    <h5>Arijit Sinh</h5> <span class="mail-desc">I am a singer!</span> <span class="time">9:08 AM</span> </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)">
                                                <div class="user-img"> <img src="../assets/images/users/4.jpg" alt="user" class="img-circle"> <span class="profile-status offline pull-right"></span> </div>
                                                <div class="mail-contnet">
                                                    <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="nav-link text-center link" href="javascript:void(0);"> <strong>See all e-Mails</strong> <i class="fa fa-angle-right"></i> </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Messages -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- mega menu -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown mega-dropdown"> <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ti-layout-width-default"></i></a>
                            <div class="dropdown-menu animated bounceInDown">
                                <ul class="mega-dropdown-menu row">
                                    <li class="col-lg-3 col-xlg-2 m-b-30">
                                        <h4 class="m-b-20">CAROUSEL</h4>
                                        <!-- CAROUSEL -->
                                        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner" role="listbox">
                                                <div class="carousel-item active">
                                                    <div class="container"> <img class="d-block img-fluid" src="../assets/images/big/img1.jpg" alt="First slide"></div>
                                                </div>
                                                <div class="carousel-item">
                                                    <div class="container"><img class="d-block img-fluid" src="../assets/images/big/img2.jpg" alt="Second slide"></div>
                                                </div>
                                                <div class="carousel-item">
                                                    <div class="container"><img class="d-block img-fluid" src="../assets/images/big/img3.jpg" alt="Third slide"></div>
                                                </div>
                                            </div>
                                            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a>
                                            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
                                        </div>
                                        <!-- End CAROUSEL -->
                                    </li>
                                    <li class="col-lg-3 m-b-30">
                                        <h4 class="m-b-20">ACCORDION</h4>
                                        <div class="accordion" id="accordionExample">
                                            <div class="card m-b-0">
                                                <div class="card-header bg-white p-0" id="headingOne">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                            Collapsible Group Item #1
                                                        </button>
                                                    </h5>
                                                </div>

                                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        Anim pariatur cliche reprehenderit, enim eiusmod high.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card m-b-0">
                                                <div class="card-header bg-white p-0" id="headingTwo">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                                            aria-controls="collapseTwo">
                                                            Collapsible Group Item #2
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        Anim pariatur cliche reprehenderit, enim eiusmod high.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card m-b-0">
                                                <div class="card-header bg-white p-0" id="headingThree">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                                            aria-controls="collapseThree">
                                                            Collapsible Group Item #3
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        Anim pariatur cliche reprehenderit, enim eiusmod high.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="col-lg-3  m-b-30">
                                        <h4 class="m-b-20">CONTACT US</h4>
                                        <!-- Contact -->
                                        <form>
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="exampleInputname1" placeholder="Enter Name"> </div>
                                            <div class="form-group">
                                                <input type="email" class="form-control" placeholder="Enter email"> </div>
                                            <div class="form-group">
                                                <textarea class="form-control" id="exampleTextarea" rows="3" placeholder="Message"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-info text-white">Submit</button>
                                        </form>
                                    </li>
                                    <li class="col-lg-3 col-xlg-4 m-b-30">
                                        <h4 class="m-b-20">List style</h4>
                                        <!-- List style -->
                                        <ul class="list-style-none">
                                            <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> You can give link</a></li>
                                            <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Give link</a></li>
                                            <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Another Give link</a></li>
                                            <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Forth link</a></li>
                                            <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i> Another fifth link</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End mega menu -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- User Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="../assets/images/users/1.jpg" alt="user" class=""> <span class="hidden-md-down">Mark &nbsp;<i class="fa fa-angle-down"></i></span> </a>
                            <div class="dropdown-menu dropdown-menu-end animated flipInY">
                                <!-- text-->
                                <a href="javascript:void(0)" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                                <!-- text-->
                                <a href="javascript:void(0)" class="dropdown-item"><i class="ti-wallet"></i> My Balance</a>
                                <!-- text-->
                                <a href="javascript:void(0)" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
                                <!-- text-->
                                <div class="dropdown-divider"></div>
                                <!-- text-->
                                <a href="javascript:void(0)" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
                                <!-- text-->
                                <div class="dropdown-divider"></div>
                                <!-- text-->
                                <a href="login.html" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                                <!-- text-->
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End User Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item right-side-toggle"> <a class="nav-link  waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a></li>
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
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="user-pro"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><img src="../assets/images/users/1.jpg" alt="user-img" class="img-circle"><span class="hide-menu">Mark Jeckson</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="javascript:void(0)"><i class="ti-user"></i> My Profile</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-wallet"></i> My Balance</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-email"></i> Inbox</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-settings"></i> Account Setting</a></li>
                                <li><a href="javascript:void(0)"><i class="fa fa-power-off"></i> Logout</a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap">--- PERSONAL</li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard <span class="badge rounded-pill bg-cyan ms-auto">4</span></span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="index.html">Minimal </a></li>
                                <li><a href="index2.html">Analytical</a></li>
                                <li><a href="index3.html">Demographical</a></li>
                                <li><a href="index4.html">Modern</a></li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-grid2"></i><span class="hide-menu">Apps</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="app-calendar.html">Calendar</a></li>
                                <li><a href="app-chat.html">Chat app</a></li>
                                <li><a href="app-ticket.html">Support Ticket</a></li>
                                <li><a href="app-contact.html">Contact / Employee</a></li>
                                <li><a href="app-contact2.html">Contact Grid</a></li>
                                <li><a href="app-contact-detail.html">Contact Detail</a></li>
                                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><span class="hide-menu">Inbox</span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="app-email.html">Mailbox</a></li>
                                        <li><a href="app-email-detail.html">Mailbox Detail</a></li>
                                        <li><a href="app-compose.html">Compose Mail</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark two-column" href="javascript:void(0)" aria-expanded="false"><i class="ti-palette"></i><span class="hide-menu">Ui <span class="badge rounded-pill bg-primary text-white ms-auto">25</span></span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="ui-cards.html">Cards</a></li>
                                <li><a href="ui-user-card.html">User Cards</a></li>
                                <li><a href="ui-buttons.html">Buttons</a></li>
                                <li><a href="ui-modals.html">Modals</a></li>
                                <li><a href="ui-tab.html">Tab</a></li>
                                <li><a href="ui-tooltip-popover.html">Tooltip &amp; Popover</a></li>
                                <li><a href="ui-tooltip-stylish.html">Tooltip stylish</a></li>
                                <li><a href="ui-sweetalert.html">Sweet Alert</a></li>
                                <li><a href="ui-notification.html">Notification</a></li>
                                <li><a href="ui-progressbar.html">Progressbar</a></li>
                                <li><a href="ui-nestable.html">Nestable</a></li>
                                <li><a href="ui-range-slider.html">Range slider</a></li>
                                <li><a href="ui-timeline.html">Timeline</a></li>
                                <li><a href="ui-typography.html">Typography</a></li>
                                <li><a href="ui-horizontal-timeline.html">Horizontal Timeline</a></li>
                                <li><a href="ui-session-timeout.html">Session Timeout</a></li>
                                <li><a href="ui-session-ideal-timeout.html">Session Ideal Timeout</a></li>
                                <li><a href="ui-bootstrap.html">Bootstrap Ui</a></li>
                                <li><a href="ui-breadcrumb.html">Breadcrumb</a></li>
                                <li><a href="ui-bootstrap-switch.html">Bootstrap Switch</a></li>
                                <li><a href="ui-list-media.html">List Media</a></li>
                                <li><a href="ui-ribbons.html">Ribbons</a></li>
                                <li><a href="ui-grid.html">Grid</a></li>
                                <li><a href="ui-carousel.html">Carousel</a></li>
                                <li><a href="ui-offcanvas.html">Offcanvas</a></li>
                                <li><a href="ui-date-paginator.html">Date-paginator</a></li>
                                <li><a href="ui-dragable-portlet.html">Dragable Portlet</a></li>
                                <li><a href="ui-spinner.html">Spinner</a></li>
                                <li><a href="ui-scrollspy.html">Scrollspy</a></li>
                                <li><a href="ui-toasts.html">Toasts</a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap">--- FORMS, TABLE &amp; WIDGETS</li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-media-right-alt"></i><span class="hide-menu">Forms</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="form-basic.html">Basic Forms</a></li>
                                <li><a href="form-layout.html">Form Layouts</a></li>
                                <li><a href="form-addons.html">Form Addons</a></li>
                                <li><a href="form-material.html">Form Material</a></li>
                                <li><a href="form-float-input.html">Floating Lable</a></li>
                                <li><a href="form-pickers.html">Form Pickers</a></li>
                                <li><a href="form-upload.html">File Upload</a></li>
                                <li><a href="form-mask.html">Form Mask</a></li>
                                <li><a href="form-validation.html">Form Validation</a></li>
                                <li><a href="form-bootstrap-validation.html">Form Bootstrap Validation</a></li>
                                <li><a href="form-dropzone.html">File Dropzone</a></li>
                                <li><a href="form-icheck.html">Icheck control</a></li>
                                <li><a href="form-img-cropper.html">Image Cropper</a></li>
                                <li><a href="form-bootstrapwysihtml5.html">HTML5 Editor</a></li>
                                <li><a href="form-typehead.html">Form Typehead</a></li>
                                <li><a href="form-wizard.html">Form Wizard</a></li>
                                <li><a href="form-xeditable.html">Xeditable Editor</a></li>
                                <li><a href="form-summernote.html">Summernote Editor</a></li>
                                <li><a href="form-tinymce.html">Tinymce Editor</a></li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-accordion-merged"></i><span class="hide-menu">Tables</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="table-basic.html">Basic Tables</a></li>
                                <li><a href="table-layout.html">Table Layouts</a></li>
                                <li><a href="table-data-table.html">Data Tables</a></li>
                                <li><a href="table-footable.html">Footable</a></li>
                                <li><a href="table-jsgrid.html">Js Grid Table</a></li>
                                <li><a href="table-responsive.html">Responsive Table</a></li>
                                <li><a href="table-bootstrap.html">Bootstrap Tables</a></li>
                                <li><a href="table-editable-table.html">Editable Table</a></li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-settings"></i><span class="hide-menu">Widgets</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="widget-data.html">Data Widgets</a></li>
                                <li><a href="widget-apps.html">Apps Widgets</a></li>
                                <li><a href="widget-charts.html">Charts Widgets</a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap">--- EXTRA COMPONENTS</li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-files"></i><span class="hide-menu">Pages <span class="badge rounded-pill bg-info">25</span></span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="starter-kit.html">Starter Kit</a></li>
                                <li><a href="pages-blank.html">Blank page</a></li>
                                <li><a href="javascript:void(0)" class="has-arrow">Authentication <span class="badge rounded-pill bg-success pull-right">6</span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="pages-login.html">Login 1</a></li>
                                        <li><a href="pages-login-2.html">Login 2</a></li>
                                        <li><a href="pages-register.html">Register</a></li>
                                        <li><a href="pages-register2.html">Register 2</a></li>
                                        <li><a href="pages-register3.html">Register 3</a></li>
                                        <li><a href="pages-lockscreen.html">Lockscreen</a></li>
                                        <li><a href="pages-recover-password.html">Recover password</a></li>
                                    </ul>
                                </li>
                                <li><a href="pages-profile.html">Profile page</a></li>
                                <li><a href="pages-animation.html">Animation</a></li>
                                <li><a href="pages-fix-innersidebar.html">Sticky Left sidebar</a></li>
                                <li><a href="pages-fix-inner-right-sidebar.html">Sticky Right sidebar</a></li>
                                <li><a href="pages-invoice.html">Invoice</a></li>
                                <li><a href="pages-treeview.html">Treeview</a></li>
                                <li><a href="pages-utility-classes.html">Helper Classes</a></li>
                                <li><a href="pages-search-result.html">Search result</a></li>
                                <li><a href="pages-scroll.html">Scrollbar</a></li>
                                <li><a href="pages-pricing.html">Pricing</a></li>
                                <li><a href="pages-lightbox-popup.html">Lighbox popup</a></li>
                                <li><a href="pages-gallery.html">Gallery</a></li>
                                <li><a href="pages-faq.html">Faqs</a></li>
                                <li><a href="javascript:void(0)" class="has-arrow">Error Pages</a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="pages-error-400.html">400</a></li>
                                        <li><a href="pages-error-403.html">403</a></li>
                                        <li><a href="pages-error-404.html">404</a></li>
                                        <li><a href="pages-error-500.html">500</a></li>
                                        <li><a href="pages-error-503.html">503</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-pie-chart"></i><span class="hide-menu">Extra</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="chart-morris.html">Morris Chart</a></li>
                                <li><a href="chart-chartist.html">Chartis Chart</a></li>
                                <li><a href="chart-echart.html">Echarts</a></li>
                                <li><a href="chart-flot.html">Flot Chart</a></li>
                                <li><a href="chart-knob.html">Knob Chart</a></li>
                                <li><a href="chart-chart-js.html">Chartjs</a></li>
                                <li><a href="chart-sparkline.html">Sparkline Chart</a></li>
                                <li><a href="chart-extra-chart.html">Extra chart</a></li>
                                <li><a href="chart-peity.html">Peity Charts</a></li>
                                <li>
                                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><span class="hide-menu">Icons</span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="icon-material.html">Material Icons</a></li>
                                        <li><a href="icon-fontawesome.html">Fontawesome Icons</a></li>
                                        <li><a href="icon-themify.html">Themify Icons</a></li>
                                        <li><a href="icon-weather.html">Weather Icons</a></li>
                                        <li><a href="icon-simple-lineicon.html">Simple Line icons</a></li>
                                        <li><a href="icon-flag.html">Flag Icons</a></li>
                                        <li><a href="icon-iconmind.html">Mind Icons</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><span class="hide-menu">Maps</span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="map-google.html">Google Maps</a></li>
                                        <li><a href="map-vector.html">Vector Maps</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-align-left"></i><span class="hide-menu">Multi</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="javascript:void(0)">item 1.1</a></li>
                                <li><a href="javascript:void(0)">item 1.2</a></li>
                                <li> <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">Menu 1.3</a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="javascript:void(0)">item 1.3.1</a></li>
                                        <li><a href="javascript:void(0)">item 1.3.2</a></li>
                                        <li><a href="javascript:void(0)">item 1.3.3</a></li>
                                        <li><a href="javascript:void(0)">item 1.3.4</a></li>
                                    </ul>
                                </li>
                                <li><a href="javascript:void(0)">item 1.4</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
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
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor">Dashboard 1</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-end">
                        <div class="d-flex justify-content-end align-items-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard 1</li>
                            </ol>
                            <button type="button" class="btn btn-info d-none d-lg-block m-l-15 text-white"><i class="fa fa-plus-circle"></i> Create New</button>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Info box -->
                <!-- ============================================================== -->
                <div class="row g-0">
          <div class="col-lg-3 col-md-6">
            <div class="card border">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                      <div>
                        <h3><i class="icon-screen-desktop"></i></h3>
                        <p class="text-muted">MYNEW CLIENTS</p>
                      </div>
                      <div class="ms-auto">
                        <h2 class="counter text-primary">23</h2>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="progress">
                      <div class="progress-bar bg-primary" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card border">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                      <div>
                        <h3><i class="icon-note"></i></h3>
                        <p class="text-muted">NEW PROJECTS</p>
                      </div>
                      <div class="ms-auto">
                        <h2 class="counter text-cyan">169</h2>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="progress">
                      <div class="progress-bar bg-cyan" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card border">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                      <div>
                        <h3><i class="icon-doc"></i></h3>
                        <p class="text-muted">NEW INVOICES</p>
                      </div>
                      <div class="ms-auto">
                        <h2 class="counter text-purple">157</h2>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="progress">
                      <div class="progress-bar bg-purple" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card border">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                      <div>
                        <h3><i class="icon-bag"></i></h3>
                        <p class="text-muted">All PROJECTS</p>
                      </div>
                      <div class="ms-auto">
                        <h2 class="counter text-success">431</h2>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="progress">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
                <!-- ============================================================== -->
                <!-- End Info box -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Over Visitor, Our income , slaes different and  sales prediction -->
                <!-- ============================================================== -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex m-b-40 align-items-center no-block">
                                    <h5 class="card-title ">YEARLY SALES</h5>
                                    <div class="ms-auto">
                                        <ul class="list-inline font-12">
                                            <li><i class="fa fa-circle text-cyan"></i> Iphone</li>
                                            <li><i class="fa fa-circle text-primary"></i> Ipad</li>
                                            <li><i class="fa fa-circle text-purple"></i> Ipod</li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="morris-area-chart" style="height: 340px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-4 col-md-12">
                        <div class="row">
                            <!-- Column -->
                            <div class="col-md-12">
                                <div class="card bg-cyan text-white">
                                    <div class="card-body ">
                                        <div class="row weather">
                                            <div class="col-6 m-t-40">
                                                <h3>&nbsp;</h3>
                                                <div class="display-4">73<sup>°F</sup></div>
                                                <p class="text-white">AHMEDABAD, INDIA</p>
                                            </div>
                                            <div class="col-6 text-end">
                                                <h1 class="m-b-"><i class="wi wi-day-cloudy-high"></i></h1>
                                                <b class="text-white">SUNNEY DAY</b>
                                                <p class="op-5">April 14</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                            <div class="col-md-12">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div id="myCarouse2" class="carousel slide" data-bs-ride="carousel">
                                            <!-- Carousel items -->
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                    <h4 class="cmin-height">My Acting blown <span class="font-medium">Your Mind</span> and you also <br/>laugh at the moment</h4>
                                                    <div class="d-flex no-block">
                                                        <span><img src="../assets/images/users/1.jpg" alt="user" width="50" class="img-circle"></span>
                                                        <span class="m-l-10">
                                                    <h4 class="text-white m-b-0">Govinda</h4>
                                                    <p class="text-white">Actor</p>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="carousel-item">
                                                    <h4 class="cmin-height">My Acting blown <span class="font-medium">Your Mind</span> and you also <br/>laugh at the moment</h4>
                                                    <div class="d-flex no-block">
                                                        <span><img src="../assets/images/users/2.jpg" alt="user" width="50" class="img-circle"></span>
                                                        <span class="m-l-10">
                                                    <h4 class="text-white m-b-0">Govinda</h4>
                                                    <p class="text-white">Actor</p>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="carousel-item">
                                                    <h4 class="cmin-height">My Acting blown <span class="font-medium">Your Mind</span> and you also <br/>laugh at the moment</h4>
                                                    <div class="d-flex no-block">
                                                        <span><img src="../assets/images/users/3.jpg" alt="user" width="50" class="img-circle"></span>
                                                        <span class="m-l-10">
                                                    <h4 class="text-white m-b-0">Govinda</h4>
                                                    <p class="text-white">Actor</p>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Comment - table -->
                <!-- ============================================================== -->
                <div class="row">
                    <!-- ============================================================== -->
                    <!-- Comment widgets -->
                    <!-- ============================================================== -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Recent Comments</h5>
                            </div>
                            <!-- ============================================================== -->
                            <!-- Comment widgets -->
                            <!-- ============================================================== -->
                            <div class="comment-widgets" id="comment" style="height: 629px;position: relative;">
                                <!-- Comment Row -->
                                <div class="d-flex no-block comment-row">
                                    <div class="p-2"><span class="round"><img src="../assets/images/users/1.jpg" alt="user" width="50"></span></div>
                                    <div class="comment-text w-100">
                                        <h5 class="font-medium">James Anderson</h5>
                                        <p class="m-b-10 text-muted">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                                        <div class="comment-footer">
                                            <span class="text-muted pull-right">April 14, 2016</span> <span class="badge rounded-pill bg-info">Pending</span> <span class="action-icons">
                                                    <a href="javascript:void(0)"><i class="ti-pencil-alt"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-check"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-heart"></i></a>    
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Comment Row -->
                                <div class="d-flex no-block comment-row border-top">
                                    <div class="p-2"><span class="round"><img src="../assets/images/users/2.jpg" alt="user" width="50"></span></div>
                                    <div class="comment-text active w-100">
                                        <h5 class="font-medium">Michael Jorden</h5>
                                        <p class="m-b-10 text-muted">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry..</p>
                                        <div class="comment-footer">
                                            <span class="text-muted pull-right">April 14, 2016</span>
                                            <span class="badge rounded-pill bg-success">Approved</span>
                                            <span class="action-icons active">
                                                    <a href="javascript:void(0)"><i class="ti-pencil-alt"></i></a>
                                                    <a href="javascript:void(0)"><i class="icon-close"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-heart text-danger"></i></a>    
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Comment Row -->
                                <div class="d-flex no-block comment-row border-top">
                                    <div class="p-2"><span class="round"><img src="../assets/images/users/3.jpg" alt="user" width="50"></span></div>
                                    <div class="comment-text w-100">
                                        <h5 class="font-medium">Johnathan Doeting</h5>
                                        <p class="m-b-10 text-muted">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                                        <div class="comment-footer">
                                            <span class="text-muted pull-right">April 14, 2016</span>
                                            <span class="badge rounded-pill bg-danger">Rejected</span>
                                            <span class="action-icons">
                                                    <a href="javascript:void(0)"><i class="ti-pencil-alt"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-check"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-heart"></i></a>    
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Comment Row -->
                                <div class="d-flex no-block comment-row border-top">
                                    <div class="p-2"><span class="round"><img src="../assets/images/users/4.jpg" alt="user" width="50"></span></div>
                                    <div class="comment-text active w-100">
                                        <h5 class="font-medium">Genelia doe</h5>
                                        <p class="m-b-10 text-muted">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry..</p>
                                        <div class="comment-footer">
                                            <span class="text-muted pull-right">April 14, 2016</span>
                                            <span class="badge rounded-pill bg-success">Approved</span>
                                            <span class="action-icons active">
                                                    <a href="javascript:void(0)"><i class="ti-pencil-alt"></i></a>
                                                    <a href="javascript:void(0)"><i class="icon-close"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-heart text-danger"></i></a>    
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- Table -->
                    <!-- ============================================================== -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h5 class="card-title">Sales Overview</h5>
                                        <h6 class="card-subtitle">Check the monthly sales </h6>
                                    </div>
                                    <div class="ms-auto">
                                        <select class="form-control b-0">
                                            <option>January</option>
                                            <option value="1">February</option>
                                            <option value="2" selected="">March</option>
                                            <option value="3">April</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-6">
                                        <h3>March 2017</h3>
                                        <h5 class="font-light m-t-0">Report for this month</h5></div>
                                    <div class="col-6 align-self-center display-6 text-end">
                                        <h2 class="text-success">$3,690</h2></div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover no-wrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>NAME</th>
                                            <th>STATUS</th>
                                            <th>DATE</th>
                                            <th>PRICE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td class="txt-oflo">Elite admin</td>
                                            <td><span class="badge bg-success rounded-pill">sale</span> </td>
                                            <td class="txt-oflo">April 18, 2017</td>
                                            <td><span class="text-success">$24</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td class="txt-oflo">Real Homes</td>
                                            <td><span class="badge bg-info rounded-pill">extended</span></td>
                                            <td class="txt-oflo">April 19, 2017</td>
                                            <td><span class="text-info">$1250</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">3</td>
                                            <td class="txt-oflo">Ample Admin</td>
                                            <td><span class="badge bg-info rounded-pill">extended</span></td>
                                            <td class="txt-oflo">April 19, 2017</td>
                                            <td><span class="text-info">$1250</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">4</td>
                                            <td class="txt-oflo">Medical Pro</td>
                                            <td><span class="badge bg-danger rounded-pill">tax</span></td>
                                            <td class="txt-oflo">April 20, 2017</td>
                                            <td><span class="text-danger">-$24</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">5</td>
                                            <td class="txt-oflo">Hosting press html</td>
                                            <td><span class="badge bg-success rounded-pill">sale</span></td>
                                            <td class="txt-oflo">April 21, 2017</td>
                                            <td><span class="text-success">$24</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">6</td>
                                            <td class="txt-oflo">Digital Agency PSD</td>
                                            <td><span class="badge bg-success rounded-pill">sale</span> </td>
                                            <td class="txt-oflo">April 23, 2017</td>
                                            <td><span class="text-danger">-$14</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">7</td>
                                            <td class="txt-oflo">Helping Hands</td>
                                            <td><span class="badge bg-warning rounded-pill">member</span></td>
                                            <td class="txt-oflo">April 22, 2017</td>
                                            <td><span class="text-success">$64</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">8</td>
                                            <td class="txt-oflo">Ample Admin</td>
                                            <td><span class="badge bg-info rounded-pill">extended</span></td>
                                            <td class="txt-oflo">April 19, 2017</td>
                                            <td><span class="text-info">$1250</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Comment - chats -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Over Visitor, Our income , slaes different and  sales prediction -->
                <!-- ============================================================== -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex m-b-40 align-items-center no-block">
                                    <h5 class="card-title ">SALES DIFFERENCE</h5>
                                    <div class="ms-auto">
                                        <ul class="list-inline font-12">
                                            <li><i class="fa fa-circle text-cyan"></i> SITE A</li>
                                            <li><i class="fa fa-circle text-primary"></i> SITE B</li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="morris-area-chart2" style="height: 340px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-4 col-md-12">
                        <div class="row">
                            <!-- Column -->
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">SALES DIFFERENCE</h5>
                                        <div class="row">
                                            <div class="col-6  m-t-30">
                                                <h1 class="text-info">$647</h1>
                                                <p class="text-muted">APRIL 2017</p>
                                                <b>(150 Sales)</b> </div>
                                            <div class="col-6">
                                                <div id="sparkline2dash" class="text-end"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                            <div class="col-md-12">
                                <div class="card bg-purple text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">VISIT STATASTICS</h5>
                                        <div class="row">
                                            <div class="col-6  m-t-30">
                                                <h1 class="text-white">$347</h1>
                                                <p class="light_op_text">APRIL 2017</p>
                                                <b class="text-white">(150 Sales)</b> </div>
                                            <div class="col-6">
                                                <div id="sales1" class="text-end"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Page Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Todo, chat, notification -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex no-block align-items-center">
                                    <div>
                                        <h5 class="card-title m-b-0">TO DO LIST</h5>
                                    </div>
                                    <div class="ms-auto">
                                        <button class="pull-right btn btn-circle btn-success text-white" data-bs-toggle="modal" data-bs-target="#myModal"><i class="ti-plus"></i></button>
                                    </div>
                                </div>
                                <!-- ============================================================== -->
                                <!-- To do list widgets -->
                                <!-- ============================================================== -->
                                <div class="to-do-widget m-t-20" id="todo" style="height: 400px;position: relative;">
                                    <!-- .modal for add task -->
                                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Add Task</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" class="btn-close"aria-label="Close"> <span aria-hidden="true"></span> </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form>
                                                        <div class="form-group">
                                                            <label class="form-label">Task name</label>
                                                            <input type="text" class="form-control" placeholder="Enter Task Name"> </div>
                                                        <div class="form-group">
                                                            <label class="form-label">Assign to</label>
                                                            <select class="form-select form-control pull-right">
                                                                <option selected="">Sachin</option>
                                                                <option value="1">Sehwag</option>
                                                                <option value="2">Pritam</option>
                                                                <option value="3">Alia</option>
                                                                <option value="4">Varun</option>
                                                            </select>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-success text-white" data-bs-dismiss="modal">Submit</button>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->
                                    <ul class="list-task todo-list list-group m-b-0" data-role="tasklist">
                                        <li class="list-group-item" data-role="task">
                                            <div class="form-check d-flex align-items-start">
                                                <input type="checkbox" class="form-check-input flex-shrink-0" id="customCheck">
                                                <label class="form-check-label w-100 ms-2 d-md-flex align-items-start font-normal lh-25" for="customCheck">
                                                    <span>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been</span> <span class="badge rounded-pill bg-danger ms-auto mb-2 mb-md-0">Today</span>
                                                </label>
                                            </div>
                                            <ul class="assignedto mt-2">
                                                <li><img src="../assets/images/users/1.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Steave"></li>
                                                <li><img src="../assets/images/users/2.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Jessica"></li>
                                                <li><img src="../assets/images/users/3.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Priyanka"></li>
                                                <li><img src="../assets/images/users/4.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Selina"></li>
                                            </ul>
                                        </li>
                                        <li class="list-group-item" data-role="task">
                                            <div class="form-check d-flex align-items-start">
                                                <input type="checkbox" class="form-check-input flex-shrink-0" id="customCheck1">
                                                <label class="form-check-label w-100 ms-2 d-md-flex align-items-start font-normal lh-25"  for="customCheck1">
                                                    <span>Lorem Ipsum is simply dummy text of the printing</span><span class="badge rounded-pill bg-primary ms-auto mb-2 mb-md-0">1 week </span>
                                                </label>
                                            </div>
                                            <div class="item-date"> 26 jun 2017</div>
                                        </li>
                                        <li class="list-group-item" data-role="task">
                                            <div class="form-check d-flex align-items-start">
                                                <input type="checkbox" class="form-check-input flex-shrink-0" id="customCheck2">
                                                <label class="form-check-label w-100 ms-2 d-md-flex align-items-start font-normal lh-25" for="customCheck2">
                                                    <span>Give Purchase report to</span> <span class="badge rounded-pill bg-info ms-auto mb-2 mb-md-0">Yesterday</span>
                                                </label>
                                            </div>
                                            <ul class="assignedto">
                                                <li><img src="../assets/images/users/3.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Priyanka"></li>
                                                <li><img src="../assets/images/users/4.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Selina"></li>
                                            </ul>
                                        </li>
                                        <li class="list-group-item" data-role="task">
                                            <div class="form-check d-flex align-items-start">
                                                <input type="checkbox" class="form-check-input flex-shrink-0" id="customCheck1">
                                                <label class="form-check-label w-100 ms-2 d-md-flex align-items-start font-normal lh-25"  for="customCheck1">
                                                    <span>Lorem Ipsum is simply dummy text of the printing</span><span class="badge rounded-pill bg-warning ms-auto mb-2 mb-md-0">2 week </span>
                                                </label>
                                            </div>
                                            <div class="item-date"> 26 jun 2017</div>
                                        </li>
                                        <li class="list-group-item" data-role="task">
                                            <div class="form-check d-flex align-items-start">
                                                <input type="checkbox" class="form-check-input flex-shrink-0" id="customCheck2">
                                                <label class="form-check-label w-100 ms-2 d-md-flex align-items-start font-normal lh-25" for="customCheck2">
                                                    <span>Give Purchase report to</span> <span class="badge rounded-pill bg-info ms-auto mb-2 mb-md-0">Yesterday</span>
                                                </label>
                                            </div>
                                            <ul class="assignedto">
                                                <li><img src="../assets/images/users/3.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Priyanka"></li>
                                                <li><img src="../assets/images/users/4.jpg" alt="user" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title="Selina"></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">YOU HAVE 5 NEW MESSAGES</h5>
                                <div class="message-box" id="msg" style="height: 430px;position: relative;">
                                    <div class="message-widget message-scroll">
                                        <!-- Message -->
                                        <a href="javascript:void(0)">
                                            <div class="user-img"> <img src="../assets/images/users/1.jpg" alt="user" class="img-circle"> <span class="profile-status online pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has been.</span> <span class="time">9:30 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)">
                                            <div class="user-img"> <img src="../assets/images/users/2.jpg" alt="user" class="img-circle"> <span class="profile-status busy pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)">
                                            <div class="user-img"> <span class="round">A</span> <span class="profile-status away pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Arijit Sinh</h5> <span class="mail-desc">Simply dummy text of the printing and typesetting industry.</span> <span class="time">9:08 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)">
                                            <div class="user-img"> <img src="../assets/images/users/4.jpg" alt="user" class="img-circle"> <span class="profile-status offline pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)">
                                            <div class="user-img"> <img src="../assets/images/users/1.jpg" alt="user" class="img-circle"> <span class="profile-status online pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">Welcome to the Elite Admin</span> <span class="time">9:30 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)">
                                            <div class="user-img"> <img src="../assets/images/users/2.jpg" alt="user" class="img-circle"> <span class="profile-status busy pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CHAT</h5>
                                <div class="chat-box" id="chat" style="height: 327px;position: relative;">
                                    <!--chat Row -->
                                    <ul class="chat-list">
                                        <!--chat Row -->
                                        <li>
                                            <div class="chat-img"><img src="../assets/images/users/1.jpg" alt="user"></div>
                                            <div class="chat-content">
                                                <h5>James Anderson</h5>
                                                <div class="box bg-light-info">Lorem Ipsum is simply dummy text of the printing &amp; type setting industry.</div>
                                            </div>
                                            <div class="chat-time">10:56 am</div>
                                        </li>
                                        <!--chat Row -->
                                        <li>
                                            <div class="chat-img"><img src="../assets/images/users/2.jpg" alt="user"></div>
                                            <div class="chat-content">
                                                <h5>Bianca Doe</h5>
                                                <div class="box bg-light-info">It’s Great opportunity to work.</div>
                                            </div>
                                            <div class="chat-time">10:57 am</div>
                                        </li>
                                        <!--chat Row -->
                                        <li class="odd">
                                            <div class="chat-content">
                                                <div class="box bg-light-inverse">I would love to join the team.</div>
                                                <br>
                                            </div>
                                            <div class="chat-time">10:58 am</div>
                                        </li>
                                        <!--chat Row -->
                                        <li class="odd">
                                            <div class="chat-content">
                                                <div class="box bg-light-inverse">Whats budget of the new project.</div>
                                                <br>
                                            </div>
                                            <div class="chat-time">10:59 am</div>
                                        </li>
                                        <!--chat Row -->
                                        <li>
                                            <div class="chat-img"><img src="../assets/images/users/3.jpg" alt="user"></div>
                                            <div class="chat-content">
                                                <h5>Angelina Rhodes</h5>
                                                <div class="box bg-light-info">Well we have good budget for the project</div>
                                            </div>
                                            <div class="chat-time">11:00 am</div>
                                        </li>
                                        <!--chat Row -->
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body border-top">
                                <div class="row">
                                    <div class="col-8">
                                        <textarea placeholder="Type your message here" class="form-control border-0"></textarea>
                                    </div>
                                    <div class="col-4 text-end">
                                        <button type="button" class="btn btn-info btn-circle btn-lg text-white"><i class="fas fa-paper-plane"></i> </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Page Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <div class="right-sidebar">
                    <div class="slimscrollright">
                        <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span> </div>
                        <div class="r-panel-body">
                            <ul id="themecolors" class="m-t-20">
                                <li><b>With Light sidebar</b></li>
                                <li><a href="javascript:void(0)" data-skin="skin-default" class="default-theme">1</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-green" class="green-theme">2</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-red" class="red-theme">3</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-blue" class="blue-theme">4</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-purple" class="purple-theme">5</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-megna" class="megna-theme working">6</a></li>
                                <li class="d-block m-t-30"><b>With Dark sidebar</b></li>
                                <li><a href="javascript:void(0)" data-skin="skin-default-dark" class="default-dark-theme ">7</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-green-dark" class="green-dark-theme">8</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-red-dark" class="red-dark-theme">9</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-blue-dark" class="blue-dark-theme">10</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-purple-dark" class="purple-dark-theme">11</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-megna-dark" class="megna-dark-theme ">12</a></li>
                            </ul>
                            <ul class="m-t-20 chatonline">
                                <li><b>Chat option</b></li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/1.jpg" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/2.jpg" alt="user-img" class="img-circle"> <span>Genelia Deshmukh <small class="text-warning">Away</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/3.jpg" alt="user-img" class="img-circle"> <span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/4.jpg" alt="user-img" class="img-circle"> <span>Arijit Sinh <small class="text-muted">Offline</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/5.jpg" alt="user-img" class="img-circle"> <span>Govinda Star <small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/6.jpg" alt="user-img" class="img-circle"> <span>John Abraham<small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/7.jpg" alt="user-img" class="img-circle"> <span>Hritik Roshan<small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/8.jpg" alt="user-img" class="img-circle"> <span>Pwandeep rajan <small class="text-success">online</small></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
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
        <footer class="footer">
            © 2021 Eliteadmin by themedesigner.in
            <a href="https://www.wrappixel.com/">WrapPixel</a>
        </footer>
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
    <script src="../assets/node_modules/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="dist/js/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="dist/js/custom.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!--morris JavaScript -->
    <script src="../assets/node_modules/raphael/raphael-min.js"></script>
    <script src="../assets/node_modules/morrisjs/morris.min.js"></script>
    <script src="../assets/node_modules/jquery-sparkline/jquery.sparkline.min.js"></script>
    <!-- Popup message jquery -->
    <script src="../assets/node_modules/toast-master/js/jquery.toast.js"></script>
    <!-- Chart JS -->
    <script src="dist/js/dashboard1.js"></script>
    <script src="../assets/node_modules/toast-master/js/jquery.toast.js"></script>
    <script>
        $(function(){
            $('#chat, #msg, #comment, #todo').perfectScrollbar();
        });
    </script>
</body>
<body class="horizontal-nav skin-green-dark fixed-layout">
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
<!-- <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="hidden-md-down"><i class="fas fa-language"></i>&nbsp;🌍 Change Language&nbsp;<i class="fa fa-angle-down"></i></span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="languageDropdown" style="min-width: 260px;">
        <li>
            <div id="custom-lang-switcher" class="lang-grid">
                <button class="btn btn-light lang-btn" data-lang="or">OD Odia</button>
                <button class="btn btn-light lang-btn" data-lang="en">🇬🇧 English</button>
                <button class="btn btn-light lang-btn" data-lang="hi">🇮🇳 Hindi</button>
                <button class="btn btn-light lang-btn" data-lang="bn">🇧🇩 Bengali</button>
                <button class="btn btn-light lang-btn" data-lang="ta">🇮🇳 Tamil</button>
                <button class="btn btn-light lang-btn" data-lang="te">🇮🇳 Telugu</button>
                <button class="btn btn-light lang-btn" data-lang="ml">🇮🇳 Malayalam</button>
                <button class="btn btn-light lang-btn" data-lang="gu">🇮🇳 Gujarati</button>
                <button class="btn btn-light lang-btn" data-lang="pa">🇮🇳 Punjabi</button>
                <button class="btn btn-light lang-btn" data-lang="mr">🇮🇳 Marathi</button>
                <button class="btn btn-light lang-btn" data-lang="kn">🇮🇳 Kannada</button>
                <button class="btn btn-light lang-btn" data-lang="as">🇮🇳 Assamese</button>
                <button class="btn btn-light lang-btn" data-lang="ks">🇮🇳 Kashmiri</button>
                <button class="btn btn-light lang-btn" data-lang="kok">🇮🇳 Konkani</button>
                <button class="btn btn-light lang-btn" data-lang="ne">🇮🇳 Nepali</button>
                <button class="btn btn-light lang-btn" data-lang="sd">🇮🇳 Sindhi</button>
                <button class="btn btn-light lang-btn" data-lang="doi">🇮🇳 Dogri</button>
                <button class="btn btn-light lang-btn" data-lang="mai">🇮🇳 Maithili</button>
                <button class="btn btn-light lang-btn" data-lang="bho">🇮🇳 Bhojpuri</button>
                <button class="btn btn-light lang-btn" data-lang="mni">🇮🇳 Meitei (Manipuri)</button>
                <button class="btn btn-light lang-btn" data-lang="sat">🇮🇳 Santali</button>
                <button class="btn btn-light lang-btn" data-lang="brx">🇮🇳 Bodo</button>
                <button class="btn btn-light lang-btn" data-lang="trp">🇮🇳 Tripuri (Kokborok)</button>
                <button class="btn btn-light lang-btn" data-lang="lus">🇮🇳 Mizo (Lushai)</button>
                <button class="btn btn-light lang-btn" data-lang="hne">🇮🇳 Chhattisgarhi</button>
                <button class="btn btn-light lang-btn" data-lang="raj">🇮🇳 Rajasthani</button>
                <button class="btn btn-light lang-btn" data-lang="mag">🇮🇳 Magahi</button>
                <button class="btn btn-light lang-btn" data-lang="awa">🇮🇳 Awadhi</button>
                <button class="btn btn-light lang-btn" data-lang="ang">🇮🇳 Angika</button>
                <button class="btn btn-light lang-btn" data-lang="ho">🇮🇳 Ho</button>
                <button class="btn btn-light lang-btn" data-lang="kur">🇮🇳 Kurukh</button>
                <button class="btn btn-light lang-btn" data-lang="gon">🇮🇳 Gondi</button>
                <button class="btn btn-light lang-btn" data-lang="tcy">🇮🇳 Tulu</button>
                <button class="btn btn-light lang-btn" data-lang="bh">🇮🇳 Bhili/Bhilodi</button>
                <button class="btn btn-light lang-btn" data-lang="sa">🇮🇳 Sanskrit</button>
            </div>
            <div id="google_translate_element" style="display:none;"></div>
        </li>
    </ul>
</li> -->
<li class="nav-item dropdown mega-dropdown">
  <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-language"></i>&nbsp;🌍 Change Language&nbsp;<i class="fa fa-angle-down"></i>
</a>

<div class="dropdown-menu animated bounceInDown p-4" style="min-width: 700px;">
    <ul class="mega-dropdown-menu row">

      <!-- Left Column: Language Info -->
      <li class="col-lg-3 col-md-12 mb-3">
        <h4 class="mb-3"><i class="fas fa-globe"></i> Select Your Language</h4>
        <p class="text-muted small">
          Choose your preferred language from the list.  
          The page will automatically translate.
      </p>
      <div id="google_translate_element" style="display:none;"></div>
  </li>

  <!-- Right Column: Language Buttons -->
  <li class="col-lg-9 col-md-12">
    <div id="custom-lang-switcher" class="lang-grid">
      <button class="btn btn-light lang-btn" data-lang="or">OD Odia</button>
      <button class="btn btn-light lang-btn" data-lang="en">🇬🇧 English</button>
      <button class="btn btn-light lang-btn" data-lang="hi">🇮🇳 Hindi</button>
      <button class="btn btn-light lang-btn" data-lang="bn">🇧🇩 Bengali</button>
      <button class="btn btn-light lang-btn" data-lang="ta">🇮🇳 Tamil</button>
      <button class="btn btn-light lang-btn" data-lang="te">🇮🇳 Telugu</button>
      <button class="btn btn-light lang-btn" data-lang="ml">🇮🇳 Malayalam</button>
      <button class="btn btn-light lang-btn" data-lang="gu">🇮🇳 Gujarati</button>
      <button class="btn btn-light lang-btn" data-lang="pa">🇮🇳 Punjabi</button>
      <button class="btn btn-light lang-btn" data-lang="mr">🇮🇳 Marathi</button>
      <button class="btn btn-light lang-btn" data-lang="kn">🇮🇳 Kannada</button>
      <button class="btn btn-light lang-btn" data-lang="as">🇮🇳 Assamese</button>
      <button class="btn btn-light lang-btn" data-lang="ks">🇮🇳 Kashmiri</button>
      <button class="btn btn-light lang-btn" data-lang="kok">🇮🇳 Konkani</button>
      <button class="btn btn-light lang-btn" data-lang="ne">🇮🇳 Nepali</button>
      <button class="btn btn-light lang-btn" data-lang="sd">🇮🇳 Sindhi</button>
      <button class="btn btn-light lang-btn" data-lang="doi">🇮🇳 Dogri</button>
      <button class="btn btn-light lang-btn" data-lang="mai">🇮🇳 Maithili</button>
      <button class="btn btn-light lang-btn" data-lang="bho">🇮🇳 Bhojpuri</button>
      <button class="btn btn-light lang-btn" data-lang="mni">🇮🇳 Meitei (Manipuri)</button>
      <button class="btn btn-light lang-btn" data-lang="sat">🇮🇳 Santali</button>
      <button class="btn btn-light lang-btn" data-lang="brx">🇮🇳 Bodo</button>
      <button class="btn btn-light lang-btn" data-lang="trp">🇮🇳 Tripuri (Kokborok)</button>
      <button class="btn btn-light lang-btn" data-lang="lus">🇮🇳 Mizo (Lushai)</button>
      <button class="btn btn-light lang-btn" data-lang="hne">🇮🇳 Chhattisgarhi</button>
      <button class="btn btn-light lang-btn" data-lang="raj">🇮🇳 Rajasthani</button>
      <button class="btn btn-light lang-btn" data-lang="mag">🇮🇳 Magahi</button>
      <button class="btn btn-light lang-btn" data-lang="awa">🇮🇳 Awadhi</button>
      <button class="btn btn-light lang-btn" data-lang="ang">🇮🇳 Angika</button>
      <button class="btn btn-light lang-btn" data-lang="ho">🇮🇳 Ho</button>
      <button class="btn btn-light lang-btn" data-lang="kur">🇮🇳 Kurukh</button>
      <button class="btn btn-light lang-btn" data-lang="gon">🇮🇳 Gondi</button>
      <button class="btn btn-light lang-btn" data-lang="tcy">🇮🇳 Tulu</button>
      <button class="btn btn-light lang-btn" data-lang="bh">🇮🇳 Bhili/Bhilodi</button>
      <button class="btn btn-light lang-btn" data-lang="sa">🇮🇳 Sanskrit</button>
  </div>
</li>
</ul>
</div>
</li>


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
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }

    function changeLanguage(lang) {
        var select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = lang;
            select.dispatchEvent(new Event('change'));
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                changeLanguage(this.dataset.lang);
            });
        });
    });
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script type="text/javascript">
    function triggerHtmlEvent(element, eventName) {
        var event;
        if (document.createEvent) {
            event = document.createEvent('HTMLEvents');
            event.initEvent(eventName, true, true);
            element.dispatchEvent(event);
        } else {
            event = document.createEventObject();
            element.fireEvent('on' + eventName, event);
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        var match = document.cookie.match('(^|;) ?googtrans=([^;]*)(;|$)');
        if (match && match[2] !== '/auto/en') {
            var lang = match[2].split('/').pop();
            setTimeout(function () {
                var select = document.querySelector('.goog-te-combo');
                if (select) {
                    select.value = lang;
                    triggerHtmlEvent(select, 'change');
                }
            }, 1000);
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
      const langButtons = document.querySelectorAll(".lang-btn");

      langButtons.forEach(button => {
        button.addEventListener("click", function() {
          langButtons.forEach(btn => btn.classList.remove("active"));
          this.classList.add("active");

          const select = document.querySelector(".goog-te-combo");
          if (select) {
            const langValue = this.getAttribute("data-lang");
            select.value = langValue;
            select.dispatchEvent(new Event("change"));
        }
    });
    });

      const selectedLang = getCookie("googtrans")?.split("/").pop();
      if (selectedLang) {
        langButtons.forEach(btn => {
          if (btn.getAttribute("data-lang") === selectedLang) {
            btn.classList.add("active");
        }
    });
    }

    function getCookie(name) {
        const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
        return match ? match[2] : null;
    }
});
</script>

</body>
</html>