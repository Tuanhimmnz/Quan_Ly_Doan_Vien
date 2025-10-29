<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/lop_functions.php';
// Lấy danh sách lớp
$lops = getAllLop();
$pageTitle = 'Danh sách lớp';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <div class="card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">DANH SÁCH LỚP</h3>
      <a href="create_lop.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm lớp mới</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" aria-label="Đóng">×</button>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">
        <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" aria-label="Đóng">×</button>
      </div>
    <?php endif; ?>

    <div class="table-responsive shadow-sm">
      <table class="table table-striped table-hover align-middle mb-0">
        <thead>
          <tr>
            <th style="width:60px;">ID</th>
            <th>Mã lớp</th>
            <th>Tên lớp</th>
            <th>Khoa / Viện</th>
            <th>Cố vấn học tập</th>
            <th>Bí thư Chi đoàn</th>
            <th style="width:180px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($lops)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted" style="padding:16px;">Chưa có lớp nào.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($lops as $lop): ?>
              <tr>
                <td><?= htmlspecialchars($lop['id']) ?></td>
                <td><?= htmlspecialchars($lop['ma_lop']) ?></td>
                <td><?= htmlspecialchars($lop['ten_lop']) ?></td>
                <td><?= htmlspecialchars($lop['ten_khoa']) ?></td>
                <td><?= htmlspecialchars($lop['co_van']) ?></td>
                <td><?= htmlspecialchars($lop['bi_thu']) ?></td>
                <td>
                  <a class="btn btn-sm btn-warning me-1" href="edit_lop.php?id=<?= urlencode($lop['id']) ?>"><i class="fa fa-pen"></i> Sửa</a>
                  <a class="btn btn-sm btn-danger" href="delete_lop.php?id=<?= urlencode($lop['id']) ?>" onclick="return confirm('Bạn chắc chắn muốn xóa lớp này?');"><i class="fa fa-trash"></i> Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      <a href="../../views/doanvien.php" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Về trang quản trị</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
