<?php
session_start();

// Pastikan pengguna adalah trainer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$trainer_id = $_SESSION['user_id'];

// Periksa apakah sudah ada permintaan izin sebelumnya
$sql_check = "SELECT id FROM trainer_access WHERE trainer_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $trainer_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Jika sudah ada permintaan, set kembali approved_by_pemadam menjadi 0 (permintaan ulang)
    $update_sql = "UPDATE trainer_access SET approved_by_pemadam = 0 WHERE trainer_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("i", $trainer_id);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    // Jika belum ada permintaan, tambahkan permintaan baru
    $insert_sql = "INSERT INTO trainer_access (trainer_id, approved_by_pemadam) VALUES (?, 0)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("i", $trainer_id);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();

header("Location: trainer.php");
exit();
?>
