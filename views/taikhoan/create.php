<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
$pageTitle = 'Thêm tài khoản';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="m-0">Thêm tài khoản</h3>
      <a href="../taikhoan.php" class="btn btn-secondary btn-sm">Quay lại</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form action="../../handle/taikhoan_process.php" method="post" class="row g-3">
        <input type="hidden" name="action" value="create">
        <div class="col-md-6">
          <label class="form-label">Họ tên</label>
          <input type="text" name="ho_ten" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Tên đăng nhập</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Mật khẩu</label>
          <input type="text" name="password" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Vai trò</label>
          <input type="text" name="role" class="form-control" placeholder="admin/Cán bộ/user" value="user">
        </div>
        <div class="col-md-4">
          <label class="form-label">Trạng thái</label>
          <input type="text" name="trang_thai" class="form-control" placeholder="Hoạt động/Khóa">
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
