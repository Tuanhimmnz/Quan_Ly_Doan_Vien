<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm Khoa / Viện mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h3 class="mt-3 mb-4 text-center">THÊM KHOA / VIỆN MỚI</h3>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <script>
                setTimeout(() => {
                    let alertNode = document.querySelector('.alert');
                    if (alertNode) {
                        let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                        bsAlert.close();
                    }
                }, 3000);
                </script>

                <form action="../../handle/khoa_process.php" method="POST">
                    <input type="hidden" name="action" value="create">

                    <div class="mb-3">
                        <label class="form-label">Tên Khoa / Viện</label>
                        <input
                            type="text"
                            class="form-control"
                            name="ten_khoa"
                            placeholder="Ví dụ: Khoa Công nghệ thông tin"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trưởng khoa / Viện trưởng</label>
                        <input
                            type="text"
                            class="form-control"
                            name="truong_khoa"
                            placeholder="Ví dụ: TS. Nguyễn Văn A">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SĐT liên hệ</label>
                        <input
                            type="text"
                            class="form-control"
                            name="sdt_lien_he"
                            placeholder="Ví dụ: 0988xxxxxx">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email liên hệ</label>
                        <input
                            type="email"
                            class="form-control"
                            name="email_lien_he"
                            placeholder="Ví dụ: cntt@dainam.edu.vn">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả / Ghi chú</label>
                        <textarea class="form-control" name="mo_ta" rows="3"
                                  placeholder="Giới thiệu khoa, ngành đào tạo, định hướng..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="../khoa.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
