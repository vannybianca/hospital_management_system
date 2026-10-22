<?php
require_once "../config/database.php";

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $like = "%{$search}%";
    $statement = $conn->prepare("SELECT * FROM patients WHERE patient_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? ORDER BY id ASC");
    $statement->bind_param("ssss", $like, $like, $like, $like);
    $statement->execute();
    $result = $statement->get_result();
} else {
    $result = $conn->query("SELECT * FROM patients ORDER BY id ASC");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="container">
        <h1>Patients</h1>

        <!-- Top Navigation Bar -->
        <p>
            <a href="../index.php" class="button secondary-button">Home</a>
            <a href="add.php" class="button">+ Register Patient</a>
            <a href="../reports.php" class="button secondary-button">View Reports</a>
        </p>

        <!-- Search Form -->
        <form method="get">
            <label for="search">Search patients</label>
            <input id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Patient number, name or phone">
            <button type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a href="index.php" class="button secondary-button" style="margin-left: 6px;">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Patient Records Table -->
        <table>
            <thead>
                <tr>
                    <th>Patient No.</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="5">No patients found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['patient_number']) ?></td>
                            <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                            <td><?= htmlspecialchars($r['gender']) ?></td>
                            <td><?= htmlspecialchars($r['phone'] ?? '') ?></td>
                            <td class="row-actions">
                                <a class="action-button" href="view.php?id=<?= $r['id'] ?>">View</a>
                                <a class="action-button" href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                                <form method="post" action="delete.php" class="inline-form" onsubmit="return confirm('Delete this patient?')">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button class="delete-button" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>