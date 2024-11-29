<?php
session_start();

// Cek apakah user memiliki akses
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'admin_super'])) {
    header('Location: login.php');
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

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header('Location: admin_dashboard.php');
    exit();
}

// Ambil data pengguna berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM tb_user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: admin_dashboard.php');
    exit();
}

// Cek role yang boleh mengedit
if (
    ($_SESSION['role'] === 'admin' && $user['role'] === 'admin_super') || // Admin tidak boleh edit admin_super
    ($_SESSION['role'] === 'admin' && $user['role'] === 'admin' && $user['username'] !== $_SESSION['username']) // Admin tidak boleh edit admin lain
) {
    $_SESSION['error_message'] = "Anda tidak diizinkan mengedit pengguna ini.";
    header('Location: admin_dashboard.php');
    exit();
}


// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $role = $_POST['role'];
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Email tidak valid. Silakan masukkan email yang benar.";
    } else {
        $stmt = $conn->prepare("UPDATE tb_user SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data pengguna berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data pengguna.";
        }
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
    <title>Edit Pengguna</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 400px;
        }

        h1 {
            font-size: 1.8rem;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            font-size: 1rem;
            margin-bottom: 20px;
            color: red;
        }

        label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 8px;
            color: #555;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: #007bff;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            text-align: center;
            margin-top: 10px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Pengguna</h1>

        <?php if (isset($_SESSION['error_message'])): ?>
            <p style="color: red;"><?= $_SESSION['error_message'];
            unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>

        <form action="edit_user.php?id=<?= $user['id']; ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>"
                required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                <?php if ($_SESSION['role'] === 'admin_super'): ?>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <?php endif; ?>
            </select>
            <?php if ($_SESSION['username'] === $user['username']): ?>
                <label for="password">Password Baru:</label>
                <input type="password" id="password" name="password"
                    placeholder="Kosongkan jika tidak ingin mengganti password">
            <?php endif; ?>
            <button type="submit">Perbarui Data</button>
        </form>
    </div>
</body>

</html>

<?php
$conn->close();
?>