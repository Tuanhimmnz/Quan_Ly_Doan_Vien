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
      <a class="nav-item" href="<?= $base ?>views/doanvien.php"><i class="fa-solid fa-users"></i><span>Đoàn viên</span></a>
      <a class="nav-item" href="<?= $base ?>views/lop.php"><i class="fa-solid fa-layer-group"></i><span>Lớp / Chi đoàn</span></a>
      <a class="nav-item" href="<?= $base ?>views/khoa.php"><i class="fa-solid fa-building-columns"></i><span>Khoa / Viện</span></a>
      <a class="nav-item" href="<?= $base ?>views/doanphi.php"><i class="fa-solid fa-coins"></i><span>Đoàn phí</span></a>
      <a class="nav-item" href="<?= $base ?>views/danhgia.php"><i class="fa-solid fa-clipboard-check"></i><span>Đánh giá</span></a>
      <a class="nav-item" href="<?= $base ?>views/tracuu.php"><i class="fa-solid fa-magnifying-glass"></i><span>Tra cứu</span></a>
    </nav>
  </aside>

  <!-- Main Content Wrapper -->
  <main class="app-main">
    <!-- Header include: /includes/header.php -->
