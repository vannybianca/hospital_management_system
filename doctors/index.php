<?php
require_once "../config/database.php";

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $like = "%{$search}%";
    $statement = $conn->prepare("SELECT d.*, dep.department_name FROM doctors d LEFT JOIN departments dep ON dep.id = d.department_id WHERE d.doctor_number LIKE ? OR d.first_name LIKE ? OR d.last_name LIKE ? OR d.specialization LIKE ? ORDER BY d.id DESC");
    $statement->bind_param("ssss", $like, $like, $like, $like);
    $statement->execute();
    $result = $statement->get_result();
} else {
    $result = $conn->query("SELECT d.*, dep.department_name FROM doctors d LEFT JOIN departments dep ON dep.id = d.department_id ORDER BY d.id DESC");
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Doctors</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="container">
<h1>Doctors</h1>
<p>Manage doctors, specialties and department assignments.</p>
<a class="button" href="../index.php">Home</a> <a class="button" href="add.php">+ Add Doctor</a>
<form method="get">
<label for="search">Search doctors</label><input id="search" name="search" value="<?=htmlspecialchars($search)?>" placeholder="Number, name or specialty">
<button type="submit">Search</button><?php if ($search !== ''): ?> <a href="index.php">Clear</a><?php endif; ?>
</form>
<div class="table-wrap"><table><tr><th>Doctor No.</th><th>Name</th><th>Specialization</th><th>Department</th><th>Phone</th><th>Actions</th></tr>
<?php if ($result->num_rows === 0): ?><tr><td colspan="6">No doctors found.</td></tr><?php endif; ?>
<?php while ($doctor = $result->fetch_assoc()): ?><tr>
<td><?=htmlspecialchars($doctor['doctor_number'])?></td><td><?=htmlspecialchars($doctor['first_name'].' '.$doctor['last_name'])?></td><td><?=htmlspecialchars($doctor['specialization'] ?: '-')?></td><td><?=htmlspecialchars($doctor['department_name'] ?: '-')?></td><td><?=htmlspecialchars($doctor['phone'] ?: '-')?></td>
<td><a href="view.php?id=<?=$doctor['id']?>">View</a> | <a href="edit.php?id=<?=$doctor['id']?>">Edit</a> | <form method="post" action="delete.php" class="inline-form" onsubmit="return confirm('Delete this doctor?')"><input type="hidden" name="id" value="<?=$doctor['id']?>"><button type="submit">Delete</button></form></td>
</tr><?php endwhile; ?></table></div>
</div></body></html>
