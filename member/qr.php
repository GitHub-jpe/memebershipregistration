<?php
include '../config/db.php';
$id = $_SESSION['member_id'];
$m = $conn->query("SELECT * FROM members WHERE id=$id")->fetch_assoc();

$data = "https://yourdomain.com/verify.php?no=".$m['membership_number'];
?>

<img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?= urlencode($data) ?>">
