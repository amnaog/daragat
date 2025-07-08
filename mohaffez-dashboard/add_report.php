<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}
// إعداد الاتصال بقاعدة البيانات
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// جلب معرف الطالب والحلقة من الرابط
$student_id = intval($_GET['student_id'] ?? 0);
$halaqa_id = intval($_GET['halaqa_id'] ?? 0);

// جلب اسم الطالب لعرضه في النموذج
$student_name = '';
if ($student_id) {
    $stmt = $conn->prepare("SELECT full_name FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name);
    $stmt->fetch();
    $stmt->close();
}

// جلب كل السور من جدول quran_surahs
$surahs = $conn->query("SELECT id, name, ayah_count FROM quran_surahs");

// جلب آخر سورة حفظها الطالب بناءً على أحدث تاريخ ثم أحدث id
$last_surah_id = null;
$last_query = $conn->query("SELECT surah_id FROM reports WHERE student_id = $student_id ORDER BY created_at DESC, id DESC LIMIT 1");
if ($last_row = $last_query->fetch_assoc()) {
    $last_surah_id = $last_row['surah_id'];
}

$error_message = "";

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surah_id = intval($_POST['surah_id']);
    $from_ayah = intval($_POST['from_ayah']);
    $to_ayah = intval($_POST['to_ayah']);
    $created_at = $_POST['created_at'];

    // جلب عدد آيات السورة المختارة للتحقق من صحة الأرقام
    $ayah_count = $conn->query("SELECT ayah_count FROM quran_surahs WHERE id = $surah_id")
        ->fetch_assoc()['ayah_count'] ?? 0;

    // ✅ التحقق من صحة الترتيب وحدود الآيات
    if ($from_ayah > $to_ayah || $from_ayah < 1 || $to_ayah > $ayah_count) {
        $error_message = "⚠️ Error: Please ensure the 'From Ayah' is less than or equal to the 'To Ayah', and that both numbers are within the Surah range";
    } else {
        // ✅ التحقق من التداخل مع أي مقاطع محفوظة مسبقًا في نفس السورة
       $overlap_sql = "
    SELECT * FROM reports 
    WHERE student_id = $student_id 
      AND surah_id = $surah_id
      AND NOT (
          $to_ayah < from_ayah OR
          $from_ayah > to_ayah
      )";
        $overlap_result = $conn->query($overlap_sql);
        if ($overlap_result->num_rows > 0) {
            $error_message = "❌ This passage or part of it has already been memorized. Please select a different range.";
        } else {
            // ✅ حفظ التقرير
            $conn->query("INSERT INTO reports (student_id, surah_id, from_ayah, to_ayah, created_at)
                          VALUES ($student_id, $surah_id, $from_ayah, $to_ayah, '$created_at')");

            // ✅ حساب التقدم العام من مجموع الآيات
            $total = $conn->query("SELECT SUM(to_ayah - from_ayah + 1) as total FROM reports WHERE student_id = $student_id")
                          ->fetch_assoc()['total'] ?? 0;
            $progress = round(($total / 6236) * 100);
            $conn->query("UPDATE students SET progress = $progress WHERE id = $student_id");

            // ✅ إعادة التوجيه للوحة التحكم
            header("Location: index.php?halaqa_id=$halaqa_id&refresh=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Memorization Report</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // تحديث قائمة الآيات عند تغيير السورة المختارة
//         function updateAyahOptions() {
//   var surahId = document.getElementById("surah_id").value;

//   if (!surahId) {
//     document.getElementById("from_ayah").innerHTML = "";
//     document.getElementById("to_ayah").innerHTML = "";
//     return;
//   }

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      document.getElementById("from_ayah").innerHTML = xhttp.responseText;
      document.getElementById("to_ayah").innerHTML = xhttp.responseText;
    }
  };

  xhttp.open("GET", "get_ayahs.php?surah_id=" + surahId, true);
  xhttp.send();
}

document.addEventListener("DOMContentLoaded", updateAyahOptions);
    </script>
</head>
<body>

<div class="sidebar">
    <div>
        <div class="logo">QuranFlow</div>
        <ul class="menu">
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
    </div>
    <div class="user-info">
        <div class="avatar"></div>
         <div>Sheikh <?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
</div>

<!--  محتوى الصفحة الرئيسية -->
<div class="main">
    <div class="add-report-container">
        <h2>Add Memorization Report for Student: <?= htmlspecialchars($student_name) ?></h2>

    
        <?php if ($error_message): ?>
            <div class="error-box"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- نموذج إدخال التقرير -->
        <form method="post" class="report-form">
            <div class="form-group">
                <label for="created_at">Date:</label>
                <input type="date" id="created_at" name="created_at" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="surah_id">Surah:</label>
                <select name="surah_id" id="surah_id" onchange="updateAyahOptions()" required>
                    <option value="">Select Surah</option>
                    <?php while ($s = $surahs->fetch_assoc()): ?>
                        <option value="<?= $s['id'] ?>" data-ayah-count="<?= $s['ayah_count'] ?>"
                            <?= ($s['id'] == $last_surah_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="from_ayah">From Ayah:</label>
                <select id="from_ayah" name="from_ayah" required></select>
            </div>

            <div class="form-group">
                <label for="to_ayah">To Ayah:</label>
                <select id="to_ayah" name="to_ayah" required></select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Done</button>
                <a href="index.php?halaqa_id=<?= $halaqa_id ?>" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>