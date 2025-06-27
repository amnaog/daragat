<?php
$conn = new mysqli("localhost", "root", "", "quran_circle");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// إضافة تنبيه جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = $conn->real_escape_string($_POST['message']);
    $conn->query("INSERT INTO notifications (message) VALUES ('$msg')");
    header("Location: notifications.php");
    exit;
}

// جلب التنبيهات
$result = $conn->query("SELECT *, TIMESTAMPDIFF(MINUTE, created_at, NOW()) as mins_ago FROM notifications ORDER BY created_at DESC");
$notifications = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <style>
        body { margin: 0; font-family: sans-serif; background: #f9f9f9; }
        .sidebar { width: 220px; background: #0f172a; height: 100vh; position: fixed; color: white; padding: 20px; }
        .sidebar a { color: white; display: block; margin: 15px 0; text-decoration: none; }
        .main { margin-left: 240px; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .btn { background: #10b981; color: white; padding: 10px 16px; border: none; border-radius: 8px; cursor: pointer; }
        .notif { border-bottom: 1px solid #eee; padding: 10px 0; }
        .timestamp { color: gray; font-size: 12px; }
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
        <h2>Notifications</h2>

        <form method="POST" style="margin-bottom: 20px;">
            <input type="text" name="message" placeholder="New notification..." required style="width: 300px; padding: 8px;">
            <button type="submit" class="btn">+ Send Notification</button>
        </form>

        <div class="card">
            <?php foreach ($notifications as $n): ?>
                <div class="notif">
                    <p><?= htmlspecialchars($n['message']) ?></p>
                    <div class="timestamp">
                        <?php
                            $mins = (int)$n['mins_ago'];
                            echo $mins < 60 ? "$mins min ago" :
                                 ($mins < 1440 ? floor($mins/60)." hours ago" : "Yesterday");
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
