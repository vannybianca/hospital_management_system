<?php
require_once "../config/database.php";

$id = (int)($_GET['id'] ?? 0);

$statement = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$statement->bind_param("i", $id);
$statement->execute();
$patient = $statement->get_result()->fetch_assoc();

if (!$patient) {
    http_response_code(404);
    exit("Patient not found.");
}

function display_value(?string $value): string
{
    return $value !== null && $value !== '' ? htmlspecialchars($value) : "-";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="container">
        <h1>Patient Details</h1>

        <div class="patient-info">
            <p><strong>Patient Number:</strong> <?= display_value($patient['patient_number']) ?></p>
            <p><strong>First Name:</strong> <?= display_value($patient['first_name']) ?></p>
            <p><strong>Last Name:</strong> <?= display_value($patient['last_name']) ?></p>
            <p><strong>Gender:</strong> <?= display_value($patient['gender']) ?></p>
            <p><strong>Date of Birth:</strong> <?= display_value($patient['date_of_birth']) ?></p>
            <p><strong>Phone:</strong> <?= display_value($patient['phone']) ?></p>
            <p><strong>Email:</strong> <?= display_value($patient['email']) ?></p>
            <p><strong>Address:</strong> <?= display_value($patient['address']) ?></p>
            <p><strong>Emergency Contact:</strong> <?= display_value($patient['emergency_contact']) ?></p>
            <p><strong>Emergency Phone:</strong> <?= display_value($patient['emergency_phone']) ?></p>
            <p><strong>Blood Group:</strong> <?= display_value($patient['blood_group']) ?></p>
        </div>

        <div class="actions">
            <a href="edit.php?id=<?= $patient['id'] ?>" class="button">Edit Patient</a>
            <a href="../index.php" class="button secondary-button">Home</a>
            <a href="index.php" class="button secondary-button">Back to Patients</a>
            <button type="button" class="button secondary-button" onclick="window.print()">Print Record</button>
            <a href="export_vcard.php?id=<?= $patient['id'] ?>" class="button secondary-button">Export Contact (.vcf)</a>
        </div>
    </div>

</body>
</html>