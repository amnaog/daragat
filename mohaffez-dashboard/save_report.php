<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// استقبال البيانات
$student_id = intval($_POST['student_id']);
$surah_id = intval($_POST['surah_id']);
$from_ayah = intval($_POST['from_ayah']);
$to_ayah = intval($_POST['to_ayah']);
$created_at = $_POST['created_at'] ?? date('Y-m-d');

// جلب عدد آيات السورة
$surah_info = $conn->query("SELECT name, ayah_count FROM quran_surahs WHERE id = $surah_id");
if (!$surah_info || $surah_info->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Surah not found']);
    exit;
}
$surah = $surah_info->fetch_assoc();
$surah_name = $surah['name'];
$ayah_count = intval($surah['ayah_count']);

// حساب عدد الآيات المحفوظة
$memorized_ayahs = $to_ayah - $from_ayah + 1;

// حساب التقدم داخل السورة
$progress = $ayah_count > 0 ? round(($memorized_ayahs / $ayah_count) * 100, 2) : 0;

// حفظ التقرير
$insert = "INSERT INTO reports (student_id, surah_id, from_ayah, to_ayah, created_at)
           VALUES ($student_id, $surah_id, $from_ayah, $to_ayah, '$created_at')";
if (!$conn->query($insert)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save report']);
    exit;
}

// تحديث progress في students
$conn->query("UPDATE students SET progress = $progress WHERE id = $student_id");

// الرد للواجهة
echo json_encode([
    'status' => 'success',
    'surah' => $surah_name,
    'from_ayah' => $from_ayah,
    'to_ayah' => $to_ayah,
    'date' => $created_at,
    'progress' => $progress
]);
?>