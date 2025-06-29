<?php
include 'db.php';

// تحضير بيانات الرسم البياني لآخر 4 أسابيع
$weekly_avg = [];
$labels = [];

for ($i = 3; $i >= 0; $i--) {
    $start = date('Y-m-d', strtotime("-$i week"));
    $end = date('Y-m-d', strtotime("-" . ($i - 1) . " week"));

    $labels[] = date('M d', strtotime($start)); // مثال: Jun 12

    $query = "SELECT AVG(progress_percent) as avg_progress 
              FROM reports 
              WHERE created_at BETWEEN '$start' AND '$end'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $weekly_avg[] = round($row['avg_progress'] ?? 0);
}

// سحب آخر 4 إنجازات
$achievements = [];
$query = "SELECT students.full_name, reports.performance, reports.created_at 
          FROM reports 
          JOIN students ON reports.student_id = students.id 
          ORDER BY reports.created_at DESC 
          LIMIT 4";
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
    <h2 class="logo">QuranFlow</h2>
    <ul class="menu">
      <li><a href="index.php">Dashboard</a></li>
      <li><a href="students.php">Students</a></li>
      <li><a href="progress.php" class="active">Progress</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
    <div class="user-info">
      <div class="avatar"></div>
      <div class="email">Sheikh Abdullah<br><small>sheikh.abdullah@quran.com</small></div>
    </div>
  </div>

  <div class="main">
    <header>
      <h2>Progress</h2>
    </header>

    <div class="progress-layout">
      <!-- Progress Chart -->
      <div class="progress-chart-box">
        <h4>Overall Progress Trend</h4>
        <p>Average progress of all students over the last 4 weeks.</p>
        <canvas id="progressChart" height="120"></canvas>
      </div>

      <!-- Achievements -->
      <div class="progress-achievements">
        <h4>Recent Achievements</h4>
        <p>Latest progress from your students.</p>
        <ul class="achievement-list">
          <?php foreach ($achievements as $item): ?>
            <li>
              <div class="avatar-sm"></div> 
              <b><?php echo $item['full_name']; ?></b><br>
              <span><?php echo htmlspecialchars($item['performance']); ?></span>
              <small>
                <?php
                  $daysAgo = (new DateTime())->diff(new DateTime($item['created_at']))->days;
                  echo "$daysAgo days ago";
                ?>
              </small>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Average Progress',
          data: <?php echo json_encode($weekly_avg); ?>,
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