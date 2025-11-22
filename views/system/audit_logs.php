<?php
// [FITDNU-ADD] Hệ thống > Lịch sử thay đổi
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/audit_functions.php';
require_once __DIR__ . '/../../functions/common_ui.php';
checkLogin(__DIR__ . '/../../index.php');
$user = getCurrentUser();
if (!$user || !in_array($user['role'], ['admin','Cán bộ'])) { $_SESSION['error']='Không có quyền'; header('Location: ../doanvien.php'); exit(); }

$pageTitle = 'Lịch sử thay đổi';
require __DIR__ . '/../../includes/header.php';

$filters = [
  'from' => $_GET['from'] ?? '',
  'to' => $_GET['to'] ?? '',
  'ma_sv' => $_GET['ma_sv'] ?? ''
];
$all = list_audit_logs($filters);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per = 20;
$p = paginate_array($all, $page, $per);
$logs = $p['data'];
$meta = $p['meta'];
?>

<div class="container mt-3">
  <h3>Lịch sử thay đổi</h3>
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4"><label class="form-label">Từ ngày</label><input class="form-control" type="datetime-local" name="from" value="<?= htmlspecialchars($filters['from']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Đến ngày</label><input class="form-control" type="datetime-local" name="to" value="<?= htmlspecialchars($filters['to']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Mã sinh viên</label><input class="form-control" type="text" name="ma_sv" placeholder="VD: SV210001" value="<?= htmlspecialchars($filters['ma_sv']) ?>"></div>
    <div class="col-12 mt-2"><button class="btn btn-outline-primary btn-sm">Tìm kiếm</button> <a class="btn btn-outline-secondary btn-sm" href="audit_logs.php">Làm mới</a></div>
  </form>

  <div class="table-responsive">
    <table class="table table-sm table-striped" style="table-layout:fixed;">
      <thead><tr><th>Thời gian</th><th>User</th><th>Action</th><th>Entity</th><th>Entity ID</th><th>Before</th><th>After</th></tr></thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
          <tr>
            <td style="width:140px; word-wrap:break-word; white-space:normal;">&nbsp;<?= htmlspecialchars($l['created_at']) ?></td>
            <td style="width:200px; word-wrap:break-word; white-space:normal;">&nbsp;<?= htmlspecialchars(($l['username'] ?? '') . ' (#' . $l['user_id'] . ')') ?></td>
            <td style="width:100px;"><span class="badge bg-secondary"><?= htmlspecialchars($l['action']) ?></span></td>
            <td style="width:140px; word-wrap:break-word; white-space:normal;">&nbsp;<?= htmlspecialchars($l['entity']) ?></td>
            <td style="width:90px;">&nbsp;<?= htmlspecialchars((string)$l['entity_id']) ?></td>
            <td style="width:380px; word-wrap:break-word; white-space:pre-wrap; overflow:hidden;"><pre class="small m-0" style="white-space:pre-wrap;"><?= htmlspecialchars($l['before_json'] ?? '') ?></pre></td>
            <td style="width:380px; word-wrap:break-word; white-space:pre-wrap; overflow:hidden;"><pre class="small m-0" style="white-space:pre-wrap;"><?= htmlspecialchars($l['after_json'] ?? '') ?></pre></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="mt-2">
      <?= render_pagination('audit_logs.php', $meta['page'], $meta['pages'], $_GET) ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
