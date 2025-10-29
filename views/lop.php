<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Danh sách Lớp';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
  <div class="card">
    <div class="card-header">
      <h3 class="m-0">DANH SÁCH LỚP</h3>
      <a href="lop/create_lop.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm mới</a>
    </div>

    <?php
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '<button type="button" class="btn-close" aria-label="Đóng">×</button></div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '<button type="button" class="btn-close" aria-label="Đóng">×</button></div>';
    }
    ?>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
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
          foreach ($lops as $lop) { ?>
            <tr>
              <td><?= $lop["id"] ?></td>
              <td><?= htmlspecialchars($lop["ma_lop"]) ?></td>
              <td><?= htmlspecialchars($lop["ten_lop"]) ?></td>
              <td><?= htmlspecialchars($lop["ten_khoa"] ?? '') ?></td>
              <td><?= htmlspecialchars($lop["co_van"] ?? '') ?></td>
              <td><?= htmlspecialchars($lop["bi_thu"] ?? '') ?></td>
              <td>
                <a href="lop/edit_lop.php?id=<?= $lop["id"] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                <a href="../handle/lop_process.php?action=delete&id=<?= $lop["id"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa lớp này?')"><i class="fa fa-trash"></i> Xóa</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
