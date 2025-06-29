<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}

include 'db.php'; // الاتصال بقاعدة البيانات

// جلب الإحصائيات
$students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM students"));
$teachers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM teachers"));
$halaqat  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM halaqat"));
$notifications = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - Quran Circle Nexus</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <?php include 'components/sidebar.html'; ?>

  <div class="main-content">
    <h1>Dashboard</h1>

    <div class="stats-grid">
      <div class="card">
        <h3>Total Students</h3>
        <p class="count"><?= $students['total'] ?></p>
      </div>
      <div class="card">
        <h3>Total Teachers</h3>
        <p class="count"><?= $teachers['total'] ?></p>
      </div>
      <div class="card">
        <h3>Active Halaqat</h3>
        <p class="count"><?= $halaqat['total'] ?></p>
      </div>
    </div>

    <div class="dashboard-sections">
      <div class="quick-actions">
        <h3>Quick Actions</h3>
        <a href="add_teacher.php" class="btn">+ Add Teacher</a>
        <a href="add_student.php" class="btn">+ Add Student</a>
        <a href="add_circle.php" class="btn">+ Add Circle</a>
      </div>

      <div class="notifications">
        <h3>Notifications</h3>
        <ul>
          <?php while ($row = mysqli_fetch_assoc($notifications)) : ?>
            <li>
              <?= htmlspecialchars($row['message']) ?><br />
              <small><?= date('F j, g:i a', strtotime($row['created_at'])) ?></small>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </div>
</body>
</html>
