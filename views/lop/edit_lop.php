<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

require_once __DIR__ . '/../../functions/khoa_functions.php';
$khoas = getAllKhoaForDropdown(); 
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Chỉnh sửa lớp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h3 class="mt-3 mb-4 text-center">CHỈNH SỬA LỚP</h3>
        <?php
            // Kiểm tra có ID không
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                header("Location: ../lop.php?error=Không tìm thấy lớp");
                exit;
            }
            
            $id = $_GET['id'];
            
            // Lấy thông tin lớp
            require_once __DIR__ . '/../../handle/lop_process.php';
            $lop = handleGetLopById($id);

            if (!$lop) {
                header("Location: ../lop.php?error=Không tìm thấy lớp");
                exit;
            }
            
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

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        <form action="../../handle/lop_process.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($lop['id']); ?>">

                            <div class="mb-3">
                                <label for="ma_lop" class="form-label">Mã lớp</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="ma_lop"
                                    name="ma_lop"
                                    value="<?php echo htmlspecialchars($lop['ma_lop']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="ten_lop" class="form-label">Tên lớp</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="ten_lop"
                                    name="ten_lop"
                                    value="<?php echo htmlspecialchars($lop['ten_lop']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="khoa_id" class="form-label">Khoa / Viện</label>
                                <select class="form-select" id="khoa_id" name="khoa_id" required>
                                    <option value="">-- Chọn khoa --</option>
                                    <?php foreach ($khoas as $khoa): ?>
                                        <option
                                            value="<?= $khoa['id'] ?>"
                                            <?= ($khoa['id'] == $lop['khoa_id']) ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars($khoa['ten_khoa']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="co_van" class="form-label">Cố vấn học tập</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="co_van"
                                    name="co_van"
                                    value="<?php echo htmlspecialchars($lop['co_van'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="bi_thu" class="form-label">Bí thư Chi đoàn</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="bi_thu"
                                    name="bi_thu"
                                    value="<?php echo htmlspecialchars($lop['bi_thu'] ?? ''); ?>">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../lop.php" class="btn btn-secondary me-md-2">Hủy</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>

                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
