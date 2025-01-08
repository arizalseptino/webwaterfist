<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>WaterFist</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons 
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
-->
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

  <link href="assets/css/main.css" rel="stylesheet">
  <style>
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
    color: #373E97; /* Set the text color to navy blue */
    font-family: 'Montserrat', sans-serif; /* Set the font to Montserrat */
    font-weight: 700; 
    margin-top: 10px
  }
  .member {
  background-color: #f8f9fa; /* Warna latar belakang yang lembut */
  border-radius: 8px;        /* Membuat sudut bulat */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Menambahkan bayangan halus */
}

.member img {
  width: 100px;
  height: 100px;
}

.social a {
  color: #3b5998;
  margin: 0 5px;
  font-size: 1.2em;
}

.social a:hover {
  color: #007bff;
}

.navmenu {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navmenu ul {
  display: flex;
  list-style: none;
}

.navmenu li {
  margin: 0;
}

.mobile-nav-toggle {
  display: none;
  cursor: pointer;
}

@media (max-width: 768px) {
  .navmenu ul {
    display: none;
    flex-direction: column;
    position: absolute;
    top: 60px;
    right: 0;
    background-color: white;
    width: 100%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .navmenu.active ul {
    display: flex;
  }

  .mobile-nav-toggle {
    display: block;
  }
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
            <li><a href="index.php" class="active">Beranda</a></li>
            <li><a href="login.php">Masuk</a></li>
            <li><a href="signup.php">Daftar</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
      </div>

    </div>

  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <img src="pipa.jpg" alt="" data-aos="fade-in">

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-start">
          <div class="col-lg-8">
            <h2>Meningkatkan Keselamatan, Mengurangi Risiko</h2>
            <h3>Solusi Inovatif untuk Pemadam Kebakaran dan Pelatihan Efisien</h3>
            <p>Water Fist adalah sistem berbasis web yang dirancang untuk menghadirkan solusi efisien dalam pengelolaan alat pemadam kebakaran jarak jauh dan pelatihan personel pemadam. Kami memahami tantangan di lapangan yang memerlukan operasi manual dan berisiko, sehingga sistem ini menawarkan cara baru untuk mengatasi situasi darurat dengan aman dan efektif.</p>
            <a href="login.php" class="btn-get-started">Masuk</a>
            <a href="signup.php" class="btn-get-started">Daftar</a>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Tentang Kami<br></span>
        <h2>Tentang Kami<br></h2>
        <p>Waterfist adalah Solusi Inovatif untuk Pemadam Kebakaran dan Pelatihan Efisien</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/about.jpg" class="img-fluid" alt="">
          </div>

          <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
            <h3>Meningkatkan Keselamatan, Mengurangi Risiko</h3>
            <p class="fst-italic">
            Water Fist adalah sebuah website kontrol yang dirancang untuk mengelola sistem semprotan air dalam situasi darurat kebakaran.
Website ini bersifat monitoring dan kontroling. Memonitori sisa baterai dan sisa air. Fungsi Utama kontroli dapat mengaktifkan atau menonaktifkan alat Waterfist, mengatur sudut seprotan air kekanan, kiri, dan tengah. Kontrol kecepatan air juga dapat dilakukan oleh website Waterfist. Sistem ini dirancang dengan antarmuka yang mudah digunakan dan dilengkapi dengan fitur mode pelatihan manual dan otomatis untuk memastikan alat selalu siap digunakan dalam situasi kritis. Tujuan utama dari website ini adalah untuk mempermudah pemadam dalam menangani kebakaran dari luar sehingga meningkatkan efektivitas penanganan kebakaran, dan memastikan kesiapan sistem melalui pemeliharaan dan simulasi yang terjadwal.
           
            </p>
            <ul>
              <li><i class="bi bi-check-circle"></i> <span>Pemadam</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Admin</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Trainer</span></li>
            </ul>
            </div>

        </div>

      </div>
<!--batas batas -->

    </section>

    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Layanan</span>
        <h2>Layanan</h2>
        <p>Waterfist adalah Solusi Inovatif untuk Pemadam Kebakaran dan Pelatihan Efisien</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item  position-relative">
              <div class="icon">
                <i class="bi bi-activity"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Admin</h3>
              </a>
              <p>Mengatur Penjadwalan Pemeliharaan dan mengorganisir pemadam serta trainer.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-broadcast"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Pemadam</h3>
              </a>
              <p>Memonitori sisa baterai dan sisa air, mengaktifkan alat, mengatur sudut seprotan air, kontrol kecepatan air.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-easel"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Trainer</h3>
              </a>
              <p>Melakukan pelatihan alat dan Mencatat feedback serta evaluasi.</p>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->
    
    <!-- Team Section -->
    <section id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Kelompok</span>
        <h2>Kelompok</h2>
        <p>Waterfist-KBBI</p>
      </div><!-- End Section Title -->

<!-- End Team Member -->
<div class="container">
  <div class="row gy-4 justify-content-center">
    
    <!-- Team Member 1 -->
    <div class="col-lg-4 col-md-6 d-flex justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="100">
      <div class="member text-center p-3">
        <img src="foto_arizal2.png" class="img-fluid rounded-circle mb-3" alt="">
        <div class="member-content">
          <h4>Arizal Septino</h4>
          <span>FrontEnd dan BackEnd</span>
          <p>J0404221168</p>
          <div class="social d-flex justify-content-center">
            <a href="https://x.com/arizalseptin0?t=W96Ncy_sh0DZBlVTQGcIfw&s=08 "><i class="bi bi-twitter"></i></a>
            <a href="https://www.instagram.com/arizalseptino"><i class="bi bi-instagram"></i></a>
            <a href="https://www.linkedin.com/in/arizalseptino?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app "><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Team Member 2 -->
    <div class="col-lg-4 col-md-6 d-flex justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="100">
      <div class="member text-center p-3">
        <img src="dopan.jpg" class="img-fluid rounded-circle mb-3" alt="">
        <div class="member-content">
          <h4>Dopan Ermandongi</h4>
          <span>Content Writer</span>
          <p>J0404221110</p>
          <div class="social d-flex justify-content-center">
            <a href=""><i class="bi bi-twitter"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>
    <!-- Team Member 2 -->
    <div class="col-lg-4 col-md-6 d-flex justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="100">
      <div class="member text-center p-3">
        <img src="aryo.jpg" class="img-fluid rounded-circle mb-3" alt="">
        <div class="member-content">
          <h4>Aryo Erlangga Hafiz</h4>
          <span>Content Writer</span>
          <p>J0404221023</p>
          <div class="social d-flex justify-content-center">
            <a href=""><i class="bi bi-twitter"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6 d-flex justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="100">
      <div class="member text-center p-3">
        <img src="zio.jpg" class="img-fluid rounded-circle mb-3" alt="">
        <div class="member-content">
          <h4>Farizky Naufal Febrizio</h4>
          <span>Content Writer</span>
          <p>J0404221046</p>
          <div class="social d-flex justify-content-center">
            <a href=""><i class="bi bi-twitter"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/naufal_zio22/profilecard/?igsh=NjEzMjQ0N2JoanMz"><i class="bi bi-instagram"></i></a>
            <a href="https://www.linkedin.com/in/farizky-naufal-febrizio-95384831b"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>

    
<div class="col-lg-4 col-md-6 d-flex justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="100">
  <div class="member text-center p-3">
    <!-- Image Container -->
    <div class="img-container">
      <img src="hanif.jpg" class="img-fluid rounded-circle mb-3" alt="">
    </div>
    <div class="member-content">
      <h4>Hauzan Hanif Khairullah</h4>
      <span>FrontEnd dan BackEnd</span>
      <p>J0404221106</p>
      <div class="social d-flex justify-content-center">
        <a href="https://facebook.com/hauzan.khairullah" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i></a>
        <a href="https://instagram.com/hauzanhk" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a>
        <a href="https://linkedin.com/in/hauzan-hanif-khairullah-392656302" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </div>
</div>

<!-- CSS to handle image scaling -->
<style>
  .img-container {
    width: 150px;  /* Adjust this to the size you want */
    height: 150px; /* Ensure the image container has fixed dimensions */
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
  }

  .img-container img {
    transition: transform 0.3s ease-in-out; /* Smooth scaling transition */
    object-fit: cover; /* Ensure image covers the container */
  }

  .img-container:hover img {
    transform: scale(1.2); /* Scale image when hovering */
  }
</style>



      </div>

    </section><!-- /Team Section -->


  </main>


  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>



  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const navMenu = document.getElementById('navmenu');
    const toggleButton = document.querySelector('.mobile-nav-toggle');

    toggleButton.addEventListener('click', function() {
      navMenu.classList.toggle('active');
    });
  });
</script>

</body>

</html>