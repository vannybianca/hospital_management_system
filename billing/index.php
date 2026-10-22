<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
require_once "../config/database.php";

$search = trim($_GET['search'] ?? '');
$query = "
    SELECT b.id, b.bill_date, b.total_amount, b.status,
           CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
           COALESCE(SUM(pay.amount), 0) AS paid_amount
    FROM bills b
    JOIN patients p ON p.id = b.patient_id
    LEFT JOIN payments pay ON pay.bill_id = b.id
";

if ($search !== '') {
    $like = "%{$search}%";
    $statement = $conn->prepare("
        {$query}
        WHERE p.first_name LIKE ? OR p.last_name LIKE ? OR CAST(b.id AS CHAR) LIKE ?
        GROUP BY b.id, b.bill_date, b.total_amount, b.status, p.first_name, p.last_name
        ORDER BY b.id DESC
    ");
    $statement->bind_param("sss", $like, $like, $like);
    $statement->execute();
    $result = $statement->get_result();
} else {
    $result = $conn->query("
        {$query}
        GROUP BY b.id, b.bill_date, b.total_amount, b.status, p.first_name, p.last_name
        ORDER BY b.id DESC
    ");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container dashboard-container">
        <p class="dashboard-brand">MEDICORE</p>
        <div class="dashboard-heading">
            <span class="dashboard-accent" aria-hidden="true"></span>
            <h1>Billing & Invoices</h1>
        </div>

        <p style="margin-bottom: 20px;">
            <a href="../admin/dashboard.php" class="button">Dashboard</a>
            <a href="create.php" class="button">+ Create New Bill</a>
        </p>

        <form method="get" style="margin-bottom: 20px; display: flex; gap: 8px;">
            <input id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by patient name or bill no." style="padding: 8px; width: 300px;">
            <button type="submit" class="button">Search</button>
            <?php if ($search !== ''): ?>
                <a href="index.php" class="button danger-button">Clear</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Bill #</th>
                    <th>Patient</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$result || $result->num_rows === 0): ?>
                    <tr>
                        <td colspan="7">No billing records found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td><?= htmlspecialchars($r['patient_name'] ?? 'N/A') ?></td>
                            <td>$<?= number_format($r['total_amount'] ?? 0, 2) ?></td>
                            <td>$<?= number_format($r['paid_amount'] ?? 0, 2) ?></td>
                            <td>
                                <strong><?= htmlspecialchars(strtoupper($r['status'] ?? 'PENDING')) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($r['bill_date'] ?? 'N/A') ?></td>
                            <td>
                                <a class="button" href="view.php?id=<?= $r['id'] ?>" style="padding: 4px 8px; font-size: 0.85rem;">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>