<?php
include 'db.php'; // تضمين ملف الاتصال بقاعدة البيانات

// التعامل مع إضافة طالب جديد عند استلام POST مع action = 'add'
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    // تأمين البيانات المدخلة من حقول النموذج لتجنب حقن SQL
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $halaqa_id = intval($_POST['halaqa_id']); // تحويل المعرف إلى عدد صحيح
    
    // تنفيذ استعلام الإدخال في جدول الطلاب
    mysqli_query($conn, "INSERT INTO students (full_name, email, phone, level, halaqa_id) VALUES ('$name', '$email', '$phone', '$level', $halaqa_id)");
    
    // إعادة التوجيه إلى صفحة الطلاب بعد الإضافة
    header("Location: students.php");
    exit();
}

// التعامل مع تحديث بيانات طالب موجود عند استلام POST مع action = 'update'
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']); // تحويل معرف الطالب إلى عدد صحيح
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $halaqa_id = intval($_POST['halaqa_id']);
    
    // تنفيذ استعلام التحديث للطالب المحدد
    mysqli_query($conn, "UPDATE students SET full_name='$name', email='$email', phone='$phone', level='$level', halaqa_id=$halaqa_id WHERE id=$id");
    
    // إعادة التوجيه إلى صفحة الطلاب بعد التحديث
    header("Location: students.php");
    exit();
}

// التعامل مع حذف طالب عند استلام GET مع معامل delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // تحويل المعرف إلى عدد صحيح
    
    // تنفيذ استعلام الحذف للطالب المحدد
    mysqli_query($conn, "DELETE FROM students WHERE id=$id");
    
    // إعادة التوجيه إلى صفحة الطلاب بعد الحذف
    header("Location: students.php");
    exit();
}

// جلب جميع الطلاب مع اسم الحلقة المرتبطة بهم عبر LEFT JOIN
$students = mysqli_query($conn, "SELECT students.*, halaqat.name AS halaqa_name FROM students LEFT JOIN halaqat ON students.halaqa_id = halaqat.id");

// التحقق من وضع التعديل لتحميل بيانات الطالب المطلوب تعديله
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editResult = mysqli_query($conn, "SELECT * FROM students WHERE id=$editId");
    $editData = mysqli_fetch_assoc($editResult);
    $editMode = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <style>
        /* تنسيق عام للصفحة */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f9f9f9; }
        /* تصميم الشريط الجانبي */
        .sidebar {
            background: #0f172a; color: white; width: 220px; height: 100vh; position: fixed; padding: 20px 10px;
        }
        .sidebar h2 { font-size: 18px; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 10px; color: white; text-decoration: none; border-radius: 6px; margin-bottom: 10px; }
        .sidebar a.active, .sidebar a:hover { background-color: #1e293b; }
        /* مساحة المحتوى الرئيسية بجانب الشريط الجانبي */
        .main { margin-left: 240px; padding: 30px; }
        /* تصميم البطاقات (الأقسام البيضاء) */
        .card {
            background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        h2 { margin-top: 0; }
        /* تنسيق حقول الإدخال والقوائم */
        form input, form select {
            width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;
            box-sizing: border-box;
        }
        /* تنسيق أزرار الإرسال */
        button {
            background-color: #22c55e; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;
        }
        /* تنسيق الجدول */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        /* روابط التعديل والحذف داخل الجدول */
        a.edit, a.delete { margin-right: 10px; color: #22c55e; text-decoration: none; }
        a.delete { color: red; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2> 📗 QuranFlow</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="teachers.php">Teachers</a>
    <a class="active" href="students.php">Students</a>
    <a href="halaqat.php">Halaqat</a>
    <a href="Requests.php">Requests</a>
</div>

<div class="main">
    <div class="card">
        <!-- عنوان القسم يعكس إذا كان إضافة أو تعديل -->
        <h2><?= $editMode ? 'Edit' : 'Add' ?> Student</h2>
        <form method="POST">
            <!-- إرسال نوع العملية: إضافة أو تعديل -->
            <input type="hidden" name="action" value="<?= $editMode ? 'update' : 'add' ?>">
            <?php if ($editMode): ?>
                <!-- عند التعديل، إرسال معرف الطالب -->
                <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            <!-- حقول الإدخال مع تعبئة البيانات عند التعديل -->
            <input type="text" name="full_name" placeholder="Full Name" required value="<?= $editMode ? htmlspecialchars($editData['full_name']) : '' ?>">
            <input type="email" name="email" placeholder="Email" required value="<?= $editMode ? htmlspecialchars($editData['email']) : '' ?>">
            <input type="text" name="phone" placeholder="Phone" required value="<?= $editMode ? htmlspecialchars($editData['phone']) : '' ?>">
            <input type="text" name="level" placeholder="Level" required value="<?= $editMode ? htmlspecialchars($editData['level']) : '' ?>">

            <label for="halaqa_id">اختيار الحلقة:</label>
            <select name="halaqa_id" id="halaqa_id" required>
                <option value="">-- اختر الحلقة --</option>
                <?php
                // استعلام جلب الحلقات لترتيبها وعرضها في القائمة المنسدلة
                $halaqat = $conn->query("SELECT id, name FROM halaqat ORDER BY name ASC");
                while ($halaqaRow = $halaqat->fetch_assoc()):
                ?>
                    <option value="<?= $halaqaRow['id'] ?>" <?= ($editMode && $editData['halaqa_id'] == $halaqaRow['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($halaqaRow['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- زر الإرسال -->
            <button type="submit"><?= $editMode ? 'Update' : 'Add' ?> Student</button>
        </form>
    </div>

    <div class="card">
        <h2>All Students</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Level</th>
                <th>Halaqa</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($students)): ?>
                <tr>
                    <!-- عرض بيانات كل طالب -->
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['level']) ?></td>
                    <td><?= htmlspecialchars($row['halaqa_name'] ?? 'غير محددة') ?></td>
                    <td>
                        <!-- رابط التعديل يوجه إلى نفس الصفحة مع معرف الطالب -->
                        <a class="edit" href="?edit=<?= $row['id'] ?>">Edit</a>
                        <!-- رابط الحذف مع تأكيد الحذف -->
                        <a class="delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this student?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
