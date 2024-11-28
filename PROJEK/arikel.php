<?php
session_start();

// Cek jika user bukan admin, redirect
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Konfigurasi database
$servername = "localhost";
$dbUsername = "pemweb";
$dbPassword = "admin_123";
$dbname = "layanan";

// Buat koneksi
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inisialisasi variabel pesan
$message = '';

// Fungsi untuk tambah, edit, dan hapus artikel
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'create') {
        // Tambah Artikel
        $judul = $conn->real_escape_string($_POST['judul']);
        $link = $conn->real_escape_string($_POST['link']);
        $stmt = $conn->prepare("INSERT INTO tb_artikel (judul, link) VALUES (?, ?)");
        $stmt->bind_param("ss", $judul, $link);
        if ($stmt->execute()) {
            $message = "Artikel berhasil ditambahkan!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action == 'update') {
        // Edit Artikel
        $id = intval($_POST['id']);
        $judul = $conn->real_escape_string($_POST['judul']);
        $link = $conn->real_escape_string($_POST['link']);
        $stmt = $conn->prepare("UPDATE tb_artikel SET judul = ?, link = ? WHERE id = ?");
        $stmt->bind_param("ssi", $judul, $link, $id);
        if ($stmt->execute()) {
            $message = "Artikel berhasil diupdate!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action == 'delete') {
        // Hapus Artikel
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM tb_artikel WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Artikel berhasil dihapus!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Artikel</title>
    <link rel="stylesheet" href="dashboard1.css">
</head>
<body>
    <h1>Kelola Artikel</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Form Tambah/Edit Artikel -->
    <form action="artikel.php" method="POST" style="margin-bottom: 20px;">
        <input type="hidden" name="action" value="create">
        <input type="hidden" name="id" value="">
        <input type="text" name="judul" placeholder="Judul Artikel" required>
        <input type="url" name="link" placeholder="Link Artikel" required>
        <button type="submit">Tambah Artikel</button>
    </form>

    <!-- Tabel Artikel -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Link</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM tb_artikel ORDER BY created_at DESC");
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['judul']); ?></td>
                <td><a href="<?= htmlspecialchars($row['link']); ?>" target="_blank">Buka</a></td>
                <td>
                    <!-- Form Edit Artikel -->
                    <form action="artikel.php" method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <input type="text" name="judul" value="<?= htmlspecialchars($row['judul']); ?>" required>
                        <input type="url" name="link" value="<?= htmlspecialchars($row['link']); ?>" required>
                        <button type="submit">Edit</button>
                    </form>

                    <!-- Form Hapus Artikel -->
                    <form action="artikel.php" method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <button type="submit" onclick="return confirm('Yakin ingin menghapus artikel ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="4">Belum ada artikel.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>

<?php
$conn->close();
?>
