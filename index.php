<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit;
}
if (isset($_SESSION['member_id'])) {
    header("Location: member/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Membership System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --bg: #0f172a;
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --card-bg: rgba(255, 255, 255, 0.04);
            --glass: rgba(255, 255, 255, 0.08);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .navbar {
            background: transparent;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass);
        }

        .navbar-brand {
            font-weight: 900;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: var(--text) !important;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--accent) !important;
            transform: translateY(-2px);
        }

        .hero {
            position: relative;
            min-height: 90vh;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, var(--primary), transparent 40%),
                        radial-gradient(circle at 70% 70%, var(--secondary), transparent 40%);
            animation: rotate 20s linear infinite;
            opacity: 0.25;
            z-index: 0;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            line-height: 1.1;
            background: linear-gradient(135deg, #fff, var(--muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.125rem;
            color: var(--muted);
            max-width: 500px;
        }

        .btn-glow {
            position: relative;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: #fff;
            box-shadow: 0 0 20px var(--primary);
            transition: all 0.3s;
            overflow: hidden;
        }

        .btn-glow::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: radial-gradient(circle, rgba(255,255,255,0.3), transparent 40%);
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.5s;
        }

        .btn-glow:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }

        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 40px var(--secondary);
        }

        .btn-outline {
            border: 2px solid var(--glass);
            background: transparent;
            color: var(--text);
            backdrop-filter: blur(10px);
        }

        .btn-outline:hover {
            border-color: var(--accent);
            color: var(--accent);
            box-shadow: 0 0 20px var(--accent);
        }

        .btn-verify {
            border: 2px solid var(--glass);
            background: transparent;
            color: var(--text);
            backdrop-filter: blur(5px);
        }

        .btn-verify:hover {
            border-color: var(--accent);
            color: var(--accent);
            box-shadow: 0 0 40px var(--secondary);
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--glass);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-color: var(--accent);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            font-size: 1.5rem;
            color: #fff;
        }

        footer {
            background: transparent;
            border-top: 1px solid var(--glass);
            color: var(--muted);
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 50%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 60%;
            right: 10%;
            width: 120px;
            height: 120px;
            background: var(--secondary);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            width: 60px;
            height: 60px;
            background: var(--accent);
            transform: rotate(45deg);
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Membership System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1>Next-Gen Membership Management</h1>
                <p>Experience seamless registration, secure verification, and instant QR code validation in one powerful platform.</p>
                <div class="mt-4 d-flex gap-3">
                    <a href="auth/register.php" class="btn btn-glow">Join Now</a>
                    <a href="auth/login.php" class="btn btn-outline">Member Login</a>
                    <a href="verify.php" class="btn btn-verify">Verify Member</a>
                </div>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
                     class="img-fluid" 
                     style="max-height: 400px; filter: drop-shadow(0 20px 40px rgba(99, 102, 241, 0.3));">
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5" data-aos="fade-up">Key Features</h2>
        <div class="row g-4">
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <h5>Easy Registration</h5>
                    <p class="text-muted">Streamlined onboarding for new and existing members</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Admin Approval</h5>
                    <p class="text-muted">Multi-layer verification for maximum security</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <h5>QR Code ID</h5>
                    <p class="text-muted">Instant digital membership validation</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="bi bi-envelope-check"></i>
                    </div>
                    <h5>Email Alerts</h5>
                    <p class="text-muted">Real-time notifications for all actions</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> Membership System. All rights reserved.</p>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>
</body>
</html>