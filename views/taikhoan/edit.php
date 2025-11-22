<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/taikhoan_functions.php';
checkLogin(__DIR__ . '/../../index.php');
if (!isset($_GET['id'])) { header("Location: ../taikhoan.php?error=Thiếu id"); exit; }
$id = (int)$_GET['id'];
$record = getTaiKhoanById($id);
if (!$record) { header("Location: ../taikhoan.php?error=Không tìm thấy tài khoản"); exit; }
$pageTitle = 'Sửa tài khoản';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="m-0">Sửa tài khoản</h3>
      <a href="../taikhoan.php" class="btn btn-secondary btn-sm">Quay lại</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form action="../../handle/taikhoan_process.php" method="post" class="row g-3">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="col-md-6">
          <label class="form-label">Họ tên</label>
          <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($record['ho_ten'] ?? '') ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">Tên đăng nhập</label>
          <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($record['username'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Mật khẩu (để trống nếu giữ nguyên)</label>
          <input type="text" name="password" class="form-control" value="">
        </div>
        <div class="col-md-4">
          <label class="form-label">Vai trò</label>
          <input type="text" name="role" class="form-control" value="<?= htmlspecialchars($record['role'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Trạng thái</label>
          <input type="text" name="trang_thai" class="form-control" value="<?= htmlspecialchars($record['trang_thai'] ?? '') ?>">
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="../taikhoan.php" class="btn btn-secondary">Hủy</a>
          <button class="btn btn-primary" type="submit">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
