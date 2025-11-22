<?php
// [FITDNU-ADD] Dữ liệu > Nhập từ Excel/CSV
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/security.php';
checkLogin(__DIR__ . '/../../index.php');

$pageTitle = 'Nhập dữ liệu từ Excel/CSV';
require __DIR__ . '/../../includes/header.php';

$preview = isset($_GET['preview']);
$done = isset($_GET['done']);
$previewData = $_SESSION['import_preview'] ?? null;
$result = $_SESSION['import_result'] ?? null;
if ($done) unset($_SESSION['import_preview']);
$token = csrf_generate_token('import_data');
?>

<div class="container mt-3">
  <div class="card mb-3">
    <div class="card-header">
      <h3 class="m-0">Dữ liệu &raquo; Nhập từ Excel/CSV</h3>
    </div>
    <div class="card-body">
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <?php
        function render_form($entity, $token, $title, $desc) {
          $template = '../../samples/' . $entity . '_template.csv';
      ?>
        <div class="import-section card mb-3 shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <strong><?= htmlspecialchars($title) ?></strong>
              <div class="text-muted small"><?= htmlspecialchars($desc) ?></div>
            </div>
            <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars($template) ?>">Tải file mẫu</a>
          </div>
          <div class="card-body">
            <form class="row g-3 align-items-end" method="post" action="../../handle/import_process.php" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
              <input type="hidden" name="entity" value="<?= htmlspecialchars($entity) ?>">
              <div class="col-md-4">
                <label class="form-label">Chọn tệp (.xlsx/.csv)</label>
                <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
              </div>
              <div class="col-md-4 d-flex flex-wrap gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="skip_missing" id="skip_missing_<?= $entity ?>" checked>
                  <label class="form-check-label" for="skip_missing_<?= $entity ?>">Bỏ qua dòng thiếu cột</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="update_on_dup" id="update_on_dup_<?= $entity ?>" checked>
                  <label class="form-check-label" for="update_on_dup_<?= $entity ?>">Cập nhật nếu trùng khóa</label>
                </div>
              </div>
              <div class="col-md-4 d-flex gap-2">
                <button type="submit" name="action" value="preview" class="btn btn-secondary">Xem trước</button>
                <button type="submit" name="action" value="import" class="btn btn-primary" onclick="return confirm('Xác nhận nhập dữ liệu?');">Nhập dữ liệu</button>
              </div>
            </form>
          </div>
        </div>
      <?php } ?>

      <?php
        render_form('doanvien', $token, 'Nhập Đoàn viên', 'Mã SV, họ tên, ngày sinh, lớp/chi đoàn...');
        render_form('lop', $token, 'Nhập Lớp/Chi đoàn', 'Mã lớp, tên lớp, khoa...');
        render_form('khoa', $token, 'Nhập Khoa/Viện', 'Tên khoa, mô tả, liên hệ...');
        render_form('khenthuong', $token, 'Nhập Khen thưởng', 'Mã SV, mô tả, ngày quyết định, nội dung...');
        render_form('kyluat', $token, 'Nhập Kỷ luật', 'Mã SV, mô tả, ngày quyết định, nội dung...');
        render_form('diemrenluyen', $token, 'Nhập Điểm rèn luyện', 'Mã SV, năm học, điểm, xếp loại...');
        render_form('sukien', $token, 'Nhập Sự kiện', 'Tên sự kiện, ngày tổ chức, cấp tổ chức...');
        render_form('taikhoan', $token, 'Nhập Tài khoản', 'Họ tên, username, password, role...');
      ?>

      <?php if ($preview && is_array($previewData)): ?>
        <hr>
        <h5>Xem trước (20 dòng đầu)</h5>
        <p class="text-muted">Tệp: <?= htmlspecialchars($previewData['name']) ?> | Đối tượng: <?= htmlspecialchars($previewData['entity']) ?></p>
        <?php $rows = array_slice($previewData['rows'], 0, 20); ?>
        <div class="table-responsive">
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <?php if (!empty($rows)) { foreach (array_keys($rows[0]) as $h) { echo '<th>' . htmlspecialchars($h) . '</th>'; } } ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <?php foreach ($r as $v): ?>
                    <td><?= htmlspecialchars((string)$v) ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <?php if ($done && is_array($result)): ?>
        <hr>
        <h5>Kết quả</h5>
        <div class="alert alert-info">
          Tổng: <?= (int)$result['total'] ?> | Thành công: <?= (int)$result['ok'] ?> | Lỗi: <?= count($result['errors'] ?? []) ?>
        </div>
        <?php if (!empty($result['errors'])): ?>
          <div class="table-responsive">
            <table class="table table-sm table-hover">
              <thead><tr><th>Dòng</th><th>Lý do</th></tr></thead>
              <tbody>
                <?php foreach ($result['errors'] as $e): ?>
                  <tr><td><?= (int)$e['row'] ?></td><td><?= htmlspecialchars($e['reason']) ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <hr>
      <div class="alert alert-info">
        Gợi ý cột bắt buộc:<br>
        - Đoàn viên: ma_sv, ho_ten, ngay_sinh(YYYY-MM-DD hoặc DD/MM/YYYY), gioi_tinh, sdt, email, lop_ma (hoặc lop_id), chuc_vu, trang_thai, ngay_vao_doan<br>
        - Lớp: ma_lop, ten_lop, khoa_id (ưu tiên), co_van_hoc_tap / co_van, bi_thu_chi_doan / bi_thu (hỗ trợ đọc file cũ có khoa_ten nhưng khuyến khích dùng khoa_id để đồng bộ)<br>
        - Khoa: ten_khoa, mo_ta, truong_khoa, sdt_lien_he, email_lien_he<br>
        - Khen thưởng/Kỷ luật: ma_sv, mo_ta, ngay_quyet_dinh, noi_dung, trang_thai<br>
        - Điểm rèn luyện: ma_sv, nam_hoc, diem, xep_loai, ghi_chu<br>
        - Sự kiện: ten_su_kien, mo_ta, ngay_to_chuc, cap_to_chuc, trang_thai<br>
        - Tài khoản: ho_ten, username, password, role, trang_thai
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
