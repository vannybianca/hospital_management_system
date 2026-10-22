<?php
require_once "../config/database.php";

$id = (int)($_GET["id"] ?? 0);
$statement = $conn->prepare("
    SELECT b.id, b.bill_date, b.total_amount, b.status,
           CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
           p.patient_number
    FROM bills b
    JOIN patients p ON p.id = b.patient_id
    WHERE b.id = ?
");
$statement->bind_param("i", $id);
$statement->execute();
$bill = $statement->get_result()->fetch_assoc();
if (!$bill) {
    die("Bill not found.");
}

$items = $conn->prepare("
    SELECT bi.quantity, bi.amount, s.service_name
    FROM bill_items bi
    JOIN services s ON s.id = bi.service_id
    WHERE bi.bill_id = ?
");
$items->bind_param("i", $id);
$items->execute();
$item_result = $items->get_result();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bill #<?=htmlspecialchars($bill["id"])?> | MEDICORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container dashboard-container">
    <p class="dashboard-brand">MEDICORE</p>
    <h1>Bill #<?=htmlspecialchars($bill["id"])?></h1>
    <p><strong>Patient:</strong> <?=htmlspecialchars($bill["patient_number"] . " - " . $bill["patient_name"])?></p>
    <p><strong>Date:</strong> <?=htmlspecialchars($bill["bill_date"])?></p>
    <table>
        <thead><tr><th>Service</th><th>Quantity</th><th>Amount</th></tr></thead>
        <tbody>
        <?php while ($item = $item_result->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($item["service_name"])?></td>
                <td><?=htmlspecialchars($item["quantity"])?></td>
                <td><?=number_format($item["amount"] * $item["quantity"], 2)?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <p><strong>Total:</strong> <?=number_format($bill["total_amount"], 2)?></p>
    <p><strong>Status:</strong> <?=htmlspecialchars($bill["status"])?></p>
    <a href="index.php" class="button">Back to Billing</a>
</div>
</body>
</html>
