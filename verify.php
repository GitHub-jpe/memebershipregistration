<?php
/* verify.php  —  ultra-modern membership verifier */
include 'config/db.php';

$membershipNo = $_GET['no'] ?? '';
$member = null;
$error = '';

/* ---- handle GET verification ---- */
if ($membershipNo) {
    $stmt = $conn->prepare("SELECT first_name, last_name, status, membership_type FROM members WHERE membership_number = ?");
    $stmt->bind_param("s", $membershipNo);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();
    if (!$member) {
        $error = 'Membership number not found.';
    }
}

/* ---- handle POST manual verification ---- */
if (isset($_POST['verify'])) {
    $inputNo = trim($_POST['membership_no']);
    if ($inputNo) {
        header("Location: ?no=" . urlencode($inputNo));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Membership</title>
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
            background: radial-gradient(ellipse at top left, var(--primary), transparent 40%),
                        radial-gradient(ellipse at bottom right, var(--secondary, #8b5cf6), transparent 40%),
                        var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .verify-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.5);
            animation: slideIn .6s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-control {
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

        .btn-verify {
            background: linear-gradient(90deg, var(--primary), #8b5cf6);
            border: none;
            border-radius: 50px;
            font-weight: 600;
            padding: .75rem;
            color: #fff;
            box-shadow: 0 0 20px var(--primary);
            transition: all .3s;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 40px #8b5cf6;
        }

        .result-card {
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            text-align: center;
        }
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            display: grid;
            place-items: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
            color: #fff;
        }
        .badge-status {
            font-size: .875rem;
            padding: .5em 1em;
            border-radius: 999px;
            font-weight: 600;
        }
        .badge-approved { background: rgba(16,185,129,.15); color: var(--success); }
        .badge-pending  { background: rgba(245,158,11,.15); color: var(--warning, #f59e0b); }
        .badge-rejected { background: rgba(239,68,68,.15); color: var(--danger); }

        .error-msg {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            color: #fecaca;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .875rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="verify-card">
    <h4 class="text-center mb-4"><i class="bi bi-shield-check me-2"></i>Verify Membership</h4>

    <?php if (!$member && !$error): ?>
        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">Membership Number</label>
                <input type="text" name="membership_no" class="form-control" placeholder="e.g. M-12345" required>
            </div>
            <button type="submit" name="verify" class="btn btn-verify w-100">
                <i class="bi bi-search me-2"></i>Verify
            </button>
            
        </form>
        <a href="index.php" class="btn btn-verify w-100">Back</a>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-msg">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
        <div class="text-center mt-3">
            <a href="verify.php" class="btn btn-outline-light btn-sm">Try Again</a>
        </div>
    <?php endif; ?>

    <?php if ($member): ?>
        <div class="result-card">
            <div class="avatar">
                <i class="bi bi-person-check"></i>
            </div>
            <h5 class="mb-1"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h5>
            <p class="text-muted mb-2"><?= htmlspecialchars($member['membership_type']) ?></p>
            <span class="badge-status badge-<?= strtolower($member['status']) ?>">
                <?= $member['status'] ?>
            </span>
        </div>
        <div class="text-center mt-3">
            <a href="verify.php" class="btn btn-outline-light btn-sm">Verify Another</a>
            <a href="index.php" class="btn btn-outline-light btn-sm">Back</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>