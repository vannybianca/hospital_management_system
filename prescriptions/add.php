<?php
require_once "../config/database.php";
$error = "";
$patients = $conn->query("SELECT id, patient_number, first_name, last_name FROM patients ORDER BY last_name, first_name");
$doctors = $conn->query("SELECT id, first_name, last_name FROM doctors ORDER BY last_name, first_name");
$medicines = $conn->query("SELECT id, medicine_name, quantity FROM medicines ORDER BY medicine_name");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = (int)($_POST["patient_id"] ?? 0);
    $doctor_id = (int)($_POST["doctor_id"] ?? 0);
    $medicine_id = (int)($_POST["medicine_id"] ?? 0);
    $quantity = (int)($_POST["quantity"] ?? 0);
    $date = $_POST["prescription_date"] ?? "";
    $dosage = trim($_POST["dosage"] ?? "");
    $frequency = trim($_POST["frequency"] ?? "");
    $duration = trim($_POST["duration"] ?? "");

    if ($patient_id <= 0 || $doctor_id <= 0 || $medicine_id <= 0 || $quantity <= 0 || $date === "") {
        $error = "Complete all required prescription fields.";
    } else {
        $conn->begin_transaction();
        $statement = $conn->prepare("INSERT INTO prescriptions (patient_id, doctor_id, prescription_date) VALUES (?, ?, ?)");
        $statement->bind_param("iis", $patient_id, $doctor_id, $date);
        $item = $conn->prepare("INSERT INTO prescription_items (prescription_id, medicine_id, dosage, frequency, duration, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        if ($statement->execute()) {
            $prescription_id = $conn->insert_id;
            $item->bind_param("iisssi", $prescription_id, $medicine_id, $dosage, $frequency, $duration, $quantity);
        }
        if (!empty($prescription_id) && $item->execute()) {
            $conn->commit();
            header("Location: index.php");
            exit;
        }
        $conn->rollback();
        $error = $conn->error ?: "Unable to save prescription.";
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Add Prescription</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="container"><h1>Add Prescription</h1><?php if($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?><form method="post">
<label>Patient</label><select name="patient_id" required><option value="">Select patient</option><?php while($p=$patients->fetch_assoc()): ?><option value="<?=$p["id"]?>"><?=htmlspecialchars($p["patient_number"]." - ".$p["first_name"]." ".$p["last_name"])?></option><?php endwhile; ?></select>
<label>Doctor</label><select name="doctor_id" required><option value="">Select doctor</option><?php while($d=$doctors->fetch_assoc()): ?><option value="<?=$d["id"]?>"><?=htmlspecialchars($d["first_name"]." ".$d["last_name"])?></option><?php endwhile; ?></select>
<label>Medicine</label><select name="medicine_id" required><option value="">Select medicine</option><?php while($m=$medicines->fetch_assoc()): ?><option value="<?=$m["id"]?>"><?=htmlspecialchars($m["medicine_name"]." - Stock: ".$m["quantity"])?></option><?php endwhile; ?></select>
<label>Quantity</label><input type="number" name="quantity" min="1" required><label>Dosage</label><input name="dosage" placeholder="e.g. 500 mg"><label>Frequency</label><input name="frequency" placeholder="e.g. Twice daily"><label>Duration</label><input name="duration" placeholder="e.g. 5 days"><label>Date</label><input type="date" name="prescription_date" value="<?=date("Y-m-d")?>" required>
<button type="submit">Save Prescription</button></form><a href="index.php">← Back</a></div></body></html>
