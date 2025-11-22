<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php';
require_once __DIR__ . '/../functions/diemrenluyen_functions.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Điểm rèn luyện';
require __DIR__ . '/../includes/header.php';

$params = ['q' => $_GET['q'] ?? '', 'nam_hoc' => $_GET['nam_hoc'] ?? ''];
$has = (trim($params['q']) !== '') || (trim($params['nam_hoc']) !== '');
$rowsAll = $has ? searchDiemRenLuyen($params) : getAllDiemRenLuyen();
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">ĐIỂM RÈN LUYỆN</h3>
      <div class="d-flex gap-2">
        <a href="diemrenluyen_requests.php" class="btn btn-success btn-sm"><i class="fa fa-clipboard-check"></i> Duyệt điểm rèn luyện</a>
        <a href="diemrenluyen/create.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm điểm rèn luyện</a>
      </div>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-5"><input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm mã SV / họ tên"></div>
        <div class="col-md-3"><input type="text" name="nam_hoc" value="<?= htmlspecialchars($_GET['nam_hoc'] ?? '') ?>" class="form-control" placeholder="Năm học"></div>
        <div class="col-md-4 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="diemrenluyen.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
        </div>
      </form>

      <div class="table-responsive reveal-on-scroll">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Mã SV</th>
              <th>Đoàn viên</th>
              <th>Năm học</th>
              <th>Điểm</th>
              <th>Xếp loại</th>
              <th>Ghi chú</th>
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
              <td><?= htmlspecialchars($row['ma_sv'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['ho_ten'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['nam_hoc'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['diem'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['xep_loai'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['ghi_chu'] ?? '') ?></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="diemrenluyen/edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                  <a href="../handle/diemrenluyen_process.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa bản ghi này?')"><i class="fa fa-trash"></i> Xóa</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="mt-2"><?= render_pagination('diemrenluyen.php', $meta['page'], $meta['pages'], $_GET) ?></div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
