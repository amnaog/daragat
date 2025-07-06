<?php
$full_name = $email = $phone = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // تحقق الحقول المطلوبة
    if (empty($full_name) || empty($email) || empty($phone) ) {
        $error_msg = "Please fill all the required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format.";
    } elseif (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "Please upload a valid certificate file.";
    } else {
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($_FILES['certificate']['type'], $allowed_types)) {
            $error_msg = "Only PDF, JPG or PNG files are allowed.";
        } else {
            $uploads_dir = __DIR__ . '/uploads/certificates';
            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir, 0755, true);
            }

            $tmp_name = $_FILES['certificate']['tmp_name'];
            $original_name = basename($_FILES['certificate']['name']);
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $new_filename = uniqid('cert_') . '.' . $ext;
            $destination = $uploads_dir . '/' . $new_filename;

            if (move_uploaded_file($tmp_name, $destination)) {
                $conn = new mysqli("localhost", "root", "", "darajat");
                if ($conn->connect_error) {
                    $error_msg = "Database connection failed.";
                    unlink($destination); // حذف الملف لأن DB فشلت
                } else {
                    $stmt = $conn->prepare("INSERT INTO teachers (full_name, email, phone, certificate_path) VALUES (?, ?, ?, ?)");
                    $relative_path = 'uploads/certificates/' . $new_filename;
                    $stmt->bind_param("ssss", $full_name, $email, $phone, $relative_path);

                    if ($stmt->execute()) {
                        echo "<script>
                            alert('✅ Registration successful! Your certificate was uploaded.');
                            window.location.href = '../Index.html';
                        </script>";
                        exit;
                    } else {
                        $error_msg = "Database error: " . $stmt->error;
                        unlink($destination);
                    }
                    $stmt->close();
                    $conn->close();
                }
            } else {
                $error_msg = "Failed to upload certificate file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Teacher Registration</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #fff;
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
  <h2>Teacher Registration</h2>

  <?php
  if (!empty($error_msg)) {
      echo "<script>alert('❌ " . addslashes($error_msg) . "');</script>";
  }
  ?>

  <form method="post" enctype="multipart/form-data">
    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required />

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required />

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required />

    <label>Upload Quran Certificate (PDF, JPG, PNG)</label>
    <input type="file" name="certificate" accept=".pdf, .jpg, .jpeg, .png" required />

    <button type="submit">Register</button>
  </form>
</div>

</body>
</html>
