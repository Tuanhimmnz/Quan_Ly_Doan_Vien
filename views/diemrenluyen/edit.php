<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/doanvien_functions.php';
require_once __DIR__ . '/../../functions/diemrenluyen_functions.php';
checkLogin(__DIR__ . '/../../index.php');
if (!isset($_GET['id'])) { header("Location: ../diemrenluyen.php?error=Thiếu id"); exit; }
$id = (int)$_GET['id'];
$record = getDiemRenLuyenById($id);
if (!$record) { header("Location: ../diemrenluyen.php?error=Không tìm thấy bản ghi"); exit; }
$doanviens = getAllDoanVien();
$pageTitle = 'Sửa điểm rèn luyện';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h3 class="m-0">Sửa điểm rèn luyện</h3>
      <a href="../diemrenluyen.php" class="btn btn-secondary btn-sm">Quay lại</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form action="../../handle/diemrenluyen_process.php" method="post" class="row g-3">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="col-md-6">
          <label class="form-label">Đoàn viên</label>
          <select name="doanvien_id" class="form-select" required>
            <?php foreach ($doanviens as $dv): ?>
              <option value="<?= $dv['id'] ?>" <?= ($dv['id']==$record['doanvien_id'])?'selected':''; ?>><?= htmlspecialchars($dv['ma_sv'] . ' - ' . $dv['ho_ten']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Năm học</label>
          <input type="text" name="nam_hoc" class="form-control" value="<?= htmlspecialchars($record['nam_hoc']) ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Điểm</label>
          <input type="number" step="0.01" name="diem" class="form-control" value="<?= htmlspecialchars($record['diem']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Xếp loại</label>
          <input type="text" name="xep_loai" class="form-control" value="<?= htmlspecialchars($record['xep_loai'] ?? '') ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Ghi chú</label>
          <textarea name="ghi_chu" class="form-control" rows="3"><?= htmlspecialchars($record['ghi_chu'] ?? '') ?></textarea>
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
