<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');

require_once __DIR__ . '/../handle/khoa_process.php';
$khoas = handleGetAllKhoa();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Quản lý Khoa / Viện - Đại học Đại Nam 2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include './menu.php'; ?>

    <div class="container mt-3">
        <h3 class="mt-3 mb-3">DANH SÁCH KHOA / VIỆN</h3>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

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

        <a href="khoa/create_khoa.php" class="btn btn-primary mb-3">Thêm Khoa / Viện</a>

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tên Khoa / Viện</th>
                    <th>Trưởng khoa / Viện trưởng</th>
                    <th>SĐT liên hệ</th>
                    <th>Email liên hệ</th>
                    <th>Mô tả</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($khoas as $index => $khoa): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($khoa['ten_khoa']) ?></td>
                        <td><?= htmlspecialchars($khoa['truong_khoa'] ?? '') ?></td>
                        <td><?= htmlspecialchars($khoa['sdt_lien_he'] ?? '') ?></td>
                        <td><?= htmlspecialchars($khoa['email_lien_he'] ?? '') ?></td>
                        <td style="max-width: 250px; white-space: pre-line;">
                            <?= nl2br(htmlspecialchars($khoa['mo_ta'] ?? '')); ?>
                        </td>
                        <td>
                            <a href="khoa/edit_khoa.php?id=<?= $khoa['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="../handle/khoa_process.php?action=delete&id=<?= $khoa['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa khoa / viện này?');">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($khoas) === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Chưa có khoa / viện nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
