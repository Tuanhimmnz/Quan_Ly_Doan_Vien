<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đoàn viên - FITDNU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row vh-100">
           
            <div class="col-md-4 d-flex flex-column justify-content-center align-items-center bg-white">
                <img src="./images/logo-doan.webp" alt="Logo" class="mb-3" style="max-width:120px;">
                <h6 class="text-center fw-bold text-primary mb-4">
                    Quản Lý Đoàn Viên
                </h6>

                <form action="./handle/login_process.php" method="POST" style="width:80%; max-width:300px;">
                    <h6 class="text-primary mb-3 text-center">THÔNG TIN ĐĂNG NHẬP</h6>

                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Tài khoản" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger py-2">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success py-2">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <button type="submit" name="login" class="btn btn-primary w-100">Đăng nhập</button>
                    </div>
                </form>
            </div>

            <!-- CỘT BÊN PHẢI: ẢNH NỀN -->
            <div class="col-md-8 d-none d-md-block p-0">
                <img src="./images/bg-tnvn.webp"
                     alt="Ảnh nền"
                     class="w-100 h-100"
                     style="object-fit: cover;">
            </div>
        </div>
    </div>

    <footer class="text-center py-2 bg-primary text-white">
        Copyright © 2025
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
