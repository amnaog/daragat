<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed");
}

$surah_id = intval($_GET['surah_id'] ?? 0);

if ($surah_id > 0) {
    $result = $conn->query("SELECT ayah_count FROM quran_surahs WHERE id = $surah_id");
    $row = $result->fetch_assoc();
    $ayah_count = $row['ayah_count'] ?? 0;

    for ($i = 1; $i <= $ayah_count; $i++) {
        echo "<option value='$i'>$i</option>";
    }
}