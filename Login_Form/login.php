<?php
// بدء جلسة عمل جديدة أو استئناف الجلسة الحالية
session_start();

// استدعاء رسالة الخطأ المخزنة في الجلسة إذا كانت موجودة، أو تعيينها إلى سلسلة فارغة إذا لم توجد
$error = $_SESSION['error'] ?? '';

// حذف رسالة الخطأ من الجلسة بعد استدعائها لتجنب ظهورها مرة أخرى عند إعادة تحميل الصفحة
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <style>
    /* تنسيق عام لجسم الصفحة */
    body {
      font-family: Arial, sans-serif; /* نوع الخط */
      background-color: #f2f2f2;      /* لون الخلفية */
      display: flex;                  /* استخدام Flexbox لتوسيط المحتوى */
      justify-content: center;        /* محاذاة أفقية في المنتصف */
      align-items: center;            /* محاذاة رأسية في المنتصف */
      height: 100vh;                  /* ارتفاع الصفحة يملأ كامل شاشة العرض */
    }
    /* صندوق تسجيل الدخول */
    .login-container {
      background: #fff;               /* خلفية بيضاء للصندوق */
      padding: 2rem;                 /* حشوة داخلية */
      border-radius: 10px;            /* زوايا مدورة */
      box-shadow: 0 0 10px #ccc;     /* ظل خفيف حول الصندوق */
      max-width: 400px;              /* أقصى عرض للصندوق */
      width: 100%;                   /* عرض الصندوق 100% من الحاوية */
    }
    /* عنوان الصفحة */
    h2 {
      text-align: center;            /* محاذاة النص إلى الوسط */
      margin-bottom: 1rem;           /* مسافة أسفل العنوان */
    }
    /* تنسيق حقول الإدخال */
    input {
      width: 100%;                  /* عرض الحقل 100% من الصندوق */
      padding: 0.6rem;              /* حشوة داخلية للحقل */
      margin: 0.5rem 0;             /* هامش عمودي بين الحقول */
      border: 1px solid #ccc;       /* حد رمادي */
      border-radius: 5px;           /* زوايا مدورة للحقل */
    }
    /* تنسيق رسالة الخطأ */
    .error {
      color: red;                   /* لون الخط أحمر */
      font-size: 0.9rem;            /* حجم الخط أصغر قليلاً */
      margin: 0.3rem 0;             /* هامش عمودي */
    }
    /* تنسيق زر الدخول */
    button {
      width: 100%;                  /* عرض الزر كامل عرض الحاوية */
      padding: 0.7rem;              /* حشوة داخلية */
      background-color: #2ca89a;   /* لون الخلفية أخضر مائل للأزرق */
      color: white;                 /* لون النص أبيض */
      border: none;                 /* إزالة الحدود الافتراضية */
      border-radius: 5px;           /* زوايا مدورة */
      margin-top: 1rem;             /* هامش علوي */
      cursor: pointer;              /* تغيير مؤشر الماوس عند المرور فوق الزر */
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <!-- نموذج تسجيل الدخول -->
    <form action="login_process.php" method="POST">
      <!-- حقل اسم المستخدم أو البريد الإلكتروني -->
      <input type="text" name="username" placeholder="Username or Email" required />
      <!-- حقل كلمة المرور -->
      <input type="password" name="password" placeholder="Password" required />
      
      <?php if ($error): ?>
        <!-- عرض رسالة الخطأ إذا كانت موجودة -->
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <!-- زر تسجيل الدخول -->
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
