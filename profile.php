<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Database connection parameters
$host = 'localhost';
$dbname = 'waterfist_db';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the last logged-in user's ID
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

$sql = "SELECT username, email, role, password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($username, $email, $role, $hashedPassword);
$stmt->fetch();
$stmt->close();

// Initialize error message variable
$error = '';

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedUsername = $_POST['username'];
    $updatedEmail = $_POST['email'];

    // Check for duplicate username
    $checkUsernameSql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $checkUsernameStmt = $conn->prepare($checkUsernameSql);
    $checkUsernameStmt->bind_param('si', $updatedUsername, $userId);
    $checkUsernameStmt->execute();
    $checkUsernameStmt->store_result();

    if ($checkUsernameStmt->num_rows > 0) {
        $error = "Username sudah digunakan oleh pengguna lain.";
    } else {
        // Check for duplicate email
        $checkEmailSql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $checkEmailStmt = $conn->prepare($checkEmailSql);
        $checkEmailStmt->bind_param('si', $updatedEmail, $userId);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if ($checkEmailStmt->num_rows > 0) {
            $error = "Email sudah digunakan oleh pengguna lain.";
        } else {
            // Update password if provided
            if (!empty($_POST['new_password'])) {
                $newPassword = $_POST['new_password'];

                // Check if the new password is the same as the old one
                if (password_verify($newPassword, $hashedPassword)) {
                    $error = "Password baru tidak bisa sama dengan password sebelumnya.";
                } else {
                    // Hash the new password and update
                    $updatedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    $updateSql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param('sssi', $updatedUsername, $updatedEmail, $updatedPassword, $userId);
                    $updateStmt->execute();
                    $updateStmt->close();

                    header("Location: login.php");
                    exit();
                }
            } else {
                // Update without password change
                $updateSql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param('ssi', $updatedUsername, $updatedEmail, $userId);
                $updateStmt->execute();
                $updateStmt->close();

                header("Location: login.php");
                exit();
            }
        }
        $checkEmailStmt->close();
    }
    $checkUsernameStmt->close();
}

// Jika pengguna bukan "pemadam", cek apakah sesi masih aktif di tabel `aktif_akun`
if ($role !== 'pemadam') {
    $session_query = "SELECT * FROM aktif_akun WHERE user_id = ? AND role = ?";
    $stmt = $conn->prepare($session_query);
    $stmt->bind_param("is", $userId, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika sesi tidak aktif, redirect ke login.php
    if ($result->num_rows === 0) {
        header("Location: login.php");
        exit();
    }
    // Tutup koneksi setelah pengecekan selesai
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .navbar {
            background-color: #373E97;
            padding: 1rem 2rem;
            width: 100%;
        }

        .profile-container {
            background: #fff;
            padding: 2rem;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
        }

        .profile-info h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .profile-details {
            margin-top: 1.5rem;
            text-align: left;
        }

        .profile-details div {
            margin-bottom: 0.75rem;
        }

        .profile-details label {
            font-weight: bold;
            color: #4b5563;
        }

        .save-button {
            background-color: #10b981;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="waterfistlogo2.png" class="logo" alt="Logo" style="max-width: 30px; margin-right: 10px;">
                <span class="sitename">Waterfist</span>
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <?php
                            $backPage = ($role == 'admin') ? 'admin.php' : (($role == 'trainer') ? 'trainer.php' : 'pemadam.php');
                        ?>
                        <a href="<?= $backPage; ?>" class="btn btn-light">
                            <i class="bi bi-arrow-left-circle"></i> Back
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-light">
                            <a href="logout.php" class="text-decoration-none text-dark">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container d-flex justify-content-center">
        <div class="profile-container">
            <div class="profile-details">
                <form method="POST" action="">
                    <div>
                        <label>Username:</label>
                        <input type="text" id="username" name="username" value="<?= $username; ?>" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label>Email:</label>
                        <input type="email" id="email" name="email" value="<?= $email; ?>" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label>Role:</label>
                        <input type="text" value="<?= $role; ?>" disabled class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label>Password Baru:</label>
                        <input type="password" id="new_password" name="new_password" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="save-button">Save</button>
                    </div>
                    <?php if (!empty($error)): ?>
                        <p class="error-message"><?= $error; ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
