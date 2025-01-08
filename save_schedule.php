<?php
// Memulai sesi
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Fungsi untuk membersihkan input
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Set header untuk response JSON
header('Content-Type: application/json');

// Handle POST request untuk menyimpan atau mengupdate jadwal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => 'Unknown action'];
    
    $action = cleanInput($_POST['action'] ?? 'create');
    
    if ($action === 'create' || $action === 'update') {
        // Validasi input
        $date = cleanInput($_POST['date']);
        $time = cleanInput($_POST['time']);
        $duration = (int)cleanInput($_POST['duration']);
        $description = cleanInput($_POST['description']);
        $status = ($_POST['status'] === 'active') ? 'Sudah' : 'Belum';
        
        // Validasi tambahan
        if (empty($date) || empty($time) || empty($description) || $duration <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi dengan benar']);
            exit;
        }

        if ($action === 'create') {
            $stmt = $conn->prepare("INSERT INTO schedules (date, time, duration, description, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $date, $time, $duration, $description, $status);
            $successMessage = 'Jadwal berhasil ditambahkan';
        } else {
            $id = (int)cleanInput($_POST['id']);
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
                exit;
            }

            $stmt = $conn->prepare("UPDATE schedules SET date=?, time=?, duration=?, description=?, status=? WHERE id=?");
            $stmt->bind_param("ssissi", $date, $time, $duration, $description, $status, $id);
            $successMessage = 'Jadwal berhasil diperbarui';
        }

        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => $successMessage,
                'id' => $action === 'create' ? $conn->insert_id : $id
            ];
        } else {
            $response = ['status' => 'error', 'message' => $stmt->error];
        }
        
        $stmt->close();
    }
    // Handle delete
    else if ($action === 'delete') {
        $id = (int)cleanInput($_POST['id']);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Jadwal berhasil dihapus'];
        } else {
            $response = ['status' => 'error', 'message' => $stmt->error];
        }
        
        $stmt->close();
    }
    
    echo json_encode($response);
    exit;
}

// Handle GET request untuk mengambil data jadwal
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT * FROM schedules ORDER BY date, time");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $schedules]);
    
    $stmt->close();
    exit;
}

$conn->close();
?>
