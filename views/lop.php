<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php'; // [FITDNU-ADD]
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Danh sách Lớp';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">DANH SÁCH LỚP</h3>
      <a href="lop/create_lop.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm mới</a>
    </div>

    <div class="card-body">
      <!-- [FITDNU-ADD] Tìm kiếm Lớp/Chi đoàn -->
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-6">
          <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm theo mã lớp / tên lớp / khoa / cố vấn / bí thư">
        </div>
        <div class="col-md-3">
          <input type="text" name="khoa" value="<?= htmlspecialchars($_GET['khoa'] ?? '') ?>" class="form-control" placeholder="Khoa">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="lop.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
        </div>
      </form>
      <?php
      if (isset($_GET['success'])) {
          echo '<div class="alert alert-success reveal-on-scroll">' . htmlspecialchars($_GET['success']) . '<button type="button" class="btn-close" aria-label="Đóng">×</button></div>';
      }
      if (isset($_GET['error'])) {
          echo '<div class="alert alert-danger reveal-on-scroll">' . htmlspecialchars($_GET['error']) . '<button type="button" class="btn-close" aria-label="Đóng">×</button></div>';
      }
      ?>

      <div class="table-responsive reveal-on-scroll">
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
          $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
          $per = 15;
          $p = paginate_array($lops, $page, $per);
          $rows = $p['data'];
          $meta = $p['meta'];
          foreach ($rows as $lop) { ?>
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
        <div class="mt-2">
          <?= render_pagination('lop.php', $meta['page'], $meta['pages'], $_GET) ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
