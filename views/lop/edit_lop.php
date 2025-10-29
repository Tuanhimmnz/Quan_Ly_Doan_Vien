<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/lop_functions.php';

$lop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lop = getLopById($lop_id);
if (!$lop) {
    header('Location: list_lop.php?error=Không tìm thấy lớp');
    exit;
}
$khoas = getAllKhoaForDropdown();
$pageTitle = 'Chỉnh sửa lớp';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
      <div class="card">
        <h3 class="mb-4 text-center">CHỈNH SỬA LỚP</h3>

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

        <form method="post" action="update_lop.php">
          <input type="hidden" name="id" value="<?= htmlspecialchars($lop['id']) ?>">
          <div class="mb-3">
            <label class="form-label">Mã lớp</label>
            <input type="text" class="form-control" name="ma_lop" value="<?= htmlspecialchars($lop['ma_lop']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tên lớp</label>
            <input type="text" class="form-control" name="ten_lop" value="<?= htmlspecialchars($lop['ten_lop']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Khoa / Viện</label>
            <select name="khoa_id" class="form-select" required>
              <option value="">-- Chọn khoa / viện --</option>
              <?php foreach ($khoas as $khoa): ?>
                <option value="<?= htmlspecialchars($khoa['id']) ?>" <?= ($khoa['id'] == $lop['khoa_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($khoa['ten_khoa']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Cố vấn học tập</label>
            <input type="text" class="form-control" name="co_van" value="<?= htmlspecialchars($lop['co_van']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Bí thư Chi đoàn</label>
            <input type="text" class="form-control" name="bi_thu" value="<?= htmlspecialchars($lop['bi_thu']) ?>">
          </div>
          <div class="d-flex justify-content-between">
            <a href="list_lop.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu thay đổi</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
