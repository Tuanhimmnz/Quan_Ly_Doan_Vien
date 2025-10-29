<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Danh sách Đoàn viên';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
  <div class="card">
    <div class="card-header">
      <h3 class="m-0">DANH SÁCH ĐOÀN VIÊN</h3>
      <a href="doanvien/create_doanvien.php" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Thêm mới
      </a>
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
                <a href="doanvien/edit_doanvien.php?id=<?= $dv["id"] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                <a href="../handle/doanvien_process.php?action=delete&id=<?= $dv["id"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đoàn viên này?')"><i class="fa fa-trash"></i> Xóa</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
