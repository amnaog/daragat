<?php
session_start();
include 'db.php';
$preselected_student_id = intval($_GET['student_id'] ?? 0);

// التحقق من تسجيل الدخول
$teacher_id = $_SESSION['teacher_id'] = 1;
/*$teacher_id = $_SESSION['teacher_id'] ?? null;
if (!$teacher_id) {
    header("Location: login.php");
    exit;
}*/

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $message = $_POST['message'];
    $created_at = date('Y-m-d H:i:s');

   $query = "INSERT INTO messages (student_id, teacher_id, content, sent_at, is_read) 
          VALUES (?, ?, ?, ?, 0)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiss', $student_id, $teacher_id, $message, $created_at);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $success = "Message sent successfully.";
    } else {
        $error = "Failed to send message.";
    }
}

// جلب طلاب المحفظ الحالي
$query = "
    SELECT students.id, students.full_name 
    FROM students
    JOIN halaqat ON students.halaqa_id = halaqat.id
    WHERE halaqat.teacher_id = ?
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $teacher_id);
mysqli_stmt_execute($stmt);
$students_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Messages - QuranFlow</title>
  <link rel="stylesheet" href="styles.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

</head>
<body>
  <div class="sidebar">
    <div>
        <div class="logo">QuranFlow</div>
        <ul class="menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="progress.php">Progress</a></li>
            <li><a href="messages.php"class="active">Messages</a></li>
        </ul>
    </div>
    <div class="user-info"><div class="avatar"></div>
        <div>Sheikh Abdullah</div>
        <div style="font-size: 12px;">sheikh.abdullah@quran.com</div>
    </div>
</div>
  <div class="main">
    <header><h2>New Message</h2></header>
    <p>Send a message to one of your students.</p>

    <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST" class="message-form">
      <label>Student</label>
      <select name="student_id" required>
        <option value="">Select a student</option>
        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
         <option value="<?php echo $student['id']; ?>" <?php if ($student['id'] == $preselected_student_id) echo 'selected'; ?>>
       <?php echo htmlspecialchars($student['full_name']); ?>
        </option>
        <?php endwhile; ?>
      </select>

      <label>Message</label>
      <textarea name="message" rows="5" required placeholder="Write your message here..."></textarea>

      <button type="submit" class="send-button">Send Message</button>
    </form>
  </div>
</body>
</html>