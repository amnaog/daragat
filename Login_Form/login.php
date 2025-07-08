<?php
session_start();
$conn = new mysqli("localhost", "root", "", "darajat");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// إذا المستخدم مسجل دخول فعلًا، أعد توجيهه
if (isset($_SESSION['role'])) {
    header("Location: index.php?error=1");
    exit();
}

$error = "";

// تنفيذ عملية تسجيل الدخول إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                        $error = "Teacher not found in teachers table.";
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
                        $error = "Student not found in students table.";
                    }
                    break;

                case 'admin':
                    header("Location: http://localhost/daragat/Admin-dashboard/dashboard.php");
                    exit;

                default:
                    $error = "Unknown user role.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
      max-width: 400px;
      width: 100%;
    }

    h2 {
      text-align: center;
      margin-bottom: 1rem;
    }

    input {
      width: 100%;
      padding: 0.6rem;
      margin: 0.5rem 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .error {
      color: red;
      font-size: 0.9rem;
      margin: 0.3rem 0;
    }

    .forgot {
      text-align: right;
      display: block;
      font-size: 0.9rem;
      margin-top: 0.3rem;
      color: #007BFF;
      text-decoration: none;
    }

    button {
      width: 100%;
      padding: 0.7rem;
      background-color: #2ca89a;
      color: white;
      border: none;
      border-radius: 5px;
      margin-top: 1rem;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <form action="" method="POST">
      <input type="text" name="username" placeholder="Username or Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
      <?php endif; ?>
      <a href="#" class="forgot">Forgot your password?</a>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
