<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');

$pageTitle = 'Tổng quan';
$currentUser = getCurrentUser();
$infoSubmitted = false;
$submittedProfile = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $infoSubmitted = true;
    $submittedProfile = [
        'ho_ten'        => trim($_POST['ho_ten'] ?? ''),
        'email'         => trim($_POST['email'] ?? ''),
        'sdt'           => trim($_POST['sdt'] ?? ''),
        'lop_chi_doan'  => trim($_POST['lop_chi_doan'] ?? ''),
        'ngay_sinh'     => trim($_POST['ngay_sinh'] ?? ''),
        'dia_chi'       => trim($_POST['dia_chi'] ?? ''),
        'so_doan_vien'  => trim($_POST['so_doan_vien'] ?? ''),
        'ghi_chu'       => trim($_POST['ghi_chu'] ?? ''),
    ];
}

require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll mb-3">
    <div class="card-header">
      <h3 class="m-0">Chào mừng<?= $currentUser ? ', ' . htmlspecialchars($currentUser['username']) : '' ?>!</h3>
    </div>
    <div class="card-body">
      <p class="text-muted mb-3">Hệ thống quản lý đoàn viên giúp bạn theo dõi hồ sơ, đoàn phí và hoạt động của chi đoàn một cách trực quan. Hãy dành ít phút để cập nhật thông tin cá nhân nhằm hỗ trợ ban cán sự quản lý chính xác hơn.</p>
      <ul class="text-muted" style="margin:0; padding-left:18px;">
        <li>Cập nhật thông tin liên hệ mới nhất.</li>
        <li>Ghi chú hoạt động, thành tích nổi bật hoặc nhu cầu hỗ trợ.</li>
        <li>Theo dõi tiến độ hoàn thành hồ sơ đoàn viên.</li>
      </ul>
    </div>
  </div>

  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h4 class="m-0">Bảng khai thông tin đoàn viên</h4>
      <span class="text-muted small">Thông tin chỉ mang tính tham khảo nội bộ</span>
    </div>
    <div class="card-body">
      <?php if ($infoSubmitted): ?>
        <div class="alert alert-success reveal-on-scroll">
          Cảm ơn bạn đã cập nhật thông tin! Bạn có thể điều chỉnh lại bất kỳ lúc nào.
        </div>
      <?php endif; ?>

      <form method="POST" class="ui-form">
        <div class="mb-3">
          <label for="ho_ten" class="form-label">Họ và tên</label>
          <input type="text" id="ho_ten" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($submittedProfile['ho_ten'] ?? ($currentUser['username'] ?? '')) ?>">
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="tenban@example.com" value="<?= htmlspecialchars($submittedProfile['email'] ?? '') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="sdt" class="form-label">Số điện thoại</label>
            <input type="tel" id="sdt" name="sdt" class="form-control" placeholder="09xx xxx xxx" value="<?= htmlspecialchars($submittedProfile['sdt'] ?? '') ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="lop_chi_doan" class="form-label">Lớp / Chi đoàn</label>
            <input type="text" id="lop_chi_doan" name="lop_chi_doan" class="form-control" placeholder="CNTT K14" value="<?= htmlspecialchars($submittedProfile['lop_chi_doan'] ?? '') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="ngay_sinh" class="form-label">Ngày sinh</label>
            <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" value="<?= htmlspecialchars($submittedProfile['ngay_sinh'] ?? '') ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="dia_chi" class="form-label">Địa chỉ liên hệ</label>
            <input type="text" id="dia_chi" name="dia_chi" class="form-control" placeholder="Số nhà, đường, quận / huyện, tỉnh / thành" value="<?= htmlspecialchars($submittedProfile['dia_chi'] ?? '') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="so_doan_vien" class="form-label">Số thẻ đoàn viên</label>
            <input type="text" id="so_doan_vien" name="so_doan_vien" class="form-control" placeholder="Nhập số thẻ nếu có" value="<?= htmlspecialchars($submittedProfile['so_doan_vien'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-3">
          <label for="ghi_chu" class="form-label">Ghi chú thêm</label>
          <textarea id="ghi_chu" name="ghi_chu" rows="4" class="form-control" placeholder="Thêm thành tích, mục tiêu rèn luyện hoặc thông tin cần hỗ trợ..."><?= htmlspecialchars($submittedProfile['ghi_chu'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu thông tin</button>
          <button type="reset" class="btn btn-secondary"><i class="fa fa-rotate"></i> Nhập lại</button>
        </div>
      </form>

      <?php if ($infoSubmitted): ?>
        <div class="mt-4 reveal-on-scroll">
          <h5 class="mb-2">Thông tin vừa cập nhật</h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <tbody>
                <tr><th class="text-muted" style="width:220px;">Họ và tên</th><td><?= htmlspecialchars($submittedProfile['ho_ten']) ?: '—' ?></td></tr>
                <tr><th class="text-muted">Email</th><td><?= htmlspecialchars($submittedProfile['email']) ?: '—' ?></td></tr>
                <tr><th class="text-muted">Số điện thoại</th><td><?= htmlspecialchars($submittedProfile['sdt']) ?: '—' ?></td></tr>
                <tr><th class="text-muted">Lớp / Chi đoàn</th><td><?= htmlspecialchars($submittedProfile['lop_chi_doan']) ?: '—' ?></td></tr>
                <tr><th class="text-muted">Ngày sinh</th><td><?= htmlspecialchars($submittedProfile['ngay_sinh']) ?: '—' ?></td></tr>
                <tr><th class="text-muted">Địa chỉ</th><td><?= htmlspecialchars($submittedProfile['dia_chi']) ?: '—' ?></td></tr>
                <tr><th class="text-muted">Số thẻ đoàn viên</th><td><?= htmlspecialchars($submittedProfile['so_doan_vien']) ?: '—' ?></td></tr>
                <tr><th class="text-muted">Ghi chú</th><td><?= htmlspecialchars($submittedProfile['ghi_chu']) ?: '—' ?></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
