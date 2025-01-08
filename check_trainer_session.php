<?php
session_start();
header('Content-Type: application/json');

// Periksa apakah pengguna yang login adalah trainer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    echo json_encode(['active' => false]);
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    echo json_encode(['active' => false]);
    exit();
}

// Cek apakah ada sesi aktif untuk trainer ini di tabel aktif_akun
$trainer_id = $_SESSION['user_id'];
$session_query = "SELECT * FROM aktif_akun WHERE user_id = ? AND role = 'trainer'";
$stmt = $conn->prepare($session_query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

// Jika sesi ada, kirimkan respons aktif, jika tidak, kirim respons tidak aktif
if ($result->num_rows > 0) {
    echo json_encode(['active' => true]);
} else {
    echo json_encode(['active' => false]);
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>
