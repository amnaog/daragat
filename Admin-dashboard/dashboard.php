<?php
// استدعاء ملف الاتصال بقاعدة البيانات
include 'db.php';

// بدء الجلسة لتتبع حالة المستخدم
session_start();

// التحقق من صلاحية المستخدم، فقط المستخدم ذو دور 'admin' يمكنه الدخول
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // إعادة التوجيه لصفحة dashboard إذا لم يكن المستخدم أدمن
    header("Location: ../dashboard.php");
    exit();
}

// إنشاء اتصال جديد بقاعدة البيانات 'darajat' عبر mysqli
$conn = new mysqli('localhost', 'root', '', 'darajat');
// التحقق من نجاح الاتصال
if ($conn->connect_error) {
    // إنهاء البرنامج وطباعة رسالة الخطأ في حالة فشل الاتصال
    die("Connection failed: " . $conn->connect_error);
}

// جلب عدد الطلاب من جدول students
$studentsCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];
// جلب عدد المعلمين من جدول teachers
$teachersCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM teachers"))[0];
// جلب عدد الحلقات النشطة من جدول halaqat
$halaqatCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM halaqat"))[0];

// مصفوفة لتخزين نشاط الطلاب (عدد التسجيلات) لكل يوم من أيام الأسبوع
$studentsPerDay = [];
// الأيام التي سيتم تتبعها
$weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
// تهيئة المصفوفة بالقيم صفر لكل يوم
foreach ($weekdays as $day) {
    $studentsPerDay[$day] = 0;
}

// استعلام لجلب تواريخ إنشاء حسابات الطلاب
$result = mysqli_query($conn, "SELECT created_at FROM students");
// المرور على كل صف من النتائج
while ($row = mysqli_fetch_assoc($result)) {
    // استخراج اليوم من تاريخ الإنشاء بصيغة اليوم المختصر (مثل Mon)
    $weekday = date('D', strtotime($row['created_at']));
    // التحقق من أن اليوم موجود في المصفوفة لزيادة العد
    if (isset($studentsPerDay[$weekday])) {
        $studentsPerDay[$weekday]++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quran Dashboard</title>
    <!-- تحميل مكتبة Chart.js للرسم البياني -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* تنسيق عام لصفحة الويب */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f9f9f9;
        }
        /* تصميم الشريط الجانبي */
        .sidebar {
            background: #0f172a;
            color: white;
            width: 220px;
            height: 100vh; /* ارتفاع كامل النافذة */
            position: fixed; /* يبقى ثابت أثناء التمرير */
            padding: 20px 10px;
        }
        /* عنوان الشريط الجانبي */
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
        }
        /* روابط الشريط الجانبي */
        .sidebar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        /* تنسيق الرابط النشط أو عند المرور عليه */
        .sidebar a.active, .sidebar a:hover {
            background-color: #1e293b;
        }
        /* المساحة الرئيسية للمحتوى */
        .main {
            margin-left: 240px; /* تباعد بجانب الشريط الجانبي */
            padding: 20px;
        }
        /* كروت المحتوى */
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        /* عناوين الكروت */
        .card h3 {
            margin-top: 0;
        }
        /* صفوف مرنة لتوزيع الكروت بجانب بعضها */
        .flex-row {
            display: flex;
            gap: 20px;
        }
        /* كل كرت يأخذ نفس المساحة داخل الصف */
        .flex-row .card {
            flex: 1;
        }
        /* روابط الإجراءات السريعة */
        .quick-actions a {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
            padding: 10px 20px;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        /* أيقونات داخل روابط الإجراءات */
        .quick-actions a svg {
            margin-right: 8px;
        }
        /* تنسيق إشعارات */
        .notifications li {
            margin-bottom: 12px;
            font-size: 14px;
        }
        /* بادج يظهر بجانب العنوان لتوضيح الدور */
        .badge {
            font-size: 12px;
            background: #bbf7d0;
            color: #16a34a;
            padding: 2px 8px;
            border-radius: 9999px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<!-- الشريط الجانبي للتنقل بين الصفحات -->
<div class="sidebar">
    <h2> 📗 QuranFlow</h2>
    <a class="active" href="#">Dashboard</a>
    <a href="teachers.php">Teachers</a>
    <a href="students.php">Students</a>
    <a href="halaqat.php">Halaqat</a>
    <a href="Requests.php">Requests</a>
</div>

<!-- المحتوى الرئيسي -->
<div class="main">
    <h1>Quran Circle Nexus <span class="badge">Admin</span></h1>

    <!-- صف يحتوي على ثلاث بطاقات تعرض إحصائيات -->
    <div class="flex-row">
        <div class="card">
            <h3>Total Students</h3>
            <!-- عرض عدد الطلاب -->
            <p style="font-size: 32px; color: #16a34a;">
                <?php echo $studentsCount; ?>
            </p>
        </div>
        <div class="card">
            <h3>Total Teachers</h3>
            <!-- عرض عدد المعلمين -->
            <p style="font-size: 32px; color: #16a34a;">
                <?php echo $teachersCount; ?>
            </p>
        </div>
        <div class="card">
            <h3>Active Halaqat</h3>
            <!-- عرض عدد الحلقات النشطة -->
            <p style="font-size: 32px; color: #16a34a;">
                <?php echo $halaqatCount; ?>
            </p>
        </div>
    </div>

    <!-- صف يحتوي على بطاقتين: الإجراءات السريعة ونشاط الطلاب -->
    <div class="flex-row">
        <div class="card quick-actions">
            <h3>Quick Actions</h3>
            <!-- روابط لإضافة معلم، طالب، أو حلقة -->
            <a href="teachers.php">➕ Add Teacher</a>
            <a href="students.php">👤 Add Student</a>
            <a href="halaqat.php">☰ Add Halaqa</a>
        </div>
        <div class="card">
            <h3>Recent Activity</h3>
            <!-- عنصر كانفاس لعرض الرسم البياني -->
            <canvas id="progressChart" height="140"></canvas>
        </div>
    </div>
</div>

<script>
// الحصول على العنصر canvas لإنشاء الرسم البياني
const ctx = document.getElementById('progressChart').getContext('2d');

// إنشاء مخطط خطي باستخدام مكتبة Chart.js
const chart = new Chart(ctx, {
    type: 'line', // نوع المخطط: خطي
    data: {
        // محاور المخطط (أيام الأسبوع)
        labels: <?php echo json_encode(array_keys($studentsPerDay)); ?>,
        datasets: [{
            label: 'Students Joined', // تسمية البيانات
            // بيانات عدد الطلاب المنضمين لكل يوم
            data: <?php echo json_encode(array_values($studentsPerDay)); ?>,
            backgroundColor: 'rgba(34,197,94,0.2)', // لون خلفية المنحنى
            borderColor: '#22c55e', // لون الخط
            fill: true, // ملء تحت الخط
            tension: 0.3, // انحناء الخط
            pointRadius: 5, // حجم نقاط البيانات
            pointHoverRadius: 7 // حجم النقاط عند التحويم
        }]
    },
    options: {
        responsive: true, // يجعل المخطط يتجاوب مع حجم الشاشة
        plugins: {
            legend: {
                display: false // إخفاء وسيلة الإيضاح
            },
            tooltip: {
                enabled: false // إخفاء الأدوات المساعدة عند المرور على النقاط
            }
        },
        scales: {
            y: {
                beginAtZero: true, // بدء المحور الصادي من الصفر
                precision: 0 // عرض القيم بدون أرقام عشرية
            }
        }
    }
});
</script>

</body>
</html>
