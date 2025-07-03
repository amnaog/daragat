<?php
include 'db.php';

// ✅ تحضير بيانات الرسم البياني لآخر 7 أيام
$daily_avg = [];
$labels = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i day"));
    $labels[] = date('M d', strtotime($date)); // مثال: Jul 01

    $query = "SELECT AVG(progress_percent) as avg_progress 
              FROM reports 
              WHERE DATE(created_at) = '$date'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $daily_avg[] = round($row['avg_progress'] ?? 0, 2);
}

// ✅ جلب آخر إنجاز لكل طالب (عدم التكرار)
$achievements = [];
$query = "
    SELECT students.id, students.full_name, halaqat.name AS halaqa_name, reports.performance, reports.created_at
    FROM reports
    JOIN students ON reports.student_id = students.id
    JOIN halaqat ON students.halaqa_id = halaqat.id
    INNER JOIN (
        SELECT student_id, MAX(created_at) AS latest_report
        FROM reports
        GROUP BY student_id
    ) latest ON latest.student_id = reports.student_id AND latest.latest_report = reports.created_at
    ORDER BY reports.id DESC
";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $achievements[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Progress - QuranFlow</title>
  <link rel="stylesheet" href="styles.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
 <div class="sidebar">
    <div>
        <div class="logo">QuranFlow</div>
        <ul class="menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="progress.php" class="active">Progress</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
    </div>
    <div class="user-info"><div class="avatar"></div>
        <div>Sheikh Abdullah</div>
        <div style="font-size: 12px;">sheikh.abdullah@quran.com</div>
    </div>
</div>

  <div class="main">
    <header>
      <h2>Progress</h2>
    </header>

    <div class="progress-layout">
      <!-- ✅ الرسم البياني -->
      <div class="progress-chart-box">
        <h4>Overall Progress Trend</h4>
        <p>Average progress of all students over the last 7 days.</p>
        <canvas id="progressChart" height="120"></canvas>
      </div>

      <!-- ✅ الإنجازات الأخيرة -->
      <div class="progress-achievements">
        <h4>Recent Achievements</h4>
        <p>Latest progress from your students.</p>
        <ul class="achievement-list">
          <?php foreach ($achievements as $item): ?>
            <li>
              <b><?= htmlspecialchars($item['full_name']) ?></b><br>
              <span><?= htmlspecialchars($item['performance']) ?></span><br>
              <small><?= htmlspecialchars($item['halaqa_name']) ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <!-- ✅ رسم الرسم البياني -->
  <script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Average Progress',
          data: <?= json_encode($daily_avg) ?>,
          borderColor: '#22c55e',
          backgroundColor: '#22c55e',
          tension: 0.3,
          pointRadius: 5,
          pointHoverRadius: 6,
          fill: false,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#e5e7eb' }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  </script>
</body>
</html>