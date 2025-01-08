<?php
session_start();
header('Content-Type: application/json');

// Periksa apakah pengguna yang login adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['active' => false]);
    exit();
}

// Informasi koneksi ke database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    echo json_encode(['active' => false]);
    exit();
}

// Memeriksa apakah sesi `admin` masih aktif di tabel `aktif_akun`
$admin_id = $_SESSION['user_id'];
$session_query = "SELECT * FROM aktif_akun WHERE user_id = ? AND role = 'admin'";
$stmt = $conn->prepare($session_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

// Kirimkan respons JSON apakah sesi aktif atau tidak
if ($result->num_rows > 0) {
    echo json_encode(['active' => true]);
} else {
    echo json_encode(['active' => false]);
}

// Menutup koneksi
$stmt->close();
$conn->close();
?>
