<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil status alat dan battery_level dari database
$result = $conn->query("SELECT status, battery_level FROM device_status WHERE id = 1");
$device_data = $result->fetch_assoc();

$status = $device_data['status'];
$battery_level = $device_data['battery_level'];

if ($status === 'on' && $battery_level > 0) {
    $new_battery_level = max(0, $battery_level - 1); // Kurangi baterai setiap detik

    // Update battery_level di database
    $conn->query("UPDATE device_status SET battery_level = $new_battery_level WHERE id = 1");

    if ($new_battery_level == 0) {
        // Matikan alat jika baterai habis
        $conn->query("UPDATE device_status SET status = 'off' WHERE id = 1");
        echo json_encode(["success" => false, "logout" => true, "message" => "Battery depleted, user logged out."]);
        exit();
    }

    echo json_encode(["success" => true, "battery_level" => $new_battery_level]);
} else {
    echo json_encode(["success" => false, "message" => "Device is off or battery is zero."]);
}

$conn->close();
