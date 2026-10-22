<?php
require_once "../config/database.php";

$error = "";
$patients = $conn->query("SELECT id, patient_number, first_name, last_name FROM patients ORDER BY last_name, first_name");
$doctors = $conn->query("SELECT id, first_name, last_name, specialization FROM doctors ORDER BY last_name, first_name");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = (int)($_POST["patient_id"] ?? 0);
    $doctor_id = (int)($_POST["doctor_id"] ?? 0);
    $appointment_date = $_POST["appointment_date"] ?? "";
    $appointment_time = $_POST["appointment_time"] ?? "";
    $reason = trim($_POST["reason"] ?? "");

    $statement = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
    $statement->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);

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
    <title>Make Appointment | MEDICORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Make Appointment</h1>
    <?php if ($error): ?><p class="error" role="alert"><?=htmlspecialchars($error)?></p><?php endif; ?>
    <?php if ($patients->num_rows === 0 || $doctors->num_rows === 0): ?>
        <p class="error" role="alert">Add at least one patient and one doctor before making an appointment.</p>
    <?php endif; ?>
    <form method="post">
        <label for="patient_id">Patient</label>
        <select id="patient_id" name="patient_id" required>
            <option value="">Select patient</option>
            <?php while ($patient = $patients->fetch_assoc()): ?>
                <option value="<?=$patient["id"]?>" <?=((int)($_POST["patient_id"] ?? 0) === (int)$patient["id"]) ? "selected" : ""?>>
                    <?=htmlspecialchars($patient["patient_number"] . " - " . $patient["first_name"] . " " . $patient["last_name"])?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="doctor_id">Doctor</label>
        <select id="doctor_id" name="doctor_id" required>
            <option value="">Select doctor</option>
            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                <option value="<?=$doctor["id"]?>" <?=((int)($_POST["doctor_id"] ?? 0) === (int)$doctor["id"]) ? "selected" : ""?>>
                    <?=htmlspecialchars($doctor["first_name"] . " " . $doctor["last_name"] . ($doctor["specialization"] ? " - " . $doctor["specialization"] : ""))?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="appointment_date">Date</label>
        <input id="appointment_date" type="date" name="appointment_date" value="<?=htmlspecialchars($_POST["appointment_date"] ?? "")?>" required>
        <label for="appointment_time">Time</label>
        <input id="appointment_time" type="time" name="appointment_time" value="<?=htmlspecialchars($_POST["appointment_time"] ?? "")?>" required>
        <label for="reason">Reason</label>
        <textarea id="reason" name="reason" rows="4"><?=htmlspecialchars($_POST["reason"] ?? "")?></textarea>
        <button type="submit">Save Appointment</button>
    </form>
    <a href="index.php">← Back to Appointments</a>
</div>
</body>
</html>
