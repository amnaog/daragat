<?php
// بدء الجلسة حتى نتمكن من استخدام متغيرات $_SESSION
session_start();

// التحقق مما إذا كان المستخدم الحالي لديه دور "محفّظ"
// لو لا، يتم تحويله إلى صفحة تسجيل الدخول
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

// إعداد بيانات الاتصال بقاعدة البيانات
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

// إنشاء اتصال بقاعدة البيانات باستخدام كائن mysqli
$conn = new mysqli($host, $user, $password, $database);

// التحقق من نجاح الاتصال، وفي حال وجود خطأ يتم إنهاء السكربت برسالة خطأ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// استلام رقم الحلقة من الرابط (GET)
// إذا لم يتم تمريره، القيمة تكون 0 كافتراضية
$halaqa_id = isset($_GET['halaqa_id']) ? intval($_GET['halaqa_id']) : 0;
$halaqa = []; // مصفوفة ستُستخدم لتخزين بيانات الحلقة لاحقًا

// إذا تم تمرير halaqa_id، نقوم بجلب بيانات الحلقة من قاعدة البيانات
if ($halaqa_id) {
    $result = $conn->query("SELECT * FROM halaqat WHERE id = $halaqa_id");

    // إذا تم العثور على الحلقة، نخزنها في المصفوفة
    if ($result->num_rows > 0) {
        $halaqa = $result->fetch_assoc();
    } else {
        // إذا لم يتم العثور على الحلقة، نعرض رسالة ونوقف تنفيذ السكربت
        die("Halaqa not found");
    }
}

// التحقق مما إذا تم إرسال النموذج عبر POST (أي تم النقر على زر "Save")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استلام البيانات المُعدلة من النموذج
    $name = $_POST['name'];           // اسم الحلقة الجديد
    $schedule = $_POST['schedule'];   // الجدول الزمني الجديد

    // تحضير استعلام التحديث باستخدام Prepared Statement لتأمين البيانات
    $stmt = $conn->prepare("UPDATE halaqat SET name = ?, schedule = ? WHERE id = ?");

    // ربط المتغيرات بالقيم حسب الترتيب: string, string, integer
    $stmt->bind_param("ssi", $name, $schedule, $halaqa_id);

    // تنفيذ الاستعلام، وفي حالة النجاح يتم تحويل المستخدم إلى الصفحة الرئيسية
    if ($stmt->execute()) {
        header("Location: index.php?halaqa_id=$halaqa_id&updated=1");
        exit;
    }
}
?> <!DOCTYPE html>
<html>
<head>
    <title>Edit Halaqa</title>
    <link rel="stylesheet" href="styles.css"> <!-- ربط ملف التنسيقات العامة -->
</head>
<body>

<!-- الشريط الجانبي الذي يحتوي على روابط التنقل -->
<div class="sidebar">
    <div>
        <div class="logo">📗 QuranFlow</div>
        <ul class="menu">
            <li><a href="index.php">Dashboard</a></li> <!-- رابط لوحة التحكم -->
            <li><a href="students.php">Students</a></li> <!-- رابط قائمة الطلاب -->
            <li><a href="messages.php">Messages</a></li> <!-- رابط صفحة الرسائل -->
        </ul>
    </div>

    <!-- جزء معلومات المستخدم (المحفّظ) -->
    <div class="user-info">
        <div class="avatar"></div> <!-- صورة رمزية -->
        <div>Sheikh <?php echo htmlspecialchars($_SESSION['username']); ?></div> <!-- اسم المحفظ -->
    </div>
</div>

<!-- القسم الرئيسي للصفحة -->
<div class="main">
    <div class="edit-form-container">
        <h2>Edit Halaqa</h2> <!-- عنوان الصفحة -->
        <p>Update information for this Halaqa.</p> <!-- وصف مختصر -->

        <!-- نموذج تعديل بيانات الحلقة -->
        <form method="POST">
            <!-- حقل تعديل اسم الحلقة -->
            <label for="name">Halaqa Name:</label>
            <input type="text" id="name" name="name" 
                   value="<?= htmlspecialchars($halaqa['name']) ?>" required>

            <!-- حقل تعديل جدول الحلقة -->
            <label for="schedule">Schedule:</label>
            <input type="text" id="schedule" name="schedule" 
                   value="<?= htmlspecialchars($halaqa['schedule']) ?>" required>

            <!-- أزرار حفظ أو إلغاء -->
            <div class="button-group">
                <button type="submit" class="save-button">💾 Save Changes</button>
                <a href="index.php" class="cancel-button">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>