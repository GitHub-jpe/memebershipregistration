<?php
include '../config/db.php';

/* ---------- HANDLE SUBMISSION ---------- */
if (isset($_POST['register'])) {
    $regType   = $_POST['registration_type'];
    $memNo     = $regType === 'Existing' ? trim($_POST['membership_number']) : null;
    $first     = trim($_POST['first_name']);
    $last      = trim($_POST['last_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $hash      = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $memType   = $_POST['membership_type'];
    $agreed    = isset($_POST['agreed_terms']) ? 1 : 0;

    $stmt = $conn->prepare("
        INSERT INTO members 
        (membership_number, first_name, last_name, email, phone, password, membership_type, registration_type, agreed_terms, status)
        VALUES (?,?,?,?,?,?,?,?,?,'Pending')
    ");
    $stmt->bind_param("ssssssssi", $memNo, $first, $last, $email, $phone, $hash, $memType, $regType, $agreed);
    $stmt->execute();

    $_SESSION['flash'] = 'Registration submitted for approval.';
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join — Membership System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --success:#10b981;
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
            padding:2rem 1rem;
            position:relative;
            overflow-x:hidden;
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
        .register-card{
            background:var(--glass);
            border:1px solid rgba(255,255,255,.08);
            border-radius:24px;
            backdrop-filter:blur(24px);
            box-shadow:0 25px 50px -12px rgba(0,0,0,.5);
            padding:2.5rem 2rem;
            width:100%;
            max-width:520px;
            animation:slideIn .6s ease-out;
        }
        @keyframes slideIn{
            from{opacity:0;transform:translateY(30px);}
            to{opacity:1;transform:translateY(0);}
        }
        .register-card h3{
            font-weight:800;
            background:linear-gradient(135deg,#fff,var(--muted));
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            margin-bottom:1.5rem;
        }
        .form-control,.form-select{
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.1);
            color:var(--text);
            border-radius:12px;
            padding:.75rem 1rem;
            transition:all .3s;
        }
        .form-control:focus,.form-select:focus{
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
        .btn-register{
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
        .btn-register::before{
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);
            transform:translateX(-100%);
            transition:transform .6s;
        }
        .btn-register:hover::before{
            transform:translateX(100%);
        }
        .btn-register:hover{
            transform:translateY(-2px);
            box-shadow:0 0 40px var(--secondary);
        }
        .form-check-input:checked{
            background-color:var(--accent);
            border-color:var(--accent);
        }
        .alert-success{
            background:rgba(16,185,129,.12);
            border:1px solid rgba(16,185,129,.25);
            color:#a7f3d0;
            border-radius:12px;
            padding:.75rem 1rem;
            font-size:.875rem;
            margin-bottom:1rem;
            animation:fadeIn .5s;
        }
        @keyframes fadeIn{
            from{opacity:0;transform:scale(.95);}
            to{opacity:1;transform:scale(1);}
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

<i class="bi bi-person-plus floating-icon"></i>
<i class="bi bi-card-checklist floating-icon"></i>

<div class="register-card">
    <h3 class="text-center">Create Your Account</h3>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert-success text-center">
            <i class="bi bi-check-circle me-2"></i><?= $_SESSION['flash'] ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">Registration Type</label>
            <select name="registration_type" id="regType" class="form-select" required>
                <option value="">Choose…</option>
                <option value="New">New Member</option>
                <option value="Existing">Existing Member</option>
            </select>
        </div>

        <div class="mb-3" id="memNoWrap" style="display:none;">
            <label class="form-label">Membership Number</label>
            <input type="text" name="membership_number" class="form-control" placeholder="e.g. M-12345">
        </div>

        <div class="row g-3 mb-3">
            <div class="col">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Membership Type</label>
            <select name="membership_type" class="form-select" required>
                <option value="">Select…</option>
                <option>Individual</option>
                <option>Corporate</option>
                <option>Student</option>
            </select>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="agreed_terms" id="terms" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="#" class="link-muted">Terms & Conditions</a>
            </label>
        </div>

        <button type="submit" name="register" class="btn btn-register w-100">
            <i class="bi bi-arrow-right-circle me-2"></i>Submit Registration
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" class="link-muted">Already have an account? <strong>Sign in</strong></a>
    </div>
</div>

<script>
    document.getElementById('regType').addEventListener('change', e => {
        document.getElementById('memNoWrap').style.display = e.target.value === 'Existing' ? 'block' : 'none';
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>