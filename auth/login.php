<?php

include '../config/db.php';

$error = "";
if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    /* ---------- 1. ADMIN ---------- */
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['role']     = 'admin';
            header("Location: ../admin/dashboard.php");
            exit;
        }
        $error = "Invalid credentials.";
    } else {
        /* ---------- 2. MEMBER ---------- */
        $stmt = $conn->prepare("SELECT id, password, status FROM members WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if (!password_verify($password, $row['password'])) {
                $error = "Invalid credentials.";
            } elseif ($row['status'] !== 'Approved') {
                $error = "Membership pending approval.";
            } else {
                $_SESSION['member_id'] = $row['id'];
                $_SESSION['role']      = 'member';
                header("Location: ../member/dashboard.php");
                exit;
            }
        } else {
            $error = "Account not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — Membership System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root{
            --bg:#0f172a;
            --primary:#6366f1;
            --secondary:#8b5cf6;
            --accent:#06b6d4;
            --text:#e2e8f0;
            --muted:#94a3b8;
            --glass:rgba(255,255,255,.06);
            --error:#ef4444;
        }
        body{
            background:radial-gradient(ellipse at top left,var(--primary),transparent 40%),
                       radial-gradient(ellipse at bottom right,var(--secondary),transparent 40%),
                       var(--bg);
            color:var(--text);
            font-family:'Inter',sans-serif;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            position:relative;
        }
        body::before{
            content:"";
            position:absolute;
            inset:0;
            background:url('data:image/svg+xml;utf8,\
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">\
            <defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">\
            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,.03)" stroke-width=".5"/>\
            </pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            mask-image:radial-gradient(circle at center,black,transparent 70%);
            pointer-events:none;
        }
        .login-card{
            background:var(--glass);
            border:1px solid rgba(255,255,255,.08);
            border-radius:20px;
            backdrop-filter:blur(20px);
            box-shadow:0 25px 50px -12px rgba(0,0,0,.5);
            padding:2.5rem 2rem;
            width:100%;
            max-width:420px;
            animation:slideIn .6s ease-out;
        }
        @keyframes slideIn{
            from{opacity:0;transform:translateY(30px);}
            to{opacity:1;transform:translateY(0);}
        }
        .login-card h3{
            font-weight:800;
            background:linear-gradient(135deg,#fff,var(--muted));
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            margin-bottom:1.5rem;
        }
        .form-control{
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.1);
            color:var(--text);
            border-radius:12px;
            padding:.75rem 1rem;
            transition:all .3s;
        }
        .form-control:focus{
            background:rgba(255,255,255,.08);
            border-color:var(--accent);
            box-shadow:0 0 0 3px rgba(6,182,212,.25);
            color:#fff;
        }
        .form-label{
            font-size:.875rem;
            font-weight:500;
            margin-bottom:.5rem;
            color:var(--muted);
        }
        .btn-login{
            background:linear-gradient(90deg,var(--primary),var(--secondary));
            border:none;
            border-radius:50px;
            font-weight:600;
            padding:.75rem;
            color:#fff;
            box-shadow:0 0 20px var(--primary);
            transition:all .3s;
            position:relative;
            overflow:hidden;
        }
        .btn-login::before{
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);
            transform:translateX(-100%);
            transition:transform .6s;
        }
        .btn-login:hover::before{
            transform:translateX(100%);
        }
        .btn-login:hover{
            transform:translateY(-2px);
            box-shadow:0 0 40px var(--secondary);
        }
        .alert-error{
            background:rgba(239,68,68,.12);
            border:1px solid rgba(239,68,68,.25);
            color:#fecaca;
            border-radius:12px;
            padding:.75rem 1rem;
            font-size:.875rem;
            margin-bottom:1rem;
            animation:shake .5s;
        }
        @keyframes shake{
            0%,100%{transform:translateX(0);}
            25%{transform:translateX(-5px);}
            75%{transform:translateX(5px);}
        }
        .link-muted{
            color:var(--muted);
            text-decoration:none;
            font-size:.875rem;
            transition:color .3s;
        }
        .link-muted:hover{
            color:var(--accent);
        }
        .floating-icon{
            position:absolute;
            font-size:6rem;
            opacity:.04;
            animation:float 8s ease-in-out infinite;
        }
        .floating-icon:nth-child(1){top:10%;left:10%;animation-delay:0s;}
        .floating-icon:nth-child(2){bottom:10%;right:10%;animation-delay:2s;}
        @keyframes float{
            0%,100%{transform:translateY(0) rotate(0deg);}
            50%{transform:translateY(-20px) rotate(180deg);}
        }
    </style>
</head>
<body>

<i class="bi bi-shield-lock floating-icon"></i>
<i class="bi bi-person-badge floating-icon"></i>

<div class="login-card">
    <h3 class="text-center">Welcome Back</h3>

    <?php if ($error): ?>
        <div class="alert-error text-center">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn btn-login w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="register.php" class="link-muted">New member? <strong>Create account</strong></a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>