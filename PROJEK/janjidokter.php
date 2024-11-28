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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Janji Temu</title>
    <link rel="stylesheet" href="janji.css">
</head>
<body>
    <!-- Navbar -->
    <header>
        <div class="logo">
            <img src="2.png" alt="Logo" class="logo-img">
            <h3>Hai Dokter!!</h3>
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
            <a href="profil.php" class="logout-btn">Profil</a>
            <a href="?logout" class="logout-btn">Logout</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="content">
        <!-- Hero Section -->
        <section class="hero">
            <h2>JANJI TEMU</h2>
        </section>

        <!-- Search and Filter Section -->
        <section class="search-filter">
            <div class="search-bar">
                <form method="POST" action="janjidokter.php">
                    <input type="text" name="keyword" placeholder="Cari dokter atau Rumah Sakit" value="<?= $_POST['keyword'] ?? ''; ?>">
                    <button type="submit" class="btn-search">Cari</button>
                </form>
            </div>

            <!-- Doctor List -->
            <div class="doctor-list">
            <?php
            include 'koneksi.php';

            $keyword = $_POST['keyword'] ?? '';
            $query = "SELECT * FROM dokter WHERE nama LIKE '%$keyword%' OR lokasi LIKE '%$keyword%' OR spesialisasi LIKE '%$keyword%'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
            ?>
            <div class="doctor-card">
                <img src="<?= $row['gambar']; ?>" alt="Doctor Image" class="doctor-img">
                <div class="doctor-info">
                    <h3><?= $row['nama']; ?></h3>
                    <p><?= $row['spesialisasi']; ?></p>
                    <p><?= $row['lokasi']; ?></p>
                    <p>Mulai Dari Rp. <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                    <!-- Form untuk membuat janji temu -->
                    <form method="POST" action="buat_janji.php">
                        <input type="hidden" name="dokter_id" value="<?= $row['id']; ?>"> <!-- Pastikan ini ada dan benar -->
                        <button type="submit" class="btn-appointment">Buat Janji</button>
                    </form>
                </div>
            </div>
            <?php endwhile; else: ?>
            <p>Tidak ada hasil ditemukan.</p>
            <?php endif; ?>
            </div>
        </section>
    </div>

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
</body>
</html>
