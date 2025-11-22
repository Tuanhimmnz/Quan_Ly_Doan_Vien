<?php
// views/user/index.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../functions/auth.php';
checkLogin('../../index.php');

$user = $_SESSION['user'] ?? [];
$pref_ma_sv = $user['ma_sv'] ?? ($user['mssv'] ?? '');
$pref_ho_ten = $user['ho_ten'] ?? '';
$pref_lop_id = $user['lop_id'] ?? '';
$pref_sdt    = $user['sdt'] ?? '';
$pref_email  = $user['email'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Trang người dùng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-2"><i class="bi bi-info-circle"></i> Giới thiệu</h4>
          <p>Chào mừng bạn đến trang người dùng. Tại đây bạn có thể <strong>khai báo thông tin</strong> để Nhà trường/Đoàn trường cập nhật hồ sơ. Bản khai sẽ ở trạng thái <em>chờ duyệt</em>.</p>
          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
          <?php endif; ?>
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-2">Khai báo thông tin</h4>
          <form method="post" action="../../handle/khai_bao_process.php">
            <input type="hidden" name="action" value="create">

            <div class="mb-2">
              <label class="form-label">Họ tên</label>
              <input class="form-control" type="text" name="ho_ten" value="<?= htmlspecialchars($pref_ho_ten) ?>" required>
            </div>

            <div class="mb-2">
              <label class="form-label">Mã SV</label>
              <input class="form-control" type="text" name="ma_sv" value="<?= htmlspecialchars($pref_ma_sv) ?>">
            </div>

            <div class="mb-2">
              <label class="form-label">Lớp (ID)</label>
              <input class="form-control" type="text" name="lop_id" value="<?= htmlspecialchars((string)$pref_lop_id) ?>">
            </div>

            <div class="mb-2">
              <label class="form-label">Số điện thoại</label>
              <input class="form-control" type="text" name="sdt" value="<?= htmlspecialchars($pref_sdt) ?>">
            </div>

            <div class="mb-2">
              <label class="form-label">Email</label>
              <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($pref_email) ?>">
            </div>

            <div class="mb-2">
              <label class="form-label">Địa chỉ</label>
              <input class="form-control" type="text" name="dia_chi">
            </div>

            <div class="mb-2">
              <label class="form-label">Ngày sinh</label>
              <input class="form-control" type="date" name="ngay_sinh">
            </div>

            <div class="mb-2">
              <label class="form-label">Giới tính</label>
              <select class="form-select" name="gioi_tinh">
                <option value="">-- Chọn --</option>
                <option value="nam">Nam</option>
                <option value="nu">Nữ</option>
                <option value="khac">Khác</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Thông tin khác</label>
              <textarea class="form-control" name="thong_tin_khac" rows="3"></textarea>
            </div>

            <div>
              <button class="btn btn-primary" type="submit">Gửi khai báo</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
