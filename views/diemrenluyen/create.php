<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/doanvien_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$doanviens = getAllDoanVien();
$pageTitle = 'Thêm điểm rèn luyện';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h3 class="m-0">Thêm điểm rèn luyện</h3>
      <a href="../diemrenluyen.php" class="btn btn-secondary btn-sm">Quay lại</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form action="../../handle/diemrenluyen_process.php" method="post" class="row g-3">
        <input type="hidden" name="action" value="create">
        <div class="col-md-6">
          <label class="form-label">Đoàn viên</label>
          <select name="doanvien_id" class="form-select" required>
            <option value="">-- Chọn đoàn viên --</option>
            <?php foreach ($doanviens as $dv): ?>
              <option value="<?= $dv['id'] ?>"><?= htmlspecialchars($dv['ma_sv'] . ' - ' . $dv['ho_ten']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Năm học</label>
          <input type="text" name="nam_hoc" class="form-control" placeholder="2024-2025" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Điểm</label>
          <input type="number" step="0.01" name="diem" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Xếp loại</label>
          <input type="text" name="xep_loai" class="form-control" placeholder="Xuất sắc/Tốt/...">
        </div>
        <div class="col-12">
          <label class="form-label">Ghi chú</label>
          <textarea name="ghi_chu" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="../diemrenluyen.php" class="btn btn-secondary">Hủy</a>
          <button class="btn btn-primary" type="submit">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
