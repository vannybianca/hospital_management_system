<?php
// Include your project's actual database connection file
include '../config/database.php';

// Fetch prescription details through their related patient, doctor, and medicine records.
$sql = "SELECT pr.id, pr.prescription_date,
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
               CONCAT(d.first_name, ' ', d.last_name) AS doctor_name,
               GROUP_CONCAT(CONCAT(m.medicine_name, ' (', pi.dosage, ')') SEPARATOR ', ') AS medicine_details
        FROM prescriptions pr
        JOIN patients p ON p.id = pr.patient_id
        JOIN doctors d ON d.id = pr.doctor_id
        LEFT JOIN prescription_items pi ON pi.prescription_id = pr.id
        LEFT JOIN medicines m ON m.id = pi.medicine_id
        GROUP BY pr.id, pr.prescription_date, p.first_name, p.last_name, d.first_name, d.last_name
        ORDER BY pr.prescription_date DESC, pr.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicore - Prescriptions Management</title>
    
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background-color: #0f766e; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; font-weight: bold; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px; cursor: pointer; }
        .btn-success { background-color: #0f766e; color: white; border: none; }
        .btn-primary { background-color: #0f766e; color: white; border: none; }
        .btn-danger { background-color: #991b1b; color: white; border: none; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 16px; border-bottom: 1px solid #dee2e6; }
        th { background-color: #1e293b; color: white; font-weight: 600; }
        tr:hover { background-color: #f1f1f1; }
        .text-center { text-align: center; color: #6c757d; padding: 20px; }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div><a href="../admin/dashboard.php">Medicore Hospital System</a></div>
        <div><a href="../admin/dashboard.php">Back to Dashboard</a></div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="header-flex">
            <h2>Prescriptions Management</h2>
            <a href="add.php" class="btn btn-success">+ Add New Prescription</a>
        </div>

        <!-- Table Card -->
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Doctor</th>
                        <th>Medicines</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result &&$result->num_rows > 0): ?>
                        <?php while($row =$result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['patient_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['medicine_details'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['prescription_date']); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 12px;">Edit</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 4px 10px; font-size: 12px;" onclick="return confirm('Are you sure you want to delete this prescription?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No prescriptions found in the database. (Ensure your table is named `prescriptions`).</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php 
    if (isset($conn)) {$conn->close(); 
    }
    ?>
</body>
</html>