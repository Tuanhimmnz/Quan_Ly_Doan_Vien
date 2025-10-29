<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

require_once __DIR__ . '/../../functions/lop_functions.php';

// Lấy danh sách khoa để chọn khoa cho lớp
$khoas = getAllKhoaForDropdown();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm lớp / chi đoàn mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h3 class="mt-3 mb-4 text-center">THÊM LỚP / CHI ĐOÀN MỚI</h3>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <script>
                // Tự ẩn thông báo sau 3s
                setTimeout(() => {
                    let alertNode = document.querySelector('.alert');
                    if (alertNode) {
                        let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                        bsAlert.close();
                    }
                }, 3000);
                </script>

                <form action="../../handle/lop_process.php" method="POST">
                    <input type="hidden" name="action" value="create">

                    <!-- Mã lớp / Chi đoàn -->
                    <div class="mb-3">
                        <label class="form-label">Mã lớp / Chi đoàn</label>
                        <input
                            type="text"
                            class="form-control"
                            name="ma_lop"
                            placeholder="VD: DCT123, CNTT-K45..."
                            required>
                    </div>

                    <!-- Tên lớp / Chi đoàn -->
                    <div class="mb-3">
                        <label class="form-label">Tên lớp / Chi đoàn</label>
                        <input
                            type="text"
                            class="form-control"
                            name="ten_lop"
                            placeholder="VD: Công nghệ thông tin K45"
                            required>
                    </div>

                    <!-- Thuộc khoa nào -->
                    <div class="mb-3">
                        <label class="form-label">Khoa / Viện</label>
                        <select class="form-select" name="khoa_id" required>
                            <option value="">-- Chọn khoa / viện --</option>
                            <?php foreach ($khoas as $khoa): ?>
                                <option value="<?= $khoa['id'] ?>">
                                    <?= htmlspecialchars($khoa['ten_khoa']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Cố vấn học tập -->
                    <div class="mb-3">
                        <label class="form-label">Cố vấn học tập / GVCN</label>
                        <input
                            type="text"
                            class="form-control"
                            name="co_van"
                            placeholder="VD: ThS. Nguyễn Văn A">
                    </div>

                    <!-- Bí thư chi đoàn -->
                    <div class="mb-3">
                        <label class="form-label">Bí thư chi đoàn</label>
                        <input
                            type="text"
                            class="form-control"
                            name="bi_thu"
                            placeholder="VD: Trần Thị B">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Lưu lớp</button>
                        <a href="../lop.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
