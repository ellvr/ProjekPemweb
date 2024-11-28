<?php
session_start();

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "layanan";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Username dan Password wajib diisi!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Gunakan password_verify untuk memeriksa password yang terenkripsi
            if (password_verify($password, $user['password'])) { 
                $_SESSION['username'] = $user['username']; // Simpan username di session
                $_SESSION['role'] = $user['role']; // Simpan role di session
                
                // Arahkan berdasarkan role
                if ($user['role'] == 'admin') {
                    header('Location: admin_dashboard.php'); // Halaman admin
                } else if ($user['role'] == 'admin_super') {
                    header('Location: admin_dashboard.php'); // Halaman admin super
                } else if ($user['role'] == 'user') {
                    header('Location: dashboard.php'); // Halaman user
                }
                exit();
            } else {
                $_SESSION['error_message'] = "Password salah!";
            }
        } else {
            $_SESSION['error_message'] = "Username tidak ditemukan!";
        }
    }
    header('Location: login.php'); // Kembali ke halaman login
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
    <style>
        .error-box {
            color: red;
            background-color: #ffe6e6;
            border: 1px solid red;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-side"></div>
        <div class="form-side">
            <div class="form-box">
                <div class="login-box">
                    <h2>Welcome</h2>
                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="error-box">
                            <?php 
                            echo htmlspecialchars($_SESSION['error_message']); 
                            unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="input-box">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="input-box">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="options">
                            <label><input type="checkbox" name="remember"> Remember Me</label>
                            <a href="#">Forgot Password?</a>
                        </div>
                        <button type="submit">Log In</button>

                        
                    </form>
                    <script>
                        const loginForm = document.getElementById('loginForm');
                        loginForm.addEventListener('submit', function (event) {
                            const username = document.getElementById('username').value.trim();
                            const password = document.getElementById('password').value.trim();

                            if (!username || !password) {
                                event.preventDefault();
                                alert('Harap isi semua kolom sebelum login!');
                            }
                        });
                    </script>
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>