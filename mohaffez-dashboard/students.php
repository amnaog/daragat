<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}
include 'db.php';

$teacher_id = $_SESSION['teacher_id'];

// ✅ جلب الطلاب المرتبطين بالمحفظ مع حساب التقدم وآخر حفظ
$query = "
    SELECT 
        students.id, 
        students.full_name, 
        halaqat.name AS halaqa_name,
        IFNULL(SUM(r.to_ayah - r.from_ayah + 1), 0) AS total_ayahs,
        (
            SELECT CONCAT(
                '<strong>Memorized Surah ', qs.name, '</strong><br>',
                '<span style=\"font-size:13px;color:#555;\">from Ayah ', r2.from_ayah, ' to Ayah ', r2.to_ayah, '<br>', Date(r2.created_at), '</span>'
            )
            FROM reports r2
            JOIN quran_surahs qs ON qs.id = r2.surah_id
            WHERE r2.student_id = students.id
            ORDER BY r2.created_at DESC, r2.id DESC
            LIMIT 1
        ) AS last_memorized
    FROM students
    JOIN halaqat ON students.halaqa_id = halaqat.id
    LEFT JOIN reports r ON students.id = r.student_id
    WHERE halaqat.teacher_id = ?
    GROUP BY students.id
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Students - QuranFlow</title>
  <link rel="stylesheet" href="styles.css" />
  <script>
    // ✅ البحث الديناميكي حسب الاسم أو اسم الحلقة
    function searchStudents() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll(".student-row");
      rows.forEach(row => {
        const name = row.dataset.name.toLowerCase();
        const halaqa = row.dataset.halaqa.toLowerCase();
        row.style.display = name.includes(input) || halaqa.includes(input) ? "" : "none";
      });
    }
  </script>
</head>
<body>
  <div class="sidebar">
    <div>
        <div class="logo">📗 QuranFlow</div>
        <ul class="menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="students.php" class="active">Students</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
    </div>
    <div class="user-info"><div class="avatar"></div>
        <div>Sheikh <?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
</div>

  <div class="main">
    <h2>Students</h2>

    <!-- ✅ مربع البحث بجانب العنوان -->
    <h3 class="all-students-title">All Students</h3>
<div class="search-box-left">
  <input type="text" id="searchInput" onkeyup="searchStudents()" placeholder="Search by name or halaqa...">
</div>

    <table class="styled-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Halaqa</th>
          <th>Progress</th>
          <th>Last Memorized</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s): ?>
          <?php $progress = round(($s['total_ayahs'] / 6236) * 100, 2); ?>
          <tr class="student-row" data-name="<?= strtolower($s['full_name']) ?>" data-halaqa="<?= strtolower($s['halaqa_name']) ?>">
            <td><?= htmlspecialchars($s['full_name']) ?></td>
            <td><?= htmlspecialchars($s['halaqa_name']) ?></td>
            <td>
              <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progress ?>%;"></div>
              </div>
              <?= $progress ?>%
            </td><td><?= $s['last_memorized'] ?: '—' ?></td>
            <td>
              <a href="messages.php?student_id=<?= $s['id'] ?>" class="btn-primary">Send Message</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>