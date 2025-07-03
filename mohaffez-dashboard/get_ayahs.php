<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

header('Content-Type: application/json');

$surah_id = intval($_GET['surah_id'] ?? 0);
if (!$surah_id) {
    echo json_encode(['error' => 'Invalid surah ID']);
    exit;
}

$sql = "SELECT ayah_count FROM quran_surahs WHERE id = $surah_id";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    $ayahs = range(1, intval($row['ayah_count']));
    echo json_encode(['ayahs' => $ayahs]);
} else {
    echo json_encode(['error' => 'Surah not found']);
}