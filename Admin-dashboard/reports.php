<?php
include 'db.php';

// إعداد بيانات الرسم البياني حسب الأيام
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$data = [];

foreach ($days as $day) {
    $query = "SELECT COUNT(*) as total FROM reports WHERE DAYNAME(created_at) = '$day'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f9f9f9; }
    .sidebar {
      background: #0f172a; color: white; width: 220px; height: 100vh; position: fixed; padding: 20px 10px;
    }
    .sidebar h2 { font-size: 18px; margin-bottom: 30px; }
    .sidebar a { display: block; padding: 10px; color: white; text-decoration: none; border-radius: 6px; margin-bottom: 10px; }
    .sidebar a.active, .sidebar a:hover { background-color: #22c55e; }
    .main { margin-left: 240px; padding: 30px; }
    .card {
      background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .chart-wrapper {
      width: 100%;
      overflow-x: auto;
    }
    canvas { width: 100% !important; height: 250px !important; }
  </style>
</head>
<body>
<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="teachers.php">Teachers</a>
  <a href="students.php">Students</a>
  <a href="halaqat.php">Halaqat</a>
  <a class="active" href="reports.php">Reports</a>
    <a href="notifications.php">Notifications</a>
    <a href="roles.php">Roles</a>
</div>

<div class="main">
  <h2>Reports & Activity</h2>

  <div class="card">
    <h3>Student Attendance Trend</h3>
    <div class="chart-wrapper">
      <canvas id="attendanceChart"></canvas>
    </div>
  </div>

  <div class="card">
    <h3>System Logs</h3>
    <p>06:44 AM: Attendance recorded for Circle 1</p>
    <p>Yesterday: Student Khalid joined Circle 2</p>
    <p>2 days ago: Absence alert generated for Jamal</p>
  </div>
</div>

<script>
  const ctx = document.getElementById('attendanceChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
      datasets: [{
        label: 'Reports Count',
        data: <?php echo json_encode($data); ?>,
        borderColor: '#22c55e',
        backgroundColor: 'transparent',
        tension: 0.3
      }]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
</body>
</html>