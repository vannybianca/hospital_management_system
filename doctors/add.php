<?php
require_once "../config/database.php";
$error = "";
$departments = $conn->query("SELECT id, department_name FROM departments ORDER BY department_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_id = ($_POST['department_id'] ?? '') !== '' ? (int)$_POST['department_id'] : null;
    $statement = $conn->prepare("INSERT INTO doctors (doctor_number, first_name, last_name, specialization, phone, email, department_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $statement->bind_param("ssssssi", $_POST['doctor_number'], $_POST['first_name'], $_POST['last_name'], $_POST['specialization'], $_POST['phone'], $_POST['email'], $department_id);
    if ($statement->execute()) { header("Location: index.php"); exit; }
    $error = $statement->error;
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Add Doctor</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="container"><h1>Add Doctor</h1><p>Enter the doctor's professional and contact details.</p>
<?php if ($error): ?><p class="error" role="alert"><?=htmlspecialchars($error)?></p><?php endif; ?>
<form method="post">
<label for="doctor_number">Doctor Number</label><input id="doctor_number" name="doctor_number" required>
<label for="first_name">First Name</label><input id="first_name" name="first_name" required>
<label for="last_name">Last Name</label><input id="last_name" name="last_name" required>
<label for="specialization">Specialization</label><input id="specialization" name="specialization" placeholder="e.g. General Physician">
<label for="department_id">Department</label><select id="department_id" name="department_id"><option value="">Select department</option><?php while ($department = $departments->fetch_assoc()): ?><option value="<?=$department['id']?>"><?=htmlspecialchars($department['department_name'])?></option><?php endwhile; ?></select>
<label for="phone">Phone</label><input id="phone" name="phone" type="tel">
<label for="email">Email</label><input id="email" name="email" type="email">
<button type="submit">Save Doctor</button></form><a href="index.php">← Back to Doctors</a></div></body></html>
