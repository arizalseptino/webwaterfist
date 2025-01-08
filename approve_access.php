<?php
session_start();

// Pastikan pengguna adalah pemadam
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemadam') {
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

// Memastikan bahwa ada trainer_access_id dan action
if (isset($_POST['trainer_access_id']) && isset($_POST['action'])) {
    $trainer_access_id = $_POST['trainer_access_id'];
    $action = $_POST['action'];

    if ($action === "approve") {
        // Setujui akses trainer
        $approve_sql = "UPDATE trainer_access SET approved_by_pemadam = 1 WHERE id = ?";
        $stmt = $conn->prepare($approve_sql);
        $stmt->bind_param("i", $trainer_access_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = "Akses berhasil disetujui.";

    } elseif ($action === "reject") {
        // Hapus permintaan akses dari database
        $reject_sql = "DELETE FROM trainer_access WHERE id = ?";
        $stmt = $conn->prepare($reject_sql);
        $stmt->bind_param("i", $trainer_access_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = "Akses berhasil ditolak.";
    }
}

header("Location: pemadam.php");
exit();

$conn->close();
?>
<?php
session_start();

// Pastikan pengguna adalah pemadam
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemadam') {
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

if (isset($_POST['trainer_access_id'])) {
    $trainer_access_id = $_POST['trainer_access_id'];

    $update_sql = "UPDATE trainer_access SET approved_by_pemadam = 1 WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $trainer_access_id);

    if ($stmt->execute()) {
        echo "Akses berhasil disetujui.";
    } else {
        echo "Gagal menyetujui akses.";
    }

    $stmt->close();
}

header("Location: pemadam.php");
exit();

$conn->close();
?>
