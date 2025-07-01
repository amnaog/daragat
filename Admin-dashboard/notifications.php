<?php
require 'db.php';

function fetchNotifications($conn) {
    $sql = "SELECT n.message, n.created_at, s.full_name 
            FROM notifications n 
            LEFT JOIN students s ON n.student_id = s.id 
            ORDER BY n.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function addNotification($conn, $message, $student_id = null) {
    $message = mysqli_real_escape_string($conn, $message);
    $student_id = $student_id ? (int)$student_id : 'NULL';
    $sql = "INSERT INTO notifications (message, student_id, created_at) VALUES ('$message', $student_id, NOW())";
    mysqli_query($conn, $sql);
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return $diff . " sec ago";
    if ($diff < 3600) return floor($diff / 60) . " min ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    return "Yesterday";
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $student_id = !empty($_POST['student_id']) ? (int)$_POST['student_id'] : null;
    addNotification($conn, $message, $student_id);
    header("Location: notifications.php");
    exit();
}

$notifications = fetchNotifications($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications - Quran Circle Nexus</title>
  <style>
    body { font-family: sans-serif; background: #f9f9f9; margin: 0; }
    .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; padding: 20px; }
    .sidebar h2 { font-size: 18px; margin-bottom: 30px; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar li { margin: 15px 0; }
    .sidebar li a { color: white; text-decoration: none; display: flex; align-items: center; }
    .main { margin-left: 260px; padding: 40px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .notification { padding: 10px 0; }
    .notification p { margin: 0; font-weight: 500; }
    .notification small { color: gray; }
    hr { border: none; border-top: 1px solid #eee; margin: 10px 0; }
    .btn { background: #22c55e; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; }
    .btn:hover { background: #16a34a; }
    form { display: flex; gap: 10px; margin-bottom: 20px; }
    input[type=text], select { flex: 1; padding: 8px; border-radius: 6px; border: 1px solid #ccc; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="teachers.php">Teachers</a></li>
      <li><a href="students.php">Students</a></li>
      <li><a href="halaqat.php">Halaqat</a></li>
      <li><a href="Reports.php">Reports</a></li>
      <li><a href="notifications.php">Notifications</a></li>
      <li><a href="roles.php">Roles</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="header">
      <h2>Notifications</h2>
      <form method="POST">
        <input type="text" name="message" placeholder="Type a notification..." required>
        <select name="student_id">
          <option value="">All Students</option>
          <?php
          $result = mysqli_query($conn, "SELECT id, full_name FROM students");
          while ($student = mysqli_fetch_assoc($result)) {
              echo '<option value="' . $student['id'] . '">' . htmlspecialchars($student['full_name']) . '</option>';
          }
          ?>
        </select>
        <button class="btn" type="submit">+ Send Notification</button>
      </form>
    </div>

    <div class="card">
      <?php foreach ($notifications as $note): ?>
        <div class="notification">
          <p>
            <?= htmlspecialchars($note['message']) ?>
            <?php if (!empty($note['full_name'])): ?>
              <br><small>To: <?= htmlspecialchars($note['full_name']) ?></small>
            <?php endif; ?>
          </p>
          <small><?= timeAgo($note['created_at']) ?></small>
        </div>
        <hr>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>