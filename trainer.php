<?php
session_start();
// Tambahkan pemeriksaan sesi atau logika PHP lainnya di sini jika diperlukan
// Misalnya: periksa apakah pengguna sudah login atau memiliki akses ke halaman ini

// Contoh pemeriksaan sesi (opsional):
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php"); // Redirect ke halaman login jika belum login atau bukan 'trainer'
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "waterfist_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Memeriksa status persetujuan pemadam untuk akses trainer
$approval_query = "SELECT approved_by_pemadam FROM trainer_access WHERE trainer_id = ?";
$stmt = $conn->prepare($approval_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($approved_by_pemadam);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer - Feedback</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar { background-color: #373E97; padding: 1rem 2rem; }
        .logo-container { display: flex; align-items: center; }
        .logo { max-width: 30px; height: auto; margin-right: 10px; }
        .sitename { color: #ffffff; font-family: 'Montserrat', sans-serif; font-weight: 500; margin-top: 10px; }
        
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
        
            <!-- Simulation Card -->
            <div class="col-md-6 col-lg-6 mb-4">
                <div class="card" style="background-color: #D1DCFF;">
                    <div class="card-header d-flex align-items-center" style="background-color: #D1DCFF;">
                        <i class="bi bi-play-circle card-icon me-2"></i>
                        <h5 class="mb-0">Simulasi Alat</h5>
                    </div>
                    <div class="card-body" style="background-color: #D1DCFF;">
                    <?php if ($approved_by_pemadam): ?>
                        <!-- Jika disetujui, tampilkan tombol akses -->
                        <div class="btn-group mb-3 w-100">
                            <button class="btn btn-outline-primary" onclick="window.location.href='manual.php'">Mulai Simulasi</button>
                        </div>
                    <?php else: ?>
                        <!-- Jika belum disetujui, tampilkan tombol untuk meminta izin -->
                        <p class="text-danger">Akses ke Mode Manual dan Mode Otomatis memerlukan persetujuan dari akun pemadam.</p>
                        <form action="request_permission.php" method="POST">
                            <button type="submit" class="btn btn-primary">Meminta Izin</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
            <!-- Feedback Card -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-chat-square-text card-icon me-2"></i>
                            <h5 class="mb-0">Feedback & Evaluasi</h5>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal" onclick="resetFeedbackForm()">
                            <i class="bi bi-plus-circle"></i> Tambah Catatan
                        </button>
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
                                    <!-- Row feedback lainnya dapat ditambahkan secara dinamis -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">Tambah Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="feedbackForm">
                <div class="mb-3">
                    <label for="feedbackTitle" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="feedbackTitle" required>
                    <div class="invalid-feedback">Judul harus diisi.</div>
                </div>
                <div class="mb-3">
                    <label for="feedbackDescription" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="feedbackDescription" rows="3" required></textarea>
                    <div class="invalid-feedback">Deskripsi harus diisi.</div>
                </div>
                <div class="mb-3">
                    <label for="feedbackDate" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="feedbackDate" required>
                    <div class="invalid-feedback">Tanggal harus diisi.</div>
                </div>
                <input type="hidden" id="feedbackIndex">
            </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveFeedback()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load feedback saat halaman dimuat
document.addEventListener('DOMContentLoaded', loadFeedback);

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
                            <button class="btn btn-sm btn-primary" onclick="editFeedback(this)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteFeedback(this)">
                                <i class="bi bi-trash"></i>
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

function saveFeedback() {
    // Ambil elemen form
    const title = document.getElementById('feedbackTitle');
    const description = document.getElementById('feedbackDescription');
    const date = document.getElementById('feedbackDate');

    // Validasi input
    let valid = true;

    if (!title.value.trim()) {
        title.classList.add('is-invalid');
        valid = false;
    } else {
        title.classList.remove('is-invalid');
    }

    if (!description.value.trim()) {
        description.classList.add('is-invalid');
        valid = false;
    } else {
        description.classList.remove('is-invalid');
    }

    if (!date.value.trim()) {
        date.classList.add('is-invalid');
        valid = false;
    } else {
        date.classList.remove('is-invalid');
    }

    // Jika ada input kosong, hentikan proses
    if (!valid) {
        return;
    }

    // Proses pengiriman data jika valid
    const formData = new FormData();
    formData.append('judul', title.value.trim());
    formData.append('deskripsi', description.value.trim());
    formData.append('tanggal', date.value.trim());

    const id = document.getElementById('feedbackIndex').value;
    if (id) {
        formData.append('action', 'update');
        formData.append('id', id);
    } else {
        formData.append('action', 'create');
    }

    fetch('feedback_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('feedbackModal').querySelector('.btn-close').click();
                loadFeedback();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}


function editFeedback(button) {
    const row = button.closest('tr');
    const id = row.dataset.id;
    const title = row.cells[0].innerText;
    const description = row.cells[1].innerText;
    const date = row.cells[2].innerText;

    document.getElementById('feedbackTitle').value = '';
    document.getElementById('feedbackDescription').value = '';
    document.getElementById('feedbackDate').value = '';
    document.getElementById('feedbackIndex').value = id;
    document.getElementById('feedbackModalLabel').innerText = 'Edit Feedback';
    
    new bootstrap.Modal(document.getElementById('feedbackModal')).show();
}

function deleteFeedback(button) {
    if (confirm('Apakah Anda yakin ingin menghapus feedback ini?')) {
        const row = button.closest('tr');
        const id = row.dataset.id;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        fetch('feedback_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                row.remove();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function resetFeedbackForm() {
    document.getElementById('feedbackTitle').value = '';
    document.getElementById('feedbackDescription').value = '';
    document.getElementById('feedbackDate').value = '';
    document.getElementById('feedbackIndex').value = '';
    document.getElementById('feedbackModalLabel').innerText = 'Tambah Feedback';
}

    </script>

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
    document.addEventListener('DOMContentLoaded', function() {
        function checkTrainerSession() {
            fetch('check_trainer_session.php')
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

        const profileLink = document.querySelector('a[href="profile.php"]');
        if (profileLink) {
            profileLink.addEventListener('click', function(event) {
                event.preventDefault();
                checkTrainerSession();
            });
        }
    });
</script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

