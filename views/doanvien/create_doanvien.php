<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/lop_functions.php';

// Lấy danh sách lớp / chi đoàn cho dropdown
$lops = getAllLopForDropdown();
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Thêm đoàn viên mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="mt-3 mb-4 text-center">THÊM ĐOÀN VIÊN MỚI</h3>
                
                <?php
                // Hiển thị thông báo lỗi
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($_GET['error']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
                }
                ?>
                <script>
                // Sau 3 giây sẽ tự động ẩn alert
                setTimeout(() => {
                    let alertNode = document.querySelector('.alert');
                    if (alertNode) {
                        let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                        bsAlert.close();
                    }
                }, 3000);
                </script>
                
                <form action="../../handle/doanvien_process.php" method="POST">
                    <input type="hidden" name="action" value="create">

                    <div class="mb-3">
                        <label for="ma_sv" class="form-label">Mã sinh viên</label>
                        <input type="text" class="form-control" id="ma_sv" name="ma_sv" required>
                    </div>

                    <div class="mb-3">
                        <label for="ho_ten" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="gioi_tinh" class="form-label">Giới tính</label>
                            <select class="form-select" id="gioi_tinh" name="gioi_tinh">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="sdt" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="sdt" name="sdt">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lop_id" class="form-label">Lớp / Chi đoàn</label>
                        <select class="form-select" id="lop_id" name="lop_id" required>
                            <option value="">-- Chọn lớp / chi đoàn --</option>
                            <?php foreach ($lops as $lop): ?>
                                <option value="<?= $lop['id'] ?>">
                                    <?= htmlspecialchars($lop['ma_lop']) ?> - <?= htmlspecialchars($lop['ten_lop']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="chuc_vu" class="form-label">Chức vụ trong chi đoàn</label>
                        <select class="form-select" id="chuc_vu" name="chuc_vu">
                            <option value="">-- Chọn chức vụ --</option>
                            <option value="Đoàn viên">Đoàn viên</option>
                            <option value="Bí thư">Bí thư</option>
                            <option value="Phó bí thư">Phó bí thư</option>
                            <option value="Ủy viên BCH">Ủy viên BCH</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="trang_thai" class="form-label">Trạng thái sinh hoạt</label>
                        <select class="form-select" id="trang_thai" name="trang_thai">
                            <option value="Đang sinh hoạt">Đang sinh hoạt</option>
                            <option value="Chuyển sinh hoạt">Chuyển sinh hoạt</option>
                            <option value="Đã ra trường">Đã ra trường</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ngay_vao_doan" class="form-label">Ngày vào Đoàn</label>
                        <input type="date" class="form-control" id="ngay_vao_doan" name="ngay_vao_doan">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Thêm đoàn viên</button>
                        <a href="../doanvien.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
