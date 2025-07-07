<?php
// بدء الجلسة
session_start();

// حذف كل المتغيرات الموجودة في الجلسة
session_unset();

// تدمير الجلسة بالكامل
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول
header("Location: login.php");
exit;
?>
