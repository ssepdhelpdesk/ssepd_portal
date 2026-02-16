<header class="header-two"> 
            <div class="container">
                <div class="header-nav">
                    <div class="navbar-header">
                        <a id="mobile_btn" href="javascript:void(0);">
                            <span class="bar-icon">
                                <i class="isax isax-menu"></i>
                            </span>
                        </a>
                        <div class="navbar-logo">
                            <a class="logo-white header-logo" href="{{ route('website.pensioners.index') }}">
                                <img src="{{ asset('website_assets/assets/img/logo.svg') }}" class="logo" alt="Logo">
                            </a>
                            <a class="logo-dark header-logo" href="{{ route('website.pensioners.index') }}">
                                <img src="{{ asset('website_assets/assets/img/logo.svg') }}" class="logo" alt="Logo">
                            </a>
                        </div>
                    </div>
                    <div class="main-menu-wrapper">                             
                        <div class="menu-header">
                            <a href="{{ route('website.pensioners.index') }}" class="menu-logo">
                                <img src="{{ asset('website_assets/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                            </a>
                            <a id="menu_close" class="menu-close" href="javascript:void(0);">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        <ul class="main-nav">
                            <li class="has-submenu megamenu active">
                                <a href="{{ route('website.pensioners.index') }}">Home </a>
                            </li>
                            <li class="has-submenu">
                                <a href="#">Beneficiary Services <i class="fas fa-chevron-down"></i></a>
                                <ul class="submenu">
                                    <li class="has-submenu">
                                        <a href="javascript:void(0);">Pension</a>
                                        <ul class="submenu">
                                            <li><a href="{{route('website.pension.index')}}">Apply Pension</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{route('website.pension.index')}}">Apply Pension</a></li>
                                </ul>
                            </li>
                        </ul>
                        
                        <div class="menu-dropdown">
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Light
                                </a>
                                <ul class="dropdown-menu p-2 mt-2">
                                    <li><a class="dropdown-item rounded" href="javascript:void(0);">Light</a></li>
                                    <li><a class="dropdown-item rounded" href="javascript:void(0);">Dark</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="menu-login">
                            <a href="{{route('login')}}" class="btn btn-primary w-100 mb-2"><i class="isax isax-user me-2"></i>Department Login</a>
                            <a href="{{route('login')}}" class="btn btn-secondary w-100"><i class="isax isax-user-edit me-2"></i>Sign IN</a>
                        </div>
                    </div>
                    <div class="header-btn d-flex align-items-center">                          
                            
                        <div class="icon-btn me-2">
                            <a href="javascript:void(0);" id="dark-mode-toggle" class="theme-toggle activate">
                                <i class="isax isax-sun-15"></i>
                            </a>
                            <a href="javascript:void(0);" id="light-mode-toggle" class="theme-toggle">
                                <i class="isax isax-moon"></i>
                            </a>
                        </div>
                        <a href="{{route('login')}}" class="btn btn-primary d-inline-flex align-items-center me-2">
                            <i class="isax isax-lock-circle me-2"></i>Department Login
                        </a>
                        <a href="{{route('login')}}" class="btn btn-secondary me-0">
                            <i class="isax isax-user-edit me-2"></i>Sign IN
                        </a>
                    </div>
                </div>
            </div>
        </header>