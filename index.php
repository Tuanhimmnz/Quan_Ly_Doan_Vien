<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đoàn viên - FITDNU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/ui.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="auth-wrapper">
        <section class="d-flex align-items-stretch reveal-on-scroll" style="min-height:100vh; padding:24px;">
            <div class="login-panel reveal-on-scroll" style="flex:1;">
                <div class="brand-login mb-2">
                    <img src="images/logo-doan.webp" alt="Logo">
                    <div style="color:#0072BC; font-weight:600;">Quản Lý Đoàn Viên</div>
                </div>

                <h3 class="text-center" style="margin:8px 0 16px; color:#0072BC;">Thông tin đăng nhập</h3>

                <form action="handle/login_process.php" method="POST" novalidate class="ui-form">
                    <div class="mb-3">
                        <label for="username">Tài khoản</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Nhập tài khoản" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password">Mật khẩu</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" name="login" class="btn btn-primary" style="width:100%;">Đăng nhập</button>
                </form>

                <p class="text-center text-muted" style="margin-top:12px;">FITDNU • Hệ thống quản lý đoàn viên</p>
            </div>
        </section>
        <div class="login-hero reveal-on-scroll">
            <img src="images/bg-tnvn.webp" alt="Ảnh nền">
        </div>
    </div>

    <footer class="text-center py-2 bg-primary text-white">
        Copyright © 2025
    </footer>
    <script src="assets/js/ui.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
