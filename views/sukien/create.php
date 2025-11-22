<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
$pageTitle = 'Thêm sự kiện';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="m-0">Thêm sự kiện</h3>
      <a href="../sukien.php" class="btn btn-secondary btn-sm">Quay lại</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form action="../../handle/sukien_process.php" method="post" class="row g-3">
        <input type="hidden" name="action" value="create">
        <div class="col-md-6">
          <label class="form-label">Tên sự kiện</label>
          <input type="text" name="ten_su_kien" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Ngày tổ chức</label>
          <input type="date" name="ngay_to_chuc" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Cấp tổ chức</label>
          <input type="text" name="cap_to_chuc" class="form-control" placeholder="Khoa/Viện/Đoàn trường">
        </div>
        <div class="col-md-4">
          <label class="form-label">Trạng thái</label>
          <input type="text" name="trang_thai" class="form-control" placeholder="Sắp diễn ra/Hoàn thành...">
        </div>
        <div class="col-12">
          <label class="form-label">Mô tả</label>
          <textarea name="mo_ta" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="../sukien.php" class="btn btn-secondary">Hủy</a>
          <button class="btn btn-primary" type="submit">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
