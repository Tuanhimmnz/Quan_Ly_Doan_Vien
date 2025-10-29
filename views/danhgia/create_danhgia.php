<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/doanvien_functions.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm đánh giá đoàn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">
    <h3 class="text-center mb-4">THÊM ĐÁNH GIÁ ĐOÀN VIÊN</h3>

    <?php
    // Hiển thị thông báo lỗi nếu có
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="../../handle/danhgia_process.php" method="POST">
                <input type="hidden" name="action" value="create">

                <div class="mb-3">
                    <label for="doanvien_id" class="form-label">Chọn đoàn viên</label>
                    <select name="doanvien_id" id="doanvien_id" class="form-select" required>
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
                    <label for="nam_hoc" class="form-label">Năm học</label>
                    <input type="text" name="nam_hoc" id="nam_hoc" class="form-control" placeholder="VD: 2024-2025" required>
                </div>

                <div class="mb-3">
                    <label for="xep_loai" class="form-label">Xếp loại đoàn viên</label>
                    <select name="xep_loai" id="xep_loai" class="form-select" required>
                        <option value="">-- Chọn xếp loại --</option>
                        <option value="Xuất sắc">Xuất sắc</option>
                        <option value="Tốt">Tốt</option>
                        <option value="Khá">Khá</option>
                        <option value="Trung bình">Trung bình</option>
                        <option value="Yếu">Yếu</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="khen_thuong" class="form-label">Khen thưởng</label>
                    <input type="text" name="khen_thuong" id="khen_thuong" class="form-control" placeholder="VD: Giấy khen đoàn viên xuất sắc">
                </div>

                <div class="mb-3">
                    <label for="ky_luat" class="form-label">Kỷ luật</label>
                    <input type="text" name="ky_luat" id="ky_luat" class="form-control" placeholder="VD: Không có">
                </div>

                <div class="mb-3">
                    <label for="ghi_chu" class="form-label">Ghi chú</label>
                    <textarea name="ghi_chu" id="ghi_chu" class="form-control" rows="3" placeholder="Thêm nhận xét, góp ý..."></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../danhgia.php" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Lưu đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
