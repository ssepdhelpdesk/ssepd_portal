<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO & Metadata -->
    <title>SSEPD-IT Digital Gateway - Government of Odisha</title>
    <meta name="description" content="Centralized digital gateway and command centre for Social Security & Empowerment of Persons with Disabilities Department (SSEPD-IT), Government of Odisha. Access pensions, institution portals, and welfare services.">
    <meta name="keywords" content="SSEPD-IT, SSEPD Odisha, Odisha Government, Disability Welfare Odisha, Pension Portal Odisha, e-Governance Odisha, Digital India, Konark Wheel">
    <meta name="author" content="SSEPD IT Cell, Government of Odisha">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="SSEPD-IT Digital Gateway - Government of Odisha">
    <meta property="og:description" content="Official unified gateway for the Social Security & Empowerment of Persons with Disabilities Department (SSEPD-IT), Odisha.">
    <meta property="og:image" content="images/logo.png">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <!-- Fonts: Poppins (Headings) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ==========================================================================
           Core Design System & Theme Variables
           ========================================================================== */
           :root {
            /* Light Mode Variables */
            --primary-deep-blue: #0B3D91;
            --secondary-blue: #1565C0;
            --light-blue: #EAF4FF;
            --saffron-accent: #FF8C00;
            --saffron-hover: #E07B00;
            --white: #FFFFFF;
            --soft-grey: #F5F7FA;
            --text-dark: #263238;
            --text-muted: #546E7A;
            --success-green: #2E7D32;
            --border-radius: 20px;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 4px 6px -1px rgba(11, 61, 145, 0.05);
            --shadow-md: 0 10px 20px -3px rgba(11, 61, 145, 0.08);
            --shadow-lg: 0 20px 30px -5px rgba(11, 61, 145, 0.12);
            --shadow-glass: 0 8px 32px 0 rgba(11, 61, 145, 0.08);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.6);
            --navbar-bg: rgba(255, 255, 255, 0.95);
            --hero-bg-gradient: linear-gradient(135deg, #F0F6FC 0%, #FFFFFF 100%);
            --glow-color: rgba(11, 61, 145, 0.15);
        }

        /* Dark Mode Theme Variables */
        body.dark-mode {
            --primary-deep-blue: #3E8BFF;
            --secondary-blue: #2979FF;
            --light-blue: #1A2332;
            --saffron-accent: #FFA726;
            --saffron-hover: #FB8C00;
            --white: #121824;
            --soft-grey: #0B0F19;
            --text-dark: #ECEFF1;
            --text-muted: #90A4AE;
            --success-green: #4CAF50;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 10px 20px -3px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 20px 30px -5px rgba(0, 0, 0, 0.5);
            --shadow-glass: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
            --glass-bg: rgba(18, 24, 36, 0.85);
            --glass-border: rgba(255, 255, 255, 0.1);
            --navbar-bg: rgba(18, 24, 36, 0.95);
            --hero-bg-gradient: linear-gradient(135deg, #0B0F19 0%, #151D2A 100%);
            --glow-color: rgba(62, 139, 255, 0.15);
        }

        /* High Contrast Theme Variables (Yellow on Black - standard accessibility) */
        body.high-contrast {
            --primary-deep-blue: #FFFF00;
            --secondary-blue: #FFFF00;
            --light-blue: #000000;
            --saffron-accent: #00FF00;
            --saffron-hover: #00FF00;
            --white: #000000;
            --soft-grey: #000000;
            --text-dark: #FFFF00;
            --text-muted: #FFFF00;
            --success-green: #00FF00;
            --glass-bg: #000000;
            --glass-border: #FFFF00;
            --navbar-bg: #000000;
            --hero-bg-gradient: #000000;
            --shadow-sm: none;
            --shadow-md: none;
            --shadow-lg: none;
            --shadow-glass: none;
            border: 2px solid #FFFF00;
        }

        /* Font Resizing Classes */
        body.text-scale-down {
            font-size: 0.9rem;
        }
        body.text-scale-up {
            font-size: 1.15rem;
        }

        /* ==========================================================================
           Base Rules & General Styles
           ========================================================================== */
           * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--soft-grey);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--primary-deep-blue);
        }

        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3,
        body.high-contrast h4, body.high-contrast h5, body.high-contrast h6 {
            color: #FFFF00 !important;
        }

        a {
            color: var(--secondary-blue);
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        a:hover {
            color: var(--saffron-accent);
        }

        /* Skip Link for Accessibility */
        .skip-link {
            position: absolute;
            top: -100px;
            left: 20px;
            background: var(--saffron-accent);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            z-index: 9999;
            transition: top 0.2s ease;
        }

        .skip-link:focus {
            top: 20px;
            outline: 3px solid var(--primary-deep-blue);
        }

        /* Keyboard Focus Indicator */
        *:focus-visible {
            outline: 3px solid var(--saffron-accent) !important;
            outline-offset: 3px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: var(--soft-grey);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--secondary-blue);
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-deep-blue);
        }

        /* ==========================================================================
           Accessibility Utility Bar
           ========================================================================== */
           .accessibility-toolbar {
            background-color: #051C3F;
            color: #E2E8F0;
            font-size: 0.8rem;
            padding: 6px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            z-index: 1050;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        body.high-contrast .accessibility-toolbar {
            background-color: #000000;
            color: #FFFF00;
            border-bottom: 1px solid #FFFF00;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .toolbar-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #E2E8F0;
            padding: 3px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.75rem;
            transition: var(--transition-smooth);
        }

        .toolbar-btn:hover {
            background-color: var(--saffron-accent);
            color: white;
            border-color: var(--saffron-accent);
        }

        body.high-contrast .toolbar-btn {
            border-color: #FFFF00;
            color: #FFFF00;
        }

        body.high-contrast .toolbar-btn:hover {
            background-color: #FFFF00;
            color: #000000;
        }

        .helplines {
            font-weight: 500;
        }
        .helplines i {
            color: var(--saffron-accent);
            margin-right: 5px;
        }

        /* ==========================================================================
           Tricolor Ribbon & Premium Header
           ========================================================================== */
           .tricolor-ribbon {
            height: 4px;
            width: 100%;
            display: flex;
            position: relative;
            z-index: 1040;
        }
        .ribbon-saffron { background-color: #FF9933; flex: 1; }
        .ribbon-white { background-color: #FFFFFF; flex: 1; }
        .ribbon-green { background-color: #128807; flex: 1; }

        header.sticky-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: var(--navbar-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 2px solid rgba(11, 61, 145, 0.1);
            box-shadow: var(--shadow-sm);
            padding: 12px 6%;
            transition: var(--transition-smooth);
        }

        body.high-contrast header.sticky-header {
            border-bottom: 2px solid #FFFF00;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .header-logo-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .emblem-logo {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .ssepd-logo {
            height: 52px;
            width: auto;
            object-fit: contain;
            border-left: 1.5px solid rgba(0, 0, 0, 0.15);
            padding-left: 12px;
        }

        body.dark-mode .ssepd-logo {
            border-left: 1.5px solid rgba(255, 255, 255, 0.15);
        }

        .header-title-block {
            display: flex;
            flex-direction: column;
        }

        .header-main-title {
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--primary-deep-blue);
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-top: 2px;
            max-width: 480px;
            line-height: 1.3;
        }

        /* Navigation Links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 22px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-item a {
            font-weight: 600;
            font-size: 0.92rem;
            color: var(--text-dark);
            position: relative;
            padding: 6px 0;
        }

        .nav-item a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--saffron-accent);
            transition: var(--transition-smooth);
        }

        .nav-item a:hover::after,
        .nav-item.active a::after {
            width: 100%;
        }

        .nav-item a:hover {
            color: var(--saffron-accent);
        }

        /* Search Box & Controls */
        .header-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-trigger-btn {
            background: var(--light-blue);
            color: var(--secondary-blue);
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .search-trigger-btn:hover {
            background-color: var(--secondary-blue);
            color: white;
        }

        .dark-mode-toggle {
            background: var(--light-blue);
            color: var(--secondary-blue);
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .dark-mode-toggle:hover {
            background-color: var(--secondary-blue);
            color: white;
            transform: rotate(30deg);
        }

        /* Responsive Mobile Toggle */
        .mobile-nav-toggle {
            display: none;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-deep-blue);
            cursor: pointer;
        }

        /* ==========================================================================
           Hero Section (Digital Command Centre Framework)
           ========================================================================== */
           .hero-section {
            min-height: calc(100vh - 120px);
            background: var(--hero-bg-gradient);
            position: relative;
            display: flex;
            align-items: center;
            padding: 60px 6%;
            z-index: 1;
            overflow: hidden;
        }

        #particleCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        /* Background Watermark Elements */
        .konark-watermark {
            position: absolute;
            right: -80px;
            top: 10%;
            width: 550px;
            height: 550px;
            opacity: 0.05;
            color: var(--primary-deep-blue);
            z-index: -2;
            pointer-events: none;
            animation: spinWheel 150s linear infinite;
        }

        body.dark-mode .konark-watermark {
            opacity: 0.03;
        }

        @keyframes spinWheel {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .temple-border-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 16px;
            opacity: 0.15;
            z-index: -2;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 20' preserveAspectRatio='none' width='100%25' height='20'%3E%3Cpath d='M0,20 L10,0 L20,20 L30,0 L40,20 L50,0 L60,20 L70,0 L80,20 L90,0 L100,20 Z' fill='%230B3D91'/%3E%3C/svg%3E");
            background-repeat: repeat-x;
        }

        /* Hero Content Layout */
        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 40px;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .hero-text-block {
            display: flex;
            flex-direction: column;
            gap: 24px;
            z-index: 5;
        }

        .badge-gov-initiative {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(11, 61, 145, 0.08);
            border: 1.5px solid rgba(11, 61, 145, 0.15);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary-deep-blue);
            align-self: flex-start;
            box-shadow: var(--shadow-sm);
        }

        body.dark-mode .badge-gov-initiative {
            background: rgba(62, 139, 255, 0.1);
            border-color: rgba(62, 139, 255, 0.2);
            color: var(--primary-deep-blue);
        }

        .badge-gov-initiative i {
            color: var(--saffron-accent);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--primary-deep-blue);
            letter-spacing: -1.5px;
        }

        .hero-title span {
            color: var(--saffron-accent);
            position: relative;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 620px;
        }

        .hero-ctas {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 12px;
            cursor: pointer;
            border: none;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .btn-cta-primary {
            background: linear-gradient(135deg, var(--primary-deep-blue), var(--secondary-blue));
            color: white !important;
        }

        .btn-cta-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(11, 61, 145, 0.35);
        }

        .btn-cta-secondary {
            background: linear-gradient(135deg, var(--saffron-accent), #FFB74D);
            color: white !important;
        }

        .btn-cta-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(255, 140, 0, 0.35);
        }

        /* Ripple Effect */
        .btn-ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.35);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .hero-tagline {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hero-tagline span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--saffron-accent);
        }

        /* Hero Right Column: Command Center Monitor */
        .hero-monitor-card {
            background: var(--glass-bg);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 30px;
            position: relative;
            z-index: 5;
            animation: floatCard 6s ease-in-out infinite;
        }

        @keyframes floatCard {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .monitor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid rgba(11, 61, 145, 0.1);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        body.dark-mode .monitor-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .monitor-dot-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--success-green);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .monitor-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--success-green);
            animation: pulse-dot 1.8s infinite;
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        .monitor-activity-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-height: 260px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .monitor-activity-item {
            background: rgba(11, 61, 145, 0.04);
            border-left: 3px solid var(--secondary-blue);
            padding: 10px 14px;
            border-radius: 0 8px 8px 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        body.dark-mode .monitor-activity-item {
            background: rgba(255, 255, 255, 0.02);
        }

        .activity-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .activity-desc {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        /* ==========================================================================
           Quick Services Gateway (Beautiful Cards & Dynamic Filtering)
           ========================================================================== */
           .section-padding {
            padding: 100px 6%;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .section-title-line {
            width: 60px;
            height: 4px;
            background-color: var(--saffron-accent);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        .search-filter-container {
            max-width: 600px;
            margin: 0 auto 50px;
            position: relative;
            z-index: 5;
        }

        .search-filter-input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            background: var(--white);
            border: 2px solid rgba(11, 61, 145, 0.1);
            border-radius: 30px;
            font-size: 1rem;
            color: var(--text-dark);
            box-shadow: var(--shadow-md);
            transition: var(--transition-smooth);
        }

        .search-filter-input:focus {
            outline: none;
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 5px rgba(21, 101, 192, 0.15);
        }

        .search-icon-inside {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-blue);
            font-size: 1.15rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .service-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 35px 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .service-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary-blue), var(--saffron-accent));
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(11, 61, 145, 0.2);
        }

        .service-card:hover::after {
            opacity: 1;
        }

        .service-icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(11, 61, 145, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: var(--secondary-blue);
            transition: var(--transition-smooth);
        }

        .service-card:hover .service-icon-wrap {
            background: var(--primary-deep-blue);
            color: white;
            transform: scale(1.05);
        }

        .service-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }

        .service-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-deep-blue);
        }

        .service-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .btn-service-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--secondary-blue);
            background: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
            align-self: flex-start;
            transition: var(--transition-smooth);
        }

        .btn-service-action i {
            transition: var(--transition-smooth);
        }

        .service-card:hover .btn-service-action {
            color: var(--saffron-accent);
        }

        .service-card:hover .btn-service-action i {
            transform: translateX(5px);
        }

        /* ==========================================================================
           Impact Dashboard Section
           ========================================================================== */
           .impact-section {
            background: linear-gradient(135deg, #0B3D91 0%, #051C3F 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        body.high-contrast .impact-section {
            background: #000000;
            color: #FFFF00;
            border-top: 1px solid #FFFF00;
            border-bottom: 1px solid #FFFF00;
        }

        .impact-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: 1;
            background-image: radial-gradient(circle, #FFFFFF 10%, transparent 10.5%);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .impact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 5;
        }

        .impact-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            padding: 30px 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: var(--transition-smooth);
        }

        body.high-contrast .impact-card {
            border: 1px solid #FFFF00;
            background: #000000;
        }

        .impact-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .impact-icon-wrap {
            font-size: 2.2rem;
            color: var(--saffron-accent);
            margin-bottom: 8px;
            animation: pulseIcon 3s ease-in-out infinite;
        }

        @keyframes pulseIcon {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        .impact-number {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .impact-label {
            font-size: 0.95rem;
            font-weight: 500;
            color: #CFD8DC;
        }

        body.high-contrast .impact-label {
            color: #FFFF00;
        }

        /* ==========================================================================
           Institutions Section
           ========================================================================== */
           .institutions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .institution-card {
            background: var(--white);
            border: 1.5px solid rgba(11, 61, 145, 0.08);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
        }

        .institution-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(11, 61, 145, 0.15);
        }

        .institution-illustration-box {
            height: 180px;
            background: linear-gradient(135deg, var(--light-blue) 0%, rgba(21, 101, 192, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 15px;
        }

        .institution-illustration-svg {
            max-height: 140px;
            width: auto;
            transition: var(--transition-smooth);
        }

        .institution-card:hover .institution-illustration-svg {
            transform: scale(1.05);
        }

        .institution-content {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .institution-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-deep-blue);
            line-height: 1.3;
        }

        .institution-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ==========================================================================
           About Section (Department pillars & Minister cards)
           ========================================================================== */
           .about-section {
            background-color: var(--white);
            position: relative;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 50px;
            align-items: start;
            max-width: 1400px;
            margin: 0 auto;
        }

        .about-text-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .about-pillars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .pillar-card {
            background: var(--soft-grey);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(11, 61, 145, 0.05);
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: var(--transition-smooth);
        }

        .pillar-card:hover {
            background: var(--light-blue);
            transform: translateY(-3px);
        }

        .pillar-icon {
            font-size: 1.4rem;
            color: var(--saffron-accent);
        }

        .pillar-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-deep-blue);
            margin-bottom: 4px;
        }

        .pillar-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Ministers Panel */
        .ministers-panel {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .minister-card {
            background: var(--soft-grey);
            border: 1.5px solid rgba(11, 61, 145, 0.06);
            border-radius: var(--border-radius);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
        }

        .minister-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--saffron-accent), #FFB74D);
            border-radius: 4px 0 0 4px;
        }

        .minister-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: rgba(11, 61, 145, 0.15);
            background: var(--light-blue);
        }

        .minister-img-wrap {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            flex-shrink: 0;
            background-color: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .minister-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .minister-img-wrap i {
            font-size: 2.2rem;
            color: var(--secondary-blue);
        }

        .minister-details {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .minister-role {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--saffron-accent);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .minister-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary-deep-blue);
        }

        .minister-dept {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            line-height: 1.3;
        }

        /* ==========================================================================
           Interactive Login Modals
           ========================================================================== */
           .modal-glass {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .modal-content-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .modal-header-accent {
            border-bottom: 1.5px solid rgba(11, 61, 145, 0.1);
            position: relative;
            padding: 24px 30px;
        }

        .modal-header-accent::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--primary-deep-blue), var(--secondary-blue), var(--saffron-accent));
        }

        .modal-title-bold {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--primary-deep-blue);
        }

        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary-deep-blue);
            margin-bottom: 6px;
        }

        .form-input-custom {
            width: 100%;
            padding: 12px 16px 12px 42px;
            background: rgba(255, 255, 255, 0.8);
            border: 1.5px solid rgba(11, 61, 145, 0.15);
            border-radius: 10px;
            font-size: 0.92rem;
            color: var(--text-dark);
            transition: var(--transition-smooth);
        }

        body.dark-mode .form-input-custom {
            background: rgba(18, 24, 36, 0.8);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .form-input-custom:focus {
            outline: none;
            border-color: var(--secondary-blue);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(21, 101, 192, 0.08);
        }

        .input-icon-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .btn-password-toggle {
            position: absolute;
            right: 14px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 0.95rem;
        }

        /* Captcha Block */
        .captcha-box {
            background: rgba(11, 61, 145, 0.03);
            border: 1px dashed rgba(11, 61, 145, 0.18);
            border-radius: 10px;
            padding: 12px;
            margin-top: 15px;
        }

        .captcha-inner-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .captcha-canvas-wrap {
            border-radius: 6px;
            overflow: hidden;
            background-color: var(--light-blue);
            border: 1px solid rgba(11, 61, 145, 0.08);
            display: flex;
            align-items: center;
        }

        .captcha-canvas {
            display: block;
            cursor: pointer;
        }

        .btn-captcha-refresh {
            background: var(--light-blue);
            border: 1px solid rgba(11, 61, 145, 0.12);
            color: var(--secondary-blue);
            width: 36px;
            height: 36px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
        }

        .btn-captcha-refresh:hover {
            background-color: var(--secondary-blue);
            color: white;
        }

        .form-check-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .btn-submit-login {
            background: linear-gradient(135deg, var(--primary-deep-blue), var(--secondary-blue));
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.98rem;
            box-shadow: 0 4px 15px rgba(11, 61, 145, 0.2);
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        .btn-submit-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11, 61, 145, 0.3);
        }

        /* Toast notifications */
        .toast-container-custom {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .custom-toast {
            background: #2E7D32;
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            transform: translateY(100px);
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .custom-toast.error {
            background: #D32F2F;
        }

        .custom-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* ==========================================================================
           Footer Components
           ========================================================================== */
           footer.premium-footer {
            background-color: #07152C;
            color: #ECEFF1;
            padding: 80px 6% 30px;
            position: relative;
            z-index: 5;
            border-top: 4px solid var(--saffron-accent);
        }

        body.high-contrast footer.premium-footer {
            border-top: 4px solid #FFFF00;
            background-color: #000000;
            color: #FFFF00;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr 1fr;
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto 50px;
        }

        .footer-logo-block {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 420px;
        }

        .footer-desc {
            font-size: 0.86rem;
            line-height: 1.6;
            color: #90A4AE;
        }

        body.high-contrast .footer-desc {
            color: #FFFF00;
        }

        .footer-links-group {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .footer-title {
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 8px;
        }

        body.high-contrast .footer-title {
            color: #FFFF00;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 35px;
            height: 3px;
            background-color: var(--saffron-accent);
            border-radius: 1.5px;
        }

        .footer-links-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0;
            margin: 0;
        }

        .footer-links-list a {
            color: #B0BEC5;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        body.high-contrast .footer-links-list a {
            color: #FFFF00;
        }

        .footer-links-list a:hover {
            color: white;
            transform: translateX(4px);
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.88rem;
            color: #B0BEC5;
        }

        body.high-contrast .footer-contact-item {
            color: #FFFF00;
        }

        .footer-contact-item i {
            color: var(--saffron-accent);
            margin-top: 4px;
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            font-size: 0.8rem;
            color: #78909C;
        }

        body.high-contrast .footer-bottom {
            border-top: 1px solid #FFFF00;
            color: #FFFF00;
        }

        .footer-bottom-links {
            display: flex;
            gap: 20px;
        }

        .footer-bottom-links a {
            color: #78909C;
        }

        body.high-contrast .footer-bottom-links a {
            color: #FFFF00;
        }

        .footer-bottom-links a:hover {
            color: white;
        }

        .nic-badge {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nic-logo {
            height: 22px;
            opacity: 0.7;
            transition: var(--transition-smooth);
        }

        .nic-logo:hover {
            opacity: 1;
        }

        /* ==========================================================================
           Animations (Scroll & Transitions)
           ========================================================================== */
           .reveal-element {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .reveal-element.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ==========================================================================
           Responsive Design: Media Queries
           ========================================================================== */
           @media (max-width: 1100px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            .hero-monitor-card {
                max-width: 600px;
                margin: 0 auto;
            }
            .about-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .ministers-panel {
                flex-direction: row;
                flex-wrap: wrap;
            }
            .minister-card {
                flex: 1;
                min-width: 300px;
            }
        }

        @media (max-width: 991px) {
            header.sticky-header {
                padding: 12px 4%;
            }
            .nav-links {
                display: none; /* Hide standard nav list on mobile */
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--navbar-bg);
                border-bottom: 2px solid rgba(11, 61, 145, 0.15);
                padding: 20px 4%;
                gap: 16px;
                box-shadow: var(--shadow-md);
            }
            .nav-links.show {
                display: flex;
            }
            .mobile-nav-toggle {
                display: block;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
            .section-padding {
                padding: 60px 4%;
            }
            .impact-grid {
                grid-template-columns: 1fr 1fr;
            }
            .header-subtitle {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .hero-ctas {
                flex-direction: column;
            }
            .btn-cta {
                width: 100%;
                justify-content: center;
            }
            .impact-grid {
                grid-template-columns: 1fr;
            }
            .ministers-panel {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Accessibility Toolbar -->
    <div class="accessibility-toolbar" role="complementary" aria-label="Accessibility options">
        <div class="toolbar-left">
            <span class="helplines" aria-label="Odisha Helpline Numbers">
                <i class="fa-solid fa-phone"></i> Helpline: 2919578
            </span>
        </div>
        <div class="toolbar-right">
            <div class="text-resizer">
                <span>Text: </span>
                <button onclick="adjustTextSize('decrease')" class="toolbar-btn" aria-label="Decrease Text Size">A-</button>
                <button onclick="adjustTextSize('reset')" class="toolbar-btn" aria-label="Reset Text Size">A</button>
                <button onclick="adjustTextSize('increase')" class="toolbar-btn" aria-label="Increase Text Size">A+</button>
            </div>
            <div class="contrast-switcher">
                <span>Contrast: </span>
                <button onclick="toggleContrast()" class="toolbar-btn" aria-label="Toggle Contrast Mode">High Contrast</button>
            </div>
            <div class="lang-selector">
                <select id="langSelect" class="toolbar-btn" style="background:#051C3F; border-color:rgba(255,255,255,0.25);" aria-label="Select Language">
                    <option value="en">English</option>
                    <option value="or">ଓଡ଼ିଆ (Odia)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tricolor Ribbon for National Identity -->
    <div class="tricolor-ribbon">
        <div class="ribbon-saffron"></div>
        <div class="ribbon-white"></div>
        <div class="ribbon-green"></div>
    </div>

    <!-- Sticky Navigation Header -->
    <header class="sticky-header" role="banner">
        <div class="header-container">
            <div class="header-logo-group">
                <!-- Government of Odisha emblem -->
                <img src="images/logo.png" class="emblem-logo" alt="Government of Odisha Emblem" onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/f/f6/Seal_of_Odisha.svg';">
                
                <div class="header-title-block">
                    <span class="header-main-title">SSEPD-IT</span>
                    <span class="header-subtitle">Social Security & Empowerment of Persons with Disabilities Department, Government of Odisha</span>
                </div>
            </div>

            <nav role="navigation" aria-label="Primary Navigation">
                <ul class="nav-links" id="navLinks">
                    <li class="nav-item active"><a href="#home">Home</a></li>
                    <li class="nav-item"><a href="#services">Digital Services</a></li>
                    <li class="nav-item"><a href="#institutions">Institution Portal</a></li>
                    <!-- <li class="nav-item"><a href="#about">Resources</a></li>
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

    <!-- Main Content Area -->
    <main id="main-content" role="main">

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
                    <div class="badge-gov-initiative" role="text">
                        <i class="fa-solid fa-circle-check"></i> Official Flagship e-Governance Platform
                    </div>
                    <h1 class="hero-title">
                        SSEPD-IT <br><span>Digital Gateway</span>
                    </h1>
                    <p class="hero-subtitle">
                        A unified platform for all internal operations and services.
                    </p>

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
                        <span></span> Secure Access <span></span> e-Governance <span></span> Statewide Connectivity
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
        <section id="services" class="section-padding reveal-element" aria-label="Digital Services Gateway">
            <div class="section-header">
                <h2>Digital Services Gateway</h2>
                <p class="text-muted mb-0">Navigate and branch out to individual application portals, registries, and management cells.</p>
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
                        <p class="service-desc">A unified platform for all internal operations and services.</p>
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
                        <p class="service-desc">A dedicated platform for pensioners aged 80 and above, or with 80% or more disability.</p>
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
                        <p class="service-desc">Manodaya, aims for the overall development of PwDs in Odisha</p>
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
                        <p class="service-desc">A unified platform for all institutions functioning under the SSEPD Department.</p>
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
                        <p class="service-desc">A unified platform for all Special School functioning under the SSEPD Department.</p>
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
                        <p class="service-desc">A unified platform for all Old Age Home functioning under the SSEPD Department.</p>
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
                        <p class="service-desc">A unified platform for all Therapeutic Centre functioning under the SSEPD Department.</p>
                    </div>
                    <a href="{{route('pia_institute_login')}}" target="_blank"><button class="btn-service-action" aria-label="Open Therapeutic Centre Portal">
                        Open Portal <i class="fa-solid fa-arrow-right-long"></i>
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

        <!-- Bootstrap 5 JS Bundle (Includes Popper) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Vanilla Javascript Logic -->
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
            // Initialize Bootstrap modal instances
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

            // Language translation listener helper
                const langSelect = document.getElementById("langSelect");
                if (langSelect) {
                    langSelect.addEventListener("change", (e) => {
                        handleLanguageChange(e.target.value);
                    });
                }
            });

        // ==========================================================================
        // Dark Mode & Contrast Toggle Functions
        // ==========================================================================
            function toggleDarkMode() {
                document.body.classList.toggle('dark-mode');
            // Remove high contrast if enabled, as they conflict
                document.body.classList.remove('high-contrast');
                
                const btn = document.getElementById('darkModeToggle');
                if (document.body.classList.contains('dark-mode')) {
                    btn.innerHTML = '<i class="fa-solid fa-sun"></i>';
                    btn.setAttribute("aria-label", "Toggle Light Mode");
                    showToast("Dark Mode enabled successfully.");
                } else {
                    btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
                    btn.setAttribute("aria-label", "Toggle Dark Mode");
                    showToast("Light Mode enabled successfully.");
                }
            }

            function toggleContrast() {
                document.body.classList.toggle('high-contrast');
            // Remove dark mode to prevent visual overlays
                document.body.classList.remove('dark-mode');
                
                if (document.body.classList.contains('high-contrast')) {
                    showToast("High Contrast accessibility theme enabled.");
                } else {
                    showToast("Standard contrast theme restored.");
                }
            }

        // ==========================================================================
        // Text Resizing Controls
        // ==========================================================================
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

        // ==========================================================================
        // Interactive Language Switcher Mock
        // ==========================================================================
        /*function handleLanguageChange(lang) {
            if (lang === 'or') {
                showToast("ଓଡ଼ିଆ ଭାଷା ବିକଳ୍ପ ଚୟନ କରାଯାଇଛି | Translation framework loading...");
                document.querySelector('.hero-title').innerHTML = "ଏସ.ଏସ.ଇ.ପି.ଡି-ଆଇଟି <br><span>ଡିଜିଟାଲ୍ ଗେଟୱେ</span>";
                document.querySelector('.hero-subtitle').innerText = "ଓଡିଶାରେ ସାମାଜିକ ସୁରକ୍ଷା, ପୁନର୍ବାସ, ଅନ୍ତର୍ଭୁକ୍ତୀକରଣ ଏବଂ କଲ୍ୟାଣ ସେବା ପାଇଁ ଏକ ସମନ୍ୱିତ ଡିଜିଟାଲ୍ ପ୍ଲାଟଫର୍ମ |";
            } else {
                showToast("English language selected.");
                document.querySelector('.hero-title').innerHTML = "SSEPD-IT <br><span>Digital Gateway</span>";
                document.querySelector('.hero-subtitle').innerText = "A Unified Digital Platform for Social Security, Rehabilitation, Inclusion and Welfare Services across Odisha.";
            }
        }*/

        // ==========================================================================
        // Dynamic Quick Services Filter Logic
        // ==========================================================================
            function filterServices() {
                const query = document.getElementById("serviceSearch").value.toLowerCase();
                const cards = document.querySelectorAll(".service-card");
                
                cards.forEach(card => {
                    const keywords = card.getAttribute("data-keywords").toLowerCase();
                    const title = card.querySelector(".service-title").innerText.toLowerCase();
                    const desc = card.querySelector(".service-desc").innerText.toLowerCase();
                    
                    if (keywords.includes(query) || title.includes(query) || desc.includes(query)) {
                        card.style.display = "flex";
                    } else {
                        card.style.display = "none";
                    }
                });
            }

            function focusSearch() {
                const searchInput = document.getElementById("serviceSearch");
                if (searchInput) {
                    searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        searchInput.focus();
                    }, 400);
                }
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
                    
                // Maintain list container limits
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
                            
                        // If it is the impact stats grid, trigger numeric ticker
                            if (entry.target.classList.contains("impact-section") || entry.target.querySelector(".impact-grid")) {
                                triggerStatsCounters();
                            }
                        }
                    });
                }, { threshold: 0.15 });

                revealElements.forEach(el => observer.observe(el));
                
            // Explicit check for elements that are already partially in viewport
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
                const duration = 2000; // ms
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
                
            // Generate clean random alphanumeric code
                const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
                let captcha = '';
                for (let i = 0; i < 5; i++) {
                    captcha += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
            // Draw background lines for security interference
                ctx.fillStyle = '#EAF4FF';
                if (document.body.classList.contains('dark-mode')) {
                    ctx.fillStyle = '#1A2332';
                }
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
            // Interference lines
                for (let i = 0; i < 5; i++) {
                    ctx.strokeStyle = `rgba(21, 101, 192, 0.25)`;
                    ctx.beginPath();
                    ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
                    ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
                    ctx.stroke();
                }
                
            // Captcha text styles
                ctx.font = "bold 20px 'Poppins', sans-serif";
                ctx.fillStyle = '#0B3D91';
                if (document.body.classList.contains('dark-mode')) {
                    ctx.fillStyle = '#3E8BFF';
                }
                ctx.textBaseline = 'middle';
                
            // Rotate and draw individual characters for verification complexity
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
            // Generate a random math equation for the institute login CAPTCHA
                const num1 = Math.floor(Math.random() * 9) + 1;
                const num2 = Math.floor(Math.random() * 9) + 1;
                generatedInstCaptchaAnswer = num1 + num2;
                const eqEl = document.getElementById("instCaptchaEquation");
                if (eqEl) {
                    eqEl.innerText = `${num1} + ${num2} = `;
                }
            }

            function sendDeptOtp() {
                const contactVal = document.getElementById("deptUserField").value.trim();
                if (!contactVal) {
                    showToast("Please enter your registered Government Mobile / Email ID first.", true);
                    return;
                }
                
            // Set mock OTP code
                generatedDeptOtp = "123456";
                showToast(`Simulated secure OTP sent successfully! Use OTP code "${generatedDeptOtp}" to verify.`);
            }

        // ==========================================================================
        // Interactive Modals Triggering & Validations
        // ==========================================================================
            function openLoginModal(type) {
                if (type === 'department') {
                    document.getElementById("deptUserField").value = "";
                    document.getElementById("deptOtpField").value = "";
                    document.getElementById("deptCaptchaInput").value = "";
                    if (deptModalObj) {
                        deptModalObj.show();
                        setTimeout(refreshDeptCaptcha, 300);
                    }
                } else {
                    document.getElementById("instUserField").value = "";
                    document.getElementById("instPasswordField").value = "";
                    document.getElementById("instCaptchaInput").value = "";
                    if (instModalObj) {
                        instModalObj.show();
                        setTimeout(refreshInstCaptcha, 300);
                    }
                }
            }

            function toggleInstPasswordVisibility() {
                const pass = document.getElementById("instPasswordField");
                const btn = document.getElementById("instPassToggleBtn");
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
                
                const contactVal = document.getElementById("deptUserField").value.trim();
                const otpVal = document.getElementById("deptOtpField").value.trim();
                const captchaVal = document.getElementById("deptCaptchaInput").value.trim();
                
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
                    document.getElementById("deptCaptchaInput").value = "";
                    return;
                }
                
            // Successful validation simulation
                showToast("Departmental Authentication Success! Redirecting to Command Center Dashboard...");
                setTimeout(() => {
                    if (deptModalObj) {
                        deptModalObj.hide();
                    }
                }, 1500);
            }

            function handleInstLogin(event) {
                event.preventDefault();
                
                const userVal = document.getElementById("instUserField").value.trim();
                const passVal = document.getElementById("instPasswordField").value.trim();
                const captchaVal = parseInt(document.getElementById("instCaptchaInput").value.trim());
                
                if (!userVal || !passVal || isNaN(captchaVal)) {
                    showToast("Error: Please fill all fields including User ID, Password, and CAPTCHA answer.", true);
                    return;
                }
                
                if (captchaVal !== generatedInstCaptchaAnswer) {
                    showToast("Arithmetic verification error: CAPTCHA answer is incorrect. Please try again.", true);
                    refreshInstCaptcha();
                    document.getElementById("instCaptchaInput").value = "";
                    return;
                }
                
            // Successful validation simulation
                showToast("Institute Authentication Success! Redirecting to Management Panel...");
                setTimeout(() => {
                    if (instModalObj) {
                        instModalObj.hide();
                    }
                }, 1500);
            }

        // ==========================================================================
        // Custom Toast Notifications Framework
        // ==========================================================================
            function showToast(message, isError = false) {
                const container = document.getElementById("toastContainer");
                if (!container) return;
                
                const toast = document.createElement("div");
                toast.className = `custom-toast ${isError ? 'error' : ''}`;
                toast.innerHTML = `
                <i class="fa-solid ${isError ? 'fa-triangle-exclamation' : 'fa-circle-check'}"></i>
                <span>${message}</span>
                `;
                
                container.appendChild(toast);
                
            // Trigger browser animation
                setTimeout(() => {
                    toast.classList.add("show");
                }, 50);
                
            // Remove after duration completes
                setTimeout(() => {
                    toast.classList.remove("show");
                    setTimeout(() => {
                        container.removeChild(toast);
                    }, 300);
                }, 3500);
            }

        // Mobile navbar helper
            function toggleMobileMenu() {
                const nav = document.getElementById("navLinks");
                nav.classList.toggle("show");
                
                const btn = document.getElementById("mobileMenuToggle");
                if (nav.classList.contains("show")) {
                    btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                } else {
                    btn.innerHTML = '<i class="fa-solid fa-bars"></i>';
                }
            }
        </script>
    </body>
    </html>