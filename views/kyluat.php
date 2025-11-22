<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php';
require_once __DIR__ . '/../functions/danhgia_functions.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Kỷ luật';
require __DIR__ . '/../includes/header.php';

$params = ['q' => $_GET['q'] ?? '', 'trang_thai' => $_GET['trang_thai'] ?? '', 'loai' => 'Kỷ luật'];
$has = (trim($params['q']) !== '') || (trim($params['trang_thai']) !== '');
$danhgias = $has ? searchDanhGia($params) : array_filter(getAllDanhGia(), fn($r) => ($r['loai'] ?? '') === 'Kỷ luật');
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">QUẢN LÝ KỶ LUẬT</h3>
      <a href="danhgia/create_danhgia.php?loai=Kỷ%20luật" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm kỷ luật</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-6"><input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm mã SV / họ tên / nội dung"></div>
        <div class="col-md-3"><input type="text" name="trang_thai" value="<?= htmlspecialchars($_GET['trang_thai'] ?? '') ?>" class="form-control" placeholder="Trạng thái"></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary btn-sm">Tìm kiếm</button><a href="kyluat.php" class="btn btn-outline-secondary btn-sm">Làm mới</a></div>
      </form>
      <div class="table-responsive reveal-on-scroll">
      <table class="table table-striped table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Mã SV</th>
            <th>Họ tên</th>
            <th>Mô tả</th>
            <th>Ngày quyết định</th>
            <th>Nội dung</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per = 15;
            $p = paginate_array($danhgias, $page, $per);
            $rows = $p['data'];
            $meta = $p['meta'];
            foreach($rows as $dg): ?>
          <tr>
            <td><?= $dg['id'] ?></td>
            <td><?= htmlspecialchars($dg['ma_sv']) ?></td>
            <td><?= htmlspecialchars($dg['ho_ten']) ?></td>
            <td><?= htmlspecialchars($dg['mo_ta'] ?? '') ?></td>
            <td><?= htmlspecialchars($dg['ngay_quyet_dinh'] ?? '') ?></td>
            <td><?= htmlspecialchars($dg['noi_dung'] ?? '') ?></td>
            <td><?= htmlspecialchars($dg['trang_thai'] ?? '') ?></td>
            <td>
              <a href="danhgia/edit_danhgia.php?id=<?= $dg['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
              <a href="../handle/danhgia_process.php?action=delete&id=<?= $dg['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa bản ghi này?')"><i class="fa fa-trash"></i> Xóa</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="mt-2"><?= render_pagination('kyluat.php', $meta['page'], $meta['pages'], $_GET) ?></div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
