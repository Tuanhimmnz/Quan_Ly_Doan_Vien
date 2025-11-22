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
$titleLoai = $danhgia['loai'] ?? 'Khen thưởng / Kỷ luật';

// Lấy danh sách đoàn viên để hiển thị trong dropdown
$doanviens = getAllDoanVienForDropdown();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>DNU - Cập nhật <?= htmlspecialchars($titleLoai) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/ui.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
<div class="container mt-4 reveal-on-scroll">
    <h3 class="text-center mb-4 text-primary">CHỈNH SỬA <?= strtoupper(htmlspecialchars($titleLoai)) ?></h3>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm reveal-on-scroll">
        <div class="card-body">
            <form action="../../handle/danhgia_process.php" method="POST" class="ui-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= htmlspecialchars($danhgia['id']) ?>">

                <div class="mb-3">
                    <label for="doanvien_id" class="form-label">Đoàn viên</label>
                    <select class="form-select form-control" id="doanvien_id" name="doanvien_id" required>
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
                    <label for="loai" class="form-label">Loại</label>
                    <select class="form-select form-control" id="loai" name="loai" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="Khen thưởng" <?= ($danhgia['loai'] === 'Khen thưởng') ? 'selected' : '' ?>>Khen thưởng</option>
                        <option value="Kỷ luật" <?= ($danhgia['loai'] === 'Kỷ luật') ? 'selected' : '' ?>>Kỷ luật</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="mo_ta" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="mo_ta" name="mo_ta" rows="2"><?= htmlspecialchars($danhgia['mo_ta'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="ngay_quyet_dinh" class="form-label">Ngày quyết định</label>
                    <input type="date" class="form-control" id="ngay_quyet_dinh" name="ngay_quyet_dinh"
                           value="<?= htmlspecialchars($danhgia['ngay_quyet_dinh'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="noi_dung" class="form-label">Nội dung</label>
                    <textarea class="form-control" id="noi_dung" name="noi_dung" rows="3"><?= htmlspecialchars($danhgia['noi_dung'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="trang_thai" class="form-label">Trạng thái</label>
                    <input type="text" class="form-control" id="trang_thai" name="trang_thai"
                           value="<?= htmlspecialchars($danhgia['trang_thai'] ?? '') ?>">
                </div>

                <div class="d-flex justify-content-end">
                    <a href="../danhgia.php" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/js/ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
