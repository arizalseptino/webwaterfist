<?php
// Koneksi database langsung di file ini
$host = 'localhost';
$dbname = 'waterfist_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . $e->getMessage()
    ]);
    die();
}

header('Content-Type: application/json');

// Fungsi untuk membersihkan input
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

session_start(); // Memulai sesi untuk mendapatkan user_id

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch($action) {
        case 'create':
            $judul = cleanInput($_POST['judul']);
            $deskripsi = cleanInput($_POST['deskripsi']);
            $tanggal = cleanInput($_POST['tanggal']);
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if (!$user_id) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan dalam sesi'
                ]);
                die();
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO feedback (judul, deskripsi, tanggal, user_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$judul, $deskripsi, $tanggal, $user_id]);
                $id = $pdo->lastInsertId();
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Feedback berhasil ditambahkan',
                    'id' => $id
                ]);
            } catch(PDOException $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan feedback: ' . $e->getMessage()
                ]);
            }
            break;

        case 'update':
            $id = cleanInput($_POST['id']);
            $judul = cleanInput($_POST['judul']);
            $deskripsi = cleanInput($_POST['deskripsi']);
            $tanggal = cleanInput($_POST['tanggal']);
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if (!$user_id) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan dalam sesi'
                ]);
                die();
            }

            // Periksa apakah pengguna memiliki izin untuk memperbarui feedback
            $checkStmt = $pdo->prepare("SELECT user_id FROM feedback WHERE id = ?");
            $checkStmt->execute([$id]);
            $feedback = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$feedback || $feedback['user_id'] != $user_id) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki izin untuk mengedit feedback ini.'
                ]);
                die();
            }

            try {
                $stmt = $pdo->prepare("UPDATE feedback SET judul = ?, deskripsi = ?, tanggal = ? WHERE id = ?");
                $stmt->execute([$judul, $deskripsi, $tanggal, $id]);
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Feedback berhasil diperbarui'
                ]);
            } catch(PDOException $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui feedback: ' . $e->getMessage()
                ]);
            }
            break;

        case 'delete':
            $id = cleanInput($_POST['id']);
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if (!$user_id) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan dalam sesi'
                ]);
                die();
            }

            // Periksa apakah pengguna memiliki izin untuk menghapus feedback
            $checkStmt = $pdo->prepare("SELECT user_id FROM feedback WHERE id = ?");
            $checkStmt->execute([$id]);
            $feedback = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$feedback || $feedback['user_id'] != $user_id) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki izin untuk menghapus feedback ini.'
                ]);
                die();
            }

            try {
                $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
                $stmt->execute([$id]);
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Feedback berhasil dihapus'
                ]);
            } catch(PDOException $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal menghapus feedback: ' . $e->getMessage()
                ]);
            }
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Query untuk mengambil feedback beserta username dari pembuatnya
        $stmt = $pdo->query("
            SELECT f.id, f.judul, f.deskripsi, f.tanggal, u.username 
            FROM feedback f
            JOIN users u ON f.user_id = u.id
            ORDER BY f.tanggal DESC
        ");
        $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'status' => 'success',
            'data' => $feedback
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengambil data feedback: ' . $e->getMessage()
        ]);
    }
}
?>
