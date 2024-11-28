<?php
session_start();

// Cek apakah pengguna sudah login dan apakah mereka adalah admin
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Jika belum login, arahkan ke login
    exit();
}

// Arahkan jika bukan admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Jika bukan admin, arahkan ke halaman lain
    exit();
}

// Koneksi ke database
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "layanan";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menambahkan pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Insert user baru ke database
    $stmt = $conn->prepare("INSERT INTO tb_user (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);
    $stmt->execute();
    header('Location: admin_dashboard.php');  // Refresh halaman setelah tambah pengguna
    exit();
}

// Mengambil data pengguna untuk ditampilkan
$stmt = $conn->prepare("SELECT * FROM tb_user");
$stmt->execute();
$result = $stmt->get_result();

// Hapus data pengguna
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM tb_user WHERE id = ?");
    $delete_stmt->bind_param("i", $delete_id);
    $delete_stmt->execute();
    header('Location: admin_dashboard.php'); // Redirect setelah menghapus
    exit();
}

// Edit data pengguna
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt_edit = $conn->prepare("SELECT * FROM tb_user WHERE id = ?");
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();

    // Pastikan data ditemukan
    if ($result_edit->num_rows > 0) {
        $user = $result_edit->fetch_assoc();
    } else {
        echo "Pengguna tidak ditemukan.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $update_stmt = $conn->prepare("UPDATE tb_user SET username = ?, email = ?, role = ? WHERE id = ?");
        $update_stmt->bind_param("sssi", $username, $email, $role, $edit_id);
        $update_stmt->execute();
        header('Location: admin_dashboard.php');
        exit();
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
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            margin-right: 10px;
        }

        .action-btn {
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <h1>Admin Dashboard - Kelola Data Pengguna</h1>

    <!-- Form untuk menambahkan pengguna baru -->
    <form action="admin_dashboard.php" method="POST">
        <a href="login.php" class="logout-btn">logout</a>
        <h2>Tambah Pengguna Baru</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="role">Role:</label>
        <select name="role" id="role">
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select><br><br>
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
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td>
                        <a href="admin_dashboard.php?edit_id=<?php echo $row['id']; ?>" class="action-btn">Edit</a>
                        <a href="admin_dashboard.php?delete_id=<?php echo $row['id']; ?>" class="action-btn"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if (isset($_GET['edit_id'])): ?>
        <h2>Edit Pengguna</h2>
        <form action="admin_dashboard.php?edit_id=<?php echo $_GET['edit_id']; ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required><br><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>
            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
            </select><br><br>
            <button type="submit" name="update_user">Update Pengguna</button>
        </form>
    <?php endif; ?>

</body>

</html>

<?php
$conn->close();
?>