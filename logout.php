<?php
session_start();        // بداية الجلسة
session_unset();        // إزالة كل المتغيرات
session_destroy();      // تدمير الجلسة بالكامل

header("Location: login.php");  // إعادة التوجيه لصفحة تسجيل الدخول
exit();