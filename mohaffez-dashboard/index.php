<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// تحديث تلقائي عند العودة من صفحة التقرير
if (isset($_GET['refresh'])) {
    $redirect_id = intval($_GET['halaqa_id']);
    echo "<script>location.href='index.php?halaqa_id=$redirect_id';</script>";
    exit;
}

// جلب الحلقات
$halaqat_sql = "SELECT * FROM halaqat";
$halaqat_result = $conn->query($halaqat_sql);

// أول حلقة تلقائيًا
if (!isset($_GET['halaqa_id'])) {
    $first = $conn->query("SELECT id FROM halaqat ORDER BY id ASC LIMIT 1");
    $first_id = $first->fetch_assoc()['id'];
    header("Location: ?halaqa_id=$first_id");
    exit;
}

$selected_halaqa_id = intval($_GET['halaqa_id']);
$students = [];

// جلب الطلاب
$students_sql = "SELECT * FROM students WHERE halaqa_id = $selected_halaqa_id";
$students_result = $conn->query($students_sql);
while ($row = $students_result->fetch_assoc()) {
    $student_id = $row['id'];

    // آخر تقرير
    $report_sql = "SELECT r.*, s.name as surah_name, s.ayah_count 
                   FROM reports r 
                   JOIN quran_surahs s ON r.surah_id = s.id 
                   WHERE student_id = $student_id 
                   ORDER BY r.id DESC 
                   LIMIT 1";
    $report_result = $conn->query($report_sql);
    $report = $report_result->fetch_assoc();

    $row['last_memorized'] = $report['surah_name'] ?? null;
    $row['from_ayah'] = $report['from_ayah'] ?? null;
    $row['to_ayah'] = $report['to_ayah'] ?? null;
    $row['last_date'] = $report['created_at'] ?? null;

    // حساب progress كنسبة من 6236 آية (القرآن كامل)
    $total = $conn->query("SELECT SUM(to_ayah - from_ayah + 1) as total FROM reports WHERE student_id = $student_id")->fetch_assoc()['total'] ?? 0;
    $progress = round(($total / 6236) * 100, 2);

    $row['progress'] = $progress;

    $students[] = $row;
}

// اسم الحلقة
function getHalaqaName($conn, $id) {
    $stmt = $conn->prepare("SELECT name FROM halaqat WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name);
    return $stmt->fetch() ? $name : "Unknown";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .add-report-link {
            display: inline-block;
            padding: 6px 12px;
            background-color: #4CAF50;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .add-report-link:hover {
            background-color: #45a049;
        }
        #studentsTable tbody tr {
            display: table-row;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");
            searchInput.addEventListener("input", function () {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll("#studentsTable tbody tr");
                rows.forEach(row => {
                    const studentName = row.cells[0].innerText.toLowerCase();
                    const surahName = row.cells[3]?.innerText?.toLowerCase() || "";
                    if (studentName.includes(filter) || surahName.includes(filter)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        });
    </script>
</head>
<body>
<div class="sidebar">
    <div>
        <div class="logo">📗 QuranFlow</div>
        <ul class="menu">
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="progress.php">Progress</a></li>
            <li><a href="messages.php">Messages</a></li>
        </ul>
    </div>
    <div class="user-info"><div class="avatar"></div>
        <div>Sheikh Abdullah</div>
        <div style="font-size: 12px;">sheikh.abdullah@quran.com</div>
    </div>
</div>

<div class="main">
    <header>
        <h1>Welcome back, Sheikh Abdullah!</h1>
        <p>Here's your dashboard for today.</p>
        <input type="text" class="search" id="searchInput" placeholder="Search students...">
    </header>

    <div class="halqat-overview">
        <?php
        $halaqat_result->data_seek(0);
        while ($row = $halaqat_result->fetch_assoc()):
            $halaqa_id = $row['id'];
            $count = $conn->query("SELECT COUNT(*) as c FROM students WHERE halaqa_id = $halaqa_id")->fetch_assoc()['c'];
        ?>
        <div class="halqa <?= $halaqa_id == $selected_halaqa_id ? 'active' : '' ?>">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p>🕒 <?= htmlspecialchars($row['schedule']) ?></p>
            <p>👥 <?= $count ?> Students</p>
            <div style="margin-top: 12px;">
                <a href="edit_halaqa.php?halaqa_id=<?= $halaqa_id ?>" class="edit-btn">Edit</a>
                <a href="?halaqa_id=<?= $halaqa_id ?>" class="view-btn">View Students</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php if ($selected_halaqa_id): ?>
    <div class="students-section">
        <h2>Students in <?= htmlspecialchars(getHalaqaName($conn, $selected_halaqa_id)) ?></h2>
        <table id="studentsTable">
            <thead>
            <tr>
                <th>Student</th>
                <th>Progress</th>
                <th>Last Memorized</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['full_name']) ?></td>
                    <td>
                        <div class="progress">
                            <div class="bar" style="width: <?= $student['progress'] ?>%;"></div>
                        </div>
                        <?= $student['progress'] ?>%
                    </td>
                    <td>
                        <?php if ($student['last_memorized']): ?>
                            <div class="memorized-box">
                                <div class="surah-name">Memorized Surah <?= htmlspecialchars($student['last_memorized']) ?></div>
                                <div class="memorized-date">
                                    from Ayah <?= $student['from_ayah'] ?> to Ayah <?= $student['to_ayah'] ?><br>
                                    <?= date('Y-m-d', strtotime($student['last_date'])) ?>
                                </div>
                            </div>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td>
                        <a href="add_report.php?student_id=<?= $student['id'] ?>&halaqa_id=<?= $selected_halaqa_id ?>" class="add-report-link">Add Report</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

</body>
</html>