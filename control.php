<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waterfist_db";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $status = $data['status'] ?? 'off';
    $direction = $data['direction'] ?? 'center';
    $speed = $data['speed'] ?? 50;

    // Simpan status alat ke database
    $sql = "UPDATE device_status SET status='$status', direction='$direction', speed=$speed WHERE id=1";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Control updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating control: ' . $conn->error]);
    }
}

$conn->close();
?>
