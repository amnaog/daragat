<?php
$conn = new mysqli("localhost", "root", "", "quran_circle");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// إضافة طالب
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $halaqah_id = $_POST['halaqah_id'];
    $conn->query("INSERT INTO students (name, phone, halaqah_id) VALUES ('$name', '$phone', '$halaqah_id')");
    header("Location: students.php");
    exit();
}

// تعديل طالب
if (isset($_POST['edit_student'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $halaqah_id = $_POST['halaqah_id'];
    $conn->query("UPDATE students SET name='$name', phone='$phone', halaqah_id='$halaqah_id' WHERE id=$id");
    header("Location: students.php");
    exit();
}

// حذف طالب
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id");
    header("Location: students.php");
    exit();
}

// جلب الطلاب مع اسم الحلقة
$result = $conn->query("SELECT students.*, halaqat.name AS halaqah FROM students LEFT JOIN halaqat ON students.halaqah_id = halaqat.id");

// جلب كل الحلقات للاختيار
$halaqat = $conn->query("SELECT * FROM halaqat");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students - Quran Circle Nexus</title>
    <style>
        body { font-family: sans-serif; margin: 0; background: #f9f9f9; }
        .sidebar { width: 220px; background: #0f172a; height: 100vh; position: fixed; color: white; padding: 20px; }
        .sidebar h2 { color: white; margin-bottom: 30px; }
        .sidebar a { color: white; display: block; margin: 15px 0; text-decoration: none; }
        .main { margin-left: 240px; padding: 30px; }
        table { width: 100%; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .actions a { margin-right: 10px; text-decoration: none; }
        .btn { background: #10b981; color: white; padding: 8px 14px; border: none; border-radius: 5px; cursor: pointer; }
        .form { margin-bottom: 20px; background: white; padding: 20px; border-radius: 8px; }
        input[type="text"], select { padding: 8px; width: 20%; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px; }
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
        <h2>Students</h2>

        <!-- نموذج الإضافة أو التعديل -->
        <form method="POST" class="form">
            <?php if (isset($_GET['edit'])): 
                $id = $_GET['edit'];
                $edit = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
            ?>
                <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <input type="text" name="name" value="<?= $edit['name'] ?>" required>
                <input type="text" name="phone" value="<?= $edit['phone'] ?>" required>
                <select name="halaqah_id" required>
                    <?php while ($h = $halaqat->fetch_assoc()): ?>
                        <option value="<?= $h['id'] ?>" <?= $h['id'] == $edit['halaqah_id'] ? 'selected' : '' ?>><?= $h['name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button class="btn" name="edit_student">Update</button>
            <?php else: ?>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <select name="halaqah_id" required>
                    <option value="">Select Halaqah</option>
                    <?php $halaqat->data_seek(0); while ($h = $halaqat->fetch_assoc()): ?>
                        <option value="<?= $h['id'] ?>"><?= $h['name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button class="btn" name="add_student">Add Student</button>
            <?php endif; ?>
        </form>

        <!-- جدول الطلاب -->
        <table>
            <thead>
                <tr><th>Name</th><th>Halaqah</th><th>Phone</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['halaqah'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td class="actions">
                        <a href="?edit=<?= $row['id'] ?>">⚙️</a>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete student?')">❌</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
