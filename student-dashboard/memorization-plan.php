<?php
// الاتصال بقاعدة البيانات
$conn = mysqli_connect("localhost", "root", "", "darajat");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// نحدد معرف الطالب (هنا ثابت مؤقتًا، تقدر لاحقًا تجيبه من session)
$student_id = 1;

// لو الطالب حدّث هدفه
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $daily_goal = (int)$_POST['daily_goal'];
    $monthly_goal = $daily_goal * 30;

    // تحديث الهدف
    $sql = "UPDATE student_goals SET daily_goal = $daily_goal, monthly_goal = $monthly_goal WHERE student_id = $student_id";
    mysqli_query($conn, $sql);
}

// جلب آخر هدف لهذا الطالب
$sql = "SELECT * FROM student_goals WHERE student_id = $student_id ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $sql);
$goal = mysqli_fetch_assoc($result);

// لو فيه بيانات محفوظة
$daily_goal = $goal ? $goal['daily_goal'] : 0;
$monthly_goal = $goal ? $goal['monthly_goal'] : 0;

// حساب الكروت
$total_verses = 6236;
$days_needed = $daily_goal > 0 ? ceil($total_verses / $daily_goal) : 0;
$completion_date = $days_needed > 0 ? date('Y-m-d', strtotime("+$days_needed days")) : '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Memorization Plan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <aside class="sidebar">
     <div class="logo">📗 Quranic Compass<br><small>Progress Tracker</small></div>
      <nav>
        <ul>
          <li><a href="index.php"> Dashboard</a></li>
          <li class="active"><a href="memorization-plan.php"> Memorization Plan</a></li>
          <li><a href="notifications.php"> Notifications</a></li>
        </ul>
      </nav>
       <!--<footer>May Allah guide your journey</footer>-->
    </aside>
<main class="main-content">
   <header class="main-header">
    <h1>Memorization Plan</h1>
    <p>Set and track your memorization goals</p>
   </header>
<form class="target-form" method="post">
    <input type="number" name="daily_goal" value="<?= $daily_goal ?>" placeholder="Daily Verses Target" required>
    <button type="submit">Update Targets</button>
</form>
<div class="summary-cards">
    <div class="card success">
        <h3>Estimated Completion</h3>
        <div class="big"><?= $completion_date ?></div>
        <small><?= $days_needed > 0 ? "$days_needed days remaining" : '—' ?></small>
    </div>
    <div class="card">
        <h3>Daily Progress</h3>
        <div class="big"><?= $daily_goal ?></div>
        <small>verses per day</small>
    </div>
    <div class="card">
        <h3>Weekly Progress</h3>
        <div class="big"><?= $daily_goal * 7 ?></div>
        <small>verses per week</small>
    </div>
</div> </main>
</body>
</html>