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

// Memproses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $confirmPassword = htmlspecialchars($_POST['confirmPassword'], ENT_QUOTES, 'UTF-8');

    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['error_message'] = "Semua kolom wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Format email salah! Pastikan ada '@' dan '.'";
    } elseif ($password !== $confirmPassword) {
        $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek apakah username/email sudah ada
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['alert_message'] = "Username atau email sudah terdaftar!";
        } else {
            // Masukkan data ke database
            $stmt = $conn->prepare("INSERT INTO tb_user (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);
            $stmt->execute();
            $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";

            header('Location: login.php');
            exit();
        }
    }
    header('Location: register.php'); // Refresh untuk menghapus data POST
    exit();
}

if (isset($_SESSION['success_message'])) {
    unset($_SESSION['success_message']);
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
    <title>Register</title>
    <style>
        .alert-box {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid red;
            color: red;
            background-color: #f8d7da;
            border-radius: 5px;
        }
        .success-box {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid green;
            color: green;
            background-color: #d4edda;
            border-radius: 5px;
        }
        .password-status {
            font-size: 0.9em;
            color: red;
            margin-top: 5px;
        }
        .password-status.valid {
            color: green;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="image-side"></div>
        <div class="form-side">
            <div class="form-box">
                <div class="login-box">
                    <h2>Register</h2>
                    <?php
                   
                    if (!empty($_SESSION['error_message'])) {
                        echo '<div class="alert-box">' . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') . '</div>';
                        unset($_SESSION['error_message']); 
                    }

                    
                    if (!empty($_SESSION['alert_message'])) {
                        echo '<div class="alert-box">' . htmlspecialchars($_SESSION['alert_message'], ENT_QUOTES, 'UTF-8') . '</div>';
                        unset($_SESSION['alert_message']); 
                    }

                    
                    if (!empty($_SESSION['success_message'])) {
                        echo '<div class="success-box">' . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') . '</div>';
                        unset($_SESSION['success_message']); 
                    }
                    ?>


                   
                    <form action="register.php" method="POST" id="registerForm">
                        <div class="input-box">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                        </div>
                        <div class="input-box">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        <div class="input-box">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="input-box">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                            <span id="passwordStatus" class="password-status"></span>
                        </div>
                        <button type="submit">Register</button>
                    </form>

                    <p>Already have an account? <a href="login.php">Log In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const passwordStatus = document.getElementById('passwordStatus');

        // Cek kesesuaian password dan konfirmasi password secara langsung
        confirmPassword.addEventListener('input', () => {
            if (confirmPassword.value === '') {
                passwordStatus.textContent = '';
            } else if (password.value === confirmPassword.value) {
                passwordStatus.textContent = 'Password cocok!';
                passwordStatus.classList.add('valid');
                passwordStatus.classList.remove('invalid');
            } else {
                passwordStatus.textContent = 'Password tidak cocok!';
                passwordStatus.classList.add('invalid');
                passwordStatus.classList.remove('valid');
            }
        });
    </script>
</body>

</html>