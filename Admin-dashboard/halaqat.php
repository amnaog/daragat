<?php
$conn = new mysqli("localhost", "root", "", "quran_circle");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// إضافة حلقة
if (isset($_POST['add_circle'])) {
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];
    $schedule = $_POST['schedule'];
    $capacity = $_POST['capacity'];
    $conn->query("INSERT INTO halaqat (name, teacher_id, schedule, capacity) VALUES ('$name', '$teacher_id', '$schedule', $capacity)");
    header("Location: halaqat.php");
    exit();
}

// تعديل حلقة
if (isset($_POST['edit_circle'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];
    $schedule = $_POST['schedule'];
    $capacity = $_POST['capacity'];
    $conn->query("UPDATE halaqat SET name='$name', teacher_id='$teacher_id', schedule='$schedule', capacity=$capacity WHERE id=$id");
    header("Location: halaqat.php");
    exit();
}

// حذف حلقة
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM halaqat WHERE id=$id");
    header("Location: halaqat.php");
    exit();
}

// جلب كل الحلقات مع اسم المعلم
$result = $conn->query("SELECT halaqat.*, teachers.name AS teacher FROM halaqat LEFT JOIN teachers ON halaqat.teacher_id = teachers.id");

// جلب المعلمين
$teachers = $conn->query("SELECT * FROM teachers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Halaqat - Quran Circle Nexus</title>
    <style>
        body { font-family: sans-serif; margin: 0; background: #f8f8f8; }
        .sidebar { width: 220px; background: #0f172a; height: 100vh; position: fixed; color: white; padding: 20px; }
        .sidebar a { color: white; text-decoration: none; display: block; margin: 15px 0; }
        .main { margin-left: 240px; padding: 30px; }
        table { width: 100%; background: white; border-radius: 8px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; }
        .btn { background: #10b981; color: white; padding: 8px 14px; border: none; border-radius: 5px; cursor: pointer; }
        input, select { padding: 8px; margin-right: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .form { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
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
        <h2>Memorization Circles</h2>

        <form method="POST" class="form">
            <?php if (isset($_GET['edit'])): 
                $id = $_GET['edit'];
                $edit = $conn->query("SELECT * FROM halaqat WHERE id=$id")->fetch_assoc();
            ?>
                <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <input type="text" name="name" value="<?= $edit['name'] ?>" required>
                <select name="teacher_id" required>
                    <?php while ($t = $teachers->fetch_assoc()): ?>
                        <option value="<?= $t['id'] ?>" <?= $t['id'] == $edit['teacher_id'] ? 'selected' : '' ?>><?= $t['name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="schedule" value="<?= $edit['schedule'] ?>" required>
                <input type="number" name="capacity" value="<?= $edit['capacity'] ?>" required>
                <button class="btn" name="edit_circle">Update</button>
            <?php else: ?>
                <input type="text" name="name" placeholder="Circle Name" required>
                <select name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php $teachers->data_seek(0); while ($t = $teachers->fetch_assoc()): ?>
                        <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="schedule" placeholder="Schedule (e.g. Mon, Thu)" required>
                <input type="number" name="capacity" placeholder="Capacity" required>
                <button class="btn" name="add_circle">Add Circle</button>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr><th>Name</th><th>Teacher</th><th>Schedule</th><th>Capacity</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['teacher'] ?></td>
                    <td><?= $row['schedule'] ?></td>
                    <td><?= $row['capacity'] ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>">✏️</a>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this circle?')">❌</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
