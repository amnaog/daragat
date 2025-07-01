<?php
include 'db.php';

// fetch counts
$studentsCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];
$teachersCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM teachers"))[0];
$halaqatCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM halaqat"))[0];
$notificationsResult = mysqli_query($conn, "SELECT message, created_at FROM notifications ORDER BY created_at DESC LIMIT 3");
$notifications = [];
while ($row = mysqli_fetch_assoc($notificationsResult)) {
    $notifications[] = $row;
}

// generate students activity based on actual creation dates
$studentsPerDay = [];
$weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
foreach ($weekdays as $day) {
    $studentsPerDay[$day] = 0;
}

$result = mysqli_query($conn, "SELECT created_at FROM students");
while ($row = mysqli_fetch_assoc($result)) {
    $weekday = date('D', strtotime($row['created_at']));
    if (isset($studentsPerDay[$weekday])) {
        $studentsPerDay[$weekday]++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quran Circle Nexus - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f9f9f9;
        }
        .sidebar {
            background: #0f172a;
            color: white;
            width: 220px;
            height: 100vh;
            position: fixed;
            padding: 20px 10px;
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .sidebar a.active, .sidebar a:hover {
            background-color: #22c55e;
        }
        .main {
            margin-left: 240px;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .card h3 {
            margin-top: 0;
        }
        .flex-row {
            display: flex;
            gap: 20px;
        }
        .flex-row .card {
            flex: 1;
        }
        .quick-actions a {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
            padding: 10px 20px;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        .quick-actions a svg {
            margin-right: 8px;
        }
        .notifications li {
            margin-bottom: 12px;
            font-size: 14px;
        }
        .badge {
            font-size: 12px;
            background: #bbf7d0;
            color: #16a34a;
            padding: 2px 8px;
            border-radius: 9999px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a class="active" href="#">Dashboard</a>
    <a href="teachers.php">Teachers</a>
    <a href="students.php">Students</a>
    <a href="halaqat.php">Halaqat</a>
    <a href="reports.php">Reports</a>
</div>

<div class="main">
    <h1>Quran Circle Nexus <span class="badge">Admin</span></h1>
    <div class="flex-row">
        <div class="card">
            <h3>Total Students</h3>
            <p style="font-size: 32px; color: #16a34a;">
                <?php echo $studentsCount; ?>
            </p>
        </div>
        <div class="card">
            <h3>Total Teachers</h3>
            <p style="font-size: 32px; color: #16a34a;">
                <?php echo $teachersCount; ?>
            </p>
        </div>
        <div class="card">
            <h3>Active Halaqat</h3>
            <p style="font-size: 32px; color: #16a34a;">
                <?php echo $halaqatCount; ?>
            </p>
        </div>
    </div>
    <div class="flex-row">
        <div class="card quick-actions">
            <h3>Quick Actions</h3>
            <a href="add_teacher.php">➕ Add Teacher</a>
            <a href="add_student.php">👤 Add Student</a>
            <a href="add_circle.php">☰ Add Circle</a>
        </div>
        <div class="card">
            <h3>Recent Activity</h3>
            <canvas id="progressChart" height="140"></canvas>
        </div>
        <div class="card">
            <h3>Notifications</h3>
            <ul class="notifications">
                <?php foreach ($notifications as $note): ?>
                    <li><?php echo htmlspecialchars($note['message']); ?>
                        <span style="color:gray;font-size:12px;"> - <?php echo date("M j, H:i", strtotime($note['created_at'])); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<script>
const ctx = document.getElementById('progressChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($studentsPerDay)); ?>,
        datasets: [{
            label: 'Students Joined',
            data: <?php echo json_encode(array_values($studentsPerDay)); ?>,
            backgroundColor: 'rgba(34,197,94,0.2)',
            borderColor: '#22c55e',
            fill: true,
            tension: 0.3,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});
</script>
</body>
</html>
