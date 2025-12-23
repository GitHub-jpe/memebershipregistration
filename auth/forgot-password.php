<?php
include '../config/db.php';

$msg = '';
$error = '';

if (isset($_POST['send'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Try admins
        $stmt = $conn->prepare("UPDATE admins SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->bind_param('sss', $token, $expires, $email);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            // Try members
            $stmt = $conn->prepare("UPDATE members SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->bind_param('sss', $token, $expires, $email);
            $stmt->execute();
        }

        if ($stmt->affected_rows > 0) {
            $link = "http://{$_SERVER['HTTP_HOST']}/auth/reset-password.php?token={$token}";
            mail($email, 'Password Reset', "Hi,\n\nClick the link below to reset your password:\n{$link}\n\nThis link expires in 1 hour.\n\nIf you did not request this, please ignore this email.");
        }

        $_SESSION['flash_info'] = 'If the email exists, a reset link has been sent.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (isset($_SESSION['flash_info'])) {
    $msg = $_SESSION['flash_info'];
    unset($_SESSION['flash_info']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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
            --info: #0ea5e9;
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

        .forgot-card {
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

        .forgot-card h4 {
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

        .btn-send {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
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

        .btn-send::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
            transform: translateX(-100%);
            transition: transform .6s;
        }

        .btn-send:hover::before {
            transform: translateX(100%);
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 40px var(--secondary);
        }

        .alert-info {
            background: rgba(14, 165, 233, .12);
            border: 1px solid rgba(14, 165, 233, .25);
            color: #bae6fd;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1rem;
            animation: fadeIn .5s;
        }

        .alert-error {
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(239, 68, 68, .25);
            color: #fecaca;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1rem;
            animation: shake .5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .link-muted {
            color: var(--muted);
            text-decoration: none;
            font-size: .875rem;
            transition: color .3s;
        }

        .link-muted:hover {
            color: var(--accent);
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

<i class="bi bi-envelope-open floating-icon"></i>
<i class="bi bi-key floating-icon"></i>

<div class="forgot-card">
    <h4><i class="bi bi-shield-lock me-2"></i>Forgot Password</h4>

    <?php if ($error): ?>
        <div class="alert-error text-center">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg): ?>
        <div class="alert-info text-center">
            <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
        </div>
        <button type="submit" name="send" class="btn btn-send w-100">
            <i class="bi bi-send me-2"></i>Send Reset Link
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" class="link-muted">Remember your password? <strong>Sign in</strong></a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>