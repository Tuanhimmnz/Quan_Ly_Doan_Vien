<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Danh sách Đoàn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include './menu.php'; ?>
    <div class="container mt-3">
        
        <h3 class="mt-3">DANH SÁCH ĐOÀN VIÊN</h3>
        
        <?php
        // Hiển thị thông báo thành công
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_GET['success']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
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
        
        <a href="doanvien/create_doanvien.php" class="btn btn-primary mb-3">Create</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Mã SV</th>
                    <th scope="col">Họ và tên</th>
                    <th scope="col">Lớp / Chi đoàn</th>
                    <th scope="col">Chức vụ</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Ngày vào Đoàn</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require '../handle/doanvien_process.php';
                $list = handleGetAllDoanVien();

                foreach ($list as $dv) {
                ?>
                    <tr>
                        <td><?= $dv["id"] ?></td>
                        <td><?= htmlspecialchars($dv["ma_sv"]) ?></td>
                        <td><?= htmlspecialchars($dv["ho_ten"]) ?></td>
                        <td><?= htmlspecialchars($dv["ten_lop"] ?? '') ?></td>
                        <td><?= htmlspecialchars($dv["chuc_vu"] ?? '') ?></td>
                        <td><?= htmlspecialchars($dv["trang_thai"] ?? '') ?></td>
                        <td><?= htmlspecialchars($dv["ngay_vao_doan"] ?? '') ?></td>
                        <td>
                            <a href="doanvien/edit_doanvien.php?id=<?= $dv["id"] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="../handle/doanvien_process.php?action=delete&id=<?= $dv["id"] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa đoàn viên này?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
