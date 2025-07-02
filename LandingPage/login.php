
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "darajat");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("
    SELECT users.id, users.username, users.password, roles.name AS role
    FROM users
    LEFT JOIN roles ON users.role_id = roles.id
    WHERE users.username = ? OR users.email = ?
");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
if ($password === $row['password'])
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        switch ($row['role']) {
            case 'admin':
            header("Location: http://localhost/daragat/Admin-dashboard/dashboard.php");
                    break;

            case 'teacher':
                header("Location: dashboard_teacher.php");
                 break;
            case 'student':
                header("Location: dashboard_student.php"); 
                break;
            default:
                header("Location: index.php?error=role"); 
                break;
        }
        exit();
    }

header("Location: index.php?error=1");
exit();

?>

