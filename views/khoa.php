<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../handle/khoa_process.php';
$khoas = handleGetAllKhoa();
$pageTitle = 'Quản lý Khoa / Viện - Đại học Đại Nam 2025';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
  <div class="card">
    <div class="card-header">
      <h3 class="m-0">DANH SÁCH KHOA / VIỆN</h3>
      <a href="khoa/create_khoa.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm Khoa / Viện</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">
        <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" aria-label="Đóng">×</button>
      </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" aria-label="Đóng">×</button>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Tên Khoa / Viện</th>
            <th>Trưởng khoa / Viện trưởng</th>
            <th>SĐT liên hệ</th>
            <th>Email liên hệ</th>
            <th>Mô tả</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($khoas as $index => $khoa): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($khoa['ten_khoa']) ?></td>
              <td><?= htmlspecialchars($khoa['truong_khoa'] ?? '') ?></td>
              <td><?= htmlspecialchars($khoa['sdt_lien_he'] ?? '') ?></td>
              <td><?= htmlspecialchars($khoa['email_lien_he'] ?? '') ?></td>
              <td style="max-width: 250px; white-space: pre-line;">
                <?= nl2br(htmlspecialchars($khoa['mo_ta'] ?? '')); ?>
              </td>
              <td>
                <a href="khoa/edit_khoa.php?id=<?= $khoa['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                <a href="../handle/khoa_process.php?action=delete&id=<?= $khoa['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa khoa / viện này?');">
                  <i class="fa fa-trash"></i> Xóa
                </a>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (count($khoas) === 0): ?>
            <tr>
              <td colspan="7" class="text-center text-muted">Chưa có khoa / viện nào.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
