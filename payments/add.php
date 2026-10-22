<?php
require_once "../config/database.php";

$error = "";
$bills = $conn->query("
    SELECT b.id, b.total_amount, p.patient_number, p.first_name, p.last_name,
           COALESCE(SUM(pay.amount), 0) AS amount_paid
    FROM bills b
    JOIN patients p ON p.id = b.patient_id
    LEFT JOIN payments pay ON pay.bill_id = b.id
    GROUP BY b.id, b.total_amount, p.patient_number, p.first_name, p.last_name
    HAVING b.total_amount > COALESCE(SUM(pay.amount), 0)
    ORDER BY b.id DESC
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bill_id = (int)($_POST["bill_id"] ?? 0);
    $amount = (float)($_POST["amount"] ?? 0);
    $payment_method = $_POST["payment_method"] ?? "Cash";
    $reference_number = trim($_POST["reference_number"] ?? "");

    $statement = $conn->prepare("INSERT INTO payments (bill_id, amount, payment_method, reference_number) VALUES (?, ?, ?, ?)");
    $statement->bind_param("idss", $bill_id, $amount, $payment_method, $reference_number);

    if ($statement->execute()) {
        header("Location: index.php");
        exit;
    }

    $error = $statement->error;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Payment | MEDICORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Add Payment</h1>
    <?php if ($error): ?><p class="error" role="alert"><?=htmlspecialchars($error)?></p><?php endif; ?>
    <?php if ($bills->num_rows === 0): ?>
        <p class="error" role="alert">There are no bills with an outstanding balance.</p>
    <?php endif; ?>
    <form method="post">
        <label for="bill_id">Bill / Patient</label>
        <select id="bill_id" name="bill_id" required>
            <option value="">Select bill</option>
            <?php while ($bill = $bills->fetch_assoc()): ?>
                <option value="<?=$bill["id"]?>" <?=((int)($_POST["bill_id"] ?? 0) === (int)$bill["id"]) ? "selected" : ""?>>
                    <?=htmlspecialchars("Bill #{$bill["id"]} - {$bill["patient_number"]} - {$bill["first_name"]} {$bill["last_name"]} - Balance " . number_format($bill["total_amount"] - $bill["amount_paid"], 2))?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="amount">Amount</label>
        <input id="amount" type="number" name="amount" min="0.01" step="0.01" value="<?=htmlspecialchars($_POST["amount"] ?? "")?>" required>
        <label for="payment_method">Payment Method</label>
        <select id="payment_method" name="payment_method" required>
            <?php foreach (["Cash", "Mobile Money", "Insurance", "Card"] as $method): ?>
                <option value="<?=$method?>" <?=($_POST["payment_method"] ?? "Cash") === $method ? "selected" : ""?>><?=$method?></option>
            <?php endforeach; ?>
        </select>
        <label for="reference_number">Reference Number</label>
        <input id="reference_number" name="reference_number" value="<?=htmlspecialchars($_POST["reference_number"] ?? "")?>">
        <button type="submit">Save Payment</button>
    </form>
    <a href="index.php">← Back to Payments</a>
</div>
</body>
</html>
