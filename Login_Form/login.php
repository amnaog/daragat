<?php
session_start();

$email = $password = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_msg = "Please enter both email and password.";
    } else {
        $conn = new mysqli("localhost", "root", "", "darajat");
        if ($conn->connect_error) {
            $error_msg = "Database connection failed.";
        } else {
            $stmt = $conn->prepare("SELECT password, role FROM users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($hashed_password, $role);
                    $stmt->fetch();

                    // تحقق من كلمة المرور باستخدام password_verify
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_role'] = $role;
                        $_SESSION['logged_in'] = true;

                        $redirects = [
                            'admin' => '../Admin-dashboard/dashboard.php',
                            'teacher' => 'teacher-dashboard.php',
                            'student' => 'student-dashboard.php',
                        ];

                        $redirect_url = $redirects[$role] ?? 'default-dashboard.php';

                        echo "<script>
                            alert('✅ Welcome!');
                            window.location.href = '$redirect_url';
                        </script>";
                        exit;
                    } else {
                        $error_msg = "Incorrect password.";
                    }
                } else {
                    $error_msg = "Email not found.";
                }
                $stmt->close();
            } else {
                $error_msg = "Database query error.";
            }
            $conn->close();
        }
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
      background: #f2f2f2;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .form-box {
      background: white;
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
    label {
      display: block;
      margin-top: 1rem;
      font-weight: bold;
    }
    input, button {
      width: 100%;
      padding: 0.6rem;
      margin-top: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    button {
      margin-top: 1.5rem;
      background: #2563eb;
      color: white;
      border: none;
      font-size: 1rem;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="form-box">
  <h2>Login</h2>

  <?php
  if (!empty($error_msg)) {
    echo "<script>alert('❌ " . addslashes($error_msg) . "');</script>";
  }
  ?>

  <form method="post" action="">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required />

    <label>Password</label>
    <input type="password" name="password" required />

    <button type="submit">Login</button>
  </form>
</div>

</body>
</html>
