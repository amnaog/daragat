<?php
// استدعاء ملف الاتصال بقاعدة البيانات
include 'db.php';

// جلب جميع المعلمين لترتيبهم أبجدياً في القائمة المنسدلة
$teachersResult = mysqli_query($conn, "SELECT id, full_name FROM teachers ORDER BY full_name ASC");

// التعامل مع إضافة حلقة جديدة عند إرسال نموذج الإضافة
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    // تنظيف قيمة اسم الحلقة لحمايتها من هجمات SQL Injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    // التحقق من وجود معلم مختار وتحويله إلى رقم صحيح أو NULL
    $teacher_id = !empty($_POST['teacher_id']) ? intval($_POST['teacher_id']) : NULL;
    // تنظيف قيمة الجدول الزمني للحلقة
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule']);
    // تنظيف قيمة الوقت
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    // إدخال بيانات الحلقة الجديدة في جدول halaqat
    mysqli_query($conn, "INSERT INTO halaqat (name, teacher_id, schedule, time) VALUES ('$name', ".($teacher_id ? $teacher_id : "NULL").", '$schedule', '$time')");
    // إعادة التوجيه إلى نفس الصفحة لتحديث البيانات
    header("Location: halaqat.php");
    exit();
}

// التعامل مع تعديل حلقة موجودة
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    // رقم الحلقة المراد تعديلها
    $id = intval($_POST['id']);
    // تنظيف وإعداد البيانات الجديدة
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $teacher_id = !empty($_POST['teacher_id']) ? intval($_POST['teacher_id']) : NULL;
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    // تحديث بيانات الحلقة في قاعدة البيانات
    mysqli_query($conn, "UPDATE halaqat SET name='$name', teacher_id=".($teacher_id ? $teacher_id : "NULL").", schedule='$schedule', time='$time' WHERE id=$id");
    // إعادة التوجيه لتحديث العرض
    header("Location: halaqat.php");
    exit();
}

// التعامل مع حذف حلقة عند الطلب عبر رابط الحذف
if (isset($_GET['delete'])) {
    // رقم الحلقة المراد حذفها
    $id = intval($_GET['delete']);
    // حذف الحلقة من جدول halaqat
    mysqli_query($conn, "DELETE FROM halaqat WHERE id=$id");
    // إعادة التوجيه لتحديث الصفحة
    header("Location: halaqat.php");
    exit();
}

// جلب بيانات الحلقات مع اسم المعلم المرتبط (إذا وجد) لترتيب عرضها
$halaqat = mysqli_query($conn, "SELECT h.*, t.full_name AS teacher_name FROM halaqat h LEFT JOIN teachers t ON h.teacher_id = t.id ORDER BY h.id DESC");

// وضع التعديل: التحقق من وجود طلب تعديل عبر الرابط
$editMode = false; // هل نحن في وضع التعديل؟
$editData = null;  // بيانات الحلقة التي سيتم تعديلها
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editResult = mysqli_query($conn, "SELECT * FROM halaqat WHERE id=$editId");
    $editData = mysqli_fetch_assoc($editResult);
    $editMode = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Halaqat</title>
    <style>
        /* تنسيق عام للصفحة */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f9f9f9; }
        /* الشريط الجانبي */
        .sidebar {
            background: #0f172a; color: white; width: 220px; height: 100vh; position: fixed; padding: 20px 10px;
        }
        .sidebar h2 { font-size: 18px; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 10px; color: white; text-decoration: none; border-radius: 6px; margin-bottom: 10px; }
        .sidebar a.active, .sidebar a:hover { background-color: #1e293b; }
        /* المحتوى الرئيسي */
        .main { margin-left: 240px; padding: 30px; }
        /* البطاقات */
        .card {
            background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        h2 { margin-top: 0; }
        /* تنسيق الحقول داخل النموذج */
        form input, form select {
            width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;
            font-size: 1em;
        }
        /* زر الإرسال */
        button {
            background-color: #22c55e; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;
            font-weight: bold;
            font-size: 1em;
        }
        /* جدول عرض الحلقات */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        /* روابط تعديل وحذف */
        a.edit, a.delete { margin-right: 10px; color: #22c55e; text-decoration: none; }
        a.delete { color: red; }
        /* تنسيق القائمة المنسدلة */
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: white url('data:image/svg+xml;utf8,<svg fill="%23718896" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 12px center;
            background-size: 12px;
            padding-right: 40px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>📗 QuranFlow</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="teachers.php">Teachers</a>
    <a href="students.php">Students</a>
    <a class="active" href="halaqat.php">Halaqat</a>
    <a href="Requests.php">Requests</a>
</div>

<div class="main">
    <!-- نموذج إضافة / تعديل الحلقة -->
    <div class="card">
        <h2><?php echo $editMode ? 'Edit' : 'Add'; ?> Halaqa</h2>
        <form method="POST">
            <!-- تحديد نوع الإجراء: إضافة أو تعديل -->
            <input type="hidden" name="action" value="<?php echo $editMode ? 'update' : 'add'; ?>">
            <!-- حقل مخفي للاحتفاظ برقم الحلقة عند التعديل -->
            <?php if ($editMode): ?>
                <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
            <?php endif; ?>
            <!-- حقل اسم الحلقة -->
            <input type="text" name="name" placeholder="Halaqa Name" required value="<?php echo $editMode ? htmlspecialchars($editData['name']) : ''; ?>">
            <!-- اختيار المعلم من القائمة المنسدلة -->
            <select name="teacher_id">
                <option value="">-- Select Teacher --</option>
                <?php 
                // لإعادة استخدام نتائج المعلمين بعد الاستخدام الأول، يجب إعادة تنفيذ الاستعلام:
                mysqli_data_seek($teachersResult, 0); 
                while ($teacher = mysqli_fetch_assoc($teachersResult)): ?>
                    <option value="<?= $teacher['id'] ?>" <?= ($editMode && $editData['teacher_id'] == $teacher['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($teacher['full_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <!-- حقل الجدول الزمني -->
            <input type="text" name="schedule" placeholder="Schedule" required value="<?php echo $editMode ? htmlspecialchars($editData['schedule']) : ''; ?>">
            <!-- حقل الوقت -->
            <input type="text" name="time" placeholder="Time" value="<?php echo $editMode ? htmlspecialchars($editData['time']) : ''; ?>">
            <!-- زر الإضافة أو التعديل -->
            <button type="submit"><?php echo $editMode ? 'Update' : 'Add'; ?> Halaqa</button>
        </form>
    </div>

    <!-- جدول عرض كل الحلقات -->
    <div class="card">
        <h2>All Halaqat</h2>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Teacher</th>
                <th>Schedule</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($halaqat)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['teacher_name'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($row['schedule']) ?></td>
                    <td><?= htmlspecialchars($row['time']) ?></td>
                    <td>
                        <!-- رابط تعديل الحلقة -->
                        <a class="edit" href="?edit=<?= $row['id'] ?>">Edit</a>
                        <!-- رابط حذف الحلقة مع تأكيد -->
                        <a class="delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this halaqa?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
