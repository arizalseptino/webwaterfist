<?php
session_start();

// Periksa apakah pengguna memiliki peran 'pemadam' atau 'admin'
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pemadam', 'admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

// Koneksi database
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal']);
    exit();
}

// Ambil data ID feedback dari permintaan
$feedback_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($feedback_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID feedback tidak valid']);
    exit();
}

// Jika peran pengguna adalah 'pemadam', verifikasi apakah mereka pemilik feedback
if ($_SESSION['role'] === 'pemadam') {
    $sql = "SELECT id FROM feedback WHERE id = ? AND created_by = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $feedback_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk menghapus feedback ini']);
        exit();
    }
    $stmt->close();
}

// Jika peran pengguna adalah 'admin', tidak ada batasan tambahan
// Hapus feedback
$delete_sql = "DELETE FROM feedback WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $feedback_id);
$delete_stmt->execute();

if ($delete_stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Feedback berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus feedback']);
}

$delete_stmt->close();
$conn->close();
?>
