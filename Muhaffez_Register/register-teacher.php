<?php
// تعريف متغيرات لتخزين بيانات النموذج ورسائل الخطأ
$full_name = $email = $phone = "";
$error_msg = "";

// التحقق من أن الطلب هو من نوع POST (تم إرسال النموذج)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // قراءة بيانات الحقول مع حذف الفراغات من البداية والنهاية
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // التحقق من ملء الحقول المطلوبة
    if (empty($full_name) || empty($email) || empty($phone) ) {
        $error_msg = "Please fill all the required fields."; // رسالة خطأ عند وجود حقل فارغ
    }
    // التحقق من صحة تنسيق البريد الإلكتروني
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format."; // رسالة خطأ عند تنسيق بريد غير صحيح
    }
    // التحقق من وجود ملف شهادة مرفوع وأنه تم رفعه بنجاح
    elseif (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "Please upload a valid certificate file."; // رسالة خطأ عند عدم رفع ملف أو وجود خطأ في الرفع
    } else {
        // تحديد أنواع الملفات المسموح بها للشهادة (PDF، JPG، PNG)
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];

        // التحقق من أن نوع الملف المرفوع من الأنواع المسموح بها
        if (!in_array($_FILES['certificate']['type'], $allowed_types)) {
            $error_msg = "Only PDF, JPG or PNG files are allowed."; // رسالة خطأ عند نوع ملف غير مسموح
        } else {
            // تحديد مجلد حفظ الملفات، إذا لم يكن موجودًا يتم إنشاؤه مع صلاحيات 755
            $uploads_dir = __DIR__ . '/uploads/certificates';
            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir, 0755, true);
            }

            // مسار الملف المؤقت المرفوع
            $tmp_name = $_FILES['certificate']['tmp_name'];
            // اسم الملف الأصلي مع امتداده
            $original_name = basename($_FILES['certificate']['name']);
            // استخراج امتداد الملف فقط
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            // إنشاء اسم جديد فريد للملف لتجنب التعارض
            $new_filename = uniqid('cert_') . '.' . $ext;
            // المسار الكامل للوجهة التي سيُحفظ فيها الملف
            $destination = $uploads_dir . '/' . $new_filename;

            // محاولة نقل الملف من المجلد المؤقت إلى مجلد التخزين النهائي
            if (move_uploaded_file($tmp_name, $destination)) {
                // إنشاء اتصال بقاعدة البيانات MySQL
                $conn = new mysqli("localhost", "root", "", "darajat");
                // التحقق من نجاح الاتصال
                if ($conn->connect_error) {
                    $error_msg = "Database connection failed."; // رسالة خطأ عند فشل الاتصال بقاعدة البيانات
                    unlink($destination); // حذف الملف المرفوع لأنه لن يتم تخزين بياناته
                } else {
                    // تحضير استعلام الإدخال في جدول طلبات المعلمين
                    $stmt = $conn->prepare("INSERT INTO teacher_requests (full_name, email, phone, certificate_path) VALUES (?, ?, ?, ?)");

                    // المسار النسبي للملف الذي سيتم تخزينه في قاعدة البيانات
                    $relative_path = 'uploads/certificates/' . $new_filename;
                    // ربط المتغيرات مع الاستعلام
                    $stmt->bind_param("ssss", $full_name, $email, $phone, $relative_path);

                    // تنفيذ الاستعلام
                    if ($stmt->execute()) {
                        // إذا نجح الإدخال، إظهار رسالة نجاح وتحويل المستخدم لصفحة أخرى
                        echo "<script>
                            alert('✅ Registration successful! Your certificate was uploaded.');
                            window.location.href = '../Index.html';
                        </script>";
                        exit; // إيقاف تنفيذ باقي الكود بعد إعادة التوجيه
                    } else {
                        // في حالة وجود خطأ في قاعدة البيانات، تخزين رسالة الخطأ وحذف الملف
                        $error_msg = "Database error: " . $stmt->error;
                        unlink($destination);
                    }
                    // إغلاق البيان وقاعدة البيانات
                    $stmt->close();
                    $conn->close();
                }
            } else {
                // رسالة خطأ في حال فشل رفع الملف إلى المجلد النهائي
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
  /* تنسيق عام لجسم الصفحة */
  body {
    font-family: Arial, sans-serif;   /* نوع الخط */
    background: #fff;                 /* خلفية بيضاء */
    display: flex;                   /* استخدام Flexbox لمحاذاة المحتوى */
    justify-content: center;          /* محاذاة أفقية للمنتصف */
    align-items: center;              /* محاذاة رأسية للمنتصف */
    padding: 2rem;                   /* حشوة داخلية حول المحتوى */
    min-height: 100vh;               /* الارتفاع الكامل للشاشة */
  }
  /* صندوق النموذج */
  .form-box {
    background: white;               /* خلفية بيضاء للنموذج */
    padding: 2rem;                  /* حشوة داخلية */
    border-radius: 10px;             /* زوايا مدورة */
    box-shadow: 0 0 10px #ccc;      /* ظل خفيف */
    max-width: 500px;               /* أقصى عرض للنموذج */
    width: 100%;                   /* العرض 100% */
  }
  /* عنوان النموذج */
  h2 {
    text-align: center;             /* محاذاة النص للوسط */
    margin-bottom: 1rem;            /* مسافة أسفل العنوان */
  }
  /* تنسيق العناوين داخل النموذج */
  label {
    display: block;                 /* عرض كل عنوان كتلة منفصلة */
    margin-top: 1rem;               /* مسافة أعلى كل عنوان */
    font-weight: bold;              /* تكثيف الخط */
  }
  /* تنسيق حقول الإدخال والقوائم والأزرار */
  input, select, button {
    width: 100%;                   /* عرض كامل الحاوية */
    padding: 0.6rem;               /* حشوة داخلية */
    margin-top: 0.5rem;            /* مسافة فوق كل عنصر */
    border: 1px solid #ccc;        /* حدود رمادية */
    border-radius: 5px;            /* زوايا مدورة */
    box-sizing: border-box;        /* تضمين الحشوة والحدود في الحجم */
  }
  /* تنسيق زر الإرسال */
  button {
    margin-top: 1.5rem;            /* مسافة أعلى أكبر */
    background: #16a34a;           /* لون أخضر */
    color: white;                  /* لون النص أبيض */
    border: none;                  /* إزالة الحدود */
    font-size: 1rem;               /* حجم الخط */
    cursor: pointer;               /* تغيير شكل المؤشر عند المرور */
  }
</style>
</head>
<body>

<div class="form-box">
  <h2>Teacher Registration</h2>

  <?php
  // إذا وُجدت رسالة خطأ، يتم عرضها من خلال نافذة تنبيه جافاسكريبت
  if (!empty($error_msg)) {
      echo "<script>alert('❌ " . addslashes($error_msg) . "');</script>";
  }
  ?>

  <!-- نموذج التسجيل مع استقبال الملفات -->
  <form method="post" enctype="multipart/form-data">
    <label>Full Name</label>
    <!-- حقل الاسم الكامل مع تعبئة مسبقة إذا كانت هناك بيانات -->
    <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required />

    <label>Email</label>
    <!-- حقل البريد الإلكتروني مع تعبئة مسبقة -->
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required />

    <label>Phone</label>
    <!-- حقل رقم الهاتف مع تعبئة مسبقة -->
    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required />

    <label>Upload Quran Certificate (PDF, JPG, PNG)</label>
    <!-- حقل رفع الملف مع تحديد أنواع الملفات المسموح بها -->
    <input type="file" name="certificate" accept=".pdf, .jpg, .jpeg, .png" required />

    <!-- زر الإرسال -->
    <button type="submit">Register</button>
  </form>
</div>

</body>
</html>
