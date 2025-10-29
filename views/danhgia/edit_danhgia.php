<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/danhgia_functions.php';
require_once __DIR__ . '/../../functions/doanvien_functions.php';

// Kiểm tra có ID không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../danhgia.php?error=Không tìm thấy bản đánh giá");
    exit;
}

$id = $_GET['id'];

// Lấy thông tin đánh giá theo ID
$danhgia = getDanhGiaById($id);
if (!$danhgia) {
    header("Location: ../danhgia.php?error=Không tìm thấy bản đánh giá");
    exit;
}

// Lấy danh sách đoàn viên để hiển thị trong dropdown
$doanviens = getAllDoanVienForDropdown();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>DNU - Cập nhật đánh giá đoàn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">
    <h3 class="text-center mb-4 text-primary">CHỈNH SỬA ĐÁNH GIÁ ĐOÀN VIÊN</h3>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="../../handle/danhgia_process.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= htmlspecialchars($danhgia['id']) ?>">

                <div class="mb-3">
                    <label for="doanvien_id" class="form-label">Đoàn viên</label>
                    <select class="form-select" id="doanvien_id" name="doanvien_id" required>
                        <option value="">-- Chọn đoàn viên --</option>
                        <?php foreach ($doanviens as $dv): ?>
                            <option value="<?= $dv['id'] ?>"
                                <?= ($dv['id'] == $danhgia['doanvien_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dv['ma_sv']) ?> - <?= htmlspecialchars($dv['ho_ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nam_hoc" class="form-label">Năm học</label>
                    <input type="text" class="form-control" id="nam_hoc" name="nam_hoc"
                           value="<?= htmlspecialchars($danhgia['nam_hoc']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="xep_loai" class="form-label">Xếp loại</label>
                    <select class="form-select" id="xep_loai" name="xep_loai" required>
                        <option value="">-- Chọn xếp loại --</option>
                        <option value="Xuất sắc" <?= ($danhgia['xep_loai'] == 'Xuất sắc') ? 'selected' : '' ?>>Xuất sắc</option>
                        <option value="Tốt" <?= ($danhgia['xep_loai'] == 'Tốt') ? 'selected' : '' ?>>Tốt</option>
                        <option value="Khá" <?= ($danhgia['xep_loai'] == 'Khá') ? 'selected' : '' ?>>Khá</option>
                        <option value="Trung bình" <?= ($danhgia['xep_loai'] == 'Trung bình') ? 'selected' : '' ?>>Trung bình</option>
                        <option value="Yếu" <?= ($danhgia['xep_loai'] == 'Yếu') ? 'selected' : '' ?>>Yếu</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="khen_thuong" class="form-label">Khen thưởng</label>
                    <input type="text" class="form-control" id="khen_thuong" name="khen_thuong"
                           value="<?= htmlspecialchars($danhgia['khen_thuong'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="ky_luat" class="form-label">Kỷ luật</label>
                    <input type="text" class="form-control" id="ky_luat" name="ky_luat"
                           value="<?= htmlspecialchars($danhgia['ky_luat'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="ghi_chu" class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3"><?= htmlspecialchars($danhgia['ghi_chu'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="../danhgia.php" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
