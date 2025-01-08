<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data baterai dan air dari tabel `device_metrics`
$sql = "SELECT battery_level, water_level FROM device_status WHERE id=1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'battery' => $row['battery_level'],
        'water' => $row['water_level']
    ]);
} else {
    echo json_encode(['battery' => 0, 'water' => 0]); // Default jika tidak ada data
}

$conn->close();
?>
