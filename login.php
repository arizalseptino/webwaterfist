<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

// Membuat koneksi
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk mengeluarkan pengguna yang tidak aktif selama lebih dari 30 detik
function auto_logout_inactive_users($conn) {
    $inactive_duration = 30; // dalam detik
    $sql = "DELETE FROM aktif_akun WHERE (role = 'pemadam' OR role = 'admin' OR role = 'trainer') 
            AND TIMESTAMPDIFF(SECOND, last_activity, NOW()) > ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $inactive_duration);
    $stmt->execute();
    $stmt->close();
}

// Panggil fungsi untuk mengeluarkan pengguna yang tidak aktif
auto_logout_inactive_users($conn);

// Inisialisasi variabel
$username = "";
$error = "";

// Memeriksa jika form login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $session_id = session_id();

    // Mengecek username dari database
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika username ditemukan
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Memeriksa password
        if (password_verify($password, $row['password'])) {
            // Perbarui status alat menjadi 'off' saat login berhasil
            $update_status_sql = "UPDATE device_status SET status = 'off' WHERE id = 1";
            $conn->query($update_status_sql);
            
            // Menyimpan data user ke session
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_id'] = $row['id'];

            // Cek apakah ada pemadam yang aktif saat ini
            $check_pemadam_sql = "SELECT * FROM aktif_akun WHERE role = 'pemadam'";
            $pemadam_result = $conn->query($check_pemadam_sql);

            // Jika pemadam aktif, blokir login admin
            if ($pemadam_result->num_rows > 0 && $row['role'] == 'admin') {
                $error = "Ada akun pemadam yang sedang aktif! Admin tidak dapat login saat ini.";
            } elseif ($pemadam_result->num_rows > 0 && $row['role'] == 'trainer') {
                $error = "Ada akun pemadam yang sedang aktif! Trainer tidak dapat login saat ini.";
            } elseif ($pemadam_result->num_rows > 0 && $row['role'] == 'pemadam') {
                $error = "Akun pemadam lain sudah login! Hanya satu akun pemadam yang boleh aktif.";
            } else {
                // Jika tidak ada pemadam aktif, proses login sesuai role
                if ($row['role'] == 'pemadam') {
                    // Hapus semua sesi `trainer` saat `pemadam` login
                    $delete_trainers_sql = "DELETE FROM aktif_akun WHERE role = 'trainer'";
                    $conn->query($delete_trainers_sql);

                    // Simpan sesi login untuk pemadam ke tabel aktif_akun
                    $insert_sql = "INSERT INTO aktif_akun (user_id, role, session_id, login_time, last_activity) VALUES (?, ?, ?, NOW(), NOW())";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("iss", $row['id'], $row['role'], $session_id);
                    $insert_stmt->execute();

                    header("Location: pemadam.php");
                    exit();
                } elseif ($row['role'] == 'admin' || $row['role'] == 'trainer') {
                    // Memeriksa apakah admin atau trainer sudah login di sesi lain
                    $check_sql = "SELECT * FROM aktif_akun WHERE user_id = ?";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->bind_param("i", $row['id']);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();

                    if ($check_result->num_rows > 0) {
                        $error = "Akun dengan username ini sudah login di perangkat lain!";
                    } else {
                        // Simpan sesi login baru ke tabel aktif_akun
                        $insert_sql = "INSERT INTO aktif_akun (user_id, role, session_id, login_time, last_activity) VALUES (?, ?, ?, NOW(), NOW())";
                        $insert_stmt = $conn->prepare($insert_sql);
                        $insert_stmt->bind_param("iss", $row['id'], $row['role'], $session_id);
                        $insert_stmt->execute();

                        // Redirect sesuai role
                        switch ($row['role']) {
                            case 'admin':
                                header("Location: admin.php");
                                break;
                            case 'trainer':
                                header("Location: trainer.php");
                                break;
                        }
                        exit();
                    }
                }
            }
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.16/tailwind.min.css">
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .half-background {
            display: flex;
            height: 100vh;
            margin: 0;
        }
        .background-image {
            flex: 1;
            background-image: url('fire.jpg');
            background-size: cover;
            background-position: center;
        }
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to right, #F1EAFD, #D1DCFF, #739DFF);
            margin: 0;
        }
        .logo-container {
            display: flex;
            align-items: center;
        }
        .logo {
            max-width: 30px;
            height: auto;
            margin-right: 10px;
        }
        .sitename {
            color: #373E97;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            margin-top: 10px;
        }
    </style>
</head>
<body class="index-page">

    <header id="header" class="header fixed-top">
        <div class="branding d-flex align-items-center">
            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="index.php">
                    <div class="logo-container">
                        <img src="waterfistlogo.png" alt="Logo" class="logo">
                        <h1 class="sitename">Waterfist</h1>
                    </div>
                </a>
                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="login.php" class="active text-blue-500">Masuk</a></li> 
                        <li><a href="signup.php">Daftar</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>            
            </div>
        </div>
    </header>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.16/tailwind.min.css">

    <main class="main">
        <section class="half-background">
            <div class="background-image"></div>
            <div class="content">
                <div class="w-full max-w-md">
                    <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2" for="username">
                                Nama Pengguna
                            </label>
                            <input 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="username" 
                                name="username" 
                                type="text" 
                                placeholder="Ketikkan nama pengguna" 
                                value="<?php echo htmlspecialchars($username); ?>" 
                                required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2" for="password">
                                Kata Sandi
                            </label>
                            <input 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="password" 
                                name="password" 
                                type="password" 
                                placeholder="Ketikkan kata sandi" 
                                required>
                        </div>
                        <div class="flex items-center justify-between">
                            <button 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                                type="submit">
                                Masuk
                            </button>
                        </div>
                        <?php if (!empty($error)): ?>
                            <p class="text-red-500 text-xs italic mt-4"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                    </form>
                    <p class="text-center text-gray-500 text-xs">
          &copy;2024 KBBI All rights reserved.
        </p>
        <p class="text-center text-gray-500 text-xs">
          Tunggu 30 detik, jika mengganti profile.
        </p>
                </div>
            </div>
        </section>
    </main>


    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    
</body>
</html>
