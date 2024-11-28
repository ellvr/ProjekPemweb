<?php
// Koneksi ke database
$servername = "localhost";
$dbUsername = "pemweb";
$dbPassword = "admin_123";
$dbname = "layanan";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil parameter offset dari request
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Query untuk mengambil artikel berdasarkan offset
$sql = "SELECT judul, link FROM tb_artikel ORDER BY id DESC LIMIT 3 OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $offset);
$stmt->execute();
$result = $stmt->get_result();

// Generate output artikel tambahan
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="article-card">';
        echo '<h3>' . htmlspecialchars($row['judul']) . '</h3>';
        echo '<a href="' . htmlspecialchars($row['link']) . '" target="_blank">Baca Artikel</a>';
        echo '</div>';
    }
} else {
    echo ""; // Tidak ada artikel tambahan
}
$stmt->close();
$conn->close();
?>
