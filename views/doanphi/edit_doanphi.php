<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/security.php'; // [FITDNU-ADD]
checkLogin(__DIR__ . '/../../index.php');

// cần danh sách đoàn viên để chọn ai đóng phí và các hàm đoàn phí
require_once __DIR__ . '/../../functions/doanphi_functions.php';
$dsDoanVien = getAllDoanVienForDropdown(); // id, ma_sv, ho_ten

// Phân biệt chế độ tạo mới / sửa
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$current = $editing ? getDoanPhiById($id) : null;

// Giá trị mặc định cho form
$val_doanvien_id = $current['doanvien_id'] ?? '';
$val_nam_hoc     = $current['nam_hoc'] ?? '';
$val_da_nop      = isset($current['da_nop']) ? (string)$current['da_nop'] : '1';
$val_ngay_nop    = $current['ngay_nop'] ?? '';
$val_so_tien     = isset($current['so_tien_nop']) ? (float)$current['so_tien_nop'] : '';
// [FITDNU-ADD] CSRF token for form submit
$csrf_token = csrf_generate_token('doanphi_edit');
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Ghi nhận đoàn phí</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

    <body>
        <div class="container mt-3 reveal-on-scroll">
            <div class="row justify-content-center">
                <div class="col-md-6">
                <h3 class="mt-3 mb-4 text-center"><?= $editing ? 'SỬA ĐOÀN PHÍ' : 'THÊM ĐOÀN PHÍ' ?></h3>
                
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($_GET['error']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
                }
                ?>
                <script>
                setTimeout(() => {
                    let alertNode = document.querySelector('.alert');
                    if (alertNode) {
                        let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                        bsAlert.close();
                    }
                }, 3000);
                </script>
                
                  <form action="../../handle/doanphi_process.php" method="POST" class="ui-form">
                    <!-- [FITDNU-ADD] CSRF token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="action" value="<?= $editing ? 'edit' : 'create' ?>">
                    <?php if ($editing): ?>
                      <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="doanvien_id" class="form-label">Đoàn viên</label>
                          <select class="form-select form-control" id="doanvien_id" name="doanvien_id" required>
                            <option value="">-- Chọn đoàn viên --</option>
                            <?php foreach ($dsDoanVien as $dv):
                                $sel = ($val_doanvien_id !== '' && (int)$val_doanvien_id === (int)$dv['id']) ? 'selected' : '';
                            ?>
                                <option value="<?= $dv['id'] ?>" <?= $sel ?>>
                                    <?= htmlspecialchars($dv['ma_sv']) ?> - <?= htmlspecialchars($dv['ho_ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nam_hoc" class="form-label">Năm học</label>
                        <input type="text" class="form-control" id="nam_hoc" name="nam_hoc" placeholder="2024-2025" value="<?= htmlspecialchars($val_nam_hoc) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="da_nop" class="form-label">Đã nộp?</label>
                          <select class="form-select form-control" id="da_nop" name="da_nop" required>
                            <option value="1" <?= ($val_da_nop === '1') ? 'selected' : '' ?>>Đã nộp</option>
                            <option value="0" <?= ($val_da_nop === '0') ? 'selected' : '' ?>>Chưa nộp</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="so_tien_nop" class="form-label">Số tiền nộp</label>
                        <input type="number" step="1000" min="0" class="form-control" id="so_tien_nop" name="so_tien_nop" placeholder="Ví dụ: 50000" value="<?= htmlspecialchars($val_so_tien === '' ? '' : (string)$val_so_tien) ?>">
                        <div class="form-text">Để trống hoặc 0 nếu chưa nộp.</div>
                    </div>

                    <div class="mb-3">
                        <label for="ngay_nop" class="form-label">Ngày nộp</label>
                        <input type="date" class="form-control" id="ngay_nop" name="ngay_nop" value="<?= htmlspecialchars($val_ngay_nop) ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><?= $editing ? 'Cập nhật' : 'Lưu đoàn phí' ?></button>
                        <a href="../doanphi.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Ẩn/hiện và reset số tiền + ngày nộp theo trạng thái đã nộp
      (function () {
        const daNop = document.getElementById('da_nop');
        const soTien = document.getElementById('so_tien_nop');
        const ngayNop = document.getElementById('ngay_nop');
        function syncState() {
          if (!daNop || !soTien || !ngayNop) return;
          if (daNop.value === '0') {
            // chưa nộp
            soTien.value = '';
            ngayNop.value = '';
          }
        }
        daNop && daNop.addEventListener('change', syncState);
      })();
    </script>
</body>

</html>
