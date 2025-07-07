<?php
session_start();
$conn = new mysqli("localhost", "root", "", "darajat");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// جلب بيانات المستخدم من جدول users مع الدور
$stmt = $conn->prepare("
    SELECT u.id, u.username, u.email, u.password, r.name AS role
    FROM users u
    JOIN roles r ON u.role_id = r.id
    WHERE u.username = ? OR u.email = ?
");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($password === $row['password']) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        $email = $row['email'];

        switch ($row['role']) {
            case 'teacher':
                $q = $conn->prepare("SELECT id FROM teachers WHERE email = ?");
                $q->bind_param("s", $email);
                $q->execute();
                $res = $q->get_result();
                if ($r = $res->fetch_assoc()) {
                    $_SESSION['teacher_id'] = $r['id'];
                   header("Location: http://localhost/daragat/mohaffez-dashboard/index.php");
                    exit;
                } else {
                    die("Teacher not found in teachers table");
                }
                break;

            case 'student':
                $q = $conn->prepare("SELECT id FROM students WHERE email = ?");
                $q->bind_param("s", $email);
                $q->execute();
                $res = $q->get_result();
                if ($r = $res->fetch_assoc()) {
                    $_SESSION['student_id'] = $r['id'];
                     header("Location: http://localhost/daragat/student-dashboard/index.php");
                    exit;
                } else {
                    die("Student not found in students table");
                }
                break;

            case 'admin':
                header("Location: http://localhost/daragat/Admin-dashboard/dashboard.php");
                exit;

            default:
                header("Location: index.php?error=invalid_role");
                exit;
        }

    } else {
        header("Location: index.php?error=wrong_password");
        exit;
    }
} else {
    header("Location: index.php?error=user_not_found");
    exit;
}
?>