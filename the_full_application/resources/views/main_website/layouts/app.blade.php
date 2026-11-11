<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      
      <!-- SEO & Metadata -->
      <title>@yield('title', 'SSEPD-IT Digital Gateway - Government of Odisha')</title>
      <meta name="description" content="@yield('meta_description', 'Centralized digital gateway and command centre for Social Security & Empowerment of Persons with Disabilities Department (SSEPD-IT), Government of Odisha. Access pensions, institution portals, and welfare services.')">
      <meta name="keywords" content="@yield('meta_keywords', 'SSEPD-IT, SSEPD Odisha, Odisha Government, Disability Welfare Odisha, Pension Portal Odisha, e-Governance Odisha, Digital India, Konark Wheel')">
      <meta name="author" content="SSEPD IT Cell, Government of Odisha">
      <meta name="robots" content="index, follow">
      
      <!-- Open Graph Tags -->
      <meta property="og:title" content="@yield('og_title', 'SSEPD-IT Digital Gateway - Government of Odisha')">
      <meta property="og:description" content="@yield('og_description', 'Official unified gateway for the Social Security & Empowerment of Persons with Disabilities Department (SSEPD-IT), Odisha.')">
      <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
      <meta property="og:type" content="website">
      
      <!-- Favicon -->
      <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
      
      <!-- Fonts: Poppins (Headings) & Inter (Body) -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
      
      <!-- Font Awesome 6 Icons -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
      
      <!-- Bootstrap 5 CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      
      <!-- Main Website CSS -->
      <link rel="stylesheet" href="{{ asset('css/main-website.css') }}">
      
      <!-- Page-specific Styles -->
      @yield('style')
      @stack('styles')
   </head>
   <body>
      <!-- Top Accessibility Toolbar -->
      @include('main_website.layouts.partials.accessibility-toolbar')

      <!-- Tricolor Ribbon for National Identity -->
      @include('main_website.layouts.partials.tricolor-ribbon')

      <!-- Sticky Navigation Header -->
      @include('main_website.layouts.partials.navbar')

      <!-- Main Content Area -->
      <main id="main-content" role="main">
         @yield('content')
      </main>

      <!-- Toast Container for Notifications -->
      <div id="toastContainer" class="toast-container-custom" aria-live="polite"></div>

      <!-- Bootstrap 5 JS Bundle (Includes Popper) -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

      <!-- Common Shared JavaScript Logic -->
      <script>
         // ==========================================================================
         // Shared Main Website Logic (Themes, Accessibility, Mobile Nav, Toasts)
         // ==========================================================================
         document.addEventListener("DOMContentLoaded", () => {
             // Language selection listener helper
             const langSelect = document.getElementById("langSelect");
             if (langSelect) {
                 langSelect.addEventListener("change", (e) => {
                     if (typeof handleLanguageChange === "function") {
                         handleLanguageChange(e.target.value);
                     }
                 });
             }
         });

         // Dark Mode Toggle
         function toggleDarkMode() {
             document.body.classList.toggle('dark-mode');
             document.body.classList.remove('high-contrast');
             
             const btn = document.getElementById('darkModeToggle');
             if (document.body.classList.contains('dark-mode')) {
                 if (btn) {
                     btn.innerHTML = '<i class="fa-solid fa-sun"></i>';
                     btn.setAttribute("aria-label", "Toggle Light Mode");
                 }
                 showToast("Dark Mode enabled successfully.");
             } else {
                 if (btn) {
                     btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
                     btn.setAttribute("aria-label", "Toggle Dark Mode");
                 }
                 showToast("Light Mode enabled successfully.");
             }
         }

         // High Contrast Mode Toggle
         function toggleContrast() {
             document.body.classList.toggle('high-contrast');
             document.body.classList.remove('dark-mode');
             
             const btn = document.getElementById('darkModeToggle');
             if (btn) {
                 btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
                 btn.setAttribute("aria-label", "Toggle Dark Mode");
             }
             
             if (document.body.classList.contains('high-contrast')) {
                 showToast("High Contrast accessibility theme enabled.");
             } else {
                 showToast("Standard contrast theme restored.");
             }
         }

         // Text Resizing Controls
         function adjustTextSize(mode) {
             document.body.classList.remove('text-scale-up', 'text-scale-down');
             if (mode === 'increase') {
                 document.body.classList.add('text-scale-up');
                 showToast("Text size scaled up.");
             } else if (mode === 'decrease') {
                 document.body.classList.add('text-scale-down');
                 showToast("Text size scaled down.");
             } else {
                 showToast("Text size reset to default.");
             }
         }

         // Mobile Nav Toggle
         function toggleMobileMenu() {
             const nav = document.getElementById("navLinks");
             if (!nav) return;
             nav.classList.toggle("show");
             
             const btn = document.getElementById("mobileMenuToggle");
             if (!btn) return;
             if (nav.classList.contains("show")) {
                 btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
             } else {
                 btn.innerHTML = '<i class="fa-solid fa-bars"></i>';
             }
         }

         // Search Focus or Redirect
         function focusSearch() {
             const searchInput = document.getElementById("serviceSearch");
             if (searchInput) {
                 searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 setTimeout(() => {
                     searchInput.focus();
                 }, 400);
             } else {
                 window.location.href = "{{ url('/#services') }}";
             }
         }

         // Custom Toast Notifications Framework
         function showToast(message, isError = false) {
             let container = document.getElementById("toastContainer");
             if (!container) {
                 container = document.createElement("div");
                 container.id = "toastContainer";
                 container.className = "toast-container-custom";
                 document.body.appendChild(container);
             }
             
             const toast = document.createElement("div");
             toast.className = `custom-toast ${isError ? 'error' : ''}`;
             toast.innerHTML = `
                 <i class="fa-solid ${isError ? 'fa-triangle-exclamation' : 'fa-circle-check'}"></i>
                 <span>${message}</span>
             `;
             
             container.appendChild(toast);
             
             setTimeout(() => {
                 toast.classList.add("show");
             }, 50);
             
             setTimeout(() => {
                 toast.classList.remove("show");
                 setTimeout(() => {
                     if (toast.parentElement) {
                         toast.parentElement.removeChild(toast);
                     }
                 }, 300);
             }, 3500);
         }
      </script>

      <!-- Page-specific Scripts -->
      @yield('script')
      @stack('scripts')
   </body>
</html>

