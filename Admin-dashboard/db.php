<?php
// بيانات الاتصال بقاعدة البيانات
$host = "localhost";  // اسم السيرفر (عادة يكون localhost عند التشغيل المحلي)
$user = "root";       // اسم المستخدم لقاعدة البيانات
$pass = "";           // كلمة المرور (فارغة هنا لأنها افتراضية في XAMPP)
$db   = "darajat";    // اسم قاعدة البيانات المراد الاتصال بها

// إنشاء اتصال بقاعدة البيانات باستخدام دالة mysqli_connect
$conn = mysqli_connect($host, $user, $pass, $db);

// التحقق من نجاح الاتصال
if (!$conn) {
  // في حالة فشل الاتصال، إظهار رسالة خطأ وإنهاء تنفيذ السكريبت
  die("Database connection failed: " . mysqli_connect_error());
}
?>
