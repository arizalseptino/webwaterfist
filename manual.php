<?php
session_start();

// Pastikan pengguna memiliki hak akses yang sesuai, misalnya sebagai 'trainer'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php");
    exit();
}

// Database connection (ubah sesuai dengan konfigurasi server)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT battery_level, water_level FROM device_status WHERE id = 1";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->execute();
    $stmt->bind_result($battery_level, $water_level);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Query gagal: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Manual</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f0f2f5;
        }

        .navbar {
            background-color: #373E97;
            padding: 1rem 2rem;
        }

        .card {
            margin-bottom: 1.5rem;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f0f2f5;
            padding: 1rem;
        }

        .card-icon {
            font-size: 2rem;
            color: #373E97;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #373E97;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .battery-indicator {
            width: 200px;
            height: 20px;
            background-color: #eee;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .battery-level {
            width: 75%;
            height: 100%;
            background-color: #373E97;
            transition: width 0.3s ease;
        }

        .direction-btn.active {
            background-color: #373E97;
            color: white;
        }

        .schedule-table th, .schedule-table td {
            padding: 12px;
        }

        .feedback-list {
            max-height: 300px;
            overflow-y: auto;
        }
  .logo-container {
    display: flex;
    align-items: center; /* Align items vertically in the center */
}

.logo {
    max-width: 30px; /* Set a maximum width for the logo */
    height: auto; /* Maintain aspect ratio */
    margin-right: 10px; /* Add some space between the logo and the text */
}

  .sitename {
    color: #ffffff; /* Set the text color to navy blue */
    font-family: 'Montserrat', sans-serif; /* Set the font to Montserrat */
    font-weight: 500; 
    margin-top: 10px
  }
  .battery-indicator {
    width: 100%;
    height: 20px;
    background-color: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 10px;
}

.battery-level {
    height: 100%;
    width: 0%;
    background-color: #373E97;
    transition: all 0.3s ease-in-out;
}
.value-box {
    border: 2px solid #373E97; /* Add a border */
    padding: 5px; /* Add some padding */
    border-radius: 5px; /* Round the corners */
    background-color: #ffffff; /* Set a background color */
    display: inline-block; /* Ensure the box wraps around the content */
    margin-top: 5px; /* Add some margin for spacing */
}


.btn-auto, .btn-stop {
    width: 80%; /* Mengurangi lebar tombol */
    padding: 5px; /* Mengurangi ruang di dalam tombol */
    margin-bottom: 10px; /* Memberikan jarak antar tombol */
    background-color: #373E97; /* Warna latar belakang tombol */
    color: white; /* Warna teks tombol */
    border: none; /* Menghilangkan border default */
    border-radius: 5px; /* Membuat sudut tombol melengkung */
    cursor: pointer; /* Mengubah kursor saat hover */
    font-size: 0.9em; /* Mengurangi ukuran font tombol */
}

.btn-auto:hover, .btn-stop:hover {
    background-color: #0056b3; /* Warna latar belakang saat hover */
}

button:disabled {
    background-color: #ccc;
    color: #666;
    cursor: not-allowed;
}
        
        .monitoring-display {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        
        .pulse {
            width: 12px;
            height: 12px;
            background-color: #373E97;
            border-radius: 50%;
            margin-right: 10px;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(55, 62, 151, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(55, 62, 151, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(55, 62, 151, 0);
            }
        }
    
        
        .metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        
        .metric-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: 600;
            color: #373E97;
            margin: 5px 0;
        }
        
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a>
                <div class="logo-container">
                    <img src="waterfistlogo2.png" class="logo">
                    <h1 class="sitename">Waterfist</h1>
                </div>
            </a>
            <div class="ms-auto">
                <button class="btn btn-light me-2">
                    <a href="profile.php" class="text-decoration-none text-dark"><i class="bi bi-person-circle"></i> Profile</a>
                </button>   
                <button class="btn btn-light">
                    <a href="logout.php" class="text-decoration-none text-dark">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </button>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mt-4">
        <!-- Manual Simulation Card -->
        <div class="card mb-4" style="background-color: #ffffff;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Simulasi Mode Manual</h5>
                <a href="trainer.php" class="btn btn-danger">Hentikan Simulasi</a>
            </div>
            <div class="card-body">
                <div class="row">
    <!-- Dashboard Content -->
    <div class="container mt-4">
        <div class="row">
        <div class="col-md-6 col-lg-4 mb-4">
    <div class="card" style="background-color: #D1DCFF;">
        <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
            <i class="bi bi-power card-icon me-2"></i>
            <h5 class="mb-0">Kontrol Alat</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Status Alat:</label>
                <span id="deviceStatusText" class="fw-bold text-danger">Off</span> <!-- Default "Off" -->
            </div>
            <div>
                <label class="switch">
                    <input type="checkbox">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

                    <!-- Battery Status Card -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card" style="background-color: #D1DCFF;">
                            <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
                                <i class="bi bi-battery-charging card-icon me-2"></i>
                                <h5 class="mb-0">Status Baterai</h5>
                            </div>
                            <div class="card-body">
                                <h6 id="batteryText">Baterai: 0%</h6>
                                <div class="battery-indicator">
                                    <div class="battery-level" id="batteryLevel"></div>
                                </div>
                            </div>
                            <div id="batteryMessage" style="display: none; color: red; font-weight: bold; text-align: center; margin: 0 auto;">
    Baterai habis! Silakan isi ulang baterai.
</div>
                        </div>
                    </div>

                    <!-- Water Status Card -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card" style="background-color: #D1DCFF;">
                            <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
                                <i class="bi bi-droplet card-icon me-2"></i>
                                <h5 class="mb-0">Sisa Air</h5>
                            </div>
                            <div class="card-body">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>0 L</span>
                                    <span>100 L</span>
                                    <span>200 L</span>
                                </div>
                            </div>
                            <div id="waterMessage" style="display: none; color: red; font-weight: bold; text-align: center; margin: 0 auto;">
    Air habis! Silakan isi ulang tangki air.
</div>
                        </div>
                    </div>

                    <!-- Direction Control Card -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card" style="background-color: #D1DCFF;">
                            <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
                                <i class="bi bi-arrow-left-right card-icon me-2"></i>
                                <h5 class="mb-0">Arah Semprotan</h5>
                            </div>
                            <div class="card-body">
                                <div class="btn-group w-100">
                                    <button class="btn btn-outline-primary direction-btn">
                                        <i class="bi bi-arrow-left"></i> Kiri
                                    </button>
                                    <button class="btn btn-outline-primary direction-btn active">
                                        <i class="bi bi-arrow-down"></i> Tengah
                                    </button>
                                    <button class="btn btn-outline-primary direction-btn">
                                        <i class="bi bi-arrow-right"></i> Kanan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

<!-- Speed Control Card -->
<div class="col-md-6 col-lg-4">
    <div class="card" style="background-color: #D1DCFF;">
        <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
            <i class="bi bi-speedometer2 card-icon me-2"></i>
            <h5 class="mb-0">Kecepatan Air</h5>
        </div>
        <div class="card-body">
            <input type="range" class="form-range" min="1" max="5" id="speedRange" disabled>
            <div class="d-flex justify-content-between">
                <span>1</span>
                <span id="speedValue" class="value-box">3</span>
                <span>5</span>
            </div>
            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="speedCheckbox">
                <label class="form-check-label" for="speedCheckbox">
                    Aktifkan Kecepatan Air
                </label>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6 col-lg-4">
    <div class="card" style="background-color: #D1DCFF;">
        <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
            <i class="bi bi-arrow-clockwise card-icon me-2"></i>
            <h5 class="mb-0">Reset Air & Baterai</h5>
        </div>
        <div class="card-body text-center">
        <button class="btn btn-primary" id="resetDevice" onclick="resetDevice()">Reset ke 100%</button>
        </div>
    </div>
</div>

        <!-- Automatic Simulation Card -->
        <div class="card" style="background-color: #ffffff;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Simulasi Mode Otomatis</h5>
                <a href="trainer.php" class="btn btn-danger">Hentikan Simulasi</a>
            </div>
            <div class="card-body">
                <div class="row">
                <!-- Pemadam otomatis -->
                <div class="col-md-6 col-lg-4">
                <div class="card" style="background-color: #D1DCFF;">
                    <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
                        <i class="bi bi-speedometer2 card-icon me-2"></i>
                        <h5 class="mb-0">Pemadam Otomatis</h5>
                    </div>
                    <div class="card-body">
                    <div class="metric-card">
                    <div class="metric-card">
                    <div class="monitoring-container">
    <button class="btn-auto" id="startMonitoring" onclick="toggleMonitoring()">Mulai Pemadaman Otomatis</button>
    <button class="btn-stop" id="stopMonitoring" onclick="stopAndShowReport()">Hentikan Pemadaman</button>


</div>
</div>


                <!-- Monitoring Display -->
                <div id="monitoringDisplay" class="monitoring-display">
                    <h3>Status Monitoring</h3>
                    <div class="status-indicator">
                        <span id="systemStatus"></span>
                    </div>
                    <div class="progress-bar">
                        <div id="progressBar" class="progress"></div>
                    </div>
                    <div class="metrics">
                        <div class="metric-card">
                            <div>Durasi</div>
                            <div id="duration" class="metric-value">0 menit</div>
                        </div>
                        <div class="metric-card">
                            <div>Arah Air</div>
                            <div id="direction" class="metric-value">Kiri</div>
                        </div>
                        <div class="metric-card">
                            <div>Kecepatan Air</div>
                            <div id="speed" class="metric-value">0 L/min</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

       

<script>
  let timeout;

  function resetTimer() {
    clearTimeout(timeout);
    // Set waktu timeout ke 30 detik (30000 ms)
    timeout = setTimeout(logout, 30000);
  }

  function logout() {
    alert("Anda telah logout karena tidak ada aktivitas.");
    window.location.href = "/kbbi/logout.php"; // Arahkan ke halaman logout PHP
  }

  // Setiap aktivitas pengguna akan mereset timer
  document.onmousemove = resetTimer;
  document.onkeypress = resetTimer;
  document.onclick = resetTimer;
  document.onscroll = resetTimer;

  resetTimer(); // Mulai timer saat halaman dimuat

</script>

<script>
        ///// Toggle active state for direction buttons
        const directionBtns = document.querySelectorAll('.direction-btn');
        directionBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                directionBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

        // Update speed value display
        const speedRange = document.getElementById('speedRange');
        const speedValue = document.getElementById('speedValue');
        speedRange.addEventListener('input', (e) => {
            speedValue.textContent = e.target.value;
        });
    </script>
    
    <script>
        // Fungsi untuk mengirimkan status kontrol lengkap ke control.php
        function updateControl() {
            const isOn = document.querySelector('input[type="checkbox"]').checked ? 'on' : 'off';
            const direction = document.querySelector('.direction-btn.active').textContent.trim().toLowerCase();
            const speed = document.getElementById('speedRange').value;

            if (isOn === 'off') {
                alert("Alat harus dalam keadaan ON untuk melakukan kontrol dan pemadaman otomatis.");
                stopMonitoring();
                return;
            }

            fetch('control.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    status: isOn,
                    direction: direction,
                    speed: speed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Kontrol alat berhasil diperbarui!");
                } else {
                    console.error("Gagal memperbarui kontrol:", data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }

        // Event listener untuk tombol on/off
        document.querySelector('input[type="checkbox"]').addEventListener('change', function() {
            updateControl(); // Memanggil fungsi updateControl saat tombol on/off berubah
        });

        // Event listener untuk tombol arah semprotan
        document.querySelectorAll('.direction-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Menghapus kelas 'active' dari semua tombol arah
                document.querySelectorAll('.direction-btn').forEach(btn => btn.classList.remove('active'));
                // Menambahkan kelas 'active' pada tombol yang diklik
                this.classList.add('active');

                updateControl(); // Memanggil fungsi updateControl saat arah semprotan berubah
            });
        });

        // Event listener untuk slider kecepatan air
        document.getElementById('speedRange').addEventListener('input', function() {
            document.getElementById('speedValue').textContent = this.value; // Tampilkan nilai kecepatan
            updateControl(); // Memanggil fungsi updateControl saat kecepatan air berubah
        });

        // Fungsi untuk mendapatkan status baterai dan air dari status.php
        function updateStatus() {
            fetch('status.php')
                .then(response => response.json())
                .then(data => {
                    // Update status baterai
                    document.getElementById('batteryText').textContent = `Baterai: ${data.battery}%`;
                    document.getElementById('batteryLevel').style.width = `${data.battery}%`;

                    // Update status air
                    const waterLevel = data.water;
                    const waterVolume = data.waterVolume; // Besaran air dalam liter

                    document.querySelector('.progress-bar').style.width = `${waterLevel}%`;
                    document.querySelector('.progress-bar').setAttribute('aria-valuenow', waterLevel);
                    document.getElementById('waterText').textContent = `Air: ${waterVolume} L`;
                })
                .catch(error => console.error("Error:", error));
        }

        // Interval untuk memperbarui status baterai dan air setiap 5 detik
        setInterval(updateStatus, 1000);
        updateStatus(); // Panggil saat pertama kali
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        //pemadam otomatis
        let isMonitoring = false;
        let progressInterval;
        let metricsInterval;
    
        // Initialize starting values
        let duration = 0; // Starting duration in minutes
        let speed = 1.0; // Starting speed in L/min
        let direction = 'Kiri'; // Starting direction
    
        function toggleMonitoring() {
    const display = document.getElementById('monitoringDisplay');
    const isOn = document.querySelector('input[type="checkbox"]').checked;

    if (!isOn) {
        alert("Alat harus dalam keadaan ON untuk memulai pemadaman otomatis.");
        return;
    }

    isMonitoring = !isMonitoring;

    const speedCheckbox = document.getElementById('speedCheckbox');

    if (isMonitoring) {
        display.style.display = 'block'; // Tampilkan tampilan monitoring
        speedCheckbox.checked = false; // Nonaktifkan dan matikan "Aktifkan Kecepatan Air"
        speedCheckbox.disabled = true; // Nonaktifkan kontrolnya
        startMonitoring(); // Mulai monitoring
    } else {
        display.style.display = 'none'; // Sembunyikan tampilan monitoring
        speedCheckbox.disabled = false; // Aktifkan kembali kontrolnya
        stopMonitoring(); // Hentikan monitoring
    }
}

        let autoModeInterval; // Interval untuk pengurangan air di mode otomatis

// Update tombol reset berdasarkan status fitur
function updateResetButton() {
    const resetButton = document.getElementById('resetDevice');
    const speedCheckbox = document.getElementById('speedCheckbox'); // Checkbox Aktifkan Kecepatan Air
    const isDeviceOn = document.querySelector('input[type="checkbox"]').checked; // Status alat
    const isMonitoringActive = isMonitoring; // Status Pemadaman Otomatis
    const openSpeedControlButton = document.getElementById('openSpeedControl'); // Tombol buka pengaturan

    // Nonaktifkan tombol reset jika alat menyala
    if (isDeviceOn) {
        resetButton.disabled = true;
        return;
    }

    // Aktifkan tombol reset jika pengaturan kecepatan air telah dibuka
    if (openSpeedControlButton.style.display === 'none') {
        resetButton.disabled = false;
        return;
    }

    // Nonaktifkan tombol reset jika salah satu fitur aktif
    resetButton.disabled = speedCheckbox.checked || isMonitoringActive || !isDeviceOn;
}



// Listener untuk perubahan checkbox Aktifkan Kecepatan Air
document.getElementById('speedCheckbox').addEventListener('change', updateResetButton);

// Listener untuk tombol alat (ON/OFF)
document.querySelector('input[type="checkbox"]').addEventListener('change', updateResetButton);

// Update tombol reset saat memulai atau menghentikan pemadaman otomatis
function toggleMonitoring() {
    const display = document.getElementById('monitoringDisplay');
    const systemStatus = document.getElementById('systemStatus'); // Elemen teks status
    const isOn = document.querySelector('input[type="checkbox"]').checked;

    // Periksa apakah alat dalam status "On"
    if (!isOn) {
        alert("Alat harus dalam keadaan ON untuk memulai pemadaman otomatis.");
        systemStatus.textContent = "Sistem Tidak Aktif"; // Ubah teks status
        systemStatus.classList.remove("text-success");
        systemStatus.classList.add("text-danger");
        return;
    }

    isMonitoring = !isMonitoring;

    const speedCheckbox = document.getElementById('speedCheckbox');

    if (isMonitoring) {
        display.style.display = 'block'; // Tampilkan tampilan monitoring
        speedCheckbox.checked = false; // Nonaktifkan dan matikan "Aktifkan Kecepatan Air"
        speedCheckbox.disabled = true; // Nonaktifkan kontrolnya
        systemStatus.textContent = ""; // Ubah teks status
        systemStatus.classList.remove("text-danger");
        systemStatus.classList.add("text-success");
        startMonitoring(); // Mulai monitoring
    } else {
        display.style.display = 'none'; // Sembunyikan tampilan monitoring
        speedCheckbox.disabled = false; // Aktifkan kembali kontrolnya
        systemStatus.textContent = "Sistem Tidak Aktif"; // Ubah teks status
        systemStatus.classList.remove("text-success");
        systemStatus.classList.add("text-danger");
        stopMonitoring(); // Hentikan monitoring
    }
}



function startMonitoring() {
    stopMonitoring(); // Pastikan tidak ada interval ganda

    let duration = 0; // Durasi dalam menit
    let speed = 2.0; // Kecepatan awal dalam L/min

    autoModeInterval = setInterval(() => {
        // Cek status alat
        const isOn = document.querySelector('input[type="checkbox"]').checked;
        if (!isOn) {
            alert("Alat dimatikan! Pemadaman otomatis dihentikan.");
            stopMonitoring(); // Hentikan pemadaman otomatis jika alat dimatikan
            return;
        }

        fetch('update_water.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ speed: 2 }) // Kurangi air sebanyak 2 setiap detik
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const waterLevel = data.water_level;

                    // Perbarui UI dengan level air baru
                    document.getElementById('waterText').textContent = `Sisa Air: ${waterLevel}%`;
                    document.getElementById('waterLevel').style.width = `${waterLevel}%`;

                    // Jika air habis, hentikan mode otomatis
                    if (waterLevel <= 0) {
                        alert("Sisa air habis! Mode otomatis dimatikan.");
                        stopMonitoring();
                    }
                } else {
                    console.error("Error:", data.message);
                }
            })
            .catch(error => console.error("Error:", error));
    }, 1000); // Interval 1 detik

    // Tambahkan interval untuk memperbarui durasi dan status lainnya
    metricsInterval = setInterval(() => {
        duration += 1; // Tambahkan durasi 1 menit
        const directions = ['Kiri', 'Kanan', 'Tengah'];
        direction = directions[Math.floor(Math.random() * directions.length)];
        speed += 0.5; // Tambahkan kecepatan secara bertahap

        // Perbarui UI
        document.getElementById('duration').textContent = `${duration} menit`;
        document.getElementById('direction').textContent = direction;
        document.getElementById('speed').textContent = `${speed.toFixed(1)} L/min`;
    }, 2000); // Perbarui setiap 2 detik
}


function stopMonitoring() {
    clearInterval(autoModeInterval); // Hentikan interval pengurangan air
    clearInterval(metricsInterval); // Hentikan interval pembaruan lainnya
    isMonitoring = false;
}

    
    </script>

    <script>
document.querySelector('input[type="checkbox"]').addEventListener('change', function () {
    const status = this.checked ? 'on' : 'off';

    fetch('update_device_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status }) // Kirim status ke server
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log(data.message);

                // Jika status "on", mulai pengurangan baterai
                if (status === 'on') {
                    startBatteryDrain();
                } else {
                    stopBatteryDrain();
                }
            } else {
                console.error("Error:", data.error);
            }
        })
        .catch(error => console.error("Error:", error));
});

// Fungsi untuk mengurangi baterai saat alat ON
let batteryInterval = null;

function startBatteryDrain() {
    batteryInterval = setInterval(() => {
        fetch('update_battery.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const batteryLevel = data.battery_level;

                    // Perbarui level baterai di UI
                    document.getElementById('batteryText').textContent = `Baterai: ${batteryLevel}%`;
                    document.getElementById('batteryLevel').style.width = `${batteryLevel}%`;

                    // Jika baterai habis, otomatis matikan alat
                    if (batteryLevel <= 0) {
                        alert("Baterai habis! Alat akan dimatikan.");
                        clearInterval(batteryInterval); // Hentikan pengurangan
                        toggleDeviceStatus(false); // Ubah status alat ke "off"
                    }
                }
            })
            .catch(error => console.error("Error:", error));
    }, 5000); // Kurangi setiap 5 detik
}

function stopBatteryDrain() {
    clearInterval(batteryInterval);
}
    </script>

<script>
let waterInterval;

document.getElementById('speedCheckbox').addEventListener('change', function () {
    const isSpeedChecked = this.checked;
    const speedRange = document.getElementById('speedRange');
    const resetButton = document.getElementById('resetDevice');
    const deviceCheckbox = document.querySelector('input[type="checkbox"]'); // Checkbox alat

    // Aktifkan atau nonaktifkan slider kecepatan
    speedRange.disabled = !isSpeedChecked;

    // Nonaktifkan tombol reset jika kecepatan air diaktifkan
    resetButton.disabled = isSpeedChecked;

    // Jika alat dalam keadaan mati, tombol reset tetap nonaktif
    if (!deviceCheckbox.checked) {
        resetButton.disabled = true;
    }
});


// Event listener untuk checkbox "Alat" (ON/OFF)
document.querySelector('input[type="checkbox"]').addEventListener('change', function () {
    const resetButton = document.getElementById('resetDevice');
    const isDeviceOn = this.checked; // Status alat ON jika checkbox dicentang

    // Hanya aktifkan tombol reset jika alat mati (OFF)
    resetButton.disabled = isDeviceOn; // Disable tombol reset jika alat menyala
});

// Fungsi reset perangkat
function resetDevice() {
    fetch('reset_device.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Baterai dan air berhasil di-reset ke 100%!");
            
            // Perbarui UI setelah reset
            document.getElementById('batteryText').textContent = "Baterai: 100%";
            document.getElementById('batteryLevel').style.width = "100%";
            document.querySelector('.progress-bar').style.width = "100%";
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', "100");
            document.getElementById('waterText').textContent = "Air: 100%";
        } else {
            alert("Gagal mereset perangkat: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Perbarui tombol reset saat halaman dimuat
document.addEventListener('DOMContentLoaded', function () {
    const resetButton = document.getElementById('resetDevice');
    const isDeviceOn = document.querySelector('input[type="checkbox"]').checked;

    // Pastikan tombol reset hanya aktif jika alat mati (OFF)
    resetButton.disabled = isDeviceOn;
});



// Fungsi untuk memulai pengurangan water level
function startWaterReduction() {
    stopWaterReduction(); // Hentikan proses sebelumnya jika ada

    waterInterval = setInterval(() => {
        const speed = Math.max(1, Math.min(100, document.getElementById('speedRange').value)); // Validasi rentang 1-100

        fetch('update_water.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ speed })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const waterLevel = data.water_level;

                // Update UI dengan level air baru
                document.getElementById('waterText').textContent = `Sisa Air: ${waterLevel}%`;
                document.getElementById('waterLevel').style.width = `${waterLevel}%`;

                // Hentikan jika air habis
                if (waterLevel <= 0) {
                    alert("Sisa air habis! Alat otomatis dimatikan.");
                    stopWaterReduction();
                }
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }, 1000); // Jalankan setiap 1 detik
}

// Fungsi untuk menghentikan pengurangan water level
function stopWaterReduction() {
    clearInterval(waterInterval);
}
</script>

<script>
let waterSpeedInterval;

// Event listener untuk checkbox "Aktifkan Kecepatan Air"
document.getElementById('speedCheckbox').addEventListener('change', function () {
    const isSpeedChecked = this.checked;
    const speedRange = document.getElementById('speedRange');
    const deviceCheckbox = document.querySelector('input[type="checkbox"]'); // Checkbox alat
    const startMonitoringBtn = document.getElementById('startMonitoring');
    const stopMonitoringBtn = document.getElementById('stopMonitoring');

    // Aktifkan atau nonaktifkan slider kecepatan
    speedRange.disabled = !isSpeedChecked;

    // Jika kecepatan air diaktifkan, hentikan pemadaman otomatis
    if (isSpeedChecked) {
        stopMonitoring();
        alert("Jika Pemadaman otomatis menyala maka akan dimatikan karena 'Aktifkan Kecepatan Air' telah dicentang.");
    }

    // Kunci atau buka kontrol pemadaman otomatis berdasarkan checkbox kecepatan air
    if (isSpeedChecked) {
        startMonitoringBtn.disabled = true;
        stopMonitoringBtn.disabled = true;
    } else {
        startMonitoringBtn.disabled = false;
        stopMonitoringBtn.disabled = false;
    }
});


// Event listener untuk checkbox "Alat" (ON/OFF)
document.querySelector('input[type="checkbox"]').addEventListener('change', function () {
    const isDeviceOn = this.checked;
    const isSpeedChecked = document.getElementById('speedCheckbox').checked;

    if (isDeviceOn && isSpeedChecked) {
        startWaterSpeedReduction(); // Jalankan pengurangan water_level jika alat dan kecepatan aktif
    } else {
        stopWaterSpeedReduction(); // Hentikan jika salah satu dinonaktifkan
    }
});

// Fungsi untuk memulai pengurangan water level secara perlahan
function startWaterSpeedReduction() {
    stopWaterSpeedReduction(); // Pastikan interval tidak bertumpuk

    waterSpeedInterval = setInterval(() => {
        const speed = Math.max(1, Math.min(100, document.getElementById('speedRange').value)); // Validasi rentang 1-100

        fetch('update_water_speed.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ speed })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const waterLevel = data.water_level;

                // Perbarui UI dengan level air baru
                document.getElementById('waterText').textContent = `Sisa Air: ${waterLevel}%`;
                document.getElementById('waterLevel').style.width = `${waterLevel}%`;

                // Hentikan jika air habis
                if (waterLevel <= 0) {
                    alert("Sisa air habis! Fitur 'Kecepatan Air' dimatikan.");
                    stopWaterSpeedReduction();
                }
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }, 2000); // Jalankan setiap 2 detik (perlahan)
}

// Fungsi untuk menghentikan pengurangan water level
function stopWaterSpeedReduction() {
    clearInterval(waterSpeedInterval);
}
</script>

<script>
function openSpeedControl() {
    const speedCheckbox = document.getElementById('speedCheckbox');
    const speedRange = document.getElementById('speedRange');
    const resetButton = document.getElementById('resetDevice');

    // Aktifkan akses ke checkbox dan slider tanpa mencentang checkbox
    speedCheckbox.disabled = false;
    speedRange.disabled = !speedCheckbox.checked; // Slider hanya aktif jika checkbox dicentang

    // Nonaktifkan tombol reset jika kecepatan air diaktifkan
    resetButton.disabled = speedCheckbox.checked;

    // Sembunyikan tombol setelah akses dibuka
    document.getElementById('openSpeedControl').style.display = 'none';

    alert("Pengaturan Kecepatan Air telah dibuka!");
}


function stopAndShowReport() {
    stopMonitoring(); // Hentikan pemantauan

    // Tampilkan tombol untuk membuka pengaturan Aktifkan Kecepatan Air
    document.getElementById('openSpeedControl').style.display = 'block';

    // Siapkan laporan
    const report = `Report Hasil Mode Otomatis:\nDurasi: ${duration} menit\nArah Air: ${direction}\nKecepatan: ${speed.toFixed(1)} L/min`;
    document.getElementById('reportContent').textContent = report;

    // Tampilkan modal laporan
    const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
    reportModal.show();
}

</script>

<script>
function resetDevice() {
    fetch('reset_device.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Baterai dan air berhasil di-reset ke 100%!");
            // Perbarui UI
            document.getElementById('batteryText').textContent = "Baterai: 100%";
            document.getElementById('batteryLevel').style.width = "100%";
            document.querySelector('.progress-bar').style.width = "100%";
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', "100");
            document.getElementById('waterText').textContent = "Air: 100%";

            // Nonaktifkan tombol reset jika alat menyala
            const resetButton = document.getElementById('resetDevice');
            const deviceCheckbox = document.querySelector('input[type="checkbox"]'); // Checkbox alat
            resetButton.disabled = deviceCheckbox.checked;
        } else {
            alert("Gagal mereset perangkat: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Event listener untuk checkbox "Alat" (ON/OFF)
document.querySelector('input[type="checkbox"]').addEventListener('change', function () {
    const resetButton = document.getElementById('resetDevice');
    resetButton.disabled = this.checked; // Disable tombol reset jika alat menyala
});
</script>

<script>
    let waterReductionInterval = null;

// Fungsi untuk memulai pengurangan water_level
function startWaterReduction() {
    stopWaterReduction(); // Pastikan tidak ada interval ganda

    waterReductionInterval = setInterval(() => {
        const speed = parseInt(document.getElementById('speedRange').value, 10);

        fetch('update_water.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ speed }) // Kirim kecepatan yang dipilih
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const waterLevel = data.water_level;

                // Perbarui UI dengan water_level baru
                document.getElementById('waterText').textContent = `Sisa Air: ${waterLevel}%`;
                document.getElementById('waterLevel').style.width = `${waterLevel}%`;

                // Jika air habis, hentikan pengurangan
                if (waterLevel <= 0) {
                    alert("Sisa air habis! Pengurangan dihentikan.");
                    stopWaterReduction();
                }
            } else {
                console.error("Gagal memperbarui water level:", data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }, 1000); // Jalankan setiap 1 detik
}

// Fungsi untuk menghentikan pengurangan water_level
function stopWaterReduction() {
    clearInterval(waterReductionInterval);
}

// Event listener untuk checkbox "Aktifkan Kecepatan Air" dan status alat
document.getElementById('speedCheckbox').addEventListener('change', handleWaterReduction);
document.querySelector('input[type="checkbox"]').addEventListener('change', handleWaterReduction);

// Fungsi utama untuk mengatur pengurangan air
function handleWaterReduction() {
    const isDeviceOn = document.querySelector('input[type="checkbox"]').checked; // Status alat ON/OFF
    const isSpeedActive = document.getElementById('speedCheckbox').checked; // Checkbox aktif/tidak

    if (isDeviceOn && isSpeedActive) {
        startWaterReduction(); // Jalankan pengurangan jika keduanya aktif
    } else {
        stopWaterReduction(); // Hentikan pengurangan jika salah satu mati
    }
}

</script>

<script>
    function updateStatus() {
    fetch('status.php')
        .then(response => response.json())
        .then(data => {
            // Update status baterai
            const batteryLevel = data.battery;
            document.getElementById('batteryText').textContent = `Baterai: ${batteryLevel}%`;
            document.getElementById('batteryLevel').style.width = `${batteryLevel}%`;

            // Tampilkan pesan jika baterai habis
            const batteryMessage = document.getElementById('batteryMessage');
            if (batteryLevel <= 0) {
                batteryMessage.style.display = 'block'; // Tampilkan pesan
            } else {
                batteryMessage.style.display = 'none'; // Sembunyikan pesan
            }

            // Update status air (jika diperlukan)
            const waterLevel = data.water;
            document.querySelector('.progress-bar').style.width = `${waterLevel}%`;
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', waterLevel);
            document.getElementById('waterText').textContent = `Air: ${data.waterVolume} L`;
        })
        .catch(error => console.error("Error:", error));
}

setInterval(updateStatus, 5000); // Panggil setiap 5 detik
updateStatus(); // Panggil saat pertama kali

</script>

<script>
function updateStatus() {
    fetch('status.php')
        .then(response => response.json())
        .then(data => {
            console.log(data); // Tambahkan log untuk debugging
            const waterLevel = data.water;

            const waterMessage = document.getElementById('waterMessage');
            if (waterLevel <= 0) {
                waterMessage.style.display = 'block'; // Tampilkan pesan
            } else {
                waterMessage.style.display = 'none'; // Sembunyikan pesan
            }
        })
        .catch(error => console.error("Error:", error));
}


setInterval(updateStatus, 5000); // Panggil setiap 5 detik
updateStatus(); // Panggil saat pertama kali
</script>

<script>
// Fungsi untuk memperbarui status perangkat
function updateStatus() {
    fetch('status.php')
        .then(response => response.json())
        .then(data => {
            // Update status baterai
            const batteryLevel = data.battery;
            document.getElementById('batteryText').textContent = `Baterai: ${batteryLevel}%`;
            document.getElementById('batteryLevel').style.width = `${batteryLevel}%`;

            // Tampilkan pesan jika baterai habis
            const batteryMessage = document.getElementById('batteryMessage');
            const speedCheckbox = document.getElementById('speedCheckbox');
            const resetButton = document.getElementById('resetDevice');
            const deviceCheckbox = document.querySelector('input[type="checkbox"]');

            if (batteryLevel <= 0) {
                batteryMessage.style.display = 'block'; // Tampilkan pesan

                // Nonaktifkan checkbox "Aktifkan Kecepatan Air"
                speedCheckbox.checked = false; // Hapus centang
                speedCheckbox.disabled = true; // Nonaktifkan kontrol

                // Hentikan pengurangan water_level jika baterai habis
                stopWaterReduction();

                // Pastikan tombol reset aktif jika baterai habis
                resetButton.disabled = !deviceCheckbox.checked; // Hanya aktif jika alat OFF
            } else {
                batteryMessage.style.display = 'none'; // Sembunyikan pesan

                // Aktifkan kembali checkbox jika baterai > 0
                speedCheckbox.disabled = false;
                resetButton.disabled = deviceCheckbox.checked; // Nonaktifkan tombol reset jika alat ON
            }

            // Update status air
            const waterLevel = data.water;
            document.querySelector('.progress-bar').style.width = `${waterLevel}%`;
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', waterLevel);
            document.getElementById('waterText').textContent = `Air: ${data.waterVolume} L`;

            // Tampilkan pesan jika air habis
            const waterMessage = document.getElementById('waterMessage');
            if (waterLevel <= 0) {
                waterMessage.style.display = 'block';
            } else {
                waterMessage.style.display = 'none';
            }
        })
        .catch(error => console.error("Error:", error));
}

// Fungsi reset perangkat
function resetDevice() {
    fetch('reset_device.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Baterai dan air berhasil di-reset ke 100%!");

            // Perbarui UI setelah reset
            document.getElementById('batteryText').textContent = "Baterai: 100%";
            document.getElementById('batteryLevel').style.width = "100%";
            document.querySelector('.progress-bar').style.width = "100%";
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', "100");
            document.getElementById('waterText').textContent = "Air: 100%";

            // Aktifkan kembali checkbox "Aktifkan Kecepatan Air"
            const speedCheckbox = document.getElementById('speedCheckbox');
            speedCheckbox.disabled = false;

            // Nonaktifkan tombol reset jika alat menyala
            const resetButton = document.getElementById('resetDevice');
            const deviceCheckbox = document.querySelector('input[type="checkbox"]'); // Checkbox alat
            resetButton.disabled = deviceCheckbox.checked;
        } else {
            alert("Gagal mereset perangkat: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Fungsi untuk memulai pengurangan water_level
function startWaterReduction() {
    stopWaterReduction(); // Pastikan tidak ada interval ganda

    waterReductionInterval = setInterval(() => {
        const speed = parseInt(document.getElementById('speedRange').value, 10);

        fetch('update_water.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ speed }) // Kirim kecepatan yang dipilih
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const waterLevel = data.water_level;

                // Perbarui UI dengan water_level baru
                document.getElementById('waterText').textContent = `Sisa Air: ${waterLevel}%`;
                document.getElementById('waterLevel').style.width = `${waterLevel}%`;

                // Jika air habis, hentikan pengurangan
                if (waterLevel <= 0) {
                    alert("Sisa air habis! Pengurangan dihentikan.");
                    stopWaterReduction();
                }
            } else {
                console.error("Gagal memperbarui water level:", data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }, 1000); // Jalankan setiap 1 detik
}

// Fungsi untuk menghentikan pengurangan water_level
function stopWaterReduction() {
    clearInterval(waterReductionInterval);
}

// Event listener untuk checkbox "Aktifkan Kecepatan Air" dan status alat
document.getElementById('speedCheckbox').addEventListener('change', handleWaterReduction);
document.querySelector('input[type="checkbox"]').addEventListener('change', handleWaterReduction);

// Fungsi utama untuk mengatur pengurangan air
function handleWaterReduction() {
    const isDeviceOn = document.querySelector('input[type="checkbox"]').checked; // Status alat ON/OFF
    const isSpeedActive = document.getElementById('speedCheckbox').checked; // Checkbox aktif/tidak

    if (isDeviceOn && isSpeedActive) {
        startWaterReduction(); // Jalankan pengurangan jika keduanya aktif
    } else {
        stopWaterReduction(); // Hentikan pengurangan jika salah satu mati
    }
}

// Interval untuk memperbarui status setiap 5 detik
setInterval(updateStatus, 5000);
updateStatus(); // Panggil saat pertama kali

</script>

<script>
   function updateStatus() {
    fetch('status.php')
        .then(response => response.json())
        .then(data => {
            const batteryLevel = data.battery;
            const deviceCheckbox = document.querySelector('input[type="checkbox"]');
            const resetButton = document.getElementById('resetDevice');
            const speedCheckbox = document.getElementById('speedCheckbox');
            const statusText = document.getElementById('deviceStatusText'); // Elemen untuk status alat

            // Update baterai di UI
            document.getElementById('batteryText').textContent = `Baterai: ${batteryLevel}%`;
            document.getElementById('batteryLevel').style.width = `${batteryLevel}%`;

            // Jika baterai 0, matikan alat dan disable kontrol
            if (batteryLevel <= 0) {
                deviceCheckbox.checked = false; // Matikan alat
                deviceCheckbox.disabled = true; // Disable tombol alat
                speedCheckbox.checked = false; // Matikan kecepatan
                speedCheckbox.disabled = true; // Disable kontrol kecepatan
                resetButton.disabled = false; // Aktifkan tombol reset

                // Update status alat di UI
                statusText.textContent = "Off";
                statusText.classList.remove("text-success");
                statusText.classList.add("text-danger");

                alert("Baterai habis! Silakan tekan tombol reset.");
            } else {
                deviceCheckbox.disabled = false; // Aktifkan tombol alat
                speedCheckbox.disabled = false; // Aktifkan kontrol kecepatan

                // Update status alat di UI jika alat dinyalakan
                if (deviceCheckbox.checked) {
                    statusText.textContent = "On";
                    statusText.classList.remove("text-danger");
                    statusText.classList.add("text-success");
                } else {
                    statusText.textContent = "Off";
                    statusText.classList.remove("text-success");
                    statusText.classList.add("text-danger");
                }
            }
        })
        .catch(error => console.error("Error:", error));
}

// Panggil updateStatus setiap 5 detik
setInterval(updateStatus, 100);
updateStatus(); // Panggil saat pertama kali
 
</script>

</body>
</html>

<?php $conn->close(); ?>
