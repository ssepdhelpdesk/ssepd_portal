<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>SSEPD Institute Portal - Government of Odisha</title>
    <meta name="description" content="Official inventory tracking, management, and e-Governance login portal for the Social Security & Empowerment of Persons with Disabilities Department (SSEPD), Government of Odisha.">
    <meta name="keywords" content="SSEPD, Odisha Government, Social Security, Empowerment, Persons with Disabilities, e-Governance Odisha, Institute Portal, Aids and Appliances, CM Mohan Charan Majhi">
    <meta name="author" content="SSEPD Department, Government of Odisha">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="SSEPD Institute Portal - Government of Odisha">
    <meta property="og:description" content="Secure portal to access inventory records, register vendors, and request aids & appliances from the SSEPD Department.">
    <meta property="og:image" content="images/logo.png">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <!-- Fonts: Poppins (Headings) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* ==========================================================================
           Core Design System & Global Variables
           ========================================================================== */
        :root {
            --primary-deep-blue: #0B2D5B;
            --secondary-blue: #154B8A;
            --light-blue: #EAF3FB;
            --saffron-accent: #F57C00;
            --saffron-hover: #E06900;
            --white: #FFFFFF;
            --soft-grey: #F5F7FA;
            --dark-grey: #2C3E50;
            --text-muted: #5A6B7C;
            --border-radius: 20px;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 4px 6px -1px rgba(11, 45, 91, 0.05);
            --shadow-md: 0 10px 15px -3px rgba(11, 45, 91, 0.08);
            --shadow-lg: 0 20px 25px -5px rgba(11, 45, 91, 0.12);
            --shadow-glass: 0 8px 32px 0 rgba(11, 45, 91, 0.08);
        }

        /* High Contrast Theme Variables override */
        body.high-contrast {
            --primary-deep-blue: #FFFF00;
            --secondary-blue: #FFFF00;
            --light-blue: #000000;
            --saffron-accent: #FFFF00;
            --saffron-hover: #FFFF00;
            --white: #000000;
            --soft-grey: #000000;
            --dark-grey: #FFFF00;
            --text-muted: #FFFF00;
        }

        /* ==========================================================================
           Base Rules & Typography
           ========================================================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--soft-grey);
            color: var(--dark-grey);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
            line-height: 1.6;
            transition: var(--transition-smooth);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--primary-deep-blue);
        }

        a {
            color: var(--secondary-blue);
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        a:hover {
            color: var(--saffron-accent);
        }

        /* Accessibility: Skip to Content */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: var(--saffron-accent);
            color: white;
            padding: 8px 16px;
            z-index: 2000;
            transition: top 0.2s;
        }

        .skip-link:focus {
            top: 0;
            outline: 2px solid white;
        }

        /* Keyboard Focus States */
        *:focus-visible {
            outline: 3px solid var(--saffron-accent);
            outline-offset: 2px;
        }

        /* ==========================================================================
           Accessibility & Contrast Toolbar (Sticky Top)
           ========================================================================== */
        .accessibility-bar {
            background-color: #0b1f3c;
            color: #d1dbe5;
            font-size: 0.8rem;
            padding: 6px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            z-index: 1001;
            position: relative;
        }

        .accessibility-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .accessibility-right {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .acc-btn {
            background: transparent;
            border: 1px solid #233e64;
            color: #d1dbe5;
            padding: 2px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition-smooth);
        }

        .acc-btn:hover, .acc-btn:focus {
            background-color: var(--saffron-accent);
            color: white;
            border-color: var(--saffron-accent);
        }

        .contrast-toggle {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .helplines {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }

        .helplines i {
            color: var(--saffron-accent);
        }

        /* ==========================================================================
           Header Component
           ========================================================================== */
        header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 3px solid var(--saffron-accent);
            box-shadow: var(--shadow-sm);
            padding: 12px 5%;
            transition: var(--transition-smooth);
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

        .gov-logo {
            height: 65px;
            width: auto;
            object-fit: contain;
            transition: var(--transition-smooth);
        }

        .logo-placeholder {
            width: 55px;
            height: 65px;
            background: linear-gradient(135deg, var(--light-blue), #c2dcf0);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-deep-blue);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .header-title-block {
            display: flex;
            flex-direction: column;
        }

        .header-dept-title {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1.2;
            color: var(--primary-deep-blue);
            letter-spacing: -0.5px;
        }

        .header-dept-subtitle {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-top: 2px;
            max-width: 500px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* National Flag Ribbon Graphic Overlay */
        .flag-ribbon {
            display: flex;
            height: 5px;
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .flag-orange { background-color: #FF9933; flex: 1; }
        .flag-white { background-color: #FFFFFF; flex: 1; }
        .flag-green { background-color: #128807; flex: 1; }

        /* ==========================================================================
           Animated Background Blobs & Canvas Particles
           ========================================================================== */
        #particleCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            z-index: 0;
            opacity: 0.55;
            pointer-events: none;
            animation: blobMovement 30s infinite alternate ease-in-out;
        }

        .bg-blob-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(234, 243, 251, 0.95) 0%, rgba(21, 75, 138, 0.3) 100%);
            top: 15%;
            left: -10%;
        }

        .bg-blob-2 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(254, 237, 222, 0.8) 0%, rgba(245, 124, 0, 0.15) 100%);
            bottom: 5%;
            right: -5%;
            animation-delay: -10s;
        }

        @keyframes blobMovement {
            0% { transform: translate(0, 0) scale(1) rotate(0deg); }
            33% { transform: translate(70px, -50px) scale(1.15) rotate(120deg); }
            66% { transform: translate(-50px, 80px) scale(0.9) rotate(240deg); }
            100% { transform: translate(0, 0) scale(1) rotate(360deg); }
        }

        /* ==========================================================================
           Main Layout & Hero Section
           ========================================================================== */
        main {
            flex: 1;
            position: relative;
            z-index: 1;
            padding: 2.5rem 5% 5rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.25fr 0.75fr;
            gap: 3.5rem;
            align-items: start;
        }

        /* Left Column: Info Panel */
        .info-panel {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .info-heading-block {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .gov-banner-badge {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            gap: 8px;
            background: rgba(21, 75, 138, 0.08);
            border: 1px solid rgba(21, 75, 138, 0.15);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--secondary-blue);
        }

        .gov-banner-badge i {
            color: var(--saffron-accent);
        }

        .info-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--primary-deep-blue);
            letter-spacing: -1px;
        }

        .info-title span {
            color: var(--saffron-accent);
            position: relative;
            display: inline-block;
        }

        .info-title span::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(245, 124, 0, 0.15);
            z-index: -1;
        }

        .info-subtitle {
            font-size: 1.2rem;
            font-weight: 400;
            color: var(--text-muted);
            max-width: 600px;
            line-height: 1.5;
        }

        /* ==========================================================================
           Konark Wheel Rotator Background Watermark
           ========================================================================== */
        .konark-container {
            position: absolute;
            right: -20px;
            top: 20%;
            width: 380px;
            height: 380px;
            opacity: 0.04;
            color: var(--primary-deep-blue);
            z-index: -1;
            pointer-events: none;
        }

        .konark-wheel {
            width: 100%;
            height: 100%;
            animation: rotateWheel 120s linear infinite;
        }

        @keyframes rotateWheel {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }


        /* ==========================================================================
           Floating Statistics Cards Grid
           ========================================================================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: var(--border-radius);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(21, 75, 138, 0.3);
            background: rgba(255, 255, 255, 0.9);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(21, 75, 138, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--secondary-blue);
            transition: var(--transition-smooth);
        }

        .stat-card:hover .stat-icon-wrapper {
            background-color: var(--primary-deep-blue);
            color: white;
            transform: scale(1.05);
        }

        .stat-number {
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--primary-deep-blue);
            line-height: 1.1;
            margin-top: 4px;
        }

        .stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* ==========================================================================
           Right Column: Modern Glassmorphism Login Card
           ========================================================================== */
        .login-column {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-glass);
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .login-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--primary-deep-blue), var(--secondary-blue), var(--saffron-accent));
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.2rem;
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-deep-blue);
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.6rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary-deep-blue);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
            transition: var(--transition-smooth);
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: rgba(255, 255, 255, 0.8);
            border: 1.5px solid rgba(11, 45, 91, 0.15);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--dark-grey);
            transition: var(--transition-smooth);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--secondary-blue);
            background: white;
            box-shadow: 0 0 0 4px rgba(21, 75, 138, 0.08);
        }

        .form-input:focus + .input-icon {
            color: var(--secondary-blue);
        }

        /* Password Toggle Option */
        .password-toggle-btn {
            position: absolute;
            right: 16px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 0.95rem;
            transition: var(--transition-smooth);
        }

        .password-toggle-btn:hover {
            color: var(--primary-deep-blue);
        }

        /* ==========================================================================
           Secure Client-side CAPTCHA Component
           ========================================================================== */
        .captcha-container {
            background: rgba(255, 255, 255, 0.7);
            border: 1px dashed rgba(11, 45, 91, 0.2);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 1.6rem;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .captcha-display-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .captcha-canvas-wrap {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid rgba(11, 45, 91, 0.1);
            background-color: #EAF3FB;
            display: flex;
            align-items: center;
        }

        #captchaCanvas {
            display: block;
            cursor: pointer;
        }

        .btn-refresh-captcha {
            background: var(--light-blue);
            border: 1px solid rgba(21, 75, 138, 0.15);
            color: var(--secondary-blue);
            width: 38px;
            height: 38px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: var(--transition-smooth);
        }

        .btn-refresh-captcha:hover {
            background-color: var(--secondary-blue);
            color: white;
            border-color: var(--secondary-blue);
        }

        .captcha-input-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        /* Remember Me & Forgot Password */
        .form-actions-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.85rem;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-muted);
            user-select: none;
        }

        .checkbox-container input {
            cursor: pointer;
            width: 16px;
            height: 16px;
            accent-color: var(--secondary-blue);
        }

        .forgot-link {
            font-weight: 600;
            color: var(--secondary-blue);
        }

        .forgot-link:hover {
            color: var(--saffron-accent);
            text-decoration: underline;
        }

        /* Sign In Button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-deep-blue), var(--secondary-blue));
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(11, 45, 91, 0.2);
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--secondary-blue), var(--primary-deep-blue));
            box-shadow: 0 6px 20px rgba(11, 45, 91, 0.3);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Modern Ripple Effect */
        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.35);
            border-radius: 50%;
            transform: scale(0);
            animation: rippleEffect 0.6s linear;
            pointer-events: none;
        }

        @keyframes rippleEffect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* ==========================================================================
           Quick Actions Panel (Below Login Card)
           ========================================================================== */
        .quick-actions-panel {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .quick-action-btn {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(21, 75, 138, 0.12);
            padding: 14px 20px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--primary-deep-blue);
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
        }

        .quick-action-btn i.action-icon {
            font-size: 1.15rem;
            color: var(--saffron-accent);
            background: rgba(245, 124, 0, 0.08);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }

        .quick-action-btn i.arrow-icon {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: var(--transition-smooth);
        }

        .quick-action-btn:hover {
            background: white;
            border-color: var(--secondary-blue);
            transform: translateX(5px);
            box-shadow: var(--shadow-md);
            color: var(--secondary-blue);
        }

        .quick-action-btn:hover i.action-icon {
            background-color: var(--saffron-accent);
            color: white;
        }

        .quick-action-btn:hover i.arrow-icon {
            color: var(--secondary-blue);
            transform: translateX(3px);
        }

        /* ==========================================================================
           Footer Component
           ========================================================================== */
        footer {
            background-color: #081a30;
            color: #b8c8d8;
            padding: 3rem 5% 2rem;
            margin-top: auto;
            border-top: 4px solid var(--saffron-accent);
            font-size: 0.9rem;
            position: relative;
            z-index: 10;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 2rem;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 500px;
        }

        .footer-logo-title {
            color: white;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .footer-subtitle {
            font-size: 0.8rem;
            line-height: 1.5;
            color: #8c9fae;
        }

        .footer-links-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-links-title {
            color: white;
            font-size: 1rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 6px;
        }

        .footer-links-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background-color: var(--saffron-accent);
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-links a {
            color: #8c9fae;
            font-size: 0.85rem;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 4px;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            font-size: 0.8rem;
            color: #6a7d8c;
        }

        .footer-bottom-links {
            display: flex;
            gap: 20px;
        }

        .footer-bottom-links a {
            color: #6a7d8c;
        }

        .footer-bottom-links a:hover {
            color: white;
        }

        .nic-badge {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nic-badge img {
            height: 22px;
            opacity: 0.7;
            transition: var(--transition-smooth);
        }

        .nic-badge img:hover {
            opacity: 1;
        }

        /* ==========================================================================
           Animations Framework
           ========================================================================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Toast notifications styling */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #2ecc71;
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(100px);
            opacity: 0;
            transition: var(--transition-smooth);
            z-index: 2000;
            font-weight: 500;
        }

        .toast-notification.error {
            background: #e74c3c;
        }

        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* ==========================================================================
           Responsive Design: Media Queries
           ========================================================================== */
        @media (max-width: 1024px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 3.5rem;
            }
            
            .info-panel {
                gap: 2rem;
            }
            
            .info-title {
                font-size: 2.3rem;
            }

            .konark-container {
                width: 280px;
                height: 280px;
                right: 0;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 10px 4%;
            }

            .header-dept-title {
                font-size: 1.15rem;
            }

            .header-dept-subtitle {
                font-size: 0.75rem;
            }

            .gov-logo {
                height: 50px;
            }

            main {
                padding: 2rem 4% 3.5rem;
            }

            .info-title {
                font-size: 2rem;
            }


            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .login-card {
                padding: 2.2rem 1.8rem;
            }

            .footer-top {
                flex-direction: column;
                gap: 25px;
            }
            
            .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .header-title-block {
                max-width: 220px;
            }

            .header-dept-title {
                font-size: 1rem;
            }

            .header-dept-subtitle {
                display: none; /* Hide long subtitle on mobile to conserve header space */
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .form-actions-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .footer-bottom-links {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
        }
        
        /* Backend Login Error and Captcha Styling */
        .invalid-feedback {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
            font-weight: 500;
        }
        .form-input.is-invalid {
            border-color: #e74c3c !important;
            box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.08) !important;
        }
        #captcha-img img {
            height: 100%;
            width: auto;
            max-height: 42px;
            display: block;
        }
    </style>
</head>
<body>

    <!-- Skip to Main Content Link for Keyboard Users (WCAG Compliant) -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Accessibility & Contrast Toolbar -->
    <div class="accessibility-bar" role="complementary" aria-label="Accessibility options">
        <div class="accessibility-left">
            <span class="helplines" aria-label="Emergency Helplines">
                <span><i class="fa-solid fa-phone"></i> Helpline: 2919578</span>
            </span>
        </div>
        <div class="accessibility-right">
            <div class="contrast-toggle">
                <span>Contrast:</span>
                <button onclick="toggleContrast()" class="acc-btn" aria-label="Toggle High Contrast Mode">High Contrast</button>
            </div>
            <div class="text-resize">
                <span>Text Size:</span>
                <button onclick="changeFontSize('decrease')" class="acc-btn" aria-label="Decrease Font Size">A-</button>
                <button onclick="changeFontSize('reset')" class="acc-btn" aria-label="Reset Font Size">A</button>
                <button onclick="changeFontSize('increase')" class="acc-btn" aria-label="Increase Font Size">A+</button>
            </div>
            <div class="lang-select">
                <select id="langSelect" class="acc-btn" style="background:#0b1f3c; border-color:#233e64;" aria-label="Language Selector">
                    <option value="en">English</option>
                    <option value="or">ଓଡ଼ିଆ (Odia)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Sticky Header -->
    <header role="banner">
        <div class="header-container">
            <div class="header-logo-group">
                <!-- Fallback to placeholder if local logo.png fails to load -->
                <img src="images/logo.png" class="gov-logo" alt="Government of Odisha Emblem" onerror="this.onerror=null; this.style.display='none'; document.getElementById('logoFallback').style.display='flex';">
                <div id="logoFallback" class="logo-placeholder" style="display:none;">ODISHA</div>
                
                <div class="header-title-block">
                    <span class="header-dept-title">SSEPD Institutes</span>
                    <span class="header-dept-subtitle">Social Security & Empowerment of Persons with Disabilities Department</span>
                </div>
            </div>
            
            <div class="header-right">
                <img src="https://cdn.digitalindiacorporation.in/wp-content/themes/di-child/assets/images/digital-india.svg.gzip" alt="Digital India Logo" style="height: 40px; width: auto; opacity: 0.85;" class="d-none d-md-block" onerror="this.onerror=null; this.style.display='none';">
            </div>
        </div>
        <!-- Tricolor Strip Overlay for National/Government Identity -->
        <div class="flag-ribbon">
            <div class="flag-orange"></div>
            <div class="flag-white"></div>
            <div class="flag-green"></div>
        </div>
    </header>

    <!-- Main Container -->
    <main id="main-content" role="main">
        <!-- Particle Canvas overlay for dynamic background -->
        <canvas id="particleCanvas"></canvas>
        <div class="bg-blob bg-blob-1"></div>
        <div class="bg-blob bg-blob-2"></div>

        <div class="hero-grid">
            
            <!-- LEFT COLUMN: Government Information Panel (60%) -->
            <section class="info-panel" aria-label="Government Information">
                <div class="info-heading-block">
                    <div class="gov-banner-badge" role="text">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Official e-Governance Initiative of Odisha</span>
                    </div>
                    <h1 class="info-title">SSEPD <span>Institute Portal</span></h1>
                    <p class="info-subtitle">Providing a structured framework of therapeutic support, disability rehabilitation, education, and elder care across Odisha.</p>
                </div>

                <!-- Rotating Konark Wheel Watermark Pattern -->
                <div class="konark-container">
                    <svg class="konark-wheel" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <!-- Outer Rim -->
                        <circle cx="100" cy="100" r="95" stroke="currentColor" stroke-width="3.5" fill="none" />
                        <circle cx="100" cy="100" r="85" stroke="currentColor" stroke-width="1.5" fill="none" />
                        <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="1" stroke-dasharray="2 3" fill="none" />
                        
                        <!-- Inner Rim -->
                        <circle cx="100" cy="100" r="30" stroke="currentColor" stroke-width="2.5" fill="none" />
                        <circle cx="100" cy="100" r="24" stroke="currentColor" stroke-width="1" fill="none" />
                        
                        <!-- Hub -->
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
                        
                        <!-- 8 Minor Spokes -->
                        <g transform="rotate(22.5, 100, 100)">
                            <line x1="100" y1="15" x2="100" y2="70" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 2" />
                            <line x1="100" y1="130" x2="100" y2="185" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 2" />
                            <line x1="15" y1="100" x2="70" y2="100" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 2" />
                            <line x1="130" y1="100" x2="185" y2="100" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 2" />
                        </g>
                    </svg>
                </div>


                <!-- Statistics Grid -->
                <div class="stats-grid" aria-label="Department Statistics">
                    <div class="stat-card">
                        <div class="stat-icon-wrapper"><i class="fa-solid fa-map-location-dot"></i></div>
                        <span class="stat-number" data-target="30">0</span>
                        <span class="stat-label">Districts Active</span>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-wrapper"><i class="fa-solid fa-shield-halved"></i></div>
                        <span class="stat-number" data-target="100" data-suffix="%">0%</span>
                        <span class="stat-label">Statewide Saturation</span>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-wrapper"><i class="fa-solid fa-hands-holding-child"></i></div>
                        <span class="stat-number" data-target="15" data-suffix="+">0</span>
                        <span class="stat-label">Welfare Schemes Active</span>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-wrapper"><i class="fa-solid fa-users-line"></i></div>
                        <span class="stat-number" data-target="56" data-suffix="L+">0</span>
                        <span class="stat-label">Welfare Beneficiaries</span>
                    </div>                    
                </div>
            </section>

            <!-- RIGHT COLUMN: Secure Login Panel (40%) -->
            <section class="login-column" aria-label="Account Access">
                <div class="login-card">
                    <div class="login-header">
                        <h2 class="login-title">Welcome to SSEPD</h2>
                        <p class="login-subtitle">Access your SSEPD account securely</p>
                    </div>

                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="login_type" value="institute">
                        <!-- Username Field -->
                        <div class="form-group">
                            <label for="username" class="form-label">User ID</label>
                            <div class="input-wrapper">
                                <input type="text" id="username" class="form-input @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Enter User ID" required aria-required="true" autofocus>
                                <i class="fa-solid fa-user input-icon"></i>
                            </div>
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                               <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="password" class="form-input @error('password') is-invalid @enderror" name="password" placeholder="Enter password" required aria-required="true">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Show password" id="passwordToggleBtn">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                               <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Server-side CAPTCHA Block -->
                        <div class="captcha-container">
                            <span class="form-label" style="margin-bottom:0;">Security Verification</span>
                            <div class="captcha-display-row">
                                <div class="captcha-canvas-wrap" id="captcha-img">
                                    {!! captcha_img('math_blue') !!}
                                </div>
                                <button type="button" class="btn-refresh-captcha btn-refresh" aria-label="Refresh Captcha Code">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                                <div class="captcha-input-wrap">
                                    <input type="text" id="captcha" class="form-input {{ $errors->has('captcha') ? ' has-error' : '' }}" name="captcha" style="padding-left:14px;" placeholder="Enter Answer" required aria-required="true">
                                </div>
                            </div>
                            @if ($errors->has('captcha'))
                            <span class="invalid-feedback" role="alert">
                               <strong>{{ $errors->first('captcha') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!-- Checkbox & Link Row -->
                        <div class="form-actions-row">
                            <label class="checkbox-container">
                                <input type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                                <span>Remember Me</span>
                            </label>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                            @else
                            <a href="#" class="forgot-link" onclick="handleForgotPassword(event)">Forgot Password?</a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span>Secure Sign In</span>
                            <i class="fa-solid fa-shield-halved"></i>
                        </button>
                    </form>
                </div>

                <!-- Quick Action Buttons -->
                <!-- <div class="quick-actions-panel" aria-label="Quick action links">
                    <a href="#" class="quick-action-btn" onclick="simulateQuickAction('Apply for Aids & Appliances', event)">
                        <span style="display:flex; align-items:center; gap:12px;">
                            <i class="fa-solid fa-wheelchair action-icon"></i>
                            Apply for Aids & Appliances
                        </span>
                        <i class="fa-solid fa-chevron-right arrow-icon"></i>
                    </a>
                    
                    <a href="#" class="quick-action-btn" onclick="simulateQuickAction('Vendor Registration', event)">
                        <span style="display:flex; align-items:center; gap:12px;">
                            <i class="fa-solid fa-briefcase action-icon"></i>
                            Vendor Registration
                        </span>
                        <i class="fa-solid fa-chevron-right arrow-icon"></i>
                    </a>

                    <a href="#" class="quick-action-btn" onclick="simulateQuickAction('Track Beneficiary Application', event)">
                        <span style="display:flex; align-items:center; gap:12px;">
                            <i class="fa-solid fa-magnifying-glass-location action-icon"></i>
                            Track Beneficiary Application
                        </span>
                        <i class="fa-solid fa-chevron-right arrow-icon"></i>
                    </a>
                </div> -->
            </section>

        </div>
    </main>

    <!-- Official Government Footer -->
    <footer role="contentinfo">
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-brand">
                    <span class="footer-logo-title">Social Security & Empowerment of Persons with Disabilities Department</span>
                    <span class="footer-subtitle">Providing a structured framework of therapeutic support, disability rehabilitation, education, and elder care across Odisha.</span>
                </div>
                
                <div class="footer-links-group">
                    <span class="footer-links-title">Quick Navigation</span>
                    <ul class="footer-links">
                        <li><a href="https://ssepd.odisha.gov.in" target="_blank">Main Department Website</a></li>
                        <!-- <li><a href="#" onclick="event.preventDefault(); alert('Redirecting to Odisha State Grievance Redressal...')">State Grievance Portal</a></li>
                        <li><a href="#" onclick="event.preventDefault(); alert('Redirecting to Odisha One Services...')">Odisha One Portal</a></li> -->
                    </ul>
                </div>

                <div class="footer-links-group">
                    <span class="footer-links-title">Department Helpdesk</span>
                    <ul class="footer-links" style="color: #8c9fae;">
                        <li><i class="fa-solid fa-envelope" style="color:var(--saffron-accent); margin-right:6px;"></i> ssepdhelpdesk@gmail.com</li>
                        <!-- <li><i class="fa-solid fa-phone" style="color:var(--saffron-accent); margin-right:6px;"></i> 1800-345-6770 (Toll Free)</li>
                        <li><i class="fa-solid fa-clock" style="color:var(--saffron-accent); margin-right:6px;"></i> Mon - Sat: 10:00 AM - 5:30 PM</li> -->
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; 2026 Social Security & Empowerment of Persons with Disabilities Department, Government of Odisha. All Rights Reserved.</span>
                
                <div class="footer-bottom-links">
                    <a href="#" onclick="simulateFooterLink('Privacy Policy', event)">Privacy Policy</a>
                    <a href="#" onclick="simulateFooterLink('Terms of Use', event)">Terms of Use</a>
                    <a href="#" onclick="simulateFooterLink('Contact Us', event)">Contact Us</a>
                </div>

                <div class="nic-badge">
                    <span>Designed & Developed by</span>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/ec/NIC_logo.svg" alt="National Informatics Centre Logo" onerror="this.onerror=null; this.style.display='none'; document.getElementById('nicFallback').style.display='inline';">
                    <span id="nicFallback" style="display:none; font-weight:700; color:white; letter-spacing:0.5px;">SSEPD</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Alerts Container -->
    <div id="toastNotification" class="toast-notification" role="status" aria-live="polite">
        <i id="toastIcon" class="fa-solid fa-circle-check"></i>
        <span id="toastMessage">Feedback updated successfully.</span>
    </div>

    <!-- ==========================================================================
       Vanilla JavaScript Functions & Interactions
       ========================================================================== -->
    <script>
        // Font scaling level
        let fontScalePercent = 100;

        // On document ready
        window.addEventListener('DOMContentLoaded', () => {
            // Initialize animations
            animateStats();
            initParticles();
            animateParticles();
            setupSubmitButtonRipple();
            setupCaptchaRefresh();
        });

        // 1. Stats increment animation
        function animateStats() {
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target'), 10);
                const suffix = stat.getAttribute('data-suffix') || "";
                let current = 0;
                const duration = 1200; // ms
                const increment = target / (duration / 16); // 60 FPS
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.innerText = target + suffix;
                        clearInterval(timer);
                    } else {
                        stat.innerText = Math.floor(current) + suffix;
                    }
                }, 16);
            });
        }

        // 2. Floating particle effects (HTML5 Canvas overlay)
        const canvasParticles = document.getElementById("particleCanvas");
        let particlesArray = [];
        
        function resizeParticleCanvas() {
            if (!canvasParticles) return;
            const rect = canvasParticles.parentElement.getBoundingClientRect();
            canvasParticles.width = rect.width;
            canvasParticles.height = rect.height;
        }

        class FloatingDot {
            constructor(canvasW, canvasH) {
                this.canvasW = canvasW;
                this.canvasH = canvasH;
                this.reset();
            }
            reset() {
                this.x = Math.random() * this.canvasW;
                this.y = this.canvasH + Math.random() * 80;
                this.size = Math.random() * 3.5 + 1;
                this.speedY = Math.random() * 0.7 + 0.3;
                this.alpha = Math.random() * 0.4 + 0.1;
                this.drift = (Math.random() - 0.5) * 0.3;
            }
            update() {
                this.y -= this.speedY;
                this.x += this.drift;
                if (this.y < -10 || this.x < -10 || this.x > this.canvasW + 10) {
                    this.reset();
                }
            }
            draw(ctx) {
                ctx.fillStyle = `rgba(21, 75, 138, ${this.alpha})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function initParticles() {
            if (!canvasParticles) return;
            resizeParticleCanvas();
            particlesArray = [];
            const particleCount = 25;
            for (let i = 0; i < particleCount; i++) {
                particlesArray.push(new FloatingDot(canvasParticles.width, canvasParticles.height));
            }
            window.addEventListener('resize', () => {
                resizeParticleCanvas();
                particlesArray.forEach(p => {
                    p.canvasW = canvasParticles.width;
                    p.canvasH = canvasParticles.height;
                });
            });
        }

        function animateParticles() {
            if (!canvasParticles) return;
            const ctx = canvasParticles.getContext("2d");
            ctx.clearRect(0, 0, canvasParticles.width, canvasParticles.height);
            particlesArray.forEach(p => {
                p.update();
                p.draw(ctx);
            });
            requestAnimationFrame(animateParticles);
        }

        // 3. Accessibility resizing handlers
        function changeFontSize(action) {
            if (action === 'increase') {
                if (fontScalePercent < 120) fontScalePercent += 5;
            } else if (action === 'decrease') {
                if (fontScalePercent > 85) fontScalePercent -= 5;
            } else {
                fontScalePercent = 100;
            }
            document.documentElement.style.fontSize = fontScalePercent + '%';
        }

        // 4. Contrast toggle handler
        function toggleContrast() {
            document.body.classList.toggle('high-contrast');
            const isHigh = document.body.classList.contains('high-contrast');
            showToast(isHigh ? "High contrast mode enabled." : "Standard contrast restored.", false);
        }

        // 5. Toast Notification alerts
        function showToast(message, isError = false) {
            const toast = document.getElementById("toastNotification");
            const icon = document.getElementById("toastIcon");
            const text = document.getElementById("toastMessage");
            
            if (!toast) return;

            text.innerText = message;
            if (isError) {
                toast.classList.add('error');
                icon.className = "fa-solid fa-circle-exclamation";
            } else {
                toast.classList.remove('error');
                icon.className = "fa-solid fa-circle-check";
            }

            toast.classList.add("show");
            setTimeout(() => {
                toast.classList.remove("show");
            }, 4000);
        }

        // 6. Input forms and password visibility toggler
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById("password");
            const toggleBtn = document.getElementById("passwordToggleBtn");
            if (!pwdInput || !toggleBtn) return;

            if (pwdInput.type === "password") {
                pwdInput.type = "text";
                toggleBtn.querySelector("i").className = "fa-solid fa-eye-slash";
                toggleBtn.setAttribute("aria-label", "Hide password");
            } else {
                pwdInput.type = "password";
                toggleBtn.querySelector("i").className = "fa-solid fa-eye";
                toggleBtn.setAttribute("aria-label", "Show password");
            }
        }

        // Forgot password callback
        function handleForgotPassword(event) {
            event.preventDefault();
            const userVal = document.getElementById("username").value.trim();
            if (userVal) {
                showToast(`Reset links and OTP requested for account: ${userVal}`, false);
            } else {
                showToast("Please enter your User ID or Email in the username box first.", true);
            }
        }

        // Quick action simulation
        function simulateQuickAction(actionName, event) {
            event.preventDefault();
            showToast(`Opening: ${actionName}. Accessing secure service...`, false);
        }

        // Footer links simulation
        function simulateFooterLink(linkName, event) {
            event.preventDefault();
            showToast(`Navigating to ${linkName}...`, false);
        }

        // Button Ripple Effect handler
        function setupSubmitButtonRipple() {
            const btn = document.getElementById("submitBtn");
            if (!btn) return;
            
            btn.addEventListener("click", function (e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const ripples = document.createElement("span");
                ripples.className = "ripple";
                ripples.style.left = x + "px";
                ripples.style.top = y + "px";
                this.appendChild(ripples);
                
                setTimeout(() => {
                    ripples.remove();
                }, 600);
            });
        }

        // CAPTCHA reload using dynamic AJAX Fetch (matching route("refreshCaptcha"))
        function setupCaptchaRefresh() {
            const refreshBtn = document.querySelector('.btn-refresh');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch('{{ route("refreshCaptcha") }}')
                        .then(response => response.json())
                        .then(data => {
                            const captchaImgContainer = document.getElementById('captcha-img');
                            if (captchaImgContainer) {
                                captchaImgContainer.innerHTML = data.captcha;
                            }
                        })
                        .catch(error => {
                            alert('Captcha reload failed. Please check your setup.');
                        });
                });
            }
        }
    </script>
</body>
</html>