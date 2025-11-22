<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php';
require_once __DIR__ . '/../functions/sukien_functions.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Quản lý sự kiện';
require __DIR__ . '/../includes/header.php';

$params = ['q' => $_GET['q'] ?? '', 'trang_thai' => $_GET['trang_thai'] ?? ''];
$has = (trim($params['q']) !== '') || (trim($params['trang_thai']) !== '');
$rowsAll = $has ? searchSuKien($params) : getAllSuKien();
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">QUẢN LÝ SỰ KIỆN</h3>
      <a href="sukien/create.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm sự kiện</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-6"><input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm tên sự kiện / cấp tổ chức"></div>
        <div class="col-md-3"><input type="text" name="trang_thai" value="<?= htmlspecialchars($_GET['trang_thai'] ?? '') ?>" class="form-control" placeholder="Trạng thái"></div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="sukien.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
        </div>
      </form>

      <div class="table-responsive reveal-on-scroll">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên sự kiện</th>
              <th>Mô tả</th>
              <th>Ngày tổ chức</th>
              <th>Cấp tổ chức</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
              $per = 15;
              $p = paginate_array($rowsAll, $page, $per);
              $rows = $p['data'];
              $meta = $p['meta'];
              foreach ($rows as $row):
            ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['ten_su_kien'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['mo_ta'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['ngay_to_chuc'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['cap_to_chuc'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['trang_thai'] ?? '') ?></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="sukien/edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                  <a href="../handle/sukien_process.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sự kiện này?')"><i class="fa fa-trash"></i> Xóa</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="mt-2"><?= render_pagination('sukien.php', $meta['page'], $meta['pages'], $_GET) ?></div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
