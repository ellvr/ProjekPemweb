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
    $stmt = $conn->prepare("SELECT fullname, password, email, profile_picture FROM tb_user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($fullname, $email, $dbProfilePic, $password);
    if ($stmt->fetch()) {
        $_SESSION['fullname'] = $fullname;
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['profile_picture'] = $dbProfilePic ? $dbProfilePic : 'uploads/default.png';
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle profile picture upload
    $profileDir = 'uploads/';
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
        $fileTmpPath = $_FILES['profilePic']['tmp_name'];
        $fileName = $username . '.png';
        $dest_path = $profileDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $_SESSION['profile_picture'] = $dest_path;

            // Update the profile picture path in the database
            $stmt = $conn->prepare("UPDATE tb_user SET profile_picture = ? WHERE username = ?");
            $stmt->bind_param("ss", $dest_path, $username);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Handle profile picture deletion
    if (isset($_POST['deleteProfilePic'])) {
        // Mendapatkan path foto profil dari session
        $currentProfilePic = $_SESSION['profile_picture'];

        // Menghapus file foto profil jika bukan foto default
        if ($currentProfilePic !== 'uploads/default.png' && file_exists($currentProfilePic)) {
            unlink($currentProfilePic);  // Menghapus file gambar dari server
        }

        // Reset foto profil ke default
        $_SESSION['profile_picture'] = 'uploads/default.png';

        // Update database untuk menyetel foto profil ke default
        $stmt = $conn->prepare("UPDATE tb_user SET profile_picture = ? WHERE username = ?");
        $stmt->bind_param("ss", $_SESSION['profile_picture'], $username);
        $stmt->execute();
        $stmt->close();
    }

    // Update other user data
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $newPassword = $_POST['password']; // Password baru yang dimasukkan pengguna
    $currentUsername = $_SESSION['username'];

    // Cek jika password baru diisi
    if (!empty($newPassword)) {
        if ($newPassword !== $_POST['confirm_password']) {
            $errorMessage = "Password dan konfirmasi password tidak cocok.";
        } else {
            // Hash password baru sebelum disimpan
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE tb_user SET fullname = ?, email = ?, password = ?, username = ? WHERE username = ?");
            $stmt->bind_param("sssss", $fullname, $email, $hashedPassword, $username, $currentUsername);
            $stmt->execute();
            $stmt->close();

            $_SESSION['password'] = $hashedPassword;
        }
    } else {
        // Jika password tidak diubah, update tanpa mengubah password
        $stmt = $conn->prepare("UPDATE tb_user SET fullname = ?, email = ?, username = ? WHERE username = ?");
        $stmt->bind_param("ssss", $fullname, $email, $username, $currentUsername);
        $stmt->execute();
        $stmt->close();
    }

    // Update session
    $_SESSION['fullname'] = $fullname;
    $_SESSION['email'] = $email;
    $_SESSION['username'] = $username;

    header('Location: profil.php');
    exit();
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
                <a href="profil.php" class="logout-btn">Profil</a>
                <a href="?logout" class="logout-btn">Logout</a>
            </div>
    </div>
    </header>

    <!-- Profile Section -->
    <form method="POST" enctype="multipart/form-data">
        <div class="edit-profile">EDIT PROFILE</div>
        <div class="ellipse-parent">
            <div class="profile-circle">
                <img src="<?= $_SESSION['profile_picture']; ?>" alt="Profile Picture" class="profile-pic" />
            </div>
            <input type="file" name="profilePic" accept="image/*" class="upload-photo" />
            <input type="submit" name="saveProfilePic" value="Simpan Foto Profil" class="save-profile-pic-btn" />
            <input type="submit" name="deleteProfilePic" value="Hapus Foto Profil" class="delete-profile-pic-btn"
                onclick="return confirm('Apakah Anda yakin ingin menghapus foto profil?');" />
        </div>

        <div class="profile-new-inner">
            <div class="nama-lengkap-parent">
                <div class="login">Nama Lengkap</div>
                <input type="text" name="fullname" value="<?= $_SESSION['fullname']; ?>" class="frame-item" required>

                <div class="login">Username</div>
                <input type="text" name="username" value="<?= $_SESSION['username']; ?>" class="frame-item">

                <div class="login">Kata Sandi Baru</div>
                <input type="password" name="password" class="frame-item" placeholder="Masukkan password baru">

                <div class="login">Konfirmasi Kata Sandi Baru</div>
                <input type="password" name="confirm_password" class="frame-item" placeholder="Konfirmasi password baru">

                <div class="login">Alamat Email</div>
                <input type="email" name="email" value="<?= $_SESSION['email']; ?>" class="frame-item" required>

                <input type="submit" value="Simpan Perubahan" class="save-changes-btn">
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