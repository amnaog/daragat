<?php
include 'db.php';

// Handle Add
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    mysqli_query($conn, "INSERT INTO teachers (full_name, email, phone) VALUES ('$name', '$email', '$phone')");
    header("Location: teachers.php");
    exit();
}

// Handle Update
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    mysqli_query($conn, "UPDATE teachers SET full_name='$name', email='$email', phone='$phone' WHERE id=$id");
    header("Location: teachers.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM teachers WHERE id=$id");
    header("Location: teachers.php");
    exit();
}

// Fetch teachers
$teachers = mysqli_query($conn, "SELECT * FROM teachers");

// Edit mode
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editResult = mysqli_query($conn, "SELECT * FROM teachers WHERE id=$editId");
    $editData = mysqli_fetch_assoc($editResult);
    $editMode = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Teachers</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f9f9f9;
        }
        .sidebar {
            background: #0f172a;
            color: white;
            width: 220px;
            height: 100vh;
            position: fixed;
            padding: 20px 10px;
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .sidebar a.active, .sidebar a:hover {
            background-color: #22c55e;
        }
        .main {
            margin-left: 240px;
            padding: 30px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        h2 { margin-top: 0; }
        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #22c55e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        a.edit, a.delete {
            margin-right: 10px;
            color: #22c55e;
            text-decoration: none;
        }
        a.delete { color: red; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a class="active" href="teachers.php">Teachers</a>
    <a href="students.php">Students</a>
    <a href="halaqat.php">Halaqat</a>
    <a href="reports.php">Reports</a>
    <a href="notifications.php">Notifications</a>
    <a href="roles.php">Roles</a>
</div>

<div class="main">
    <div class="card">
        <h2><?php echo $editMode ? 'Edit' : 'Add'; ?> Teacher</h2>
        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $editMode ? 'update' : 'add'; ?>">
            <?php if ($editMode): ?>
                <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
            <?php endif; ?>
            <input type="text" name="full_name" placeholder="Full Name" required value="<?php echo $editMode ? $editData['full_name'] : ''; ?>">
            <input type="email" name="email" placeholder="Email" required value="<?php echo $editMode ? $editData['email'] : ''; ?>">
            <input type="text" name="phone" placeholder="Phone" required value="<?php echo $editMode ? $editData['phone'] : ''; ?>">
            <button type="submit"><?php echo $editMode ? 'Update' : 'Add'; ?> Teacher</button>
        </form>
    </div>

    <div class="card">
        <h2>All Teachers</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($teachers)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td>
                        <a class="edit" href="?edit=<?php echo $row['id']; ?>">Edit</a>
                        <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this teacher?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
