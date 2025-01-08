<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan pengguna sudah login
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Hapus sesi pengguna dari tabel aktif_akun
    $delete_sql = "DELETE FROM aktif_akun WHERE user_id = ? AND role = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("is", $user_id, $role);
    if (!$delete_stmt->execute()) {
        error_log("Error deleting session from aktif_akun: " . $delete_stmt->error);
    }
    $delete_stmt->close();
}

// Pastikan pengguna adalah trainer sebelum mencabut akses otoritas alat
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'trainer') {
    $user_id = $_SESSION['user_id'];

    // Cabut otorisasi alat di tabel trainer_access
    $revoke_access_sql = "UPDATE trainer_access SET approved_by_pemadam = 0 WHERE trainer_id = ?";
    $stmt = $conn->prepare($revoke_access_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Hapus semua data sesi
session_unset();
session_destroy();

// Tutup koneksi database
$conn->close();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>
