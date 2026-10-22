<?php
require_once "config/database.php";

// Fetch overall total registrations directly
$total_patients = 0;
$total_query = $conn->query("SELECT COUNT(*) as total FROM patients");
if ($total_query) {
    $total_patients = (int)$total_query->fetch_assoc()['total'];
}

// Fetch daily registration counts for the 30 most recent active registration days
$sql = "SELECT DATE_FORMAT(created_at, '%b %d') as reg_date, COUNT(*) as total 
        FROM patients 
        GROUP BY DATE(created_at) 
        ORDER BY DATE(created_at) DESC 
        LIMIT 30";

$result = $conn->query($sql);

$dates = [];
$counts = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['reg_date'];
        $counts[] = (int)$row['total'];
    }
    // Reverse array so dates display chronologically (left to right) on the chart
    $dates = array_reverse($dates);
    $counts = array_reverse($counts);
}

// Fetch gender summary insights
$gender_sql = "SELECT gender, COUNT(*) as total FROM patients GROUP BY gender";
$gender_result = $conn->query($gender_sql);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration Insights</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="container">
        <h1>Patient Registration Trends</h1>
        <p>Track daily patient registrations to identify high-volume days and demographics.</p>

        <p style="margin-bottom: 24px;">
            <a href="index.php" class="button secondary-button">Home</a>
            <a href="patients/index.php" class="button secondary-button">Back to Patients</a>
        </p>

        <!-- Summary Metric Cards -->
        <div class="cards">
            <div class="card">
                <span>Total Registrations</span>
                <strong><?= $total_patients ?></strong>
            </div>
            <?php if ($gender_result): ?>
                <?php while ($g = $gender_result->fetch_assoc()): ?>
                    <div class="card">
                        <span><?= htmlspecialchars(ucfirst($g['gender'] ?? 'Unspecified')) ?> Patients</span>
                        <strong><?= $g['total'] ?></strong>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <!-- Graphical Chart -->
        <div style="max-width: 800px; margin: 30px auto;">
            <canvas id="registrationChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('registrationChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                    label: 'Patients Registered',
                    data: <?= json_encode($counts) ?>,
                    borderColor: '#17636d',
                    backgroundColor: 'rgba(23, 99, 109, 0.15)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>

</body>
</html>