
<?php include 'layout/header.php'; ?>

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
            display: flex;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: 260px;
            background: var(--panel);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 0;
        }
        .sidebar .brand {
            font-size: 1.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 2rem;
        }
        .sidebar a {
            color: var(--muted);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            font-weight: 500;
            transition: all .3s;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover,
        .sidebar a.active {
            color: var(--text);
            background: rgba(99,102,241,.12);
            border-left-color: var(--primary);
        }
        .sidebar a i {
            font-size: 1.1rem;
        }

        /* ---------- Main ---------- */
        .main {
            flex: 1;
            padding: 2rem 2.5rem;
            overflow-y: auto;
        }
        .page-title {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }

        /* ---------- Stat Cards ---------- */
        .stat-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem 1.25rem;
            text-align: center;
            transition: transform .3s, box-shadow .3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,.35);
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
        .stat-card.total   .icon { background: linear-gradient(135deg, var(--primary), #8b5cf6); }
        .stat-card.approved .icon { background: linear-gradient(135deg, var(--success), #059669); }
        .stat-card.pending  .icon { background: linear-gradient(135deg, var(--warning), #d97706); }
        .stat-card.rejected .icon { background: linear-gradient(135deg, var(--danger), #dc2626); }
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        .stat-card p {
            margin: 0;
            color: var(--muted);
            font-size: .875rem;
        }

        /* ---------- Recent Table ---------- */
        .recent-table {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }
        .recent-table th,
        .recent-table td {
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--border);
            font-size: .875rem;
        }
        .recent-table th {
            background: rgba(255,255,255,.04);
            font-weight: 600;
            color: var(--muted);
        }
        .recent-table tr:last-child td { border-bottom: none; }
        .badge-status {
            font-size: .75rem;
            padding: .35em .65em;
            border-radius: 999px;
            font-weight: 600;
        }
        .badge-approved { background: rgba(16,185,129,.15); color: var(--success); }
        .badge-pending  { background: rgba(245,158,11,.15); color: var(--warning); }
        .badge-rejected { background: rgba(239,68,68,.15); color: var(--danger); }
    </style>
</head>
<body>

<!-- ========== Sidebar ========== -->
<nav class="sidebar">
    <div class="brand">Admin Portal</div>
    <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="members.php"><i class="bi bi-people"></i> Members</a>
    <a href="approved.php"><i class="bi bi-check-circle"></i> Approved</a>
    <a href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</nav>

<!-- ========== Main Content ========== -->
<main class="main">
    <h1 class="page-title">Dashboard Overview</h1>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card total">
                <div class="icon"><i class="bi bi-people-fill"></i></div>
                <h3><?= $totalMembers ?></h3>
                <p>Total Members</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card approved">
                <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
                <h3><?= $approved ?></h3>
                <p>Approved</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card pending">
                <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                <h3><?= $pending ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card rejected">
                <div class="icon"><i class="bi bi-x-circle-fill"></i></div>
                <h3><?= $rejected ?></h3>
                <p>Rejected</p>
            </div>
        </div>
    </div>

    <!-- Recent Members -->
    <div class="recent-table">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <span class="badge-status badge-<?= strtolower($row['status']) ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($recent->num_rows === 0): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">No recent members</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>


</body>
</html>