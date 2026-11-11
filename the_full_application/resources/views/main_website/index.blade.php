@extends('main_website.layouts.app')

@section('content')
<!-- HERO SECTION: Centralized Gateway Command Centre -->
<section id="home" class="hero-section" aria-label="Introduction Command Centre">
   <div class="temple-border-top"></div>
   <!-- Interactive Floating Particles Canvas -->
   <canvas id="particleCanvas"></canvas>
   <!-- Background watermark: Detailed Konark Wheel -->
   <svg class="konark-watermark" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
      <circle cx="100" cy="100" r="95" stroke="currentColor" stroke-width="3" fill="none" />
      <circle cx="100" cy="100" r="85" stroke="currentColor" stroke-width="1.5" fill="none" />
      <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="1" stroke-dasharray="2 3" fill="none" />
      <circle cx="100" cy="100" r="30" stroke="currentColor" stroke-width="2" fill="none" />
      <circle cx="100" cy="100" r="24" stroke="currentColor" stroke-width="1" fill="none" />
      <circle cx="100" cy="100" r="10" fill="none" stroke="currentColor" stroke-width="2" />
      <circle cx="100" cy="100" r="5" fill="currentColor" />
      <!-- 8 Main Spokes -->
      <line x1="100" y1="15" x2="100" y2="70" stroke="currentColor" stroke-width="2.5" />
      <line x1="100" y1="130" x2="100" y2="185" stroke="currentColor" stroke-width="2.5" />
      <line x1="15" y1="100" x2="70" y2="100" stroke="currentColor" stroke-width="2.5" />
      <line x1="130" y1="100" x2="185" y2="100" stroke="currentColor" stroke-width="2.5" />
      <line x1="40" y1="40" x2="79" y2="79" stroke="currentColor" stroke-width="2.5" />
      <line x1="121" y1="121" x2="160" y2="160" stroke="currentColor" stroke-width="2.5" />
      <line x1="160" y1="40" x2="121" y2="79" stroke="currentColor" stroke-width="2.5" />
      <line x1="79" y1="121" x2="40" y2="160" stroke="currentColor" stroke-width="2.5" />
      <!-- 8 Secondary Spokes -->
      <g transform="rotate(22.5, 100, 100)">
         <line x1="100" y1="15" x2="100" y2="70" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 1" />
         <line x1="100" y1="130" x2="100" y2="185" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 1" />
         <line x1="15" y1="100" x2="70" y2="100" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 1" />
         <line x1="130" y1="100" x2="185" y2="100" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 1" />
      </g>
   </svg>
   <div class="hero-grid">
      <!-- Left: Portal Introduction -->
      <div class="hero-text-block">
         <!-- <div class="badge-gov-initiative" role="text">
            <i class="fa-solid fa-circle-check"></i> Official Flagship e-Governance Platform
         </div> -->
         <h1 class="hero-title">
            SSEPD-<span>IT</span> <!-- <br><span>Digital Gateway</span> -->
         </h1>
         <!-- <p class="hero-subtitle">
            A unified platform for all internal operations and services.
         </p> -->
         <div class="hero-ctas">
            <a class="btn-cta btn-cta-primary"
               href="{{route('login')}}"
               aria-label="Department Login">
            <i class="fa-solid fa-building-flag"></i>
            Department Login
            </a>
            <a class="btn-cta btn-cta-secondary"
               href="{{route('pia_institute_login')}}"
               aria-label="Institute Login">
            <i class="fa-solid fa-graduation-cap"></i>
            Institute Login
            </a>
         </div>
         <!-- <div class="hero-ctas">
            <button class="btn-cta btn-cta-primary" onclick="openLoginModal('department')" aria-label="Department Login">
                <i class="fa-solid fa-building-flag"></i> Department Login
            </button>
            <button class="btn-cta btn-cta-secondary" onclick="openLoginModal('institute')" aria-label="Institute Login">
                <i class="fa-solid fa-graduation-cap"></i> Institute Login
            </button>
            </div> -->
         <div class="hero-tagline">
            <span></span> Secure Access <span></span> e-Governance 
         </div>
      </div>
      <!-- Right: Official Carousel Slider -->
      <div id="heroCarousel" class="carousel slide carousel-fade hero-monitor-card p-3" data-bs-ride="carousel" aria-label="Official SSEPD Home Carousel">
         <div class="carousel-indicators" style="margin-bottom: 20px;">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="6" aria-label="Slide 7"></button>
         </div>
         <div class="carousel-inner" style="border-radius: 12px; overflow: hidden; height: 320px; box-shadow: var(--shadow-sm);">
            <div class="carousel-item active" style="height: 100%;">
               <img src="https://ssepd.odisha.gov.in/sites/default/files/styles/theme_six_slider/public/2026-03/WhatsApp%20Image%202026-03-18%20at%2017.35.59.jpeg?itok=rvzNOM_k" class="d-block w-100 h-100" style="object-fit: cover;" alt="SSEPD Department Activity Banner 1">
            </div>
            <div class="carousel-item" style="height: 100%;">
               <img src="https://ssepd.odisha.gov.in/sites/default/files/styles/theme_six_slider/public/2026-03/Desktop%20-%204.jpg.jpeg?itok=8SMnod7z" class="d-block w-100 h-100" style="object-fit: cover;" alt="SSEPD Department Activity Banner 2">
            </div>
            <div class="carousel-item" style="height: 100%;">
               <img src="https://ssepd.odisha.gov.in/sites/default/files/styles/theme_six_slider/public/2026-03/WhatsApp%20Image%202026-03-19%20at%2015.23.03_1.jpeg?itok=SPGmhUxY" class="d-block w-100 h-100" style="object-fit: cover;" alt="SSEPD Department Activity Banner 3">
            </div>
            <div class="carousel-item" style="height: 100%;">
               <img src="https://ssepd.odisha.gov.in/sites/default/files/styles/theme_six_slider/public/2022-11/002.jpg?itok=8rZOfi9V" class="d-block w-100 h-100" style="object-fit: cover;" alt="SSEPD Department Activity Banner 4">
            </div>
            <div class="carousel-item" style="height: 100%;">
               <img src="https://ssepd.odisha.gov.in/sites/default/files/styles/theme_six_slider/public/2022-11/001.jpg?itok=QwE6x4t-" class="d-block w-100 h-100" style="object-fit: cover;" alt="SSEPD Department Activity Banner 5">
            </div>
            <div class="carousel-item" style="height: 100%;">
               <img src="https://ssepd.odisha.gov.in/sites/default/files/styles/theme_six_slider/public/2022-10/banner-3.jpg?itok=kjGza6_b" class="d-block w-100 h-100" style="object-fit: cover;" alt="SSEPD Department Activity Banner 6">
            </div>
            <div class="carousel-item" style="height: 100%;">
               <img src="https://ssepd.odisha.gov.in/sites/default/files/styles/theme_six_slider/public/2022-09/sd-slide-02.jpg?itok=biSf5ZHg" class="d-block w-100 h-100" style="object-fit: cover;" alt="SSEPD Department Activity Banner 7">
            </div>
         </div>
         <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
         <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(11, 61, 145, 0.6); border-radius: 50%; padding: 12px; background-size: 50%;"></span>
         <span class="visually-hidden">Previous</span>
         </button>
         <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
         <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(11, 61, 145, 0.6); border-radius: 50%; padding: 12px; background-size: 50%;"></span>
         <span class="visually-hidden">Next</span>
         </button>
      </div>
   </div>
</section>

<!-- QUICK SERVICES SECTION: Central Gateway Grid -->
<section id="services" class="section-padding reveal-element" aria-label="Portals">
   <div class="section-header">
      <h2>Portals</h2>
      <!-- <p class="text-muted mb-0">Navigate and branch out to individual application portals, registries, and management cells.</p> -->
      <div class="section-title-line"></div>
   </div>
   <!-- Service Filter Bar -->
   <div class="search-filter-container">
      <i class="fa-solid fa-magnifying-glass search-icon-inside"></i>
      <input type="text" id="serviceSearch" class="search-filter-input" placeholder="Search portals or services (e.g. Pension, School, Old Age)..." onkeyup="filterServices()" aria-label="Search portals or services">
   </div>
   <!-- Services Grid -->
   <div class="services-grid" id="servicesGrid">
      <!-- SSEPD Portal -->
      <div class="service-card" data-keywords="ssepd portal main department registration beneficiary">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-globe"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">SSEPD-IT Portal</h3>
            <!-- <p class="service-desc">A unified platform for all internal operations and services.</p> -->
         </div>
         <a href="/SSEPD_REPO/ssepd_portal/" target="_blank"><button class="btn-service-action" aria-label="Open SSEPD Portal">
         Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- Enhanced Pensioner Portal -->
      <div class="service-card" data-keywords="enhanced pensioner portal pension social security money transfer mbpy">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-indian-rupee-sign"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">Enhanced Pensioner Portal</h3>
            <!-- <p class="service-desc">A dedicated platform for pensioners aged 80 and above, or with 80% or more disability.</p> -->
         </div>
         <a href="/SSEPD_REPO/old_age_and_disability_beneficiaries" target="_blank"><button class="btn-service-action" aria-label="Open Enhanced Pensioner Portal">
         Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- SIEP Portal -->
      <div class="service-card" data-keywords="siep portal state institute empowerment disabilities training">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-handshake-angle"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">SIEP Portal</h3>
            <!-- <p class="service-desc">Manodaya, aims for the overall development of PwDs in Odisha</p> -->
         </div>
         <a href="https://siep.ssepdit.in" target="_blank"><button class="btn-service-action" aria-label="Open SIEP Portal">
         Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- Institute Management Portal -->
      <div class="service-card" data-keywords="institute management portal ngo tracker vendor audit records">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-network-wired"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">Institute Management Portal</h3>
            <!-- <p class="service-desc">A unified platform for all institutions functioning under the SSEPD Department.</p> -->
         </div>
         <a href="{{route('pia_institute_login')}}" target="_blank"><button class="btn-service-action" aria-label="Open Institute Management Portal">
         Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- Special Schools Portal -->
      <div class="service-card" data-keywords="special schools portal child mapping education disabilities">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-school"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">Special Schools Portal</h3>
            <!-- <p class="service-desc">A unified platform for all Special School functioning under the SSEPD Department.</p> -->
         </div>
         <a href="{{route('pia_institute_login')}}" target="_blank"><button class="btn-service-action" aria-label="Open Special Schools Portal">
         Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- Old Age Home Portal -->
      <div class="service-card" data-keywords="old age home portal senior citizen shelter welfare">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-person-cane"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">Old Age Home Portal</h3>
            <!-- <p class="service-desc">A unified platform for all Old Age Home functioning under the SSEPD Department.</p> -->
         </div>
         <a href="{{route('pia_institute_login')}}" target="_blank"><button class="btn-service-action" aria-label="Open Old Age Home Portal">
         Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- Therapeutic Centre Portal -->
      <div class="service-card" data-keywords="therapeutic centre portal clinics assessment physio occupational therapy">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-heart-pulse"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">Therapeutic Centre Portal</h3>
            <!-- <p class="service-desc">A unified platform for all Therapeutic Centre functioning under the SSEPD Department.</p> -->
         </div>
         <a href="{{route('pia_institute_login')}}" target="_blank"><button class="btn-service-action" aria-label="Open Therapeutic Centre Portal">
         Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- Disability Day 2026 – Stall Registration -->
      <div class="service-card" data-keywords="disability day 2026 stall registration exhibition event soa university persons with disabilities">
         <div class="service-icon-wrap">
            <i class="fa-solid fa-store"></i>
         </div>
         <div class="service-details">
            <h3 class="service-title">Disability Day 2026 – Stall Registration</h3>
            <p class="service-desc">Register your organization for stall participation in the International Day of Persons with Disabilities – 2026 event at S'O'A University, scheduled from 01 December to 03 December 2026.</p>
         </div>
         <a href="{{ route('disability_day.registration') }}"><button class="btn-service-action" aria-label="Open Disability Day 2026 Stall Registration">
         Register Stall <i class="fa-solid fa-arrow-right-long"></i>
         </button></a>
      </div>
      <!-- Reports & Dashboard -->
      <!-- <div class="service-card" data-keywords="reports dashboard analytics data count analytics state command metrics">
         <div class="service-icon-wrap">
             <i class="fa-solid fa-chart-pie"></i>
         </div>
         <div class="service-details">
             <h3 class="service-title">Reports & Dashboard</h3>
             <p class="service-desc">Aggregated statistics, visual metrics, performance monitoring system (PMS) targets, and district ranking dashboards.</p>
         </div>
         <button class="btn-service-action" aria-label="Open Reports & Dashboard">
             Open Portal <i class="fa-solid fa-arrow-right-long"></i>
         </button>
         </div> -->
   </div>
</section>
@endsection

@section('script')
<!-- Vanilla Javascript Logic for Homepage -->
<script>
   // ==========================================================================
   // Constants & Global State
   // ==========================================================================
   let deptModalObj = null;
   let instModalObj = null;
   let generatedDeptCaptcha = "";
   let generatedInstCaptchaAnswer = 0;
   let generatedDeptOtp = "";

   // ==========================================================================
   // Initializations on DOM Loaded
   // ==========================================================================
   document.addEventListener("DOMContentLoaded", () => {
       // Initialize Bootstrap modal instances if present
       const deptModalEl = document.getElementById('deptLoginModal');
       if (deptModalEl) {
           deptModalObj = new bootstrap.Modal(deptModalEl);
       }
       const instModalEl = document.getElementById('instLoginModal');
       if (instModalEl) {
           instModalObj = new bootstrap.Modal(instModalEl);
       }

       // Launch floating particle canvas
       initParticles();

       // Set up scroll triggers for animations and stats
       initScrollAnimations();

       // Insert random updates into Command Centre Monitor
       initLiveMonitorFeed();
   });

   // ==========================================================================
   // Dynamic Quick Services Filter Logic
   // ==========================================================================
   function filterServices() {
       const query = document.getElementById("serviceSearch").value.toLowerCase();
       const cards = document.querySelectorAll(".service-card");
       
       cards.forEach(card => {
           const keywords = card.getAttribute("data-keywords") ? card.getAttribute("data-keywords").toLowerCase() : "";
           const titleEl = card.querySelector(".service-title");
           const title = titleEl ? titleEl.innerText.toLowerCase() : "";
           const descEl = card.querySelector(".service-desc");
           const desc = descEl ? descEl.innerText.toLowerCase() : "";
           
           if (keywords.includes(query) || title.includes(query) || desc.includes(query)) {
               card.style.display = "flex";
           } else {
               card.style.display = "none";
           }
       });
   }

   // ==========================================================================
   // Floating Background Particle Canvas Animation
   // ==========================================================================
   function initParticles() {
       const canvas = document.getElementById('particleCanvas');
       if (!canvas) return;
       const ctx = canvas.getContext('2d');
       let particles = [];
       
       function resizeCanvas() {
           canvas.width = canvas.parentElement.offsetWidth;
           canvas.height = canvas.parentElement.offsetHeight;
       }
       resizeCanvas();
       window.addEventListener('resize', resizeCanvas);
       
       class Particle {
           constructor() {
               this.x = Math.random() * canvas.width;
               this.y = Math.random() * canvas.height;
               this.size = Math.random() * 2 + 1;
               this.speedX = Math.random() * 0.4 - 0.2;
               this.speedY = Math.random() * 0.4 - 0.2;
               this.opacity = Math.random() * 0.5 + 0.1;
           }
           update() {
               this.x += this.speedX;
               this.y += this.speedY;
               if (this.x > canvas.width) this.x = 0;
               if (this.x < 0) this.x = canvas.width;
               if (this.y > canvas.height) this.y = 0;
               if (this.y < 0) this.y = canvas.height;
           }
           draw() {
               ctx.fillStyle = `rgba(11, 61, 145, ${this.opacity})`;
               if (document.body.classList.contains('dark-mode')) {
                   ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity * 0.6})`;
               }
               ctx.beginPath();
               ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
               ctx.fill();
           }
       }
       
       for (let i = 0; i < 40; i++) {
           particles.push(new Particle());
       }
       
       function animate() {
           ctx.clearRect(0, 0, canvas.width, canvas.height);
           particles.forEach(p => {
               p.update();
               p.draw();
           });
           requestAnimationFrame(animate);
       }
       animate();
   }

   // ==========================================================================
   // Live Activity Command Centre Monitor Feed Simulator
   // ==========================================================================
   function initLiveMonitorFeed() {
       const feed = document.getElementById("monitorActivityList");
       if (!feed) return;
       
       const actions = [
           { district: "Khordha", msg: "New Special School compliance certificate uploaded" },
           { district: "Cuttack", msg: "Statewide pension distribution API synched" },
           { district: "Ganjam", msg: "Applied aid: Tricycle requested by Beneficiary #4829" },
           { district: "Balasore", msg: "Old Age Home inspect validation - Success" },
           { district: "Sambalpur", msg: "ARC robot physiotherapy metric updated" },
           { district: "Koraput", msg: "Therapy record generated by Center #08" },
           { district: "Puri", msg: "New NGO partnership verified for OAH operational grants" }
       ];

       setInterval(() => {
           const act = actions[Math.floor(Math.random() * actions.length)];
           const item = document.createElement("div");
           item.className = "monitor-activity-item";
           item.style.opacity = "0";
           item.style.transform = "translateX(-15px)";
           item.style.transition = "all 0.5s ease";
           
           item.innerHTML = `
           <div class="activity-meta">
               <span>${act.district} District</span>
               <span>Just Now</span>
           </div>
           <div class="activity-desc">${act.msg}</div>
           `;
           
           feed.insertBefore(item, feed.firstChild);
           
           if (feed.childNodes.length > 5) {
               feed.removeChild(feed.lastChild);
           }

           setTimeout(() => {
               item.style.opacity = "1";
               item.style.transform = "translateX(0)";
           }, 50);
       }, 6000);
   }

   // ==========================================================================
   // Intersection Observer Scroll Animations & Statistics Counter
   // ==========================================================================
   function initScrollAnimations() {
       const revealElements = document.querySelectorAll(".reveal-element");
       
       const observer = new IntersectionObserver((entries) => {
           entries.forEach(entry => {
               if (entry.isIntersecting) {
                   entry.target.classList.add("active");
                   
                   if (entry.target.classList.contains("impact-section") || entry.target.querySelector(".impact-grid")) {
                       triggerStatsCounters();
                   }
               }
           });
       }, { threshold: 0.15 });

       revealElements.forEach(el => observer.observe(el));
       
       const checkObserver = new IntersectionObserver((entries) => {
           entries.forEach(entry => {
               if (entry.isIntersecting && entry.target.id === "impactStatsGrid") {
                   triggerStatsCounters();
               }
           });
       }, { threshold: 0.1 });
       
       const statsGrid = document.getElementById("impactStatsGrid");
       if (statsGrid) checkObserver.observe(statsGrid);
   }

   let countersAnimated = false;
   function triggerStatsCounters() {
       if (countersAnimated) return;
       countersAnimated = true;
       
       const numbers = document.querySelectorAll(".impact-number");
       numbers.forEach(num => {
           const target = parseFloat(num.getAttribute("data-target"));
           const suffix = num.getAttribute("data-suffix") || "";
           let current = 0;
           const duration = 2000;
           const steps = 50;
           const increment = target / steps;
           const stepTime = duration / steps;
           
           const timer = setInterval(() => {
               current += increment;
               if (current >= target) {
                   current = target;
                   clearInterval(timer);
               }
               if (Number.isInteger(target)) {
                   num.innerText = Math.floor(current) + suffix;
               } else {
                   num.innerText = current.toFixed(1) + suffix;
               }
           }, stepTime);
       });
   }

   // ==========================================================================
   // Dynamic Captcha Canvas Rendering
   // ==========================================================================
   function drawCaptchaText(canvasId) {
       const canvas = document.getElementById(canvasId);
       if (!canvas) return "";
       const ctx = canvas.getContext('2d');
       
       const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
       let captcha = '';
       for (let i = 0; i < 5; i++) {
           captcha += chars.charAt(Math.floor(Math.random() * chars.length));
       }
       
       ctx.clearRect(0, 0, canvas.width, canvas.height);
       
       ctx.fillStyle = '#EAF4FF';
       if (document.body.classList.contains('dark-mode')) {
           ctx.fillStyle = '#1A2332';
       }
       ctx.fillRect(0, 0, canvas.width, canvas.height);
       
       for (let i = 0; i < 5; i++) {
           ctx.strokeStyle = `rgba(21, 101, 192, 0.25)`;
           ctx.beginPath();
           ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
           ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
           ctx.stroke();
       }
       
       ctx.font = "bold 20px 'Poppins', sans-serif";
       ctx.fillStyle = '#0B3D91';
       if (document.body.classList.contains('dark-mode')) {
           ctx.fillStyle = '#3E8BFF';
       }
       ctx.textBaseline = 'middle';
       
       for (let i = 0; i < captcha.length; i++) {
           const char = captcha[i];
           const x = 20 + i * 20;
           const y = canvas.height / 2 + (Math.random() * 8 - 4);
           const angle = (Math.random() * 30 - 15) * Math.PI / 180;
           
           ctx.save();
           ctx.translate(x, y);
           ctx.rotate(angle);
           ctx.fillText(char, 0, 0);
           ctx.restore();
       }
       
       return captcha;
   }

   function refreshDeptCaptcha() {
       generatedDeptCaptcha = drawCaptchaText('deptCaptchaCanvas');
   }

   function refreshInstCaptcha() {
       const num1 = Math.floor(Math.random() * 9) + 1;
       const num2 = Math.floor(Math.random() * 9) + 1;
       generatedInstCaptchaAnswer = num1 + num2;
       const eqEl = document.getElementById("instCaptchaEquation");
       if (eqEl) {
           eqEl.innerText = `${num1} + ${num2} = `;
       }
   }

   function sendDeptOtp() {
       const userField = document.getElementById("deptUserField");
       const contactVal = userField ? userField.value.trim() : "";
       if (!contactVal) {
           showToast("Please enter your registered Government Mobile / Email ID first.", true);
           return;
       }
       
       generatedDeptOtp = "123456";
       showToast(`Simulated secure OTP sent successfully! Use OTP code "${generatedDeptOtp}" to verify.`);
   }

   // ==========================================================================
   // Interactive Modals Triggering & Validations
   // ==========================================================================
   function openLoginModal(type) {
       if (type === 'department') {
           const userF = document.getElementById("deptUserField");
           const otpF = document.getElementById("deptOtpField");
           const capF = document.getElementById("deptCaptchaInput");
           if (userF) userF.value = "";
           if (otpF) otpF.value = "";
           if (capF) capF.value = "";
           if (deptModalObj) {
               deptModalObj.show();
               setTimeout(refreshDeptCaptcha, 300);
           }
       } else {
           const userF = document.getElementById("instUserField");
           const passF = document.getElementById("instPasswordField");
           const capF = document.getElementById("instCaptchaInput");
           if (userF) userF.value = "";
           if (passF) passF.value = "";
           if (capF) capF.value = "";
           if (instModalObj) {
               instModalObj.show();
               setTimeout(refreshInstCaptcha, 300);
           }
       }
   }

   function toggleInstPasswordVisibility() {
       const pass = document.getElementById("instPasswordField");
       const btn = document.getElementById("instPassToggleBtn");
       if (!pass || !btn) return;
       if (pass.type === "password") {
           pass.type = "text";
           btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
       } else {
           pass.type = "password";
           btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
       }
   }

   function handleDeptLogin(event) {
       event.preventDefault();
       
       const userField = document.getElementById("deptUserField");
       const otpField = document.getElementById("deptOtpField");
       const captchaField = document.getElementById("deptCaptchaInput");
       
       const contactVal = userField ? userField.value.trim() : "";
       const otpVal = otpField ? otpField.value.trim() : "";
       const captchaVal = captchaField ? captchaField.value.trim() : "";
       
       if (!contactVal || !otpVal || !captchaVal) {
           showToast("Error: Please fill all fields including Mobile/Email, OTP, and CAPTCHA.", true);
           return;
       }
       
       if (!generatedDeptOtp || otpVal !== generatedDeptOtp) {
           showToast("Invalid OTP code. Please click 'Send OTP' and enter '123456'.", true);
           return;
       }
       
       if (captchaVal.toLowerCase() !== generatedDeptCaptcha.toLowerCase()) {
           showToast("Verification code error: CAPTCHA text does not match. Please try again.", true);
           refreshDeptCaptcha();
           if (captchaField) captchaField.value = "";
           return;
       }
       
       showToast("Departmental Authentication Success! Redirecting to Command Center Dashboard...");
       setTimeout(() => {
           if (deptModalObj) {
               deptModalObj.hide();
           }
       }, 1500);
   }

   function handleInstLogin(event) {
       event.preventDefault();
       
       const userField = document.getElementById("instUserField");
       const passField = document.getElementById("instPasswordField");
       const captchaField = document.getElementById("instCaptchaInput");
       
       const userVal = userField ? userField.value.trim() : "";
       const passVal = passField ? passField.value.trim() : "";
       const captchaVal = captchaField ? parseInt(captchaField.value.trim()) : NaN;
       
       if (!userVal || !passVal || isNaN(captchaVal)) {
           showToast("Error: Please fill all fields including User ID, Password, and CAPTCHA answer.", true);
           return;
       }
       
       if (captchaVal !== generatedInstCaptchaAnswer) {
           showToast("Arithmetic verification error: CAPTCHA answer is incorrect. Please try again.", true);
           refreshInstCaptcha();
           if (captchaField) captchaField.value = "";
           return;
       }
       
       showToast("Institute Authentication Success! Redirecting to Management Panel...");
       setTimeout(() => {
           if (instModalObj) {
               instModalObj.hide();
           }
       }, 1500);
   }
</script>
@endsection
