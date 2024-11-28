<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ruangpeduli.css">
    <title>Ruang Peduli</title>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="2.png" alt="Logo" class="logo-img">
            <span class="nama-web">Hai Dokter!!</span>
        </div>
        <nav>
        <ul class="menu">
            <li><a href="dashboard.php">Beranda</a></li>
            <li>
                <a href="#">Layanan</a>
                <ul class="services-dropdown">
                    <li><a href="chatdokter.php">Chat Dokter</a></li>
                    <li><a href="janjidokter.php">Janji Temu</a></li>
                    <li><a href="ruangpeduli.php">Ruang Peduli</a></li>
                </ul>
            </li>
            <li><a href="#">Kontak</a></li>
        </ul>
        </nav>
        <div class="user-info">
            <img src="<?= $_SESSION['profile_picture'] ?? 'uploads/default.png'; ?>" alt="Profile Picture" class="profile-img">
            <span class="username" onclick="toggleDropdown()">Halo, <?= $_SESSION['username']; ?></span>
            <div class="dropdown-content" id="dropdown">
                <a href="profil.php" class="logout">Profil</a>
                <a href="?logout" class="logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h2>RUANG PEDULI</h2>
    </section>

    <!-- About Section -->
    <section class="about">
        <img class="about-ruang-peduli-img" src="aboutUs.png" alt="Doktor Image">
        <div class="about-ruang-peduli-content">
            <h3>Apa itu Ruang Peduli?</h3>
            <p>Ruang Peduli adalah layanan kesehatan yang diciptakan untuk memberikan akses pelayanan medis berkualitas bagi masyarakat yang kurang mampu. Layanan ini bertujuan untuk mengatasi kesenjangan dalam pelayanan kesehatan, dengan menawarkan bantuan medis secara gratis atau bersubsidi bagi individu dan keluarga yang membutuhkan.</p>
            <a href="https://forms.gle/mWgzZPnpR7KFMjgp9" class="daftar">Daftar</a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="service-card" onclick="window.location='appointment.html';">
            <img src="Group1.png" alt="Janji Temu">
            <h4>JANJI TEMU</h4>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <button class="cari">Cari</button>
        </div>
        <div class="service-card" onclick="window.location='telemedicine.html';">
            <img src="chat.png" alt="Telemedicine">
            <h4>TELEMEDICINE</h4>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <button class="cari">Cari</button>
        </div>
        <div class="service-card" onclick="window.location='pharmacy.html';">
            <img src="chat.png" alt="Apotek & Obat">
            <h4>APOTEK & OBAT</h4>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <button class="cari">Cari</button>
        </div>
    </section>
	 <!-- Footer Section -->
	 <footer>
        <div class="footer-left">
            <h2>Lorem Ipsum</h2>
            <br>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, </p>
            <p>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
        <div class="footer-right">
            <ul>
                <li><a href=""></a>Lorem Ipsum</li>
                <li>Lorem Ipsum</li>
                <li>Lorem Ipsum</li>
            </ul>
        </div>
    </footer>

    <script>
        function logout() {
            window.location.href = "?logout=true"; // Log out mechanism
        }
    </script>
</body>
</html>
