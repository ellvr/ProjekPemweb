<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href = 'login.php';</script>";
    exit();
}

// Ambil dokter_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['dokter_id'])) {
    $dokter_id = intval($_GET['dokter_id']); // Dari parameter GET
    $_SESSION['dokter_id'] = $dokter_id; // Simpan ke session
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dokter_id'])) {
    $dokter_id = intval($_POST['dokter_id']); // Dari form POST
} elseif (isset($_SESSION['dokter_id'])) {
    $dokter_id = intval($_SESSION['dokter_id']); // Dari session jika sudah disimpan
} else {
    echo "<script>alert('Dokter ID tidak ditemukan!'); window.history.back();</script>";
    exit();
}

// Ambil data dokter berdasarkan dokter_id
$query = "SELECT * FROM dokter WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $dokter_id);
$stmt->execute();
$result = $stmt->get_result();
$dokter = $result->fetch_assoc();

if (!$dokter) {
    echo "<script>alert('Data dokter tidak ditemukan di database.'); window.history.back();</script>";
    exit();
}

// Ambil harga dokter dari database
$harga = $dokter['harga']; // Harga otomatis dari tabel dokter

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? null;
    $waktu = $_POST['waktu'] ?? null;
    $keluhan = $_POST['keluhan'] ?? null;
    $ketersediaan = $_POST['ketersediaan'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;

    // Validasi input
    $errors = [];
    if (empty($tanggal)) $errors[] = 'Tanggal harus diisi.';
    if (empty($waktu)) $errors[] = 'Waktu harus diisi.';
    if (empty($keluhan)) $errors[] = 'Keluhan harus diisi.';

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
    } else {
        $user_id = $_SESSION['id']; // ID user yang login

        $query_insert = "INSERT INTO janji (user_id, dokter_id, tanggal, waktu, keluhan, jenis_kelamin, harga) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query_insert);
        $stmt->bind_param("iissssss", $user_id, $dokter_id, $tanggal, $waktu, $keluhan, $jenis_kelamin, $harga);

        if ($stmt->execute()) {
            echo "<script>alert('Janji temu berhasil dibuat!'); window.location.href = 'janjidokter.php';</script>";
        } else {
            echo "<script>alert('Gagal membuat janji temu: " . $stmt->error . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Janji Temu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('bgDashboard.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .hero h2 {
            text-align: center;
            font-size: 24px;
            color: white;
            margin-bottom: 20px;
        }
        .appointment-form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 0 auto;
        }
        .appointment-form label {
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }
        .appointment-form input, .appointment-form textarea, .appointment-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .appointment-form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        .appointment-form button:hover {
            background-color: blue;
        }
        a {
            background-color: #007bff; 
            color: white;
            cursor: pointer;
            font-size: 16px;
            text-align: center; 
            display: inline-block; 
            padding: 10px 20px; 
            border-radius: 5px; 
            margin: 0 auto; 
            text-decoration: none; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        }
    </style>
    
</head>
<body>
    <div class="content">
        <section class="hero">
            <h2>Buat Janji Temu dengan Dr. <?= htmlspecialchars($dokter['nama']); ?></h2>
        </section>
        <section class="appointment-form">
            <form method="POST" action="">
                <input type="hidden" name="dokter_id" value="<?= htmlspecialchars($dokter_id); ?>">

                <label for="tanggal">Tanggal:</label>
                <input type="date" name="tanggal" required>

                <label for="waktu">Waktu:</label>
                <input type="time" name="waktu" required>

                <label for="keluhan">Keluhan:</label>
                <textarea name="keluhan" required></textarea>

                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <input type="text" name="jenis_kelamin">

                <p><strong>Harga Konsultasi:</strong> Rp <?= htmlspecialchars($harga); ?></p>

                <button type="submit">Buat Janji</button>
                <a href="janjidokter.php">Kembali</a>
            </form>
        </section>
    </div>
</body>
</html>
