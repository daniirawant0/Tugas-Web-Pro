<?php
session_start();
include("koneksi.php");

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard_admin.php");
    exit();
}

$error_msg = "";

if (isset($_POST["submit"])) {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $error_msg = "Username dan password harus diisi!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Cek password (mendukung plain text untuk kompatibilitas, idealnya gunakan password_hash)
            if ($password === $user['password'] || (function_exists('password_verify') && password_verify($password, $user['password']))) {
                session_regenerate_id(true);
                $_SESSION['login'] = true;
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard_admin.php");
                exit();
            } else {
                $error_msg = "Username atau password salah!";
            }
        } else {
            $error_msg = "Username atau password salah!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login Admin — Sparkleen Laundry</title>
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="card login-card">
            <div class="card-body">
                <div class="login-brand">
                    <div class="brand-icon-lg">🧺</div>
                    <h1>Sparkleen <span class="font-editorial" style="color: var(--primary); font-style: italic;">Laundry</span></h1>
                    <p>Panel Administrator</p>
                </div>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-error">
                        ⚠️ <?= htmlspecialchars($error_msg) ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan username" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password" required autocomplete="current-password">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-blue" style="width: 100%; justify-content: center;">
                            Login <span class="btn-icon">↗</span>
                        </button>
                    </div>
                    <div style="text-align: center;">
                        <a href="../public/dashboard.php" style="font-size: 14px;">← Kembali ke beranda</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
