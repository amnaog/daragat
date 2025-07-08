<?php
// تعريف متغيرات لتخزين القيم المدخلة ورسائل الخطأ
$full_name = $email = $phone = $level = "";
$error_msg = "";

// التحقق من أن الصفحة استقبلت طلب إرسال بيانات عبر POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات من النموذج مع إزالة الفراغات الزائدة من البداية والنهاية
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $level = $_POST['level'] ?? '';

    // التحقق من أن جميع الحقول المطلوبة ممتلئة
    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($level)) {
        $error_msg = "Please fill all the required fields."; // رسالة خطأ عند وجود حقل فارغ
    }
    // التحقق من صحة تنسيق البريد الإلكتروني
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format."; // رسالة خطأ عند تنسيق بريد خاطئ
    } else {
        // إنشاء اتصال بقاعدة البيانات
        $conn = new mysqli("localhost", "root", "", "darajat");

        // التحقق من نجاح الاتصال
        if ($conn->connect_error) {
            $error_msg = "Database connection failed."; // رسالة خطأ عند فشل الاتصال
        } else {
            // تحضير استعلام للتحقق من وجود البريد الإلكتروني مسبقاً في جدول طلبات الطلاب
            $checkStmt = $conn->prepare("SELECT id FROM student_requests WHERE email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();

            // إذا كان البريد موجوداً مسبقاً
            if ($checkStmt->num_rows > 0) {
                $error_msg = "This email is already registered."; // رسالة خطأ لتجنب التسجيل المكرر
            } else {
                // تشفير كلمة المرور باستخدام دالة آمنة
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // تحضير استعلام إدخال بيانات الطالب في جدول student_requests
                $stmt = $conn->prepare("INSERT INTO student_requests (full_name, email, phone, password, level) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $full_name, $email, $phone, $hashed_password, $level);

                // تنفيذ الاستعلام والتحقق من نجاحه
                if ($stmt->execute()) {
                    // في حال نجاح التسجيل، عرض رسالة نجاح وتحويل المستخدم لصفحة أخرى
                    echo "<script>
                        alert('✅ Your registration was successful. Our supervisor will contact you soon.');
                        window.location.href = '../Index.html';
                    </script>";
                    exit; // إيقاف تنفيذ باقي الكود بعد إعادة التوجيه
                } else {
                    // تخزين رسالة الخطأ في حال فشل التنفيذ
                    $error_msg = "Error: " . $stmt->error;
                }
                // إغلاق البيان
                $stmt->close();
            }
            // إغلاق بيان التحقق وقاعدة البيانات
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
    /* تنسيق عام لجسم الصفحة */
    body {
      font-family: Arial, sans-serif; /* نوع الخط */
      background: #ffffff;             /* خلفية بيضاء */
      display: flex;                   /* استخدام Flexbox لمحاذاة المحتوى */
      justify-content: center;          /* محاذاة أفقية للوسط */
      align-items: center;              /* محاذاة رأسية للوسط */
      padding: 2rem;                   /* حشوة داخلية */
      min-height: 100vh;               /* ارتفاع الصفحة كامل الشاشة */
    }
    /* صندوق النموذج */
    .form-box {
      background: white;               /* خلفية بيضاء */
      padding: 2rem;                  /* حشوة داخلية */
      border-radius: 10px;             /* زوايا مدورة */
      box-shadow: 0 0 10px #ccc;      /* ظل خفيف */
      max-width: 500px;               /* أقصى عرض للنموذج */
      width: 100%;                   /* عرض كامل الحاوية */
    }
    /* عنوان النموذج */
    h2 {
      text-align: center;             /* محاذاة النص للوسط */
      margin-bottom: 1rem;            /* مسافة أسفل العنوان */
    }
    /* تنسيق العناوين داخل النموذج */
    label {
      display: block;                 /* عرض كل عنوان في سطر منفصل */
      margin-top: 1rem;               /* مسافة أعلى كل عنوان */
      font-weight: bold;              /* تكثيف الخط */
    }
    /* تنسيق الحقول والقوائم والأزرار */
    input, select, button {
      width: 100%;                   /* عرض كامل الحاوية */
      padding: 0.6rem;               /* حشوة داخلية */
      margin-top: 0.5rem;            /* مسافة فوق كل عنصر */
      border: 1px solid #ccc;        /* حدود رمادية */
      border-radius: 5px;            /* زوايا مدورة */
      box-sizing: border-box;        /* تضمين الحشوة والحدود في الحساب */
    }
    /* تنسيق زر الإرسال */
    button {
      margin-top: 1.5rem;            /* مسافة أعلى أكبر */
      background: #16a34a;           /* خلفية خضراء */
      color: white;                  /* نص أبيض */
      border: none;                  /* إزالة الحدود */
      font-size: 1rem;               /* حجم الخط */
      cursor: pointer;               /* تغيير المؤشر عند المرور */
    }
  </style>
</head>

<body>

  <div class="form-box">
    <h2>Student Registration</h2>

    <?php
    // في حال وجود رسالة خطأ، عرضها باستخدام نافذة تنبيه جافاسكريبت
    if (!empty($error_msg)) {
      echo "<script>alert('❌ " . addslashes($error_msg) . "');</script>";
    }
    ?>

    <!-- نموذج التسجيل -->
    <form method="post" action="">
      <label>Full Name</label>
      <!-- حقل الاسم مع تعبئة القيمة السابقة إذا وجدت -->
      <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required />

      <label>Email</label>
      <!-- حقل البريد الإلكتروني مع تعبئة القيمة السابقة -->
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required />

      <label>Phone</label>
      <!-- حقل الهاتف مع تعبئة القيمة السابقة -->
      <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required />

      <label>Password</label>
      <!-- حقل كلمة المرور -->
      <input type="password" name="password" required />

      <label>Memorization Level</label>
      <!-- قائمة اختيار مستوى الحفظ -->
      <select name="level" required>
        <option value="">-- Select --</option>
        <?php
        // مصفوفة المستويات المتاحة للحفظ
        $levels = [
            "New to memorization", "Juz 1", "Juz 2", "Juz 3", "Juz 4", "Juz 5", "Juz 6",
            "Juz 7", "Juz 8", "Juz 9", "Juz 10", "Juz 11", "Juz 12", "Juz 13", "Juz 14",
            "Juz 15", "Juz 16", "Juz 17", "Juz 18", "Juz 19", "Juz 20", "Juz 21", "Juz 22",
            "Juz 23", "Juz 24", "Juz 25", "Juz 26", "Juz 27", "Juz 28", "Juz 29", "Juz 30"
        ];
        // توليد خيارات القائمة مع تحديد الخيار المحدد مسبقًا
        foreach ($levels as $lvl) {
            $selected = ($lvl === $level) ? "selected" : "";
            echo "<option value=\"" . htmlspecialchars($lvl) . "\" $selected>$lvl</option>";
        }
        ?>
      </select>

      <!-- زر الإرسال -->
      <button type="submit">Register</button>
    </form>
  </div>

</body>
</html>
