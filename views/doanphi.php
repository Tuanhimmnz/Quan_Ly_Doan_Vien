<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php'; // [FITDNU-ADD]
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Danh sách Đoàn phí';
require __DIR__ . '/../includes/header.php';
?>

<?php
require '../handle/doanphi_process.php';
$years = handleGetAllNamHocDoanPhi(); // [FITDNU-ADD]
$selectedYear = isset($_GET['nam_hoc']) ? trim($_GET['nam_hoc']) : '';
if ($selectedYear === '' && !empty($years)) {
    $selectedYear = $years[0];
    $_GET['nam_hoc'] = $selectedYear; // so handler uses it
}
$phiList = handleGetAllDoanPhi();
$totalPaid = 0;
foreach ($phiList as $phiItem) {
    if ((int)($phiItem['da_nop'] ?? 0) === 1) {
        $totalPaid += (float)($phiItem['so_tien_nop'] ?? 0);
    }
}
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">DANH SÁCH ĐOÀN PHÍ</h3>
      <div class="d-flex align-items-center gap-2">
        <!-- [FITDNU-ADD] Lọc theo năm học -->
        <form method="get" class="d-flex align-items-center gap-2">
          <label class="form-label m-0">Năm học</label>
          <select name="nam_hoc" class="form-select form-select-sm" onchange="this.form.submit()">
            <?php foreach ($years as $y): ?>
              <option value="<?= htmlspecialchars($y) ?>" <?= ($selectedYear === $y) ? 'selected' : '' ?>><?= htmlspecialchars($y) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
        <a href="doanphi/edit_doanphi.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tạo bản ghi</a>
      </div>
    </div>

    <div class="card-body">
      <?php
      if (isset($_GET['success'])) {
          echo '<div class="alert alert-success reveal-on-scroll">' . htmlspecialchars($_GET['success']) . '<button type="button" class="btn-close" aria-label="Đóng">×</button></div>';
      }
      if (isset($_GET['error'])) {
          echo '<div class="alert alert-danger reveal-on-scroll">' . htmlspecialchars($_GET['error']) . '<button type="button" class="btn-close" aria-label="Đóng">×</button></div>';
      }
      ?>

      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="totals-chip bg-light px-3 py-2 rounded-pill fw-semibold">
          Tổng tiền đã nộp (<?= htmlspecialchars($selectedYear) ?>): <span class="text-primary"><?= number_format($totalPaid, 0, ',', '.') ?> đ</span>
        </div>
        <div class="text-muted small">
          Cập nhật lúc <?= date('d/m/Y H:i') ?>
        </div>
      </div>

      <!-- [FITDNU-ADD] Tìm kiếm Đoàn phí -->
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-3">
          <select name="nam_hoc" class="form-select">
            <option value="">-- Tất cả năm học --</option>
            <?php foreach ($years as $y): ?>
              <option value="<?= htmlspecialchars($y) ?>" <?= ($selectedYear === $y) ? 'selected' : '' ?>><?= htmlspecialchars($y) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm theo mã SV / họ tên">
        </div>
        <div class="col-md-3">
          <select name="paid" class="form-select">
            <option value="">-- Trạng thái --</option>
            <option value="1" <?= (($_GET['paid'] ?? '')==='1') ? 'selected' : '' ?>>Đã nộp</option>
            <option value="0" <?= (($_GET['paid'] ?? '')==='0') ? 'selected' : '' ?>>Chưa nộp</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="doanphi.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
        </div>
      </form>

      <div class="table-responsive reveal-on-scroll">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Mã SV</th>
              <th scope="col">Họ và tên</th>
              <th scope="col">Năm học</th>
              <th scope="col">Đã nộp?</th>
              <th scope="col">Ngày nộp</th>
              <th scope="col">Số tiền nộp</th>
              <th scope="col">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // [FITDNU-ADD]
              $per = 15;
              $p = paginate_array($phiList, $page, $per);
              $rows = $p['data'];
              $meta = $p['meta'];
              foreach ($rows as $item):
                $isPaid = (int)($item['da_nop'] ?? 0) === 1;
                $amount = $isPaid ? (float)($item['so_tien_nop'] ?? 0) : 0;
            ?>
              <tr>
                <td><?= $item['id'] ?></td>
                <td><?= htmlspecialchars($item['ma_sv'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['ho_ten'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['nam_hoc'] ?? '') ?></td>
                <td><span class="badge <?= $isPaid ? 'bg-success' : 'bg-secondary' ?>"><?= $isPaid ? 'Đã nộp' : 'Chưa nộp' ?></span></td>
                <td><?= htmlspecialchars($item['ngay_nop'] ?? '') ?></td>
                <td class="fw-semibold text-end">
                  <?= $isPaid ? number_format($amount, 0, ',', '.') . ' đ' : '—' ?>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="doanphi/edit_doanphi.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                    <a href="../handle/doanphi_process.php?action=delete&id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi đoàn phí này?')"><i class="fa fa-trash"></i> Xóa</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="mt-2">
          <?= render_pagination('doanphi.php', $meta['page'], $meta['pages'], $_GET) ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
