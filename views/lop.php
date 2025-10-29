<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Danh sách Lớp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include './menu.php'; ?>
    <div class="container mt-3">
        
        <h3 class="mt-3">DANH SÁCH LỚP</h3>
        
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
        
        <a href="lop/create_lop.php" class="btn btn-primary mb-3">Create</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Mã lớp</th>
                    <th scope="col">Tên lớp</th>
                    <th scope="col">Khoa / Viện</th>
                    <th scope="col">Cố vấn học tập</th>
                    <th scope="col">Bí thư Chi đoàn</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require '../handle/lop_process.php';
                $lops = handleGetAllLop();

                foreach ($lops as $lop) {
                ?>
                    <tr>
                        <td><?= $lop["id"] ?></td>
                        <td><?= htmlspecialchars($lop["ma_lop"]) ?></td>
                        <td><?= htmlspecialchars($lop["ten_lop"]) ?></td>
                        <td><?= htmlspecialchars($lop["ten_khoa"] ?? '') ?></td>
                        <td><?= htmlspecialchars($lop["co_van"] ?? '') ?></td>
                        <td><?= htmlspecialchars($lop["bi_thu"] ?? '') ?></td>
                        <td>
                            <a href="lop/edit_lop.php?id=<?= $lop["id"] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="../handle/lop_process.php?action=delete&id=<?= $lop["id"] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa lớp này?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
