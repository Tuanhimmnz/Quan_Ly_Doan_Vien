<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

// cần danh sách đoàn viên để chọn ai đóng phí
require_once __DIR__ . '/../../functions/doanvien_functions.php';
$dsDoanVien = getAllDoanVienForDropdown(); // giả định trả về id, ma_sv, ho_ten
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Ghi nhận đoàn phí</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="mt-3 mb-4 text-center">THÊM ĐOÀN PHÍ</h3>
                
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
                
                <form action="../../handle/doanphi_process.php" method="POST">
                    <input type="hidden" name="action" value="create">

                    <div class="mb-3">
                        <label for="doanvien_id" class="form-label">Đoàn viên</label>
                        <select class="form-select" id="doanvien_id" name="doanvien_id" required>
                            <option value="">-- Chọn đoàn viên --</option>
                            <?php foreach ($dsDoanVien as $dv): ?>
                                <option value="<?= $dv['id'] ?>">
                                    <?= htmlspecialchars($dv['ma_sv']) ?> - <?= htmlspecialchars($dv['ho_ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nam_hoc" class="form-label">Năm học</label>
                        <input type="text" class="form-control" id="nam_hoc" name="nam_hoc" placeholder="2024-2025" required>
                    </div>

                    <div class="mb-3">
                        <label for="da_nop" class="form-label">Đã nộp?</label>
                        <select class="form-select" id="da_nop" name="da_nop" required>
                            <option value="1">Đã nộp</option>
                            <option value="0">Chưa nộp</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ngay_nop" class="form-label">Ngày nộp</label>
                        <input type="date" class="form-control" id="ngay_nop" name="ngay_nop">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Lưu đoàn phí</button>
                        <a href="../doanphi.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
