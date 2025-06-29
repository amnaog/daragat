<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// جلب الحلقات
$halaqat_sql = "SELECT * FROM halaqat";
$halaqat_result = $conn->query($halaqat_sql);

// التحقق من الحلقة المختارة
$selected_halaqa_id = isset($_GET['halaqa_id']) ? intval($_GET['halaqa_id']) : null;

// جلب طلاب الحلقة المحددة
$students = [];
if ($selected_halaqa_id) {
    $students_sql = "SELECT * FROM students WHERE halaqa_id = $selected_halaqa_id";
    $students_result = $conn->query($students_sql);
    if ($students_result && $students_result->num_rows > 0) {
        while ($row = $students_result->fetch_assoc()) {
            $student_id = $row['id'];
            // جلب آخر تقرير لهذا الطالب من reports
            $report_sql = "SELECT performance, created_at 
                           FROM reports 
                           WHERE student_id = $student_id 
                           ORDER BY created_at DESC 
                           LIMIT 1";
            $report_result = $conn->query($report_sql);
            $report = $report_result->fetch_assoc();

            // أضف التقرير للطالب
            $row['last_performance'] = $report['performance'] ?? null;
            $row['last_date'] = $report['created_at'] ?? null;

            $students[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="logo">QuranFlow</div>
            <ul class="menu">
                <li class="active"><a href="index.php">Dashboard</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="progress.php">Progress</a></li>
                <li><a href="messages.php">Messages</a></li>
            </ul>
        </div>
        <div class="user-info">
            <div class="avatar"></div>
            <div>Sheikh Abdullah</div>
            <div style="font-size: 12px;">sheikh.abdullah@quran.com</div>
        </div>
    </div>

    <div class="main">
        <header>
            <h1>Welcome back, Sheikh Abdullah!</h1>
            <p>Here's your dashboard for today.</p>
            <input type="text" class="search" id="searchInput" placeholder="Search students or surahs...">
        </header>

        <div class="halqat-overview">
            <?php while ($row = $halaqat_result->fetch_assoc()): ?>
                <?php
                    $halaqa_id = $row['id'];
                    $count_sql = "SELECT COUNT(*) as count FROM students WHERE halaqa_id = $halaqa_id";
                    $count_result = $conn->query($count_sql);
                    $student_count = $count_result->fetch_assoc()['count'];
                ?>
                <div class="halqa <?= $halaqa_id == $selected_halaqa_id ? 'active' : '' ?>">
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p>🕒 <?= htmlspecialchars($row['schedule']) ?></p>
                    <p>👥 <?= $student_count ?> Students</p>
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
                            <th>Level</th>
                            <th>Progress</th>
                            <th>Last Memorized</th>
                            <th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <div class="avatar-sm"></div>
                                    <?= htmlspecialchars($student['full_name']) ?>
                                </td>
                                <td><?= htmlspecialchars($student['level']) ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="bar" style="width: <?= intval($student['progress']) ?>%;"></div>
                                    </div>
                                    <?= intval($student['progress']) ?>%
                                </td>
                                <td>
                                    <?php if ($student['last_performance'] && $student['last_date']): ?>
                                        <div class="memorized-box">
                                            <div class="surah-name"><?= htmlspecialchars($student['last_performance']) ?></div>
                                            <div class="memorized-date"><?= date('Y-m-d', strtotime($student['last_date'])) ?></div>
                                        </div>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td><button>📋</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('studentsTable');

        if (searchInput && table) {
            searchInput.addEventListener('keyup', function () {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const studentName = row.cells[0].innerText.toLowerCase();
                    const surahInfo = row.cells[3].innerText.toLowerCase();
                    if (studentName.includes(searchTerm) || surahInfo.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
    </script>
</body>
</html>

<?php
function getHalaqaName($conn, $id) {
    $stmt = $conn->prepare("SELECT name FROM halaqat WHERE id = ?");
    if (!$stmt) return "Unknown";
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) return "Unknown";
    $stmt->bind_result($name);
    if ($stmt->fetch()) {
        $stmt->close();
        return $name;
    } else {
        $stmt->close();
        return "Unknown";
    }
}
?>