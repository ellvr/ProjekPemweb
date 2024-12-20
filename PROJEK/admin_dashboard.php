<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'admin_super'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$servername = "localhost";
$dbUsername = "pemweb";
$dbPassword = "admin_123";
$dbname = "layanan";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


// Tambah pengguna baru
if (isset($_POST['add_user'])) {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Email tidak valid. Silakan masukkan email yang benar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tb_user (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pengguna berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan pengguna: " . $conn->error;
        }
        header('Location: admin_dashboard.php');
        exit();
    }
    header('Location: admin_dashboard.php'); // Refresh untuk menghapus data POST
    exit();
}


// Query data pengguna
$query = "SELECT * FROM tb_user";
$result = $conn->query($query);

if (!$result) {
    die("Query gagal: " . $conn->error);
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Ambil data pengguna yang ingin dihapus
    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_to_delete = $result->fetch_assoc();

    // Cek apakah pengguna ada
    if ($user_to_delete) {
        // Cek peran untuk menghapus
        if ($_SESSION['role'] === 'admin_super') {
            // Admin Super bisa menghapus admin dan user, kecuali dirinya sendiri
            if ($user_to_delete['username'] !== $_SESSION['username']) {
                // Hapus pengguna
                $delete_stmt = $conn->prepare("DELETE FROM tb_user WHERE id = ?");
                $delete_stmt->bind_param("i", $delete_id);
                if ($delete_stmt->execute()) {
                    $_SESSION['success_message'] = "Pengguna berhasil dihapus!";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus pengguna.";
                }
            } else {
                $_SESSION['error_message'] = "Anda tidak dapat menghapus akun Anda sendiri!";
            }
        } elseif ($_SESSION['role'] === 'admin') {
            // Admin hanya bisa menghapus user, tidak bisa menghapus admin atau admin_super
            if ($user_to_delete['role'] === 'user' && $user_to_delete['username'] !== $_SESSION['username']) {
                // Hapus pengguna
                $delete_stmt = $conn->prepare("DELETE FROM tb_user WHERE id = ?");
                $delete_stmt->bind_param("i", $delete_id);
                if ($delete_stmt->execute()) {
                    $_SESSION['success_message'] = "Pengguna berhasil dihapus!";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus pengguna.";
                }
            } else {
                $_SESSION['error_message'] = "Anda tidak dapat menghapus pengguna ini!";
            }
        }
    } else {
        $_SESSION['error_message'] = "Pengguna tidak ditemukan!";
    }

    header('Location: admin_dashboard.php');
    exit();
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            padding: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #00b3b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #00a3b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #00b3b3;
            color: white;
        }

        .action-btn a {
            color: #00b3b3;
            text-decoration: none;
            margin-right: 10px;
        }

        .action-btn a:hover {
            text-decoration: underline;
        }

        .container-log {
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .logout-btn {
            background-color: #00b3b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 8px 52px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #00a3b3;
        }

        header{
            display: flex;
            background-color: #00b3b3;
            color: white

        }
    </style>

</head>

<body>
    <div class="container">
        <div class="header">
            <h1><?= strtoupper(str_replace('_', ' ', htmlspecialchars($_SESSION['role']))); ?> DASHBOARD</h1>
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
            <div class="container-log">
                <a href="artikel.php" class="logout-btn">Kelola Artikel</a>
                <a href="login.php" class="logout-btn">Logout</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <p style="color: red;"><?= $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <p style="color: green;"><?= $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?></p>
            <?php endif; ?>
        </div>

        <h2>Tambah Pengguna Baru</h2>
        <form action="admin_dashboard.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="user">User</option>
                <?php if ($_SESSION['role'] === 'admin_super'): ?>
                    <option value="admin">Admin</option>
                <?php endif; ?>
            </select>

            <button type="submit" name="add_user">Tambah Pengguna</button>
        </form>

        <h2>Daftar Pengguna</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']); ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['role']); ?></td>
                        <td class="action-btn">
                            <a href="edit_user.php?id=<?= $row['id']; ?>">Edit</a>
                            <a href="admin_dashboard.php?delete_id=<?= $row['id']; ?>"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>

</html>