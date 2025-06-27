<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$halaqa_id = isset($_GET['halaqa_id']) ? intval($_GET['halaqa_id']) : 0;
$halaqa = [];

if ($halaqa_id) {
    $result = $conn->query("SELECT * FROM halaqat WHERE id = $halaqa_id");
    if ($result->num_rows > 0) {
        $halaqa = $result->fetch_assoc();
    } else {
        die("Halaqa not found");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $schedule = $_POST['schedule'];

    $stmt = $conn->prepare("UPDATE halaqat SET name = ?, schedule = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $schedule, $halaqa_id);
    if ($stmt->execute()) {
        header("Location: index.php?halaqa_id=$halaqa_id&updated=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Halaqa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="sidebar">
    <div>
        <div class="logo">QuranFlow</div>
        <ul class="menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="progress.php">Progress</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
    </div>
    <div class="user-info">
        <div class="avatar"></div>
        <div>Sheikh Abdullah</div>
        <div style="font-size: 12px;">sheikh.abdullah@quran.com</div>
    </div>
</div>

<div class="main">
    <div class="edit-form-container">
        <h2>Edit Halaqa</h2>
        <p>Update information for this Halaqa.</p>

        <form method="POST">
            <label for="name">Halaqa Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($halaqa['name']) ?>" required>

            <label for="schedule">Schedule:</label>
            <input type="text" id="schedule" name="schedule" value="<?= htmlspecialchars($halaqa['schedule']) ?>" required>

       <div class="button-group">
  <button type="submit" class="save-button">💾 Save Changes</button>
  <a href="index.php" class="cancel-button">Cancel</a>
</div>
        </form>
    </div>
</div>
</body>
</html>