<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Danh sách Đoàn phí';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
  <div class="card">
    <div class="card-header">
      <h3 class="m-0">DANH SÁCH ĐOÀN PHÍ</h3>
      <a href="doanphi/edit_doanphi.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tạo bản ghi</a>
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
          foreach ($phiList as $item) { ?>
            <tr>
              <td><?= $item["id"] ?></td>
              <td><?= htmlspecialchars($item["ma_sv"]) ?></td>
              <td><?= htmlspecialchars($item["ho_ten"]) ?></td>
              <td><?= htmlspecialchars($item["nam_hoc"]) ?></td>
              <td><?= ($item["da_nop"] == 1) ? 'Đã nộp' : 'Chưa nộp' ?></td>
              <td><?= htmlspecialchars($item["ngay_nop"] ?? '') ?></td>
              <td>
                <a href="doanphi/edit_doanphi.php?id=<?= $item["id"] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                <a href="../handle/doanphi_process.php?action=delete&id=<?= $item["id"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi đoàn phí này?')"><i class="fa fa-trash"></i> Xóa</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
