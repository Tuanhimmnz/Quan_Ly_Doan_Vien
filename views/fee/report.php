<?php
// [FITDNU-ADD] Fee report page (basic)
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/fee_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$user = getCurrentUser();
if (!$user || !in_array($user['role'], ['admin','Cán bộ'])) { $_SESSION['error'] = 'Không có quyền'; header('Location: ../doanvien.php'); exit(); }

$pageTitle = 'Báo cáo Đoàn phí';
require __DIR__ . '/../../includes/header.php';

$years = fee_year_all();
$nam = isset($_GET['nam']) ? (int)$_GET['nam'] : (count($years) ? (int)$years[0]['nam'] : (int)date('Y'));
$receipts = fee_receipts_by_year($nam);
$total = fee_total_by_year($nam);
?>

<div class="container mt-3">
  <div class="d-flex align-items-center gap-2 mb-3">
    <h3 class="m-0">Báo cáo Đoàn phí</h3>
    <form class="d-flex align-items-center gap-2" method="get">
      <select name="nam" class="form-select form-select-sm" onchange="this.form.submit()">
        <?php foreach ($years as $y): ?>
          <option value="<?= (int)$y['nam'] ?>" <?= $nam===(int)$y['nam']?'selected':'' ?>><?= (int)$y['nam'] ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <div class="alert alert-secondary">Tổng quỹ năm <?= (int)$nam ?>: <strong><?= number_format($total, 0, ',', '.') ?> đ</strong></div>

  <div class="table-responsive">
    <table class="table table-sm table-striped">
      <thead><tr><th>Mã SV</th><th>Họ tên</th><th>Số tiền</th><th>Ngày thu</th><th>Hình thức</th><th>Ghi chú</th></tr></thead>
      <tbody>
        <?php foreach ($receipts as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['ma_sv']) ?></td>
            <td><?= htmlspecialchars($r['ho_ten']) ?></td>
            <td class="text-end"><?= number_format((float)$r['so_tien'], 0, ',', '.') ?> đ</td>
            <td><?= htmlspecialchars($r['ngay_thu'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['hinh_thuc'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['ghi_chu'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

