<?php
/* member/dashboard.php  —  ultra-modern member portal */

include '../config/db.php';
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}


/* ---- fetch member data ---- */
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $_SESSION['member_id']);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

/* ---- QR code URL ---- */
$qrData = "https://{$_SERVER['HTTP_HOST']}/verify.php?no=" . urlencode($m['membership_number']);
$qrUrl  = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($qrData);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --bg: #0f172a;
            --panel: #1e293b;
            --primary: #6366f1;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --muted: #94a3b8;
            --text: #e2e8f0;
            --border: rgba(255,255,255,.08);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding-top: 70px;
        }

        /* Navbar */
        .navbar {
            background: rgba(30, 41, 59, .8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .navbar-brand {
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Cards */
        .dashboard-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            transition: transform .3s, box-shadow .3s;
        }
        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,.35);
        }
        .stat-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-card .icon {
            width: 50px;
            height: 50px;
            margin: 0 auto .75rem;
            display: grid;
            place-items: center;
            border-radius: 12px;
            font-size: 1.5rem;
            color: #fff;
        }
        .stat-card.status .icon { background: linear-gradient(135deg, var(--success), #059669); }
        .stat-card.number .icon { background: linear-gradient(135deg, var(--primary), #8b5cf6); }
        .stat-card.type .icon { background: linear-gradient(135deg, var(--warning), #d97706); }

        /* QR Code */
        .qr-container {
            background: #fff;
            padding: 1rem;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        /* Buttons */
        .btn-action {
            border: 1px solid var(--border);
            background: rgba(255,255,255,.05);
            color: var(--text);
            border-radius: 12px;
            padding: .75rem;
            transition: all .3s;
            text-align: left;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .btn-action:hover {
            background: rgba(255,255,255,.08);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateX(4px);
        }

        /* Welcome banner */
        .welcome-banner {
            background: linear-gradient(135deg, rgba(99,102,241,.15), rgba(139,92,246,.15));
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <span class="navbar-brand">Member Portal</span>
        <div class="ms-auto d-flex gap-2">
            <a href="../auth/change-password.php" class="btn btn-sm btn-outline-light">
                <i class="bi bi-shield-lock"></i> Change Password
            </a>
            <a href="../auth/logout.php" class="btn btn-sm btn-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-25 p-3">
                <i class="bi bi-person-circle fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0">Welcome back, <?= htmlspecialchars($m['first_name']) ?>!</h4>
                <p class="mb-0 text-muted">Member since <?= date('F Y', strtotime($m['created_at'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card status">
                <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
                <h5 class="mb-1"><?= $m['status'] ?></h5>
                <p class="mb-0 text-muted small">Membership Status</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card number">
                <div class="icon"><i class="bi bi-person-badge-fill"></i></div>
                <h5 class="mb-1"><?= $m['membership_number'] ?: 'Pending' ?></h5>
                <p class="mb-0 text-muted small">Membership Number</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card type">
                <div class="icon"><i class="bi bi-award-fill"></i></div>
                <h5 class="mb-1"><?= $m['membership_type'] ?></h5>
                <p class="mb-0 text-muted small">Membership Type</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- QR Code Section -->
        <div class="col-lg-6">
            <div class="dashboard-card p-4">
                <h5 class="mb-3"><i class="bi bi-qr-code-scan me-2"></i>Membership QR Code</h5>
                <div class="text-center">
                    <div class="qr-container">
                        <img src="<?= $qrUrl ?>" alt="Membership QR Code" class="img-fluid">
                    </div>
                    <p class="text-muted small mb-0">
                        Present this QR code for instant membership verification
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-6">
            <div class="dashboard-card p-4">
                <h5 class="mb-3"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="profile.php" class="btn-action">
                        <i class="bi bi-person-vcard text-primary"></i>
                        <div>
                            <strong>View Profile</strong>
                            <small class="text-muted d-block">Update your personal information</small>
                        </div>
                    </a>
                    <a href="card.php" class="btn-action">
                        <i class="bi bi-file-earmark-pdf text-success"></i>
                        <div>
                            <strong>Download Card</strong>
                            <small class="text-muted d-block">Get your membership card (PDF)</small>
                        </div>
                    </a>
                    <a href="../auth/change-password.php" class="btn-action">
                        <i class="bi bi-shield-lock text-warning"></i>
                        <div>
                            <strong>Change Password</strong>
                            <small class="text-muted d-block">Secure your account</small>
                        </div>
                    </a>
                    <a href="../auth/logout.php" class="btn-action">
                        <i class="bi bi-box-arrow-right text-danger"></i>
                        <div>
                            <strong>Logout</strong>
                            <small class="text-muted d-block">End your session</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>