<?php
$conn = new mysqli("localhost", "root", "", "quran_circle");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// إضافة دور جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['permissions'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $perms = $conn->real_escape_string($_POST['permissions']);
    $conn->query("INSERT INTO roles (name, permissions) VALUES ('$name', '$perms')");
    header("Location: roles.php");
    exit;
}

// حذف دور
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM roles WHERE id = $id");
    header("Location: roles.php");
    exit;
}

// جلب الأدوار
$result = $conn->query("SELECT * FROM roles");
$roles = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Roles Management</title>
    <style>
        body { margin: 0; font-family: sans-serif; background: #f9f9f9; }
        .sidebar { width: 220px; background: #0f172a; height: 100vh; position: fixed; color: white; padding: 20px; }
        .sidebar a { color: white; display: block; margin: 15px 0; text-decoration: none; }
        .main { margin-left: 240px; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .btn { background: #10b981; color: white; padding: 10px 16px; border: none; border-radius: 8px; cursor: pointer; }
        .actions button { border: none; background: transparent; cursor: pointer; margin-left: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .green { color: #10b981; font-weight: bold; }
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
        <h2>Role & Permission Management</h2>

        <form method="POST" style="margin-top: 20px;">
            <input type="text" name="name" placeholder="Role name" required style="padding: 8px; width: 200px;">
            <input type="text" name="permissions" placeholder="Permissions (comma-separated)" required style="padding: 8px; width: 400px;">
            <button type="submit" class="btn">+ Add Role</button>
        </form>

        <div class="card">
            <table>
                <tr>
                    <th>ROLE</th>
                    <th>PERMISSIONS</th>
                    <th>ACTIONS</th>
                </tr>
                <?php foreach ($roles as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td>
                            <?= $r['permissions'] === 'all' ? '<span class="green">All Permissions</span>' : htmlspecialchars($r['permissions']) ?>
                        </td>
                        <td class="actions">
                            <a href="#"><img src="edit-icon.png" width="18" /></a>
                            <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete this role?')">
                                <img src="delete-icon.png" width="18" />
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
