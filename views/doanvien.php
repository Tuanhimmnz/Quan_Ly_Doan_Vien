<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php'; // [FITDNU-ADD]
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Danh sách Đoàn viên';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">DANH SÁCH ĐOÀN VIÊN</h3>
      <a href="doanvien/create_doanvien.php" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Thêm mới
      </a>
    </div>

    <div class="card-body">
      <!-- [FITDNU-ADD] Unified search bar -->
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-4">
          <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm mã SV / họ tên / lớp / khoa">
        </div>
        <div class="col-md-2"><input type="text" name="khoa" value="<?= htmlspecialchars($_GET['khoa'] ?? '') ?>" class="form-control" placeholder="Khoa"></div>
        <div class="col-md-2"><input type="text" name="lop" value="<?= htmlspecialchars($_GET['lop'] ?? '') ?>" class="form-control" placeholder="Lớp"></div>
        <div class="col-md-2"><input type="text" name="trang_thai" value="<?= htmlspecialchars($_GET['trang_thai'] ?? '') ?>" class="form-control" placeholder="Trạng thái"></div>
        <div class="col-md-2"><input type="text" name="chuc_vu" value="<?= htmlspecialchars($_GET['chuc_vu'] ?? '') ?>" class="form-control" placeholder="Chức vụ"></div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="doanvien.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
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
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // [FITDNU-ADD]
            $per = 15;
            $p = paginate_array($list, $page, $per);
            $rows = $p['data'];
            $meta = $p['meta'];

            foreach ($rows as $dv) {
                $statusRaw = $dv['trang_thai'] ?? '';
                $statusLabel = htmlspecialchars($statusRaw);
                $statusTheme = $statusRaw === 'Đang sinh hoạt' ? 'bg-success' : 'bg-secondary';
            ?>
              <tr>
                <td><?= $dv['id'] ?></td>
                <td><?= htmlspecialchars($dv['ma_sv']) ?></td>
                <td><?= htmlspecialchars($dv['ho_ten']) ?></td>
                <td><?= htmlspecialchars($dv['ten_lop'] ?? '') ?></td>
                <td><?= htmlspecialchars($dv['chuc_vu'] ?? '') ?></td>
                <td><?= $statusLabel !== '' ? '<span class="badge ' . $statusTheme . '">' . $statusLabel . '</span>' : '—' ?></td>
                <td><?= htmlspecialchars($dv['ngay_vao_doan'] ?? '') ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="doanvien/edit_doanvien.php?id=<?= $dv['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                    <a href="../handle/doanvien_process.php?action=delete&id=<?= $dv['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đoàn viên này?')"><i class="fa fa-trash"></i> Xóa</a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <div class="mt-2">
          <?= render_pagination('doanvien.php', $meta['page'], $meta['pages'], $_GET) ?>
        </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
