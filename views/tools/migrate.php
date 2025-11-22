<?php
// [FITDNU-ADD] Công cụ > Nâng cấp CSDL (Migration Runner)
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/security.php';
require_once __DIR__ . '/../../functions/migration_functions.php';
checkLogin(__DIR__ . '/../../index.php');

$user = getCurrentUser();
if (!$user || !in_array($user['role'], ['admin', 'Cán bộ'])) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập công cụ này.';
    header('Location: ../doanvien.php');
    exit();
}

$pageTitle = 'Công cụ - Nâng cấp CSDL';
require __DIR__ . '/../../includes/header.php';

$results = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_post('migrate_db', 'migrate.php');
    $results = run_all_migrations();
}

$token = csrf_generate_token('migrate_db');
?>

<div class="container mt-3">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">Nâng cấp CSDL</h3>
      <form method="post" class="m-0">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
        <button type="submit" class="btn btn-primary" onclick="return confirm('Chạy migrations?');">
          Chạy migrations
        </button>
      </form>
    </div>
    <div class="card-body">
      <p class="text-muted">Chạy các tệp .sql trong thư mục <code>db/migrations</code> theo thứ tự tên tệp.</p>

      <?php if (is_array($results)): ?>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead>
              <tr>
                <th>Tệp</th>
                <th>Kết quả</th>
                <th>Ghi chú</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['file'] ?? '-') ?></td>
                  <td>
                    <?php if ($row['ok']): ?>
                      <span class="badge bg-success">OK</span>
                    <?php else: ?>
                      <span class="badge bg-danger">LỖI</span>
                    <?php endif; ?>
                  </td>
                  <td class="small text-wrap" style="max-width: 600px;">
                    <?= htmlspecialchars($row['error'] ?? '') ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">Nhấn "Chạy migrations" để bắt đầu nâng cấp.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

