<?php
// استدعاء ملف الاتصال بقاعدة البيانات الذي يحتوي على إعدادات الاتصال وثابت الاتصال $conn
include 'db.php'; 

// تنفيذ استعلام لجلب جميع طلبات التسجيل الخاصة بالطلاب من جدول student_requests
// ترتيب النتائج حسب تاريخ الإنشاء (created_at) تنازليًا لعرض الأحدث أولاً
$studentRequests = $conn->query("SELECT * FROM student_requests ORDER BY created_at DESC");

// تنفيذ استعلام لجلب جميع طلبات التسجيل الخاصة بالمحفظين من جدول teacher_requests
// ترتيب النتائج حسب تاريخ الإنشاء (created_at) تنازليًا لعرض الأحدث أولاً
$teacherRequests = $conn->query("SELECT * FROM teacher_requests ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <!-- تعيين ترميز الحروف لتكون UTF-8 لدعم النصوص متعددة اللغات -->
  <meta charset="UTF-8" />
  <!-- ضبط عرض الصفحة لتتوافق مع عرض شاشة الجهاز -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- عنوان الصفحة الذي يظهر في تبويب المتصفح -->
  <title>Registration Requests</title>
  <!-- استيراد خط Poppins من Google Fonts للاستخدام في الصفحة -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <!-- استيراد مكتبة أيقونات Font Awesome لاستخدام الأيقونات في التصميم -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
  <style>
    /* تعريف متغيرات CSS لاستخدام الألوان والخلفيات في الصفحة */
    :root {
      --primary-bg: #f8f9fa;          /* لون خلفية الصفحة الرئيسي */
      --sidebar-bg: #1a202c;          /* لون خلفية الشريط الجانبي */
      --card-bg: #ffffff;             /* لون خلفية البطاقات */
      --text-dark: #2d3748;           /* لون النص الأساسي */
      --text-light: #718096;          /* لون النص الثانوي */
      --accent-green: #48bb78;        /* لون التمييز الأخضر */
      --danger-red: #e53e3e;          /* لون التحذير الأحمر */
      --border-color: #eef2f7;        /* لون الحدود */
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); /* تأثير الظل */
    }

    /* إعدادات عامة للجسم */
    body {
      font-family: 'Poppins', sans-serif; /* الخط الأساسي للنص */
      margin: 0;                         /* إزالة الهوامش الافتراضية */
      background-color: var(--primary-bg); /* خلفية الصفحة */
      color: var(--text-dark);           /* لون النص */
    }

    /* الحاوية الرئيسية التي تحتوي على الشريط الجانبي والمحتوى */
    .container {
      display: flex;             /* عرض المحتويات بشكل صف أفقي */
      min-height: 100vh;         /* ارتفاع الصفحة كاملاً */
    }

    /* تصميم الشريط الجانبي */
    .sidebar {
      width: 250px;              /* عرض الشريط الجانبي */
      background-color: var(--sidebar-bg); /* خلفية داكنة */
      color: #e2e8f0;            /* لون النص داخل الشريط */
      padding: 20px;             /* مساحة داخلية حول المحتوى */
      flex-shrink: 0;            /* منع الشريط من الانكماش */
      height: 100vh;             /* ارتفاع كامل الصفحة */
      position: fixed;           /* تثبيت الشريط الجانبي في مكانه أثناء التمرير */
      display: flex;             /* ترتيب المحتوى داخل الشريط */
      flex-direction: column;    /* ترتيب المحتوى عموديًا */
    }

    /* عنوان الشريط الجانبي */
    .sidebar h2 {
      font-size: 1.4em;          /* حجم الخط */
      font-weight: bold;         /* الخط عريض */
      margin-bottom: 30px;       /* مسافة تحت العنوان */
      text-align: center;        /* محاذاة النص في الوسط */
      color: white;              /* لون النص أبيض */
    }

    /* تصميم الروابط داخل الشريط الجانبي */
    .sidebar a {
      color: #cbd5e0;            /* لون النص */
      text-decoration: none;     /* إزالة الخط السفلي */
      display: block;            /* عرض كامل ككتلة */
      padding: 12px 15px;        /* الحشو الداخلي */
      border-radius: 8px;        /* زوايا مستديرة */
      margin-bottom: 10px;       /* مسافة تحت كل رابط */
      font-weight: 600;          /* خط متوسط السماكة */
      transition: background-color 0.3s; /* تأثير الانتقال عند التفاعل */
    }

    /* تأثير عند التمرير على الرابط أو عند كونه نشط */
    .sidebar a.active, .sidebar a:hover {
      background-color: #2d3748; /* لون الخلفية عند التفاعل */
      color: #fff;               /* لون النص أبيض */
    }

    /* محتوى الصفحة الرئيسي */
    .main-content {
      margin-left: 250px;        /* ترك مساحة مساوية لعرض الشريط الجانبي */
      padding: 50px 60px 60px;  /* الحشو الداخلي حول المحتوى */
      max-width: 1200px;         /* الحد الأقصى للعرض */
    }

    /* تنسيق عنوان القسم الرئيسي */
    .main-content header h1 {
      font-size: 2.5em;          /* حجم الخط كبير */
      margin-bottom: 10px;       /* مسافة تحت العنوان */
    }

    /* فقرة تحت العنوان لشرح إضافي */
    .main-content header p {
      color: var(--text-light);  /* لون النص خفيف */
      margin-bottom: 40px;       /* مسافة تحت الفقرة */
      font-size: 1.1em;          /* حجم الخط */
    }

    /* شريط البحث والتصفية */
    .filter-search-bar {
      display: flex;             /* ترتيب عناصر الشريط صف أفقي */
      gap: 20px;                 /* مسافة بين العناصر */
      margin-bottom: 35px;       /* مسافة تحت الشريط */
      align-items: center;       /* محاذاة العناصر عمودياً في الوسط */
      flex-wrap: wrap;           /* السماح بلف العناصر عند صغر العرض */
    }

    /* تصميم حقول الإدخال والقوائم المنسدلة داخل شريط البحث والتصفية */
    .filter-search-bar input,
    .filter-search-bar select {
      padding: 12px 18px;                   /* الحشو الداخلي لزيادة المساحة */
      font-size: 1em;                      /* حجم الخط */
      border-radius: 10px;                 /* زوايا مستديرة للحواف */
      border: 1px solid var(--border-color); /* لون الحدود */
      outline: none;                      /* إزالة الإطار الافتراضي عند التركيز */
      font-family: 'Poppins', sans-serif; /* نفس الخط المستخدم في الصفحة */
    }

    /* تصميم حقل الإدخال الخاص بالبحث ليشغل أكبر مساحة ممكنة */
    .filter-search-bar input {
      flex: 1;                           /* يسمح للحقل بالتمدد */
      min-width: 280px;                  /* الحد الأدنى للعرض */
    }

    /* تصميم قائمة الاختيار لتحديد الفلتر */
    .filter-search-bar select {
      min-width: 180px;                  /* الحد الأدنى للعرض */
    }

    /* الحاوية التي تحتوي على بطاقات الطلبات */
    .requests-grid {
      display: flex;                    /* عرض كصف أفقي */
      flex-direction: column;           /* ترتيب بطاقات الطلب عمودي */
      align-items: center;              /* محاذاة البطاقات في الوسط */
      gap: 40px;                       /* مسافة بين كل بطاقة وأخرى */
    }

    /* تصميم بطاقة الطلب */
    .request-card {
      width: 100%;                     /* العرض كامل داخل الحاوية */
      max-width: 1000px;               /* الحد الأقصى للعرض */
      background-color: var(--card-bg); /* خلفية بيضاء */
      border-radius: 12px;             /* زوايا مستديرة */
      box-shadow: var(--shadow);       /* تأثير الظل */
      border: 1px solid var(--border-color); /* حدود خفيفة */
      display: flex;                   /* ترتيب محتويات البطاقة */
      flex-direction: column;          /* ترتيب المحتوى عمودي */
      transition: box-shadow 0.2s;     /* تأثير انتقال الظل عند التفاعل */
    }

    /* تأثير الظل عند تحويم الماوس فوق البطاقة */
    .request-card:hover {
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07),
                  0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* الأقسام الداخلية للبطاقة (رأس، محتوى، تذييل) */
    .card-header, .card-body, .card-footer {
      padding: 25px;                   /* حشو داخلي متساوي */
    }

    /* تصميم رأس البطاقة */
    .card-header {
      border-bottom: 1px solid var(--border-color); /* خط فاصل أسفل الرأس */
    }

    /* عنوان داخل رأس البطاقة */
    .card-header h3 {
      margin: 0 0 8px;                /* مسافة تحت العنوان */
      font-size: 1.35em;              /* حجم الخط */
    }

    /* فقرة توضيحية داخل رأس البطاقة */
    .card-header p {
      margin: 0;                      /* إزالة الهوامش */
      font-size: 1em;                 /* حجم الخط */
      color: var(--text-light);       /* لون النص الثانوي */
    }

    /* جعل نص داخل الفقرة بارزاً */
    .card-header p strong {
      color: var(--text-dark);        /* لون نص داكن */
    }

    /* عنصر معلومات داخل البطاقة */
    .info-item {
      display: flex;                  /* عرض العناصر أفقياً */
      align-items: center;            /* محاذاة رأسية وسط */
      gap: 14px;                     /* مسافة بين الأيقونة والنص */
      margin-bottom: 18px;            /* مسافة تحت العنصر */
      font-size: 1em;                 /* حجم الخط */
    }

    /* تصميم الأيقونة داخل عنصر المعلومات */
    .info-item i {
      color: var(--text-light);       /* لون الأيقونة خفيف */
      width: 22px;                    /* عرض ثابت */
      text-align: center;             /* محاذاة مركزية للنص */
      font-size: 1.2em;               /* حجم الخط */
    }

    /* تصميم النص داخل عنصر المعلومات */
    .info-item span {
      font-weight: 600;               /* خط عريض */
      color: var(--text-dark);        /* لون نص داكن */
    }

    /* تصميم زر التحميل الخاص بشهادة أو ملف */
    .btn-download {
      background-color: transparent; /* خلفية شفافة */
      color: var(--text-dark);        /* لون النص */
      text-decoration: none;          /* إزالة الخط السفلي */
      font-weight: 600;               /* خط متوسط السماكة */
      display: inline-flex;           /* عرض مرن للداخل */
      align-items: center;            /* محاذاة العناصر داخل الزر عموديًا */
      gap: 10px;                     /* مسافة بين الأيقونة والنص */
    }

    /* تصميم أيقونة التحميل داخل زر التحميل */
    .btn-download .download-icon {
      color: var(--text-light);       /* لون الأيقونة */
      border: 1px solid var(--border-color); /* حدود خفيفة */
      border-radius: 6px;             /* زوايا مستديرة */
      width: 38px;                   /* عرض مربع الأيقونة */
      height: 38px;                  /* ارتفاع مربع الأيقونة */
      display: inline-grid;           /* استخدام شبكة لتمركز المحتوى */
      place-items: center;            /* تمركز محتوى الأيقونة أفقياً وعمودياً */
      transition: all 0.2s;           /* تأثير انتقال */
    }

     /* عند تحويم الماوس على زر التحميل، يتغير لون خلفية الأيقونة ولونها */
    .btn-download:hover .download-icon {
      background-color: #edf2f7;         /* لون خلفية فاتح */
      color: var(--text-dark);            /* لون نص داكن */
    }

    /* تذييل البطاقة مع خلفية ولون وحدود */
    .card-footer {
      background-color: #fdfdff;          /* خلفية فاتحة جداً */
      border-top: 1px solid var(--border-color); /* خط فاصل في الأعلى */
      display: flex;                      /* ترتيب أزرار التذييل أفقي */
      gap: 15px;                         /* مسافة بين الأزرار */
      border-bottom-left-radius: 12px;   /* زوايا مستديرة أسفل البطاقة */
      border-bottom-right-radius: 12px;
    }

    /* تنسيق الأزرار داخل التذييل */
    .card-footer .btn {
      flex: 1;                          /* كل زر يشغل نفس المساحة */
      padding: 12px;                    /* حشو داخلي */
      border: 1px solid transparent;   /* حدود شفافة بشكل افتراضي */
      border-radius: 8px;               /* حواف دائرية */
      font-weight: 700;                 /* خط عريض */
      font-size: 1em;                   /* حجم الخط */
      cursor: pointer;                  /* شكل المؤشر عند المرور */
      display: flex;                   /* ترتيب المحتوى داخل الزر */
      justify-content: center;         /* محاذاة المحتوى أفقياً في الوسط */
      align-items: center;             /* محاذاة المحتوى عمودياً في الوسط */
      gap: 10px;                       /* مسافة بين الأيقونة والنص */
    }

    /* زر الموافقة - لون أخضر */
    .btn-approve {
      background-color: var(--accent-green); /* لون أخضر */
      color: #fff;                            /* لون النص أبيض */
    }

    /* تأثير التمرير على زر الموافقة - درجة أخضر أغمق */
    .btn-approve:hover {
      background-color: #38a169;
    }

    /* زر الرفض - خلفية بيضاء مع حدود خفيفة ولون نص خفيف */
    .btn-reject {
      background-color: var(--card-bg);
      color: var(--text-light);
      border: 1px solid var(--border-color);
    }

    /* تأثير التمرير على زر الرفض - خلفية حمراء ونص أبيض */
    .btn-reject:hover {
      background-color: var(--danger-red);
      color: #fff;
      border-color: var(--danger-red);
    }
  </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    <!-- عنوان الشريط الجانبي -->
    <h2>Admin Panel</h2>
    <!-- روابط التنقل في الشريط الجانبي -->
    <a href="dashboard.php">Dashboard</a>
    <a href="teachers.php">Teachers</a>
    <a href="students.php">Students</a>
    <a href="halaqat.php">Halaqat</a>
    <!-- الرابط النشط هو صفحة طلبات التسجيل -->
    <a class="active" href="Requests.php">Registration Requests</a>
  </div>
  <main class="main-content">
    <header>
      <!-- عنوان الصفحة -->
      <h1>Registration Requests</h1>
      <!-- وصف مختصر للصفحة -->
      <p>Manage new registration requests for students and teachers.</p>
    </header>
    <!-- شريط البحث والتصفية -->
    <div class="filter-search-bar">
      <!-- حقل بحث حسب الاسم أو البريد الإلكتروني -->
      <input type="text" id="searchInput" placeholder="Search by name or email..." />
      <!-- قائمة اختيار لتصفية الطلبات حسب الدور (طالب أو محفظ) -->
      <select id="roleFilter">
        <option value="">All</option>          <!-- جميع الطلبات -->
        <option value="Teacher">Teacher</option>  <!-- طلبات المحفظين -->
        <option value="Student">Student</option>  <!-- طلبات الطلاب -->
      </select>
    </div>
    <!-- حاوية بطاقات الطلبات -->
    <div class="requests-grid">
      <!-- بدء حلقة جلب طلبات المحفظين من قاعدة البيانات -->
      <?php while($row = $teacherRequests->fetch_assoc()): ?>
        <div class="request-card">
          <div class="card-header">
            <!-- اسم الشخص مقدم الطلب -->
            <h3><?= htmlspecialchars($row['full_name']) ?></h3>
            <!-- وصف نوع الطلب -->
            <p>Request to register as a <strong>Teacher</strong></p>
          </div>
          <div class="card-body">
            <!-- البريد الإلكتروني -->
            <div class="info-item"><i class="fas fa-envelope"></i><span><?= htmlspecialchars($row['email']) ?></span></div>
            <!-- رقم الهاتف -->
            <div class="info-item"><i class="fas fa-phone"></i><span><?= htmlspecialchars($row['phone']) ?></span></div>
            <!-- تاريخ الطلب -->
            <div class="info-item"><i class="fas fa-calendar-alt"></i><span><?= date("F d, Y", strtotime($row['created_at'])) ?></span></div>
            <!-- رابط تحميل الشهادة -->
            <div class="info-item">
                <a href="/daragat/Muhaffez_Register/<?= htmlspecialchars($row['certificate_path']) ?>" class="btn-download" target="_blank">
                <span>Download Certificate</span>
                <span class="download-icon"><i class="fas fa-download"></i></span>
              </a>
            </div>
          </div>
          <div class="card-footer">
            <!-- نموذج لرفض الطلب -->
            <form method="POST" action="handle_request.php">
              <!-- معرف الطلب -->
              <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
              <!-- نوع الدور (محفظ) -->
              <input type="hidden" name="role" value="Teacher">
              <!-- الفعل المطلوب (رفض) -->
              <input type="hidden" name="action" value="reject">
              <!-- زر رفض الطلب -->
              <button class="btn btn-reject"><i class="fas fa-times"></i> Reject</button>
            </form>
            <!-- نموذج للموافقة على الطلب -->
            <form method="POST" action="handle_request.php">
              <!-- معرف الطلب -->
              <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
              <!-- نوع الدور (محفظ) -->
              <input type="hidden" name="role" value="Teacher">
              <!-- الفعل المطلوب (موافقة) -->
              <input type="hidden" name="action" value="approve">
              <!-- زر الموافقة على الطلب -->
              <button class="btn btn-approve"><i class="fas fa-check"></i> Approve</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>

      <?php while($row = $studentRequests->fetch_assoc()): ?>
  <div class="request-card">
    <div class="card-header">
      <!-- عرض اسم الطالب مقدم الطلب -->
      <h3><?= htmlspecialchars($row['full_name']) ?></h3>
      <!-- وصف نوع الطلب -->
      <p>Request to register as a <strong>Student</strong></p>
    </div>
    <div class="card-body">
      <!-- عرض البريد الإلكتروني -->
      <div class="info-item"><i class="fas fa-envelope"></i><span><?= htmlspecialchars($row['email']) ?></span></div>
      <!-- عرض رقم الهاتف -->
      <div class="info-item"><i class="fas fa-phone"></i><span><?= htmlspecialchars($row['phone']) ?></span></div>
      <!-- عرض تاريخ الطلب مع تحويل التنسيق -->
      <div class="info-item"><i class="fas fa-calendar-alt"></i><span><?= date("F d, Y", strtotime($row['created_at'])) ?></span></div>
      <!-- عرض المستوى أو إذا لم يوجد عرض 'New to memorization' -->
      <div class="info-item"><i class="fas fa-layer-group"></i><span><?= htmlspecialchars($row['level'] ?: 'New to memorization') ?></span></div>
    </div>
    <div class="card-footer">
      <!-- نموذج رفض الطلب -->
      <form method="POST" action="handle_request.php">
        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
        <input type="hidden" name="role" value="Student">
        <input type="hidden" name="action" value="reject">
        <button class="btn btn-reject"><i class="fas fa-times"></i> Reject</button>
      </form>
      <!-- نموذج الموافقة على الطلب -->
      <form method="POST" action="handle_request.php">
        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
        <input type="hidden" name="role" value="Student">
        <input type="hidden" name="action" value="approve">
        <button class="btn btn-approve"><i class="fas fa-check"></i> Approve</button>
      </form>
    </div>
  </div>
<?php endwhile; ?>
</div> <!-- نهاية requests-grid -->
</main>
</div> <!-- نهاية container -->

<script>
  // جلب عناصر البحث والتصفية من الصفحة
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');
  // جلب جميع بطاقات الطلبات
  const cards = document.querySelectorAll('.request-card');

  // دالة فلترة البطاقات حسب نص البحث والدور المختار
  function filterCards() {
    const searchTerm = searchInput.value.toLowerCase();  // نص البحث بعد تحويله لحروف صغيرة
    const selectedRole = roleFilter.value.toLowerCase(); // الدور المختار من القائمة بعد تحويله لحروف صغيرة

    cards.forEach(card => {
      // استخراج اسم الشخص داخل البطاقة (عنوان h3)
      const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
      // استخراج البريد الإلكتروني بجوار أيقونة الظرف
      const email = card.querySelector('.fa-envelope')?.nextElementSibling?.textContent.toLowerCase() || '';
      // استخراج نص الدور من الفقرة داخل البطاقة
      const roleText = card.querySelector('p')?.textContent.toLowerCase() || '';

      // تحقق من مطابقة اسم أو بريد البحث مع نص البحث
      const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
      // تحقق من تطابق الدور المختار مع نص الدور في البطاقة أو السماح لكل الأدوار
      const matchesRole = selectedRole === '' || roleText.includes(selectedRole);

      // عرض البطاقة إذا تطابق كلا الشرطين، وإخفاؤها إذا لم تتطابق
      card.style.display = (matchesSearch && matchesRole) ? 'flex' : 'none';
    });
  }

  // إضافة مستمع أحداث لتحديث الفلترة عند كتابة نص في البحث
  searchInput.addEventListener('input', filterCards);
  // إضافة مستمع أحداث لتحديث الفلترة عند تغيير الدور المختار
  roleFilter.addEventListener('change', filterCards);
</script>
</body>
</html>
