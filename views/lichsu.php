<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php';
require_once __DIR__ . '/../functions/lichsu_functions.php';
require_once __DIR__ . '/../functions/doanvien_functions.php';
require_once __DIR__ . '/../functions/db_connection.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Lịch sử tham gia';
require __DIR__ . '/../includes/header.php';

$params = ['q' => $_GET['q'] ?? '', 'trang_thai' => $_GET['trang_thai'] ?? ''];
$has = (trim($params['q']) !== '') || (trim($params['trang_thai']) !== '');
$manualRows = $has ? searchLichSuThamGia($params) : getAllLichSuThamGia();
foreach ($manualRows as &$m) {
  $m['nguon'] = 'Bảng lịch sử';
}
unset($m);

// Tự động lấy tham gia mới từ thông tin đoàn viên, có filter theo q/trang_thai và bỏ trùng doanvien_id đã có trong bảng
$autoRows = [];
$dvList = getAllDoanVien();
$manualIds = [];
foreach ($manualRows as $m) {
  if (!empty($m['doanvien_id'])) $manualIds[(int)$m['doanvien_id']] = true;
}
foreach ($dvList as $dv) {
  if (isset($manualIds[(int)$dv['id']])) continue;
  if ($has) {
    $q = trim($params['q']);
    if ($q !== '' && stripos($dv['ma_sv'], $q) === false && stripos($dv['ho_ten'], $q) === false) continue;
    $tt = trim($params['trang_thai']);
    if ($tt !== '' && ($dv['trang_thai'] ?? '') !== $tt) continue;
  }
  $autoRows[] = [
    'id' => 'join-' . $dv['id'],
    'doanvien_id' => $dv['id'],
    'ma_sv' => $dv['ma_sv'],
    'ho_ten' => $dv['ho_ten'],
    'ngay_bat_dau' => $dv['ngay_vao_doan'] ?? '',
    'ngay_ket_thuc' => '',
    'trang_thai' => $dv['trang_thai'] ?: 'Tham gia mới',
    'ghi_chu' => 'Nguồn: Hồ sơ đoàn viên',
    'nguon' => 'Hồ sơ đoàn viên'
  ];
}

$rowsAll = array_merge($autoRows, $manualRows);
usort($rowsAll, function($a,$b){
  return strcmp($b['ngay_bat_dau'] ?? '', $a['ngay_bat_dau'] ?? '');
});

?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">LỊCH SỬ THAM GIA</h3>
      <a href="lichsu/create.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm bản ghi</a>
    </div>
    <div class="card-body">
      <?php if (isset($_GET['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
      <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-6"><input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm mã SV / họ tên"></div>
        <div class="col-md-3"><input type="text" name="trang_thai" value="<?= htmlspecialchars($_GET['trang_thai'] ?? '') ?>" class="form-control" placeholder="Trạng thái"></div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="lichsu.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
        </div>
      </form>

      <div class="table-responsive reveal-on-scroll">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Mã SV</th>
              <th>Họ tên</th>
              <th>Ngày bắt đầu</th>
              <th>Ngày kết thúc</th>
              <th>Trạng thái</th>
              <th>Nguồn</th>
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
              <td><?= htmlspecialchars($row['ngay_bat_dau'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['ngay_ket_thuc'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['trang_thai'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['nguon'] ?? '—') ?></td>
              <td><?= htmlspecialchars($row['ghi_chu'] ?? '') ?></td>
              <td>
                <?php if (is_numeric($row['id'])): ?>
                  <div class="d-flex gap-1">
                    <a href="lichsu/edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                    <a href="../handle/lichsu_process.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa bản ghi này?')"><i class="fa fa-trash"></i> Xóa</a>
                  </div>
                <?php else: ?>
                  <span class="text-muted">Tự động</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="mt-2"><?= render_pagination('lichsu.php', $meta['page'], $meta['pages'], $_GET) ?></div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
