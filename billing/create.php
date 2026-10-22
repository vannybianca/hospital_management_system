<?php
require_once "../config/database.php";

$error = "";
$patients = $conn->query("SELECT id, patient_number, first_name, last_name FROM patients ORDER BY last_name, first_name");
$services = $conn->query("SELECT id, service_name, price FROM services ORDER BY service_name");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = (int)($_POST["patient_id"] ?? 0);
    $service_id = (int)($_POST["service_id"] ?? 0);
    $quantity = (int)($_POST["quantity"] ?? 0);

    $service_statement = $conn->prepare("SELECT price FROM services WHERE id = ?");
    $service_statement->bind_param("i", $service_id);
    $service_statement->execute();
    $service = $service_statement->get_result()->fetch_assoc();

    if (!$service || $patient_id <= 0 || $quantity <= 0) {
        $error = "Select a patient, service, and a valid quantity.";
    } else {
        $conn->begin_transaction();
        $bill_statement = $conn->prepare("INSERT INTO bills (patient_id) VALUES (?)");
        $bill_statement->bind_param("i", $patient_id);
        $item_statement = $conn->prepare("INSERT INTO bill_items (bill_id, service_id, quantity, amount) VALUES (?, ?, ?, ?)");
        $bill_id = 0;
        $amount = (float)$service["price"];

        if ($bill_statement->execute()) {
            $bill_id = $conn->insert_id;
            $item_statement->bind_param("iiid", $bill_id, $service_id, $quantity, $amount);
        }

        if ($bill_id > 0 && $item_statement->execute()) {
            $conn->commit();
            header("Location: view.php?id=" . $bill_id);
            exit;
        }

        $conn->rollback();
        $error = $conn->error ?: "Unable to create the bill.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Bill | MEDICORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container dashboard-container">
    <p class="dashboard-brand">MEDICORE</p>
    <h1>Create New Bill</h1>
    <?php if ($error): ?><p class="error" role="alert"><?=htmlspecialchars($error)?></p><?php endif; ?>
    <form method="post">
        <label for="patient_id">Patient</label>
        <select id="patient_id" name="patient_id" required>
            <option value="">Select patient</option>
            <?php while ($patient = $patients->fetch_assoc()): ?>
                <option value="<?=$patient["id"]?>">
                    <?=htmlspecialchars($patient["patient_number"] . " - " . $patient["first_name"] . " " . $patient["last_name"])?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="service_id">Service</label>
        <select id="service_id" name="service_id" required>
            <option value="">Select service</option>
            <?php while ($service = $services->fetch_assoc()): ?>
                <option value="<?=$service["id"]?>">
                    <?=htmlspecialchars($service["service_name"] . " - " . number_format($service["price"], 2))?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="quantity">Quantity</label>
        <input id="quantity" type="number" name="quantity" min="1" value="1" required>
        <button type="submit" class="button">Create Bill</button>
    </form>
    <a href="index.php" class="button secondary-button">Back to Billing</a>
</div>
</body>
</html>
