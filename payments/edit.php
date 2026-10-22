<?php
require_once "../config/database.php";

$id = (int)($_GET["id"] ?? 0);
$statement = $conn->prepare("SELECT pay.*, b.total_amount, b.id AS bill_id, COALESCE((SELECT SUM(other.amount) FROM payments other WHERE other.bill_id = pay.bill_id AND other.id <> pay.id), 0) AS other_paid FROM payments pay JOIN bills b ON b.id = pay.bill_id WHERE pay.id = ?");
$statement->bind_param("i", $id);
$statement->execute();
$payment = $statement->get_result()->fetch_assoc();
if (!$payment) {
    die("Payment not found.");
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = (float)($_POST["amount"] ?? 0);
    $payment_method = $_POST["payment_method"] ?? "Cash";
    $reference_number = trim($_POST["reference_number"] ?? "");
    $remaining = (float)$payment["total_amount"] - (float)$payment["other_paid"];

    if ($amount <= 0 || $amount > $remaining) {
        $error = "Payment must be greater than zero and no more than the outstanding balance.";
    } else {
        $statement = $conn->prepare("UPDATE payments SET amount = ?, payment_method = ?, reference_number = ? WHERE id = ?");
        $statement->bind_param("dssi", $amount, $payment_method, $reference_number, $id);
        if ($statement->execute()) {
            header("Location: index.php");
            exit;
        }
        $error = $statement->error;
    }

    $payment["amount"] = $amount;
    $payment["payment_method"] = $payment_method;
    $payment["reference_number"] = $reference_number;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Payment | MEDICORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Edit Payment</h1>
    <p>Bill #<?=htmlspecialchars($payment["bill_id"])?></p>
    <?php if ($error): ?><p class="error" role="alert"><?=htmlspecialchars($error)?></p><?php endif; ?>
    <form method="post">
        <label for="amount">Amount</label>
        <input id="amount" type="number" name="amount" min="0.01" step="0.01" value="<?=htmlspecialchars($payment["amount"])?>" required>
        <label for="payment_method">Payment Method</label>
        <select id="payment_method" name="payment_method" required>
            <?php foreach (["Cash", "Mobile Money", "Insurance", "Card"] as $method): ?>
                <option value="<?=$method?>" <?=$payment["payment_method"] === $method ? "selected" : ""?>><?=$method?></option>
            <?php endforeach; ?>
        </select>
        <label for="reference_number">Reference Number</label>
        <input id="reference_number" name="reference_number" value="<?=htmlspecialchars($payment["reference_number"] ?? "")?>">
        <button type="submit">Save Changes</button>
    </form>
    <a href="index.php">← Back to Payments</a>
</div>
</body>
</html>
