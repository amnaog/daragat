<?php
$conn = new mysqli("localhost", "root", "", "quran_circle");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// جلب بيانات الحضور (عدد حضور كل يوم)
$attendance = [];
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
foreach ($days as $day) {
    $result = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE DAYNAME(date) = '$day'");
    $attendance[] = $result->fetch_assoc()['total'];
}

// جلب سجل الأحداث
$logs = $conn->query("SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports & Activity</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { margin: 0; font-family: sans-serif; background: #f9f9f9; }
        .sidebar { width: 220px; background: #0f172a; height: 100vh; position: fixed; color: white; padding: 20px; }
        .sidebar a { color: white; display: block; margin: 15px 0; text-decoration: none; }
        .main { margin-left: 240px; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Quran Circle</h2>
        <a href="#">Dashboard</a>
        <a href="#">Teachers</a>
        <a href="#">Students</a>
        <a href="#">Halaqat</a>
        <a href="#">Reports</a>
        <a href="#">Notifications</a>
        <a href="#">Roles</a>
    </div>

    <div class="main">
        <h2>Reports & Activity</h2>

        <div class="card">
            <h3>Student Attendance Trend</h3>
            <canvas id="attendanceChart" width="600" height="200"></canvas>
        </div>

        <div class="card">
            <h3>System Logs</h3>
            <div>
                <?php while ($log = $logs->fetch_assoc()): ?>
                    <p><small><?= $log['created_at'] ?></small>: <?= $log['message'] ?></p>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Attendance',
                    data: <?= json_encode($attendance) ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.2)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
