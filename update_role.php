<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi ke database sudah terhubung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Validasi peran pengguna dan lakukan pembaruan
    $query = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Role berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat memperbarui role.";
    }

    $stmt->close();
    $conn->close();
}

// Redirect kembali ke halaman admin
header("Location: admin.php");
exit();
?>
