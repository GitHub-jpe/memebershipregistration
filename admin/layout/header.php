<?php
include '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* DASHBOARD STATS */
$totalMembers   = $conn->query("SELECT COUNT(*) c FROM members")->fetch_assoc()['c'];
$approved       = $conn->query("SELECT COUNT(*) c FROM members WHERE status='Approved'")->fetch_assoc()['c'];
$pending        = $conn->query("SELECT COUNT(*) c FROM members WHERE status='Pending'")->fetch_assoc()['c'];
$rejected       = $conn->query("SELECT COUNT(*) c FROM members WHERE status='Rejected'")->fetch_assoc()['c'];

/* ---- recent members ---- */
$recent = $conn->query("SELECT id, first_name, last_name, email, status, created_at
                        FROM members
                        ORDER BY created_at DESC
                        LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <title>Admin Panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
     <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


