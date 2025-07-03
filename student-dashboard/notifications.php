<?php
// اتصال بقاعدة البيانات
$conn = mysqli_connect("localhost", "root", "", "darajat");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// مؤقتًا: تحديد الطالب id = 1
$student_id = 1;

// جلب الرسائل
$sql = "SELECT m.*, t.full_name 
        FROM messages m
        JOIN teachers t ON m.teacher_id = t.id
        WHERE m.student_id = $student_id
        ORDER BY m.sent_at DESC";

$result = mysqli_query($conn, $sql);

// جلب عدد الرسائل الكلي والجديدة
$sql_count = "SELECT 
                COUNT(*) as total, 
                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as new_count 
              FROM messages 
              WHERE student_id = $student_id";

$count_result = mysqli_query($conn, $sql_count);
$counts = mysqli_fetch_assoc($count_result);
$total_messages = $counts['total'];
$new_messages = $counts['new_count'];

// تحديث الرسائل كـ "مقروءة" بعد عرضها
$update_sql = "UPDATE messages SET is_read = 1 WHERE student_id = $student_id AND is_read = 0";
mysqli_query($conn, $update_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Notifications</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">📗 Quranic Compass<br><small>Progress Tracker</small></div>
    <nav>
      <ul>
        <li><a href="index.php"> Dashboard</a></li>
        <li><a href="memorization-plan.php"> Memorization Plan</a></li>
        <li class="active"><a href="notifications.php">Notifications</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <div class="notification-header">
      <header class="main-header">
      <h1>Notifications</h1>
      <?php if ($new_messages > 0): ?>
        <span class="badge-new"><?= $new_messages ?> new</span>
      <?php endif; ?>
    </div>
    <p>Stay updated with your messages</p>
    </header>
    <div class="notif-summary">
      <div>
        <span><?= $new_messages ?></span>
        New Messages
      </div>
      <div>
        <span><?= $total_messages ?></span>
        Total Messages
      </div>
    </div>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <div class="notification-card">
        <div class="from"><?= htmlspecialchars($row['full_name']) ?></div>
        <?php if ($row['is_read'] == 0): ?>
          <span class="tag new">New</span>
        <?php endif; ?>
        <p><?= htmlspecialchars($row['content']) ?></p>
        <small>📅 <?= date("Y-m-d H:i A", strtotime($row['sent_at'])) ?></small>
      </div>
    <?php endwhile; ?>
  </main>
</body>
</html>