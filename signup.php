<?php
// signup.php

// Define variables and initialize with empty values
$username = $email = $password = "";
$error_message = "";

// Database connection
$servername = "localhost"; // Change this if your DB is hosted elsewhere
$username_db = "root"; // Database username (default for local MySQL)
$password_db = ""; // Database password (default for local MySQL)
$dbname = "waterfist_db"; // Database name

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and assign inputs to variables
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "Semua kolom harus diisi!";
    } elseif (strlen($password) < 8) { // Periksa panjang password
        $error_message = "Kata sandi harus memiliki minimal 8 karakter.";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT); // Encrypt password

        // Determine the role
        $checkAdminQuery = "SELECT COUNT(*) FROM users WHERE role = 'admin'";
        $result = $conn->query($checkAdminQuery);
        $row = $result->fetch_row();
        $adminCount = $row[0];

        // If there is no admin, set role to 'admin', else 'trainer'
        if ($adminCount == 0) {
            $role = 'admin';
        } else {
            $role = 'trainer';
        }

        // Check if username already exists in the database
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Username already exists, display error message
            $error_message = "Nama pengguna ini sudah terdaftar. Silakan gunakan nama pengguna lain.";
        } else {
            // Check if email already exists in the database
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Email already exists, display error message
                $error_message = "Email ini sudah terdaftar. Silakan gunakan email lain.";
            } else {
                // Email and username do not exist, proceed with insertion
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $password_hashed, $role);

                // Execute the query and check for errors
                if ($stmt->execute()) {
                    // Redirect to login page if registration is successful
                    header("Location: login.php");
                    exit();
                } else {
                    // Error handling for database issues
                    $error_message = "Error: " . $stmt->error;
                }
            }
        }

        // Close the prepared statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
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
                        <li><a href="login.php">Masuk</a></li> 
                        <li><a href="signup.php"class="active text-blue-500">Daftar</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>            
            </div>
        </div>
    </header>

    <main class="main">
  <section class="half-background">
    <div class="background-image"></div>
    <div class="content">
      <div class="w-full max-w-md">
        <!-- Signup Form Card -->
        <form action="signup.php" method="post" id="signupForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
          <!-- Error Message -->
          <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
              <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
          <?php endif; ?>

          <!-- Username Field -->
          <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="username">Nama pengguna</label>
            <input 
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
              id="username" 
              name="username" 
              type="text" 
              placeholder="Ketikkan nama pengguna"
              value="<?php echo htmlspecialchars($username); ?>" 
              required
            >
          </div>

          <!-- Email Field -->
          <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="email">Email</label>
            <input 
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
              id="email" 
              name="email" 
              type="email" 
              placeholder="Ketikkan alamat email"
              value="<?php echo htmlspecialchars($email); ?>" 
              required
            >
          </div>

<!-- Password Field -->
<div class="mb-6">
  <label class="block text-gray-700 font-bold mb-2" for="password">Kata sandi</label>
  <input 
    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
    id="password" 
    name="password" 
    type="password" 
    placeholder="Ketikkan kata sandi"
    value="<?php echo htmlspecialchars($password); ?>" 
    required
  >
</div>


          <!-- Submit Button -->
          <div class="flex items-center justify-between">
            <button 
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
              type="submit"
            >
              Daftar
            </button>
            <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="login.php">
              Sudah memiliki akun? Masuk
            </a>
          </div>
        </form>
        <p class="text-center text-gray-500 text-xs">
          &copy;2024 KBBI All rights reserved.
        </p>
        <p class="text-center text-gray-500 text-xs">
          Akun pertama yang dibuat adalah admin.
        </p>
      </div>
    </div>
  </section>
</main>
  

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></
</body>
</html>
