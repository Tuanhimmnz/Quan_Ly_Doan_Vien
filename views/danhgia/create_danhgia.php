<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/doanvien_functions.php';
$presetLoai = $_GET['loai'] ?? 'Khen thưởng';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm đánh giá đoàn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/ui.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
<div class="container mt-4 reveal-on-scroll">
    <h3 class="text-center mb-4">THÊM <?= strtoupper(htmlspecialchars($presetLoai)) ?></h3>

    <?php
    // Hiển thị thông báo lỗi nếu có
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="../../handle/danhgia_process.php" method="POST" class="ui-form">
                <input type="hidden" name="action" value="create">

                <div class="mb-3">
                    <label for="doanvien_id" class="form-label">Chọn đoàn viên</label>
                    <select name="doanvien_id" id="doanvien_id" class="form-select form-control" required>
                        <option value="">-- Chọn đoàn viên --</option>
                        <?php
                        $doanviens = getAllDoanVien();
                        foreach ($doanviens as $dv):
                        ?>
                            <option value="<?= $dv['id'] ?>">
                                <?= htmlspecialchars($dv['ma_sv']) ?> - <?= htmlspecialchars($dv['ho_ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="loai" class="form-label">Loại</label>
                    <select name="loai" id="loai" class="form-select form-control" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="Khen thưởng" <?= ($presetLoai === 'Khen thưởng') ? 'selected' : '' ?>>Khen thưởng</option>
                        <option value="Kỷ luật" <?= ($presetLoai === 'Kỷ luật') ? 'selected' : '' ?>>Kỷ luật</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="mo_ta" class="form-label">Mô tả</label>
                    <textarea name="mo_ta" id="mo_ta" class="form-control" rows="2" placeholder="Tóm tắt quyết định"></textarea>
                </div>

                <div class="mb-3">
                    <label for="ngay_quyet_dinh" class="form-label">Ngày quyết định</label>
                    <input type="date" name="ngay_quyet_dinh" id="ngay_quyet_dinh" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="noi_dung" class="form-label">Nội dung</label>
                    <textarea name="noi_dung" id="noi_dung" class="form-control" rows="3" placeholder="Chi tiết khen thưởng / kỷ luật"></textarea>
                </div>

                <div class="mb-3">
                    <label for="trang_thai" class="form-label">Trạng thái</label>
                    <input type="text" name="trang_thai" id="trang_thai" class="form-control" placeholder="Đã ban hành / Dự thảo...">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../danhgia.php" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Lưu đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/js/ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
