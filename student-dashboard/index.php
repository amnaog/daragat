<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
include 'db.php';

$student_id = $_SESSION['student_id'];

// جلب بيانات الطالب
$stmt = $conn->prepare("SELECT s.full_name, s.email, h.name AS halaqa_name FROM students s 
                        LEFT JOIN halaqat h ON s.halaqa_id = h.id WHERE s.id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// عدد السور المحفوظة كاملة
$stmt = $conn->prepare("SELECT COUNT(*) AS completed_surahs 
                        FROM (
                          SELECT r.surah_id, SUM(r.to_ayah - r.from_ayah + 1) AS memorized_ayahs, 
                                 qs.ayah_count
                          FROM reports r
                          JOIN quran_surahs qs ON r.surah_id = qs.id
                          WHERE r.student_id = ?
                          GROUP BY r.surah_id
                          HAVING memorized_ayahs >= qs.ayah_count
                        ) AS completed");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$completed = $stmt->get_result()->fetch_assoc();
$surahs_completed = $completed['completed_surahs'];
$surahs_total = 114;
$surah_percent = round(($surahs_completed / $surahs_total) * 100, 1);

// عدد الآيات المحفوظة
$stmt = $conn->prepare("SELECT SUM(to_ayah - from_ayah + 1) AS total_ayahs FROM reports WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$ayahs = $stmt->get_result()->fetch_assoc();
$memorized_verses = $ayahs['total_ayahs'] ?? 0;
$verses_total = 6236;
$verses_percent = round(($memorized_verses / $verses_total) * 100, 1);

// الستريك (عدد الأيام خلال آخر 30 يوم)
$stmt = $conn->prepare("SELECT COUNT(DISTINCT DATE(created_at)) AS streak 
                        FROM reports 
                        WHERE student_id = ? AND created_at >= NOW() - INTERVAL 30 DAY");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$streak = $stmt->get_result()->fetch_assoc()['streak'];
$streak_percent = round(($streak / 30) * 100, 1);

// الهدف الشهري (ثابت مؤقتًا)
$monthly_goal = 200;
$monthly_achieved = 145;
$monthly_percent = round(($monthly_achieved / $monthly_goal) * 100, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
     <div class="logo">📗 Quranic Compass<br><small>Progress Tracker</small></div>
      <nav>
        <ul>
          <li class="active"><a href="index.php"> Dashboard</a></li>
          <li><a href="memorization-plan.php"> Memorization Plan</a></li>
          <li><a href="notifications.php">Notifications</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="main-header">
        <h1>Dashboard</h1>
        <p>Welcome back to your Quranic journey</p>
        <div class="date"><?php echo date('m/d/Y'); ?></div>
      </header>

      <section class="student-card">
        <div class="avatar"><?php echo strtoupper(substr($student['full_name'], 0, 2)); ?></div>
        <div class="info">
          <h2><?php echo $student['full_name']; ?></h2>
          <p><?php echo $student['email']; ?></p>
          <div class="tags">
            <span class="tag"><?php echo $student['halaqa_name']; ?></span>
          </div>
        </div>
      </section>

      <section class="progress-cards">
        <div class="card">
          <h3>Surahs Completed</h3>
          <p><?php echo "$surahs_completed of $surahs_total"; ?></p>
          <div class="progress-bar"><div style="width:<?php echo $surah_percent; ?>%"></div></div>
          <span class="percent"><?php echo $surah_percent; ?>%</span>
        </div>
        <div class="card"><h3>Verses Memorized</h3>
          <p><?php echo "$memorized_verses of $verses_total"; ?></p>
          <div class="progress-bar"><div style="width:<?php echo $verses_percent; ?>%"></div></div>
          <span class="percent"><?php echo $verses_percent; ?>%</span>
        </div>
        <div class="card">
          <h3>Current Streak</h3>
          <p><?php echo "$streak of 30 days"; ?></p>
          <div class="progress-bar"><div style="width:<?php echo $streak_percent; ?>%"></div></div>
          <span class="percent"><?php echo $streak_percent; ?>%</span>
        </div>
      </section>
    </main>
  </div>
</body>
</html>