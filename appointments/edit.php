<?php
require_once "../config/database.php";

$id = (int)($_GET["id"] ?? 0);
$statement = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
$statement->bind_param("i", $id);
$statement->execute();
$appointment = $statement->get_result()->fetch_assoc();
if (!$appointment) {
    die("Appointment not found.");
}

$error = "";
$patients = $conn->query("SELECT id, patient_number, first_name, last_name FROM patients ORDER BY last_name, first_name");
$doctors = $conn->query("SELECT id, first_name, last_name, specialization FROM doctors ORDER BY last_name, first_name");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = (int)($_POST["patient_id"] ?? 0);
    $doctor_id = (int)($_POST["doctor_id"] ?? 0);
    $appointment_date = $_POST["appointment_date"] ?? "";
    $appointment_time = $_POST["appointment_time"] ?? "";
    $reason = trim($_POST["reason"] ?? "");
    $status = $_POST["status"] ?? "Scheduled";

    $statement = $conn->prepare("UPDATE appointments SET patient_id = ?, doctor_id = ?, appointment_date = ?, appointment_time = ?, reason = ?, status = ? WHERE id = ?");
    $statement->bind_param("iissssi", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason, $status, $id);
    if ($statement->execute()) {
        header("Location: index.php");
        exit;
    }
    $error = $statement->error;
    $appointment = array_merge($appointment, $_POST);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Appointment | MEDICORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Edit Appointment</h1>
    <?php if ($error): ?><p class="error" role="alert"><?=htmlspecialchars($error)?></p><?php endif; ?>
    <form method="post">
        <label for="patient_id">Patient</label>
        <select id="patient_id" name="patient_id" required>
            <?php while ($patient = $patients->fetch_assoc()): ?>
                <option value="<?=$patient["id"]?>" <?=((int)$appointment["patient_id"] === (int)$patient["id"]) ? "selected" : ""?>>
                    <?=htmlspecialchars($patient["patient_number"] . " - " . $patient["first_name"] . " " . $patient["last_name"])?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="doctor_id">Doctor</label>
        <select id="doctor_id" name="doctor_id" required>
            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                <option value="<?=$doctor["id"]?>" <?=((int)$appointment["doctor_id"] === (int)$doctor["id"]) ? "selected" : ""?>>
                    <?=htmlspecialchars($doctor["first_name"] . " " . $doctor["last_name"] . ($doctor["specialization"] ? " - " . $doctor["specialization"] : ""))?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="appointment_date">Date</label>
        <input id="appointment_date" type="date" name="appointment_date" value="<?=htmlspecialchars($appointment["appointment_date"])?>" required>
        <label for="appointment_time">Time</label>
        <input id="appointment_time" type="time" name="appointment_time" value="<?=htmlspecialchars(substr($appointment["appointment_time"], 0, 5))?>" required>
        <label for="reason">Reason</label>
        <textarea id="reason" name="reason" rows="4"><?=htmlspecialchars($appointment["reason"] ?? "")?></textarea>
        <label for="status">Status</label>
        <select id="status" name="status">
            <?php foreach (["Scheduled", "Completed", "Cancelled"] as $option): ?>
                <option value="<?=$option?>" <?=$appointment["status"] === $option ? "selected" : ""?>><?=$option?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Save Changes</button>
    </form>
    <a href="index.php">← Back to Appointments</a>
</div>
</body>
</html>
