<?php
session_start();

// Pastikan hanya admin yang dapat mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Informasi koneksi database
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "waterfist_db"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan metode permintaan adalah POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ambil data dari form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'pemadam'; // Role default untuk akun yang dibuat admin

    // Validasi input kosong
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua bidang harus diisi.']);
        exit();
    }

    // Periksa apakah username sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan.']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Periksa apakah email sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan.']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Masukkan data ke database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Akun pemadam berhasil dibuat.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat membuat akun.']);
    }
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>
