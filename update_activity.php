<?php
session_start();
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'pemadam') {
    $user_id = $_SESSION['user_id'];

    $update_sql = "UPDATE aktif_akun SET last_activity = CURRENT_TIMESTAMP WHERE user_id = ? AND role = 'pemadam'";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $user_id);
    $update_stmt->execute();

    echo "Activity updated";
}
?>
