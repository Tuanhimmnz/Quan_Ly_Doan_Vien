<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/lop_functions.php';
$khoas = getAllKhoaForDropdown();
$pageTitle = 'Thêm lớp mới';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4 reveal-on-scroll">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
      <div class="card reveal-on-scroll">
        <h3 class="mb-4 text-center">THÊM LỚP MỚI</h3>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" aria-label="Đóng">×</button>
          </div>
        <?php endif; ?>

          <form method="post" action="store_lop.php" class="ui-form">
          <div class="mb-3">
            <label class="form-label">Mã lớp</label>
            <input type="text" class="form-control" name="ma_lop" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tên lớp</label>
            <input type="text" class="form-control" name="ten_lop" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Khoa / Viện</label>
              <select name="khoa_id" class="form-select form-control" required>
              <option value="">-- Chọn khoa / viện --</option>
              <?php foreach ($khoas as $khoa): ?>
                <option value="<?= htmlspecialchars($khoa['id']) ?>">
                  <?= htmlspecialchars($khoa['ten_khoa']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Cố vấn học tập</label>
            <input type="text" class="form-control" name="co_van">
          </div>
          <div class="mb-3">
            <label class="form-label">Bí thư Chi đoàn</label>
            <input type="text" class="form-control" name="bi_thu">
          </div>
          <div class="d-flex justify-content-between">
            <a href="list_lop.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Quay lại</a>
            <div>
              <button type="reset" class="btn btn-outline-warning me-2">Xoá form</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Thêm mới</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
