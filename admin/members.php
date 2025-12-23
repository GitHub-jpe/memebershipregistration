

<?php include 'layout/header.php'; 

/* ---- fetch members ---- */
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, membership_type, status, created_at
                        FROM members
                        ORDER BY created_at DESC");
$stmt->execute();
$members = $stmt->get_result();
?>

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

        /* Sidebar */
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

        /* Main */
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

        /* Table */
        .table-wrapper {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }
        .table {
            margin: 0;
            color: var(--text);
        }
        .table thead th {
            background: rgba(255,255,255,.04);
            color: var(--muted);
            font-weight: 600;
            font-size: .875rem;
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--border);
        }
        .table tbody td {
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: .875rem;
        }
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        .badge-status {
            font-size: .75rem;
            padding: .35em .65em;
            border-radius: 999px;
            font-weight: 600;
        }
        .badge-approved { background: rgba(16,185,129,.15); color: var(--success); }
        .badge-pending  { background: rgba(245,158,11,.15); color: var(--warning); }
        .badge-rejected { background: rgba(239,68,68,.15); color: var(--danger); }

        .btn-approve {
            background: linear-gradient(90deg, var(--success), #059669);
            border: none;
            border-radius: 50px;
            padding: .35rem .75rem;
            font-size: .75rem;
            font-weight: 600;
            color: #fff;
            box-shadow: 0 0 10px var(--success);
            transition: all .3s;
        }
        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px #059669;
        }

        /* Search */
        .search-box {
            background: rgba(255,255,255,.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: .5rem .75rem;
            color: var(--text);
            max-width: 250px;
        }
        .search-box:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.25);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="brand">Admin Portal</div>
    <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="members.php" class="active"><i class="bi bi-people"></i> Members</a>
    <a href="approved.php"><i class="bi bi-check-circle"></i> Approved</a>
    <a href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</nav>

<!-- Main -->
<main class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">All Members</h1>
        <input type="text" id="searchInput" class="search-box" placeholder="Search members...">
    </div>

    <div class="table-wrapper">
        <table class="table table-hover" id="membersTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($m = $members->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?></td>
                        <td><?= htmlspecialchars($m['email']) ?></td>
                        <td><?= htmlspecialchars($m['membership_type']) ?></td>
                        <td>
                            <span class="badge-status badge-<?= strtolower($m['status']) ?>">
                                <?= $m['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($m['status'] === 'Pending'): ?>
                                <a href="approve.php?id=<?= $m['id'] ?>" class="btn-approve">
                                    <i class="bi bi-check-lg me-1"></i>Approve
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($members->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No members found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    // Simple live search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#membersTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>


<?php include 'layout/footer.php'; ?>