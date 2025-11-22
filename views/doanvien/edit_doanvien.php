<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/lop_functions.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Chỉnh sửa đoàn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/ui.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

    <body>
        <div class="container mt-3 reveal-on-scroll">
        <h3 class="mt-3 mb-4 text-center">CHỈNH SỬA ĐOÀN VIÊN</h3>

        <?php
            // Kiểm tra có ID không
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                header("Location: ../doanvien.php?error=Không tìm thấy đoàn viên");
                exit;
            }
            
            $id = $_GET['id'];
            
            // Lấy thông tin đoàn viên
            require_once __DIR__ . '/../../handle/doanvien_process.php';
            $doanvien = handleGetDoanVienById($id);

            if (!$doanvien) {
                header("Location: ../doanvien.php?error=Không tìm thấy đoàn viên");
                exit;
            }

            // Lấy danh sách lớp / chi đoàn cho dropdown
            $lops = getAllLopForDropdown();

            // Hiển thị thông báo lỗi nếu có
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

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card reveal-on-scroll">
                    <div class="card-body">

                            <form action="../../handle/doanvien_process.php" method="POST" class="ui-form">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($doanvien['id']); ?>">

                            <div class="mb-3">
                                <label for="ma_sv" class="form-label">Mã sinh viên</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="ma_sv"
                                    name="ma_sv"
                                    value="<?php echo htmlspecialchars($doanvien['ma_sv']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="ho_ten" class="form-label">Họ và tên</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="ho_ten"
                                    name="ho_ten"
                                    value="<?php echo htmlspecialchars($doanvien['ho_ten']); ?>"
                                    required>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="ngay_sinh"
                                        name="ngay_sinh"
                                        value="<?php echo htmlspecialchars($doanvien['ngay_sinh'] ?? ''); ?>">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="gioi_tinh" class="form-label">Giới tính</label>
                                    <select class="form-select form-control" id="gioi_tinh" name="gioi_tinh">
                                        <option value="">-- Chọn giới tính --</option>
                                        <option value="Nam"  <?= ($doanvien['gioi_tinh'] === 'Nam')  ? 'selected' : '' ?>>Nam</option>
                                        <option value="Nữ"   <?= ($doanvien['gioi_tinh'] === 'Nữ')   ? 'selected' : '' ?>>Nữ</option>
                                        <option value="Khác" <?= ($doanvien['gioi_tinh'] === 'Khác') ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="sdt" class="form-label">Số điện thoại</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="sdt"
                                        name="sdt"
                                        value="<?php echo htmlspecialchars($doanvien['sdt'] ?? ''); ?>">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="<?php echo htmlspecialchars($doanvien['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="lop_id" class="form-label">Lớp / Chi đoàn</label>
                                  <select class="form-select form-control" id="lop_id" name="lop_id" required>
                                    <option value="">-- Chọn lớp / chi đoàn --</option>
                                    <?php foreach ($lops as $lop): ?>
                                        <option
                                            value="<?= $lop['id'] ?>"
                                            <?= ($lop['id'] == $doanvien['lop_id']) ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars($lop['ma_lop']) ?> - <?= htmlspecialchars($lop['ten_lop']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="chuc_vu" class="form-label">Chức vụ trong chi đoàn</label>
                                  <select class="form-select form-control" id="chuc_vu" name="chuc_vu">
                                    <option value="">-- Chọn chức vụ --</option>
                                    <option value="Đoàn viên"     <?= ($doanvien['chuc_vu'] === 'Đoàn viên')     ? 'selected' : '' ?>>Đoàn viên</option>
                                    <option value="Bí thư"        <?= ($doanvien['chuc_vu'] === 'Bí thư')        ? 'selected' : '' ?>>Bí thư</option>
                                    <option value="Phó bí thư"    <?= ($doanvien['chuc_vu'] === 'Phó bí thư')    ? 'selected' : '' ?>>Phó bí thư</option>
                                    <option value="Ủy viên BCH"   <?= ($doanvien['chuc_vu'] === 'Ủy viên BCH')   ? 'selected' : '' ?>>Ủy viên BCH</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="trang_thai" class="form-label">Trạng thái sinh hoạt</label>
                                  <select class="form-select form-control" id="trang_thai" name="trang_thai">
                                    <option value="Đang sinh hoạt"   <?= ($doanvien['trang_thai'] === 'Đang sinh hoạt')   ? 'selected' : '' ?>>Đang sinh hoạt</option>
                                    <option value="Chuyển sinh hoạt" <?= ($doanvien['trang_thai'] === 'Chuyển sinh hoạt') ? 'selected' : '' ?>>Chuyển sinh hoạt</option>
                                    <option value="Đã ra trường"     <?= ($doanvien['trang_thai'] === 'Đã ra trường')     ? 'selected' : '' ?>>Đã ra trường</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ngay_vao_doan" class="form-label">Ngày vào Đoàn</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="ngay_vao_doan"
                                    name="ngay_vao_doan"
                                    value="<?php echo htmlspecialchars($doanvien['ngay_vao_doan'] ?? ''); ?>">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../doanvien.php" class="btn btn-secondary me-md-2">Hủy</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>

                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <script src="../../assets/js/ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
