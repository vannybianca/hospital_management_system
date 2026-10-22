<?php
require_once "../config/database.php";
$id = (int)($_GET['id'] ?? 0);
$statement = $conn->prepare("SELECT * FROM doctors WHERE id=?");
$statement->bind_param("i", $id);
$statement->execute();
$doctor = $statement->get_result()->fetch_assoc();
if (!$doctor) { http_response_code(404); exit("Doctor not found."); }
$error = "";
$departments = $conn->query("SELECT id, department_name FROM departments ORDER BY department_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_id = ($_POST['department_id'] ?? '') !== '' ? (int)$_POST['department_id'] : null;
    $statement = $conn->prepare("UPDATE doctors SET doctor_number=?, first_name=?, last_name=?, specialization=?, phone=?, email=?, department_id=? WHERE id=?");
    $statement->bind_param("ssssssii", $_POST['doctor_number'], $_POST['first_name'], $_POST['last_name'], $_POST['specialization'], $_POST['phone'], $_POST['email'], $department_id, $id);
    if ($statement->execute()) { header("Location: index.php"); exit; }
    $error = $statement->error;
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Edit Doctor</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="container"><h1>Edit Doctor</h1>
<?php if ($error): ?><p class="error" role="alert"><?=htmlspecialchars($error)?></p><?php endif; ?>
<form method="post">
<label for="doctor_number">Doctor Number</label><input id="doctor_number" name="doctor_number" value="<?=htmlspecialchars($doctor['doctor_number'])?>" required>
<label for="first_name">First Name</label><input id="first_name" name="first_name" value="<?=htmlspecialchars($doctor['first_name'])?>" required>
<label for="last_name">Last Name</label><input id="last_name" name="last_name" value="<?=htmlspecialchars($doctor['last_name'])?>" required>
<label for="specialization">Specialization</label><input id="specialization" name="specialization" value="<?=htmlspecialchars($doctor['specialization'] ?? '')?>">
<label for="department_id">Department</label><select id="department_id" name="department_id"><option value="">Select department</option><?php while ($department = $departments->fetch_assoc()): ?><option value="<?=$department['id']?>" <?=$doctor['department_id'] == $department['id'] ? 'selected' : ''?>><?=htmlspecialchars($department['department_name'])?></option><?php endwhile; ?></select>
<label for="phone">Phone</label><input id="phone" name="phone" type="tel" value="<?=htmlspecialchars($doctor['phone'] ?? '')?>">
<label for="email">Email</label><input id="email" name="email" type="email" value="<?=htmlspecialchars($doctor['email'] ?? '')?>">
<button type="submit">Save Changes</button></form><a href="index.php">← Back to Doctors</a></div></body></html>
