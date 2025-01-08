<?php
session_start();
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Koneksi database gagal.']));
}

// Ambil data dari request
$data = json_decode(file_get_contents("php://input"), true);
$speed = isset($data['speed']) ? (int)$data['speed'] : 1;

// Ambil water_level saat ini dari database
$query = "SELECT water_level FROM device_status WHERE id = 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_water_level = (int)$row['water_level'];

    // Kurangi water_level berdasarkan kecepatan
    $new_water_level = max(0, $current_water_level - $speed);

    // Update water_level ke database
    $update_query = "UPDATE device_status SET water_level = ? WHERE id = 1";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $new_water_level);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'water_level' => $new_water_level]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui water level.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Data water_level tidak ditemukan.']);
}

$conn->close();
?>
