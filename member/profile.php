<?php
/* member/profile.php  —  ultra-modern profile editor */
include '../config/db.php';
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}



$id = $_SESSION['member_id'];
$success = $error = '';

/* ---- handle update ---- */
if (isset($_POST['update'])) {
    $first = trim($_POST['first_name']);
    $last  = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);

    if ($first && $last) {
        $stmt = $conn->prepare("UPDATE members SET first_name=?, last_name=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $first, $last, $phone, $id);
        $stmt->execute();
        $success = 'Profile updated successfully.';
    } else {
        $error = 'First and last name are required.';
    }
}

/* ---- fetch latest data ---- */
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --bg: #0f172a;
            --panel: #1e293b;
            --primary: #6366f1;
            --success: #10b981;
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

        /* Card */
        .profile-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,.35);
            animation: slideIn .6s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control, .form-control:disabled {
            background: rgba(255,255,255,.05);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 12px;
            padding: .75rem 1rem;
            transition: all .3s;
        }
        .form-control:focus {
            background: rgba(255,255,255,.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.25);
            color: #fff;
        }
        .form-label {
            font-size: .875rem;
            font-weight: 500;
            margin-bottom: .5rem;
            color: var(--muted);
        }

        .btn-update {
            background: linear-gradient(90deg, var(--primary), #8b5cf6);
            border: none;
            border-radius: 50px;
            font-weight: 600;
            padding: .75rem;
            color: #fff;
            box-shadow: 0 0 20px var(--primary);
            transition: all .3s;
            position: relative;
            overflow: hidden;
        }
        .btn-update::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.25), transparent);
            transform: translateX(-100%);
            transition: transform .6s;
        }
        .btn-update:hover::before {
            transform: translateX(100%);
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 40px #8b5cf6;
        }

        .alert-success {
            background: rgba(16,185,129,.12);
            border: 1px solid rgba(16,185,129,.25);
            color: #a7f3d0;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1rem;
            animation: fadeIn .5s;
        }
        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            color: #fecaca;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1rem;
            animation: shake .5s;
        }
        @keyframes fadeIn { from { opacity: 0; transform: scale(.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes shake { 0%,100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: .5rem 0;
            border-bottom: 1px solid var(--border);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--muted); font-size: .875rem; }
        .info-value { font-weight: 600; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <span class="navbar-brand">My Profile</span>
        <a href="dashboard.php" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                <h4 class="mb-4"><i class="bi bi-person-gear me-2"></i>Personal Information</h4>

                <?php if ($success): ?>
                    <div class="alert-success text-center">
                        <i class="bi bi-check-circle me-2"></i><?= $success ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert-error text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" 
                                   value="<?= htmlspecialchars($m['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" 
                                   value="<?= htmlspecialchars($m['last_name']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($m['email']) ?>" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($m['phone']) ?>" placeholder="0240000000">
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3"><i class="bi bi-card-heading me-2"></i>Membership Details</h5>
                    
                    <div class="info-row">
                        <span class="info-label">Membership Number</span>
                        <span class="info-value"><?= $m['membership_number'] ?: 'Pending Assignment' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="badge bg-success"><?= $m['status'] ?></span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Membership Type</span>
                        <span class="info-value"><?= $m['membership_type'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Member Since</span>
                        <span class="info-value"><?= date('F d, Y', strtotime($m['created_at'])) ?></span>
                    </div>

                    <button type="submit" name="update" class="btn btn-update w-100 mt-4">
                        <i class="bi bi-check-lg me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>