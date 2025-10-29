<?php
// Bắt buộc đăng nhập trước khi cho thấy menu
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');

$currentUser = getCurrentUser();

// Lấy tên file hiện tại để biết đang ở trang nào (dùng active class nếu muốn)
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom mb-3">
    <div class="container-fluid">

        <!-- Logo / brand -->
        <a class="navbar-brand d-flex align-items-center" href="doanvien.php">
            <img src="../images/logodoan.png" alt="Doan Vien Logo"
                style="height:40px; width:auto; object-fit:contain;">

        </a>

        <!-- Nút toggle cho mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu trái -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'doanvien.php') ? 'active fw-bold' : '' ?>"
                        href="doanvien.php">
                        Quản lý Đoàn viên
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'lop.php') ? 'active fw-bold' : '' ?>" href="lop.php">
                        Quản lý Lớp / Chi đoàn
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'khoa.php') ? 'active fw-bold' : '' ?>" href="khoa.php">
                        Quản lý Khoa / Viện
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'doanphi.php') ? 'active fw-bold' : '' ?>"
                        href="doanphi.php">
                        Quản lý Đoàn phí
                    </a>
                </li>
        
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'danhgia.php') ? 'active fw-bold' : '' ?>"
                       href="danhgia.php">
                        Đánh giá đoàn viên
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'tracuu.php') ? 'active fw-bold' : '' ?>"
                       href="tracuu.php">
                        Tra cứu đoàn viên
                    </a>
                </li>

            </ul>

            <!-- Khu vực tài khoản (bên phải) -->
            <div class="d-flex align-items-center gap-3">

                <div class="d-flex align-items-center">
                    <img src="../images/aiotlab_logo.png" alt="AVT" class="rounded-circle border"
                        style="height:32px; width:32px; object-fit:cover;">
                    <span class="ms-2">
                        <?= htmlspecialchars($currentUser['username']) ?>
                        (<?= htmlspecialchars($currentUser['role']) ?>)
                    </span>
                </div>

                <a href="../handle/logout_process.php" class="btn btn-outline-danger btn-sm">
                    Logout
                </a>
            </div>

        </div>
    </div>
</nav>