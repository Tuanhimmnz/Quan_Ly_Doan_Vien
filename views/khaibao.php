<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin('../index.php');

ensureSessionStarted();
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
    header('Location: ../index.php');
    exit();
}

require_once __DIR__ . '/../functions/khaibao_functions.php';
require_once __DIR__ . '/../functions/common_ui.php'; // [FITDNU-ADD]

$statusFilter = $_GET['status'] ?? 'pending';
$validStatuses = ['pending', 'approved', 'rejected', 'all'];
if (!in_array($statusFilter, $validStatuses, true)) {
    $statusFilter = 'pending';
}

$params = [
  'status' => $statusFilter,
  'q' => $_GET['q'] ?? '',
  'from' => $_GET['from'] ?? '',
  'to' => $_GET['to'] ?? ''
];
$hasSearch = (trim($params['q']) !== '') || (trim($params['from']) !== '') || (trim($params['to']) !== '') || $statusFilter !== 'pending';
$declarations = $hasSearch ? searchDeclarations($params) : getAllDeclarations($statusFilter === 'all' ? null : $statusFilter);
$pageTitle = 'Duyệt khai báo đoàn viên';

require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll mb-3">
    <div class="card-body">
      <div>
        <h3 class="m-0">Danh sách khai báo đoàn viên</h3>
        <p class="text-muted mb-0">Kiểm tra thông tin khai báo của đoàn viên và duyệt vào hệ thống chính thức.</p>
      </div>
      <form class="row g-2 mt-3" method="GET">
        <div class="col-md-3">
          <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Đang chờ duyệt</option>
            <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
            <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Từ chối</option>
            <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Tất cả</option>
          </select>
        </div>
        <div class="col-md-3">
          <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm mã SV / họ tên / email / SĐT / lớp">
        </div>
        <div class="col-md-2">
          <input type="datetime-local" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>" class="form-control" placeholder="Từ">
        </div>
        <div class="col-md-2">
          <input type="datetime-local" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>" class="form-control" placeholder="Đến">
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="khaibao.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success reveal-on-scroll">
      <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>
  <?php if (isset($_SESSION['warning'])): ?>
    <div class="alert alert-warning reveal-on-scroll">
      <?= $_SESSION['warning']; unset($_SESSION['warning']); ?>
    </div>
  <?php endif; ?>
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger reveal-on-scroll">
      <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <div class="card reveal-on-scroll">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Người gửi</th>
            <th>Mã SV</th>
            <th>Họ tên</th>
            <th>Lớp / Chi đoàn</th>
            <th>Liên hệ</th>
            <th>Ngày gửi</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($declarations)): ?>
            <tr>
              <td colspan="9" class="text-center text-muted py-4">Chưa có khai báo nào.</td>
            </tr>
          <?php else: ?>
            <?php
              $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
              $per = 15;
              $p = paginate_array($declarations, $page, $per);
              $rows = $p['data'];
              $meta = $p['meta'];
              foreach ($rows as $row): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['ma_sv']) ?></td>
                <td>
                  <strong><?= htmlspecialchars($row['ho_ten']) ?></strong><br>
                  <small class="text-muted">Giới tính: <?= htmlspecialchars($row['gioi_tinh'] ?: '—') ?></small>
                </td>
                <td><?= htmlspecialchars($row['ten_lop'] ?? '—') ?></td>
                <td>
                  <div>Email: <?= htmlspecialchars($row['email'] ?: '—') ?></div>
                  <div>Điện thoại: <?= htmlspecialchars($row['sdt'] ?: '—') ?></div>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                <td>
                  <?php if ($row['status'] === 'approved'): ?>
                    <span class="badge bg-success">Đã duyệt</span>
                  <?php elseif ($row['status'] === 'rejected'): ?>
                    <span class="badge bg-secondary">Từ chối</span>
                  <?php else: ?>
                    <span class="badge bg-warning">Chờ duyệt</span>
                  <?php endif; ?>
                  <?php if (!empty($row['reviewer_name'])): ?>
                    <div class="text-muted small">by <?= htmlspecialchars($row['reviewer_name']) ?></div>
                  <?php endif; ?>
                </td>
                <td style="min-width:220px;">
                  <div class="d-flex gap-2 mb-2">
                    <form method="POST" action="../handle/khai_bao_process.php" class="d-inline" onsubmit="return confirm('Duyệt khai báo và thêm vào danh sách đoàn viên?');">
                      <input type="hidden" name="action" value="approve">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <button type="submit" class="btn btn-success btn-sm" <?= $row['status'] === 'approved' ? 'disabled' : '' ?>>Duyệt</button>
                    </form>
                    <form method="POST" action="../handle/khai_bao_process.php" class="d-inline" onsubmit="return confirm('Từ chối khai báo này?');">
                      <input type="hidden" name="action" value="reject">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm" <?= $row['status'] === 'rejected' ? 'disabled' : '' ?>>Từ chối</button>
                    </form>
                  </div>
                  <details>
                    <summary class="text-muted small">Chi tiết</summary>
                    <div class="small mt-1">
                      <div><strong>Địa chỉ:</strong> <?= htmlspecialchars($row['dia_chi'] ?: '—') ?></div>
                      <div><strong>Số thẻ đoàn viên:</strong> <?= htmlspecialchars($row['so_doan_vien'] ?: '—') ?></div>
                      <div><strong>Ngày sinh:</strong> <?= $row['ngay_sinh'] ? date('d/m/Y', strtotime($row['ngay_sinh'])) : '—' ?></div>
                      <div><strong>Ghi chú:</strong><br><?= nl2br(htmlspecialchars($row['ghi_chu'] ?: '—')) ?></div>
                      <?php if (!empty($row['admin_note'])): ?>
                        <div class="mt-2"><strong>Ghi chú quản trị:</strong><br><?= nl2br(htmlspecialchars($row['admin_note'])) ?></div>
                      <?php endif; ?>
                    </div>
                  </details>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="mt-2">
        <?= render_pagination('khaibao.php', $meta['page'] ?? 1, $meta['pages'] ?? 1, $_GET) ?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
