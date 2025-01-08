<?php
session_start();
// Tambahkan logika PHP yang diperlukan di sini, seperti autentikasi pengguna atau cek peran pengguna

// Contoh logika PHP opsional untuk memastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect ke halaman login jika belum login atau bukan admin
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

// Ambil data pengguna dari database
$query = "SELECT id, username, email, role FROM users";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
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
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            margin-top: 10px;
        }

        .table td {
            vertical-align: middle;
        }

        .description-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        .progress-bar {
        background-color: #373E97;
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
                    <a href="profile.php" class="text-decoration-none text-dark"><i class="bi bi-person-circle"></i> Profil</a>
                </button>   
                <button class="btn btn-light">
                    <a href="logout.php" class="text-decoration-none text-dark">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </button>
                              
            </div>
        </div>
    </nav>


    
    <!-- Dashboard Content -->
    <div class="container mt-4">
        <div class="row">


            <!-- Battery Status Card -->
            <div class="col-md-6 col-lg-4">
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
                </div>
            </div>

            <!-- Water Status Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card" style="background-color: #D1DCFF;">
                    <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
                        <i class="bi bi-droplet card-icon me-2"></i> <!-- Ikon air -->
                        <h5 class="mb-0">Sisa Air</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>0 L</span>
                            <span id="speedValue">100 L</span>
                            <span>200 L</span>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Card Buat akun pemadam -->
        <div class="col-md-6 col-lg-4">
        <div class="card" style="background-color: #D1DCFF;">
            <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
                <i class="bi bi-person-plus card-icon me-2"></i>
                <h5 class="mb-0">Buat Akun Pemadam</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                    <i class="bi bi-person-plus"></i> Buat Akun
                </button>
            </div>
            </div>
        </div>

                <!-- Modal for creating a new account -->
                <div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createAccountModalLabel">Buat Akun Pemadam Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="createAccountForm" method="POST" action="create_account.php">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <input type="hidden" name="role" value="pemadam"> <!-- Set role as pemadam -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Buat Akun</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>  

            
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-chat-square-text card-icon me-2"></i>
                            <h5 class="mb-0">Feedback & Evaluasi</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="feedbackTableBody">
                                    <!-- Data akan diisi secara dinamis -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check card-icon me-2"></i>
                            <h5 class="mb-0">Penjadwalan</h5>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal" onclick="resetForm()">
                            <i class="bi bi-plus-circle"></i> Tambah Jadwal
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Durasi</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="scheduleTableBody">
                                    <tr>
                                        <td>2024-03-20</td>
                                        <td>07:00</td>
                                        <td>30 menit</td>
                                        <td class="description-cell">Pengisian air</td>
                                        <td><span class="badge bg-success">Sudah</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="editSchedule(this)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteSchedule(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal (Add/Edit) -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Tambah Jadwal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm">
                        <input type="hidden" id="editIndex" value="-1">
                        <div class="mb-3">
                            <label for="scheduleDate" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="scheduleDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="scheduleTime" class="form-label">Waktu</label>
                            <input type="time" class="form-control" id="scheduleTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="scheduleDuration" class="form-label">Durasi</label>
                            <select class="form-select" id="scheduleDuration" required>
                                <option value="15">15 menit</option>
                                <option value="30">30 menit</option>
                                <option value="45">45 menit</option>
                                <option value="60">60 menit</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="scheduleDescription" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="scheduleDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="scheduleStatus" class="form-label">Status</label>
                            <select class="form-select" id="scheduleStatus" required>
                                <option value="active">Sudah</option>
                                <option value="inactive">Belum</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveSchedule()">Simpan</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap and Custom Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
       // Fungsi untuk mengedit jadwal
function editSchedule(button) {
    const row = button.closest('tr');
    const id = row.getAttribute('data-id');
    const cells = row.cells;
    
    // Update modal title
    document.getElementById('scheduleModalLabel').textContent = 'Edit Jadwal';
    
    // Set form values
    document.getElementById('scheduleDate').value = cells[0].textContent;
    document.getElementById('scheduleTime').value = cells[1].textContent;
    document.getElementById('scheduleDuration').value = parseInt(cells[2].textContent);
    document.getElementById('scheduleDescription').value = cells[3].textContent;
    document.getElementById('scheduleStatus').value = cells[4].querySelector('.badge').classList.contains('bg-success') ? 'active' : 'inactive';
    
    // Set the schedule ID in a hidden input
    document.getElementById('editIndex').value = id;

    // Show modal
    scheduleModal.show();
}

function deleteSchedule(button) {
    const row = button.closest('tr');
    const scheduleId = row.getAttribute('data-id');

    if (confirm("Apakah Anda yakin ingin menghapus jadwal ini?")) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', scheduleId);

        fetch('save_schedule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                row.remove(); // Hapus baris dari tabel
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus jadwal');
        });
    }
}
// Updated saveSchedule function
function saveSchedule() {
    const formData = new FormData();
    const editId = document.getElementById('editIndex').value;
    
    formData.append('date', document.getElementById('scheduleDate').value);
    formData.append('time', document.getElementById('scheduleTime').value);
    formData.append('duration', document.getElementById('scheduleDuration').value);
    formData.append('description', document.getElementById('scheduleDescription').value);
    formData.append('status', document.getElementById('scheduleStatus').value);

    // Check if we're editing or creating
    if (editId !== '-1') {
        formData.append('action', 'update');
        formData.append('id', editId);
    } else {
        formData.append('action', 'create');
    }

    fetch('save_schedule.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            scheduleModal.hide();
            resetForm();
            loadSchedules(); // Reload the table
            // Show success message
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan jadwal');
    });
}

// Updated resetForm function
function resetForm() {
    document.getElementById('scheduleModalLabel').textContent = 'Tambah Jadwal Baru';
    document.getElementById('scheduleForm').reset();
    document.getElementById('editIndex').value = '-1';
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(element => element.remove());
}

// Updated loadSchedules function with proper date and time formatting
function loadSchedules() {
    fetch('save_schedule.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const tableBody = document.getElementById('scheduleTableBody');
                tableBody.innerHTML = '';
                
                data.data.forEach(schedule => {
                    // Format the date and time
                    const formattedDate = new Date(schedule.date).toLocaleDateString('id-ID');
                    const formattedTime = new Date(`2000-01-01T${schedule.time}`).toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-id', schedule.id);
                    tr.innerHTML = `
                        <td>${formattedDate}</td>
                        <td>${formattedTime}</td>
                        <td>${schedule.duration} menit</td>
                        <td class="description-cell">${schedule.description}</td>
                        <td><span class="badge bg-${schedule.status === 'Sudah' ? 'success' : 'secondary'}">${schedule.status}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editSchedule(this)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteSchedule(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            }
        })
        .catch(error => console.error('Error:', error));
}

// Tambahkan event listener untuk form submission
document.addEventListener('DOMContentLoaded', function() {
    scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
    loadSchedules();

    // Prevent form from submitting normally
    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSchedule();
    });
});

// Load feedback saat halaman dimuat
document.addEventListener('DOMContentLoaded', loadFeedback);

    // Fungsi untuk memuat data feedback
    function loadFeedback() {
    fetch('feedback_handler.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const tableBody = document.getElementById('feedbackTableBody');
                tableBody.innerHTML = '';

                data.data.forEach(feedback => {
                    const row = document.createElement('tr');
                    row.dataset.id = feedback.id;
                    row.innerHTML = `
                        <td>${feedback.username}</td>
                        <td>${feedback.judul}</td>
                        <td>${feedback.deskripsi}</td>
                        <td>${feedback.tanggal}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteFeedback(${feedback.id})">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                alert('Gagal memuat data feedback');
            }
        })
        .catch(error => console.error('Error:', error));
}


    // Fungsi untuk menghapus feedback
    function deleteFeedback(id) {
    if (confirm('Apakah Anda yakin ingin menghapus feedback ini?')) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('delete_feedback.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                loadFeedback(); // Refresh tabel feedback
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}


    // Panggil loadFeedback saat halaman dimuat
    document.addEventListener('DOMContentLoaded', loadFeedback);
    </script>

<!-- Tambahkan di bawah bagian "Pengelolaan Role Pengguna" -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pengelolaan Role Pengguna</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td>
                                    <?php if ($row['role'] != 'admin'): ?>
                                        <form method="POST" action="update_role.php" class="d-flex align-items-center">
                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                            <select name="role" class="form-select me-2">
                                                <option value="pemadam" <?php if ($row['role'] == 'pemadam') echo 'selected'; ?>>Pemadam</option>
                                                <option value="trainer" <?php if ($row['role'] == 'trainer') echo 'selected'; ?>>Trainer</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                        </form>
                                    <?php else: ?>
                                        <span>Admin</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['role'] != 'admin'): ?>
                                        <form method="POST" action="delete_user.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
    setInterval(updateStatus, 5000);
    updateStatus(); // Panggil saat pertama kali
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk memeriksa apakah sesi admin masih aktif sebelum mengakses profil
    function checkAdminSession() {
        fetch('check_admin_session.php')
            .then(response => response.json())
            .then(data => {
                if (!data.active) {
                    alert("Sesi Anda telah berakhir. Silakan login kembali.");
                    window.location.href = "login.php";
                } else {
                    window.location.href = "profile.php";
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Pasang event listener pada tautan profil
    const profileLink = document.querySelector('a[href="profile.php"]');
    if (profileLink) {
        profileLink.addEventListener('click', function(event) {
            event.preventDefault();
            checkAdminSession();
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk memeriksa apakah sesi admin masih aktif sebelum mengakses profil
    function checkAdminSession() {
        fetch('check_admin_session.php')
            .then(response => response.json())
            .then(data => {
                if (!data.active) {
                    alert("Sesi Anda telah berakhir. Silakan login kembali.");
                    window.location.href = "login.php";
                } else {
                    window.location.href = "profile.php";
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Pasang event listener pada tautan profil
    const profileLink = document.querySelector('a[href="profile.php"]');
    if (profileLink) {
        profileLink.addEventListener('click', function(event) {
            event.preventDefault();
            checkAdminSession();
        });
    }
});
</script>

<script>
document.getElementById('createAccountForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Mencegah submit normal

    const formData = new FormData(this);

    fetch('create_account.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            location.reload(); // Reload halaman setelah sukses
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat membuat akun.');
    });
});
</script>

<?php
// Tutup koneksi
$conn->close();
?>
</body>
</html>
