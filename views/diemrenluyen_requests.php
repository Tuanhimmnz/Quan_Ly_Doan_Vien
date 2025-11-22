<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php';
require_once __DIR__ . '/../functions/diemrenluyen_request_functions.php';
checkLogin(__DIR__ . '/../index.php');
$user = getCurrentUser();
if (!$user || !in_array($user['role'], ['admin','Cán bộ'])) { $_SESSION['error']='Không có quyền'; header('Location: diemrenluyen.php'); exit(); }
$pageTitle = 'Duyệt điểm rèn luyện';
require __DIR__ . '/../includes/header.php';

$filters = [
  'status' => $_GET['status'] ?? 'pending',
  'q' => $_GET['q'] ?? ''
];
$all = drl_requests_list($filters);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per = 15;
$p = paginate_array($all, $page, $per);
$rows = $p['data'];
$meta = $p['meta'];
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">DUYỆT ĐIỂM RÈN LUYỆN</h3>
      <a href="diemrenluyen.php" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
      <?php if (isset($_SESSION['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
      <?php if (isset($_SESSION['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>

      <form method="get" class="row g-2 mb-3">
        <div class="col-md-4">
          <select name="status" class="form-select">
            <?php $st = $filters['status']; ?>
            <option value="pending" <?= $st==='pending'?'selected':''; ?>>Chờ duyệt</option>
            <option value="approved" <?= $st==='approved'?'selected':''; ?>>Đã duyệt</option>
            <option value="rejected" <?= $st==='rejected'?'selected':''; ?>>Đã từ chối</option>
          </select>
        </div>
        <div class="col-md-5"><input type="text" name="q" class="form-control" value="<?= htmlspecialchars($filters['q']) ?>" placeholder="Tìm mã SV / họ tên"></div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Lọc</button>
          <a class="btn btn-outline-secondary btn-sm" href="diemrenluyen_requests.php">Làm mới</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Mã SV</th>
              <th>Họ tên</th>
              <th>Năm học</th>
              <th>Điểm</th>
              <th>Xếp loại</th>
              <th>Người gửi</th>
              <th>Trạng thái</th>
              <th>Ghi chú</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($rows)): ?>
              <tr><td colspan="10" class="text-center text-muted py-3">Chưa có yêu cầu phù hợp.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['ma_sv']) ?></td>
                <td><?= htmlspecialchars($r['ho_ten']) ?></td>
                <td><?= htmlspecialchars($r['nam_hoc']) ?></td>
                <td><?= htmlspecialchars($r['diem']) ?></td>
                <td><?= htmlspecialchars($r['xep_loai']) ?></td>
                <td><?= htmlspecialchars($r['user_name'] ?? '') ?></td>
                <td>
                  <?php
                  $badge = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-secondary'];
                  $label = ['pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối'];
                  $stKey = $r['status'] ?? 'pending';
                  ?>
                  <span class="badge <?= $badge[$stKey] ?? 'bg-secondary' ?>"><?= $label[$stKey] ?? $stKey ?></span>
                </td>
                <td>
                  <div class="small text-muted"><?= nl2br(htmlspecialchars($r['ghi_chu'] ?? '')) ?></div>
                  <?php if (!empty($r['review_note'])): ?>
                    <div class="small">Ghi chú duyệt: <?= htmlspecialchars($r['review_note']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (($r['status'] ?? 'pending') === 'pending'): ?>
                    <form method="post" action="../handle/diemrenluyen_request_process.php" class="d-flex flex-column gap-1">
                      <input type="hidden" name="id" value="<?= $r['id'] ?>">
                      <input type="text" name="review_note" class="form-control form-control-sm" placeholder="Ghi chú">
                      <div class="d-flex gap-1">
                        <button name="action" value="approve" class="btn btn-success btn-sm" onclick="return confirm('Duyệt yêu cầu này?')"><i class="fa fa-check"></i> Duyệt</button>
                        <button name="action" value="reject" class="btn btn-danger btn-sm" onclick="return confirm('Từ chối yêu cầu này?')"><i class="fa fa-xmark"></i> Từ chối</button>
                      </div>
                    </form>
                  <?php else: ?>
                    <div class="text-muted small">Đã xử lý</div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="mt-2"><?= render_pagination('diemrenluyen_requests.php', $meta['page'], $meta['pages'], $_GET) ?></div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
