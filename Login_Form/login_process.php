<?php
session_start(); // بدء جلسة العمل لتخزين بيانات المستخدم

// إنشاء اتصال جديد بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "darajat");

// التحقق من نجاح الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // إيقاف التنفيذ مع رسالة الخطأ في حالة فشل الاتصال
}

// التحقق من أن الطلب المرسل هو POST (عند إرسال نموذج تسجيل الدخول)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على اسم المستخدم وكلمة المرور من البيانات المرسلة مع تأمين القيمة الافتراضية لتجنب الأخطاء
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // تحضير استعلام آمن لاستخراج بيانات المستخدم من جدول users مع الانضمام إلى جدول roles للحصول على اسم الدور
    $stmt = $conn->prepare("
        SELECT u.id, u.username, u.email, u.password, r.name AS role
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.username = ? OR u.email = ?
    ");
    // ربط معاملات الاستعلام بالقيم المُدخلة (اسم المستخدم أو البريد الإلكتروني)
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute(); // تنفيذ الاستعلام
    $result = $stmt->get_result(); // الحصول على النتيجة

    // التحقق من وجود المستخدم في قاعدة البيانات
    if ($row = $result->fetch_assoc()) {
        // تحقق من صحة كلمة المرور (مقارنة مباشرة - يُفضل في الواقع استخدام التجزئة)
        if ($password === $row['password']) {
            // تعيين بيانات المستخدم في جلسة العمل لاستخدامها لاحقاً في الصفحات المختلفة
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $email = $row['email'];

            // توجيه المستخدم إلى لوحة التحكم المناسبة بناءً على دوره
            switch ($row['role']) {
                case 'teacher':
                    // جلب معرف المعلم من جدول teachers اعتماداً على البريد الإلكتروني
                    $q = $conn->prepare("SELECT id FROM teachers WHERE email = ?");
                    $q->bind_param("s", $email);
                    $q->execute();
                    $res = $q->get_result();
                    if ($r = $res->fetch_assoc()) {
                        $_SESSION['teacher_id'] = $r['id']; // حفظ معرف المعلم في الجلسة
                        header("Location: http://localhost/daragat/mohaffez-dashboard/index.php"); // إعادة التوجيه للوحة معلم
                        exit;
                    } else {
                        $_SESSION['error'] = "Teacher not found in teachers table."; // رسالة خطأ في حالة عدم وجود المعلم
                    }
                    break;

                case 'student':
                    // جلب معرف الطالب من جدول students اعتماداً على البريد الإلكتروني
                    $q = $conn->prepare("SELECT id FROM students WHERE email = ?");
                    $q->bind_param("s", $email);
                    $q->execute();
                    $res = $q->get_result();
                    if ($r = $res->fetch_assoc()) {
                        $_SESSION['student_id'] = $r['id']; // حفظ معرف الطالب في الجلسة
                        header("Location: http://localhost/daragat/student-dashboard/index.php"); // إعادة التوجيه للوحة الطالب
                        exit;
                    } else {
                        $_SESSION['error'] = "Student not found in students table."; // رسالة خطأ في حالة عدم وجود الطالب
                    }
                    break;

                case 'admin':
                    // إعادة التوجيه مباشرة إلى لوحة تحكم الإدارة
                    header("Location: http://localhost/daragat/Admin-dashboard/dashboard.php");
                    exit;

                default:
                    // في حال وجود دور غير معروف، يتم تعيين رسالة خطأ
                    $_SESSION['error'] = "Unknown user role.";
            }
        } else {
            // كلمة المرور غير صحيحة
            $_SESSION['error'] = "Invalid username or password.";
        }
    } else {
        // المستخدم غير موجود في قاعدة البيانات
        $_SESSION['error'] = "User not found.";
    }

    // إعادة التوجيه إلى صفحة تسجيل الدخول مع رسالة الخطأ في حالة الفشل
    header("Location: login.php");
    exit();
}
