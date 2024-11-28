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

// Ambil data pengguna dari database jika belum tersimpan di session
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


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link rel="stylesheet" href="profil.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
</head>

<body>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const passwordField = document.querySelector("input[name='password']");
            const confirmPasswordField = document.querySelector("input[name='confirm_password']");
            const form = document.querySelector("form");

            form.addEventListener("submit", function (event) {
                confirmPasswordField.classList.remove("error");

                if (passwordField.value && (confirmPasswordField.value === "" || passwordField.value !== confirmPasswordField.value)) {
                    event.preventDefault();
                    confirmPasswordField.classList.add("error");
                    alert("Konfirmasi password tidak cocok atau belum diisi.");
                }
            });
        });
    </script>
    <div class="profile-new">
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
                <img src="<?= $_SESSION['profile_picture'] ?? 'uploads/default.png'; ?>" alt="Profile Picture"
                    class="profile-img">
                <span class="username" onclick="toggleDropdown()">Halo, <?= $_SESSION['username']; ?></span>
                <a href="?logout" class="logout-btn">Logout</a>
            </div>
        </header>

        <!-- Profile Section -->
        <form method="POST" enctype="multipart/form-data">
            <div class="edit-profile">MY PROFILE</div>
            <div class="ellipse-parent">
                <div class="profile-circle">
                    <img src="<?= $_SESSION['profile_picture']; ?>" alt="Profile Picture" class="profile-pic" />
                </div>

            </div>

            <div class="profile-new-inner">
                <div class="nama-lengkap-parent">
                    <div class="login">Nama Lengkap</div>
                    <div class="frame-item"><?= htmlspecialchars($_SESSION['fullname']); ?></div>

                    <div class="login">Username</div>
                    <div class="frame-item"><?= htmlspecialchars($username); ?></div>

                    <div class="login">Alamat Email</div>
                    <div class="frame-item"><?= htmlspecialchars($_SESSION['email']); ?></div>

                    <!-- <input type="submit" value="Edit Profile" class="save-changes-btn"> -->
                    <a href="edit_profil.php" class="logout-btn">Edit Profil</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Footer Section -->
    <footer>
        <div class="footer-left">
            <h2>Lorem Ipsum</h2>
            <br>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit,</p>
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