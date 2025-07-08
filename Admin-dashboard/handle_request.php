<?php
include 'db.php';

// التحقق من أن الطلب تم إرساله عبر طريقة POST فقط
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // جلب معرّف الطلب (request_id) وتحويله إلى رقم صحيح
    $request_id = intval($_POST['request_id'] ?? 0);
    // جلب الدور (Teacher أو Student)
    $role = $_POST['role'] ?? '';
    // جلب الإجراء المطلوب (approve أو reject)
    $action = $_POST['action'] ?? '';

    // التحقق من صحة البيانات الأساسية: معرف الطلب غير صفري، والدور من ضمن القيم المسموح بها، والإجراء من القيم المقبولة
    if (!$request_id || !in_array($role, ['Teacher', 'Student']) || !in_array($action, ['approve', 'reject'])) {
        die('Invalid request'); // إنهاء السكريبت مع رسالة خطأ في حال البيانات غير صحيحة
    }

    // تحديد أسماء جداول الطلبات والجدول الرئيسي بناءً على الدور
    if ($role === 'Teacher') {
        $table_requests = 'teacher_requests';  // جدول طلبات المعلمين
        $table_main = 'teachers';              // الجدول الرئيسي للمعلمين
    } else {
        $table_requests = 'student_requests';  // جدول طلبات الطلاب
        $table_main = 'students';               // الجدول الرئيسي للطلاب
    }

    // جلب بيانات الطلب المحدد من جدول الطلبات باستخدام معرّف الطلب مع حماية من حقن SQL
    $stmt = $conn->prepare("SELECT * FROM $table_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    // التحقق من وجود الطلب
    if (!$request) {
        die('Request not found'); // إنهاء السكريبت في حال لم يتم العثور على الطلب
    }

    // في حالة الموافقة، ننقل بيانات الطلب إلى الجدول الرئيسي المناسب
    if ($action === 'approve') {
        if ($role === 'Teacher') {
            // تحضير جملة الإدخال مع حقول المعلم
            $stmt_insert = $conn->prepare("INSERT INTO $table_main (full_name, email, phone, certificate_path, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt_insert->bind_param("ssss", $request['full_name'], $request['email'], $request['phone'], $request['certificate_path']);
        } else {
            // تحضير جملة الإدخال مع حقول الطالب
            $stmt_insert = $conn->prepare("INSERT INTO $table_main (full_name, email, phone, level, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt_insert->bind_param("ssss", $request['full_name'], $request['email'], $request['phone'], $request['level']);
        }

        // تنفيذ الإدخال، وإذا فشل يتم إظهار رسالة خطأ
        if (!$stmt_insert->execute()) {
            die("Failed to insert into $table_main");
        }
    }

    // في حال الموافقة أو الرفض، نحذف الطلب من جدول الطلبات ليتم إزالته من قائمة الانتظار
    $stmt_del = $conn->prepare("DELETE FROM $table_requests WHERE id = ?");
    $stmt_del->bind_param("i", $request_id);
    $stmt_del->execute();

    // إعادة التوجيه إلى صفحة عرض الطلبات بعد إتمام العملية
    header("Location: Requests.php");
    exit();
} else {
    // في حالة وصول الطلب بطريقة غير POST، يتم إظهار رسالة خطأ وإنهاء السكريبت
    die('Invalid request method');
}
?>
