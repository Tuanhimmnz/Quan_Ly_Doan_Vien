<?php
// views/admin/khai_bao/list.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../../functions/auth.php';
checkLogin(__DIR__ . '/../../../index.php');

// kiểm tra quyền admin
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . '/../../../functions/khai_bao_functions.php';

$status = $_GET['status'] ?? 'pending';
$list = kb_list($status);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Duyệt khai báo đoàn viên</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
  <h3 class="mb-3">Duyệt khai báo đoàn viên</h3>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_GET['success']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_GET['error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="mb-3">
    <a class="btn <?= $status==='pending'?'btn-primary':'btn-outline-primary' ?>" href="?status=pending">Chờ duyệt</a>
    <a class="btn <?= $status==='approved'?'btn-primary':'btn-outline-primary' ?>" href="?status=approved">Đã duyệt</a>
    <a class="btn <?= $status==='rejected'?'btn-primary':'btn-outline-primary' ?>" href="?status=rejected">Từ chối</a>
  </div>

  <div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Đoàn viên</th>
        <th>Mã SV</th>
        <th>Lớp</th>
        <th>SDT</th>
        <th>Email</th>
        <th>Ngày sinh</th>
        <th>Giới tính</th>
        <th>Nộp lúc</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['ho_ten'] ?: $r['dv_ho_ten']) ?></td>
          <td><?= htmlspecialchars($r['ma_sv'] ?: $r['dv_ma_sv']) ?></td>
          <td><?= htmlspecialchars((string)($r['lop_id'] ?? '')) ?></td>
          <td><?= htmlspecialchars($r['sdt'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['email'] ?? '') ?></td>
          <td><?= !empty($r['ngay_sinh']) ? date('d/m/Y', strtotime($r['ngay_sinh'])) : '' ?></td>
          <td><?= htmlspecialchars($r['gioi_tinh'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['submitted_at']) ?></td>
          <td style="white-space: nowrap;">
            <?php if ($status === 'pending'): ?>
              <form method="post" action="../../../handle/khai_bao_admin.php" style="display:inline;">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-sm btn-primary" type="submit">Duyệt</button>
              </form>
              <form method="post" action="../../../handle/khai_bao_admin.php" style="display:inline;">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-sm btn-outline-secondary" type="submit">Từ chối</button>
              </form>
            <?php else: ?>
              <em><?= htmlspecialchars($status) ?></em>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?>
        <tr><td colspan="10" class="text-center">Không có bản ghi</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
