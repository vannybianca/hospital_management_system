<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
require_once "../config/database.php";

$patients = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$doctors = $conn->query("SELECT COUNT(*) c FROM doctors")->fetch_assoc()['c'];
$appointments = $conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
$admissions = $conn->query("SELECT COUNT(*) c FROM admissions WHERE status='Admitted'")->fetch_assoc()['c'];
$bills = $conn->query("SELECT COUNT(*) c FROM bills")->fetch_assoc()['c'];
$prescriptions = $conn->query("SELECT COUNT(*) c FROM prescriptions")->fetch_assoc()['c'];
$medicines = $conn->query("SELECT COUNT(*) c FROM medicines")->fetch_assoc()['c'];
$wards = $conn->query("SELECT COUNT(*) c FROM wards")->fetch_assoc()['c'];
?>
<!doctype html>
<html>
<head>
    <title>MEDICORE Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container dashboard-container">
    <p class="dashboard-brand">MEDICORE</p>
    <div class="dashboard-heading">
        <span class="dashboard-accent" aria-hidden="true"></span>
        <h1>Dashboard</h1>
    </div>

    <?php if (!empty($_SESSION['full_name'])): ?>
        <p class="user-meta">
            Signed in as <strong><?=htmlspecialchars($_SESSION['full_name'])?></strong> (<?=htmlspecialchars($_SESSION['role'])?>)<br>
            Last login: <?=htmlspecialchars($_SESSION['last_login_at'] ?? 'Just now')?>
        </p>
    <?php endif; ?>

    <!-- Summary Stats Cards -->
    <div class="cards">
        <div class="card">Patients<strong><?=$patients?></strong></div>
        <div class="card">Doctors<strong><?=$doctors?></strong></div>
        <div class="card">Appointments<strong><?=$appointments?></strong></div>
        <div class="card">Admitted<strong><?=$admissions?></strong></div>
        <div class="card">Bills<strong><?=$bills?></strong></div>
        <div class="card">Prescriptions<strong><?=$prescriptions?></strong></div>
        <div class="card">Medicines<strong><?=$medicines?></strong></div>
        <div class="card">Wards<strong><?=$wards?></strong></div>
    </div>

    <!-- Management Action Buttons -->
    <div class="dashboard-actions" style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 8px;">
        <a class="button" href="../patients/index.php">Manage Patients</a>
        <a class="button" href="../doctors/index.php">Manage Doctors</a>
        <a class="button" href="../appointments/index.php">Appointments</a>
        <a class="button" href="../billing/index.php">Bills & Billing</a>
        <a class="button" href="../payments/index.php">Payments</a>
        <a class="button" href="../prescriptions/index.php">Prescriptions</a>
        <a class="button" href="../medicines/index.php">Medicines / Pharmacy</a>
        <a class="button" href="../wards/index.php">Wards & Beds</a>
        <a class="button danger-button" href="../logout.php">Logout</a>
    </div>
</div>
</body>
</html>