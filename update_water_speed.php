<?php
header('Content-Type: application/json');

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Ambil data dari request
$data = json_decode(file_get_contents("php://input"), true);
$speed = isset($data['speed']) ? (int)$data['speed'] : 1; // Default kecepatan adalah 1

// Ambil water level dari database
$result = $conn->query("SELECT water_level FROM device_status WHERE id = 1");
$device_data = $result->fetch_assoc();
$water_level = $device_data['water_level'];

// Kurangi water level berdasarkan kecepatan (lebih lambat)
$new_water_level = max(0, $water_level - ($speed / 100));

// Perbarui water level di database
$conn->query("UPDATE device_status SET water_level = $new_water_level WHERE id = 1");

// Respon
if ($new_water_level == 0) {
    echo json_encode(["success" => false, "message" => "Water depleted, device turned off."]);
} else {
    echo json_encode(["success" => true, "water_level" => $new_water_level]);
}

$conn->close();
?>
