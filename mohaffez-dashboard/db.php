<?php
$host = "localhost";
$username = "root";
$password = ""; // خليه فاضي لو ما عندكش كلمة مرور
$dbname = "darajat";

// الاتصال بقاعدة البيانات
$conn = mysqli_connect($host, $username, $password, $dbname);

// التحقق من الاتصال
if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}
?>