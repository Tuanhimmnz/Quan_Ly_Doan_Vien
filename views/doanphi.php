<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Danh sách Đoàn phí</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include './menu.php'; ?>
    <div class="container mt-3">
        
        <h3 class="mt-3">DANH SÁCH ĐOÀN PHÍ</h3>
        
        <?php
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_GET['success']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
        
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_GET['error']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
        ?>
        <script>
        setTimeout(() => {
            let alertNode = document.querySelector('.alert');
            if (alertNode) {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                bsAlert.close();
            }
        }, 3000);
        </script>
        
        <a href="doanphi/edit_doanphi.php" class="btn btn-primary mb-3">Create</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Mã SV</th>
                    <th scope="col">Họ và tên</th>
                    <th scope="col">Năm học</th>
                    <th scope="col">Đã nộp?</th>
                    <th scope="col">Ngày nộp</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require '../handle/doanphi_process.php';
                $phiList = handleGetAllDoanPhi();

                foreach ($phiList as $item) {
                    // giả định mỗi record chứa:
                    // id, doanvien_id, ma_sv, ho_ten, nam_hoc, da_nop, ngay_nop
                ?>
                    <tr>
                        <td><?= $item["id"] ?></td>
                        <td><?= htmlspecialchars($item["ma_sv"]) ?></td>
                        <td><?= htmlspecialchars($item["ho_ten"]) ?></td>
                        <td><?= htmlspecialchars($item["nam_hoc"]) ?></td>
                        <td>
                            <?= ($item["da_nop"] == 1) ? 'Đã nộp' : 'Chưa nộp' ?>
                        </td>
                        <td><?= htmlspecialchars($item["ngay_nop"] ?? '') ?></td>
                        <td>
                            <a href="doanphi/edit_doanphi.php?id=<?= $item["id"] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="../handle/doanphi_process.php?action=delete&id=<?= $item["id"] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi đoàn phí này?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
