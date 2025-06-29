<?php
include 'db.php'; // الاتصال بقاعدة البيانات

// جلب بيانات الطلاب مع آخر تقرير لهم
$students = [];
$sql = "SELECT * FROM students";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($student = mysqli_fetch_assoc($result)) {
        $student_id = $student['id'];

        // جلب آخر تقرير للطالب
        $report_sql = "SELECT performance, created_at 
                       FROM reports 
                       WHERE student_id = $student_id 
                       ORDER BY created_at DESC 
                       LIMIT 1";
        $report_result = mysqli_query($conn, $report_sql);
        $report = mysqli_fetch_assoc($report_result);

        $student['last_performance'] = $report['performance'] ?? null;
        $student['last_date'] = $report['created_at'] ?? null;

        $students[] = $student;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Students - QuranFlow</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <h2 class="logo">QuranFlow</h2>
    <ul class="menu">
      <li><a href="index.php">Dashboard</a></li>
      <li><a href="students.php" class="active">Students</a></li>
      <li><a href="progress.php">Progress</a></li>
      <li><a href="messages.php">Messages</a></li>
    </ul>
    <div class="user-info">
      <div class="avatar"></div>
      <div class="email">Sheikh Abdullah<br><small>sheikh.abdullah@quran.com</small></div>
    </div>
  </div>

  <div class="main">
    <header>
      <h2>Students</h2>
    </header>

    <section class="students-section">
      <h3>All Students</h3>
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Level</th>
            <th>Progress</th>
            <th>Last Memorized</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
              <tr>
                <td><div class="avatar-sm"></div> <?= htmlspecialchars($student['full_name']) ?></td>
                <td><?= htmlspecialchars($student['level'] ?? '--') ?></td>
                <td>
                  <div class="progress">
                    <div class="bar" style="width: <?= intval($student['progress']) ?>%"></div>
                  </div>
                  <?= intval($student['progress']) ?>%
                </td>
                <td>
                  <?php if ($student['last_performance'] && $student['last_date']): ?>
                    <div class="memorized-box">
                      <div class="surah-name">Surah <?= htmlspecialchars($student['last_performance']) ?></div>
                      <div class="memorized-date"><?= date('Y-m-d', strtotime($student['last_date'])) ?></div>
                    </div>
                  <?php else: ?>
                    —
                  <?php endif; ?>
                </td>
                <td>...</td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5">No students found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</body>
</html>