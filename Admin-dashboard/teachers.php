<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "quran_circle");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// إضافة معلم
if (isset($_POST['add_teacher'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $conn->query("INSERT INTO teachers (name, phone, email) VALUES ('$name', '$phone', '$email')");
    header("Location: teachers.php");
    exit();
}

// تعديل معلم
if (isset($_POST['edit_teacher'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $conn->query("UPDATE teachers SET name='$name', phone='$phone', email='$email' WHERE id=$id");
    header("Location: teachers.php");
    exit();
}

// حذف معلم
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM teachers WHERE id=$id");
    header("Location: teachers.php");
    exit();
}

// جلب كل المعلمين
$result = $conn->query("SELECT * FROM teachers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teachers - Quran Circle Nexus</title>
    <style>
        body { font-family: sans-serif; margin: 0; background: #f7f7f7; }
        .sidebar { width: 220px; background: #0f172a; height: 100vh; position: fixed; color: white; padding: 20px; }
        .sidebar h2 { color: white; margin-bottom: 30px; }
        .sidebar a { color: white; display: block; margin: 15px 0; text-decoration: none; }
        .main { margin-left: 240px; padding: 30px; }
        table { width: 100%; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .actions a { margin-right: 10px; text-decoration: none; }
        .btn { background: #10b981; color: white; padding: 8px 14px; border: none; border-radius: 5px; cursor: pointer; }
        .form { margin-bottom: 20px; background: white; padding: 20px; border-radius: 8px; }
        input[type="text"], input[type="email"] { padding: 8px; width: 25%; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px; }
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
        <h2>Teachers</h2>

        <!-- نموذج الإضافة أو التعديل -->
        <form method="POST" class="form">
            <?php if (isset($_GET['edit'])): 
                $edit_id = $_GET['edit'];
                $edit_data = $conn->query("SELECT * FROM teachers WHERE id=$edit_id")->fetch_assoc();
            ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <input type="text" name="name" value="<?= $edit_data['name'] ?>" required>
                <input type="text" name="phone" value="<?= $edit_data['phone'] ?>" required>
                <input type="email" name="email" value="<?= $edit_data['email'] ?>" required>
                <button class="btn" type="submit" name="edit_teacher">Update</button>
            <?php else: ?>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="email" name="email" placeholder="Email" required>
                <button class="btn" type="submit" name="add_teacher">Add Teacher</button>
            <?php endif; ?>
        </form>

        <!-- جدول المعلمين -->
        <table>
            <thead>
                <tr>
                    <th>Name</th><th>Phone</th><th>Email</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td class="actions">
                        <a href="?edit=<?= $row['id'] ?>">✏️</a>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this teacher?')">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>