<?php
// تعريف متغيرات للحفاظ على القيم
$full_name = $email = $phone = $level = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استلام البيانات مع حماية بسيطة
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $level = $_POST['level'] ?? '';

    // التحقق من الحقول (مثال بسيط)
    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($level)) {
        $error_msg = "Please fill all the required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format.";
    } else {
        // فتح اتصال بقاعدة البيانات
        $conn = new mysqli("localhost", "root", "", "darajat");
        if ($conn->connect_error) {
            $error_msg = "Database connection failed.";
        } else {
            // تحقق إذا كان الإيميل موجود مسبقاً (تجنب التكرار)
            $checkStmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows > 0) {
                $error_msg = "This email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO students (full_name, email, phone, password, level) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $full_name, $email, $phone, $hashed_password, $level);
                if ($stmt->execute()) {
                    // التسجيل ناجح -> إعادة التوجيه مع رسالة ناجحة (يمكن تعديل الرابط)
                    echo "<script>
                        alert('✅ Your registration was successful. Our supervisor will contact you soon.');
                        window.location.href = '../Index.html';
                    </script>";
                    exit;
                } else {
                    $error_msg = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
            $checkStmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Registration</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #ffffff;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
      min-height: 100vh;
    }
    .form-box {
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
      max-width: 500px;
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
    input, select, button {
      width: 100%;
      padding: 0.6rem;
      margin-top: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    button {
      margin-top: 1.5rem;
      background: #16a34a;
      color: white;
      border: none;
      font-size: 1rem;
      cursor: pointer;
    }
  </style>
</head>

<body>

  <div class="form-box">
    <h2>Student Registration</h2>

    <?php
    // لو في خطأ نعرضه alert و نبقى في نفس الصفحة مع إبقاء القيم
    if (!empty($error_msg)) {
      echo "<script>alert('❌ " . addslashes($error_msg) . "');</script>";
    }
    ?>

    <form method="post" action="">
      <label>Full Name</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required />

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required />

      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required />

      <label>Password</label>
      <input type="password" name="password" required />

      <label>Memorization Level</label>
      <select name="level" required>
        <option value="">-- Select --</option>
        <?php
        $levels = [
            "New to memorization", "Juz 1", "Juz 2", "Juz 3", "Juz 4", "Juz 5", "Juz 6",
            "Juz 7", "Juz 8", "Juz 9", "Juz 10", "Juz 11", "Juz 12", "Juz 13", "Juz 14",
            "Juz 15", "Juz 16", "Juz 17", "Juz 18", "Juz 19", "Juz 20", "Juz 21", "Juz 22",
            "Juz 23", "Juz 24", "Juz 25", "Juz 26", "Juz 27", "Juz 28", "Juz 29", "Juz 30"
        ];
        foreach ($levels as $lvl) {
            $selected = ($lvl === $level) ? "selected" : "";
            echo "<option value=\"" . htmlspecialchars($lvl) . "\" $selected>$lvl</option>";
        }
        ?>
      </select>

      <button type="submit">Register</button>
    </form>
  </div>

</body>
</html>
