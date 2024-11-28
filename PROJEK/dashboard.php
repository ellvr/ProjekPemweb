<?php
session_start();
$errorMessage = '';
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "layanan";

// Buat koneksi
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
if (!isset($_SESSION['fullname']) || !isset($_SESSION['email'])) {
    $stmt = $conn->prepare("SELECT fullname, email, profile_picture FROM tb_user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($fullname, $email, $dbProfilePic);
    if ($stmt->fetch()) {
        $_SESSION['fullname'] = $fullname;
        $_SESSION['email'] = $email;
        $_SESSION['profile_picture'] = $dbProfilePic ? $dbProfilePic : 'uploads/default.png';
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard1.css">
    <title>Dashboard</title>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <h2>LocalDoc</h2>
        </div>
        <ul class="menu">
            <li><a href="#">Beranda</a></li>
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
        <div class="user-info">
            <img src="<?= $_SESSION['profile_picture'] ?? 'uploads/default.png'; ?>" alt="Profile Picture"
                class="profile-img">
            <span class="username" onclick="toggleDropdown()">Halo, <?= $_SESSION['username']; ?></span>
            <div class="dropdown-content" id="dropdown">
                <a href="profil.php" class="logout-btn">Profil</a>
                <a href="?logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="image-section">
                <img src="bgDashboard.png" alt="Doctor">
            </div>
            <div class="text-section">
                <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore
                    et dolore magna aliqua.</p>
            </div>
        </section>

        <!-- About Section -->
        <section class="about">
            <img class="about-ruang-peduli-img" src="aboutUs.png" alt="Doktor Image">
            <div class="about-ruang-peduli-content">
                <h3>ABOUT US</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud
                    exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                <a href="register.html" class="daftar">Learn More</a>
            </div>
        </section>

        <!-- Our Service Section -->
        <section class="our-service">
            <h2>Our Service</h2>
            <div class="service-cards">
                <div class="service-card">
                    <a href="chatdokter.php" class="card-link">
                        <h3>Chat Dokter</h3>
                    </a>
                    <p>Konsultasikan keluhan kesehatan Anda secara langsung dengan dokter kapan saja.</p>
                </div>
                <div class="service-card">
                    <a href="janjidokter.php" class="card-link">
                        <h3>Janji Temu</h3>
                    </a>
                    <p>Buat janji temu dengan dokter secara online dengan cepat dan mudah.</p>
                </div>
                <div class="service-card">
                    <a href="ruangpeduli.php" class="card-link">
                        <h3>Ruang Peduli</h3>
                    </a>
                    <p>Bergabunglah dengan komunitas kesehatan untuk berbagi dan peduli sesama.</p>
                </div>
            </div>
        </section>

        <!-- Our Team Section -->
        <section class="our-team">
            <h2>Our Team</h2>
            <div class="team-cards">
                <div class="team-card">
                    <img src="dokter.png" alt="Doctor 1">
                    <h3>dr. Elvira</h3>
                    <p>Specialist</p>
                </div>
                <div class="team-card">
                    <img src="dokter.png" alt="Doctor 2">
                    <h3>dr. Ni'mah</h3>
                    <p>General Physician</p>
                </div>
                <div class="team-card">
                    <img src="dokter.png" alt="Doctor 3">
                    <h3>dr. Nafa</h3>
                    <p>Pediatrician</p>
                </div>
            </div>
        </section>

        <!-- Artikel Kesehatan Section -->
        <section class="artikel-kesehatan">
            <h2>Artikel Kesehatan</h2>
            <div class="article-cards">
                <div class="article-card">
                    <p>Tips Membiasakan Anak Makan Buah</p>
                </div>
                <div class="article-card">
                    <p>Bahaya Perokok Pasif</p>
                </div>
                <div class="article-card">
                    <p>Olahraga Untuk Lansia</p>
                </div>
            </div>
        </section>

        <!-- Kata Mereka Section -->
        <section class="footer">
            <h2>LocalWeb</h2>
            <div class="testimonial-card">
                <p>"Semangat Sehat Salam Sehat Oll."</p>
            </div>
            <hr>
            <p>Made With Love by ciwi ciwi developer</p>
        </section>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById("dropdown").classList.toggle("show");
        }

        // Tutup dropdown jika pengguna mengklik di luar elemen
        window.onclick = function (event) {
            if (!event.target.matches('.username')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>