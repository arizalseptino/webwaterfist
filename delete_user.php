<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi ke database sudah terhubung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];

    // Ambil role pengguna
    $query = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();

    // Hanya lanjutkan penghapusan jika role bukan admin
    if ($role !== 'admin') {
        $deleteQuery = "DELETE FROM users WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $user_id);

        if ($deleteStmt->execute()) {
            $_SESSION['success'] = "Pengguna berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat menghapus pengguna.";
        }

        $deleteStmt->close();
    } else {
        $_SESSION['error'] = "Tidak dapat menghapus akun admin.";
    }

    $conn->close();
}

// Redirect kembali ke halaman admin
header("Location: admin.php");
exit();
?>
