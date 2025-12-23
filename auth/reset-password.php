<?php
session_start();
include '../config/db.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    $_SESSION['flash_error'] = 'Invalid or missing reset token.';
    header('Location: login.php');
    exit;
}

$now = date('Y-m-d H:i:s');
$table = '';
$userId = null;

// Check admins
$stmt = $conn->prepare("SELECT id FROM admins WHERE reset_token = ? AND reset_expires > ?");
$stmt->bind_param('ss', $token, $now);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 1) {
    $table = 'admins';
    $userId = $res->fetch_assoc()['id'];
} else {
    // Check members
    $stmt = $conn->prepare("SELECT id FROM members WHERE reset_token = ? AND reset_expires > ?");
    $stmt->bind_param('ss', $token, $now);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 1) {
        $table = 'members';
        $userId = $res->fetch_assoc()['id'];
    }
}

if (!$table) {
    $_SESSION['flash_error'] = 'Reset link expired or invalid.';
    header('Location: login.php');
    exit;
}

if (isset($_POST['reset'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE $table SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->bind_param('si', $password, $userId);
    $stmt->execute();

    $_SESSION['flash_success'] = 'Password updated! Please log in.';
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --bg: #0f172a;
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --glass: rgba(255, 255, 255, .06);
            --success: #10b981;
            --error: #ef4444;
        }

        body {
            background: radial-gradient(ellipse at top left, var(--primary), transparent 40%),
                        radial-gradient(ellipse at bottom right, var(--secondary), transparent 40%),
                        var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml;utf8,\
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
            <defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">\
            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,.03)" stroke-width=".5"/>\
            </pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            mask-image: radial-gradient(circle at center, black, transparent 70%);
            pointer-events: none;
        }

        .reset-card {
            background: var(--glass);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 24px;
            backdrop-filter: blur(24px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .5);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            animation: slideIn .6s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reset-card h4 {
            font-weight: 800;
            background: linear-gradient(135deg, #fff, var(--muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-control {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            color: var(--text);
            border-radius: 12px;
            padding: .75rem 1rem;
            transition: all .3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, .08);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, .25);
            color: #fff;
        }

        .form-label {
            font-size: .875rem;
            font-weight: 500;
            margin-bottom: .5rem;
            color: var(--muted);
        }

        .btn-reset {
            background: linear-gradient(90deg, var(--success), #059669);
            border: none;
            border-radius: 50px;
            font-weight: 600;
            padding: .75rem;
            color: #fff;
            box-shadow: 0 0 20px var(--success);
            transition: all .3s;
            position: relative;
            overflow: hidden;
        }

        .btn-reset::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
            transform: translateX(-100%);
            transition: transform .6s;
        }

        .btn-reset:hover::before {
            transform: translateX(100%);
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 40px #059669;
        }

        .floating-icon {
            position: absolute;
            font-size: 6rem;
            opacity: .04;
            animation: float 8s ease-in-out infinite;
        }

        .floating-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .floating-icon:nth-child(2) { bottom: 10%; right: 10%; animation-delay: 2s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>

<i class="bi bi-shield-lock floating-icon"></i>
<i class="bi bi-key floating-icon"></i>

<div class="reset-card">
    <h4><i class="bi bi-arrow-repeat me-2"></i>Reset Password</h4>
    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter new password" required minlength="6">
        </div>
        <button type="submit" name="reset" class="btn btn-reset w-100">
            <i class="bi bi-check-circle me-2"></i>Update Password
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>