<?php
// Shared header include: /includes/header.php
require_once __DIR__ . '/../functions/auth.php';
ensureSessionStarted();
$user = getCurrentUser();

// Compute relative prefix to project root for assets/ and images/
$scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
$depth = $scriptDir === '' ? 0 : substr_count($scriptDir, '/');
$base = str_repeat('../', $depth);

// Allow pages to override title via $pageTitle before including header
$pageTitle = isset($pageTitle) ? $pageTitle : 'Hệ thống Quản lý Đoàn viên';
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-2x2b9nEobQh1gU6t9a6m2jJc1tq6J+Jm0g9yYQ9C2V/7VnR6t9Q7o7E4S3kVvWm0wNw0FJ2nE6k9gE1vX3K0dg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= $base ?>assets/css/ui.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
  <!-- App Header -->
  <header class="app-header">
      <button class="icon-btn header-toggle" id="sidebarToggle" aria-label="Mở menu">
        <i class="fa-solid fa-bars"></i>
      </button>
      <a class="brand" href="<?= $base ?>views/doanvien.php">
        <img src="<?= $base ?>images/logo-doan.webp" alt="Logo" />
        <span>HỆ THỐNG QUẢN LÝ ĐOÀN VIÊN</span>
      </a>
      <div class="header-actions">
        <button class="icon-btn notifications-btn" type="button" aria-label="Thông báo">
          <i class="fa-solid fa-bell"></i>
        </button>
        <?php if ($user): ?>
          <div class="user-chip">
            <img src="<?= $base ?>images/aiotlab_logo.png" alt="Avatar">
            <div class="user-meta">
              <strong><?= htmlspecialchars($user['username']) ?></strong>
              <small><?= htmlspecialchars($user['role']) ?></small>
            </div>
          </div>
          <a class="btn btn-danger btn-sm" href="<?= $base ?>handle/logout_process.php">
            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
          </a>
        <?php else: ?>
          <a class="btn btn-primary btn-sm" href="<?= $base ?>index.php">Đăng nhập</a>
        <?php endif; ?>
      </div>
    </header>

  <!-- Sidebar Navigation -->
  <aside class="app-sidebar" id="appSidebar">
        <nav class="nav-menu">
          <a class="nav-item <?= ($currentPage === 'gioithieu.php') ? 'active' : '' ?>" href="<?= $base ?>gioithieu.php"><i class="fa-solid fa-circle-info"></i><span>Giới thiệu</span></a>
        <?php if ($user && $user['role'] === 'admin'): ?>
        <a class="nav-item <?= ($currentPage === 'khaibao.php') ? 'active' : '' ?>" href="<?= $base ?>views/khaibao.php"><i class="fa-solid fa-user-check"></i><span>Duyệt khai báo</span></a>
        <?php endif; ?>
          <a class="nav-item <?= ($currentPage === 'doanvien.php') ? 'active' : '' ?>" href="<?= $base ?>views/doanvien.php"><i class="fa-solid fa-users"></i><span>Đoàn viên</span></a>
          <a class="nav-item <?= ($currentPage === 'lop.php') ? 'active' : '' ?>" href="<?= $base ?>views/lop.php"><i class="fa-solid fa-layer-group"></i><span>Lớp / Chi đoàn</span></a>
          <a class="nav-item <?= ($currentPage === 'khoa.php') ? 'active' : '' ?>" href="<?= $base ?>views/khoa.php"><i class="fa-solid fa-building-columns"></i><span>Khoa / Viện</span></a>
        <a class="nav-item <?= ($currentPage === 'doanphi.php') ? 'active' : '' ?>" href="<?= $base ?>views/doanphi.php"><i class="fa-solid fa-coins"></i><span>Đoàn phí</span></a>
        <!-- [FITDNU-ADD] Import menu (giữ 3 mục: Khoa, Lớp, Đoàn viên) -->
        <a class="nav-item <?= ($currentPage === 'index.php' && strpos($_SERVER['REQUEST_URI'], '/views/import/') !== false) ? 'active' : '' ?>" href="<?= $base ?>views/import/index.php"><i class="fa-solid fa-file-import"></i><span>Nhập từ Excel</span></a>
        <a class="nav-item <?= ($currentPage === 'khenthuong.php') ? 'active' : '' ?>" href="<?= $base ?>views/khenthuong.php"><i class="fa-solid fa-gift"></i><span>Khen thưởng</span></a>
        <a class="nav-item <?= ($currentPage === 'kyluat.php') ? 'active' : '' ?>" href="<?= $base ?>views/kyluat.php"><i class="fa-solid fa-scale-balanced"></i><span>Kỷ luật</span></a>
        <a class="nav-item <?= ($currentPage === 'lichsu.php') ? 'active' : '' ?>" href="<?= $base ?>views/lichsu.php"><i class="fa-solid fa-clock-rotate-left"></i><span>Lịch sử tham gia</span></a>
        <a class="nav-item <?= ($currentPage === 'diemrenluyen.php') ? 'active' : '' ?>" href="<?= $base ?>views/diemrenluyen.php"><i class="fa-solid fa-chart-line"></i><span>Điểm rèn luyện</span></a>
        <a class="nav-item <?= ($currentPage === 'sukien.php') ? 'active' : '' ?>" href="<?= $base ?>views/sukien.php"><i class="fa-solid fa-calendar-days"></i><span>Sự kiện</span></a>
        <a class="nav-item <?= ($currentPage === 'taikhoan.php') ? 'active' : '' ?>" href="<?= $base ?>views/taikhoan.php"><i class="fa-solid fa-user-gear"></i><span>Tài khoản</span></a>
      <?php if ($user && in_array($user['role'], ['admin','Cán bộ'])): ?>
        <!-- [FITDNU-ADD] Chỉ giữ Lịch sử; ẩn Công cụ theo yêu cầu -->
        <a class="nav-item <?= ($currentPage === 'audit_logs.php') ? 'active' : '' ?>" href="<?= $base ?>views/system/audit_logs.php"><i class="fa-solid fa-book"></i><span>Lịch sử</span></a>
      <?php endif; ?>
      </nav>
    </aside>

  <!-- Main Content Wrapper -->
  <main class="app-main">
    <!-- Header include: /includes/header.php -->
