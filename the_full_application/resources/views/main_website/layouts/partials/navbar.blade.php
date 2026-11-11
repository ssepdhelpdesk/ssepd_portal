<!-- Sticky Navigation Header -->
<header class="sticky-header" role="banner">
   <div class="header-container">
      <div class="header-logo-group">
         <!-- Government of Odisha emblem -->
         <!-- <img src="images/logo.png" class="emblem-logo" alt="Government of Odisha Emblem" onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/f/f6/Seal_of_Odisha.svg';"> -->
         <a href="{{url('/')}}"> <div class="header-title-block">
            <span class="header-main-title">SSEPD-IT</span>
            <span class="header-subtitle">Social Security & Empowerment of Persons with Disabilities Department, Government of Odisha</span>
         </div> </a>
      </div>
      <nav role="navigation" aria-label="Primary Navigation">
         <ul class="nav-links" id="navLinks">
            <li class="nav-item {{ request()->is('/') ? 'active' : '' }}"><a href="{{ request()->is('/') ? '#home' : url('/#home') }}">Home</a></li>
            <li class="nav-item"><a href="{{ request()->is('/') ? '#services' : url('/#services') }}">Portals</a></li>
            <!-- <li class="nav-item"><a href="#institutions">Institution Portal</a></li>
            <li class="nav-item"><a href="#about">Resources</a></li>
               <li class="nav-item"><a href="#contact">Contact</a></li> -->
         </ul>
      </nav>
      <div class="header-controls">
         <button onclick="focusSearch()" class="search-trigger-btn" aria-label="Focus search services" id="searchTriggerBtn">
         <i class="fa-solid fa-magnifying-glass"></i>
         </button>
         <button onclick="toggleDarkMode()" class="dark-mode-toggle" aria-label="Toggle Dark Mode" id="darkModeToggle">
         <i class="fa-solid fa-moon"></i>
         </button>
         <button class="mobile-nav-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Mobile Menu" id="mobileMenuToggle">
         <i class="fa-solid fa-bars"></i>
         </button>
      </div>
   </div>
</header>

