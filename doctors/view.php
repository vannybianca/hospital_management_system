<?php
require_once "../config/database.php";
$id = (int)($_GET['id'] ?? 0);
$statement = $conn->prepare("SELECT d.*, dep.department_name FROM doctors d LEFT JOIN departments dep ON dep.id=d.department_id WHERE d.id=?");
$statement->bind_param("i", $id);
$statement->execute();
$doctor = $statement->get_result()->fetch_assoc();
if (!$doctor) { http_response_code(404); exit("Doctor not found."); }
function doctor_value(?string $value): string { return $value !== null && $value !== '' ? htmlspecialchars($value) : '-'; }
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Doctor Details</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="container"><h1>Doctor Details</h1>
<p><strong>Doctor Number:</strong> <?=doctor_value($doctor['doctor_number'])?></p>
<p><strong>Name:</strong> <?=doctor_value($doctor['first_name'].' '.$doctor['last_name'])?></p>
<p><strong>Specialization:</strong> <?=doctor_value($doctor['specialization'])?></p>
<p><strong>Department:</strong> <?=doctor_value($doctor['department_name'])?></p>
<p><strong>Phone:</strong> <?=doctor_value($doctor['phone'])?></p>
<p><strong>Email:</strong> <?=doctor_value($doctor['email'])?></p>
<p><a href="edit.php?id=<?=$doctor['id']?>">Edit Doctor</a> | <a href="index.php">Back to Doctors</a></p>
</div></body></html>
