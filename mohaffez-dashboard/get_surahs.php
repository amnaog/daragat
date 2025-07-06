<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'darajat';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed");
}

$result = $conn->query("SELECT id, name FROM quran_surahs ORDER BY id ASC");
$surahs = [];

while ($row = $result->fetch_assoc()) {
    $surahs[] = $row;
}

header('Content-Type: application/json');
echo json_encode($surahs);
?>
