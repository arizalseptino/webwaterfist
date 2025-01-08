<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
$status = $data['status'] ?? null;
$speed = $data['speed'] ?? null;

if ($status !== null) {
    if ($status === 'on') {
        $conn->query("UPDATE device_status SET status = 'on' WHERE id = 1");
    } else {
        $conn->query("UPDATE device_status SET status = 'off' WHERE id = 1");
    }
}

if ($speed !== null) {
    $conn->query("UPDATE device_status SET speed = $speed WHERE id = 1");
}

echo json_encode(["success" => true, "message" => "Device status updated successfully."]);

$conn->close();
