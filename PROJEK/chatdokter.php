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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHATDOKTER</title>
    <link rel="stylesheet" href="chatdokter.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="2.png" alt="Logo" class="logo-img">
            <h3>Hai Docter!!</h3>
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
        </div>
    </header>    

    <div class="main-container">
        <aside class="doctor-list">
            <div class="search-bar">
                <input type="text" placeholder="Cari dokter atau spesialis">
            </div>
            <div class="doctor-item" onclick="showChat('dr. Elvira Rosa')">
                <img src="1.png" alt="Doctor Image" class="doctor-img">
                <div class="doctor-info">
                    <h4>dr. Elvira Rosa</h4>
                    <p>Umum</p>
                    <span>⭐ 5</span>
                    <p>Rp. 20.000,00</p>
                </div>
            </div>            
            <div class="doctor-item" onclick="showChat('dr. Elvira Rosa')">
                <img src="1.png" alt="Doctor Image" class="doctor-img">
                <div class="doctor-info">
                    <h4>dr. Elvira Rosa</h4>
                    <p>Umum</p>
                    <span>⭐ 5</span>
                    <p>Rp. 20.000,00</p>
                </div>
            </div>      
            <div class="doctor-item" onclick="showChat('dr. Elvira Rosa')">
                <img src="1.png" alt="Doctor Image" class="doctor-img">
                <div class="doctor-info">
                    <h4>dr. Elvira Rosa</h4>
                    <p>Umum</p>
                    <span>⭐ 5</span>
                    <p>Rp. 20.000,00</p>
                </div>
            </div>      
        </aside>
        <section class="message-display" id="chat-area">
            <div class="chat-header" id="chat-header" style="display: none;">
                <div class="chat-header-info">
                    <img id="doctor-chat-image" src="1.png" alt="Doctor Image" class="doctor-img">
                    <h3 id="doctor-chat-name">dr. Elvira Rosa</h3>
                </div>
            </div>
            <p id="default-message">Tidak ada pesan ditampilkan</p>
            <div id="chat-content" style="display: none; position: relative;">
                <div class="chat-messages" id="chat-messages"></div>
                <div class="chat-input">
                    <input type="text" id="message-input" placeholder="Ketik pesan...">
                    <button onclick="sendMessage()">Kirim</button>
                </div>
            </div>
        </section>
    </div>

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

    <script src="chatdokter.js"></script>
</body>
</html>
