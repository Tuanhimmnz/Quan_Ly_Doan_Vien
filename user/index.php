<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/lop_functions.php';
require_once __DIR__ . '/../functions/khaibao_functions.php';
require_once __DIR__ . '/../functions/diemrenluyen_request_functions.php';

checkLogin('../index.php');

ensureSessionStarted();
if ($_SESSION['role'] !== 'user') {
    header('Location: ../views/doanvien.php');
    exit();
}

$currentUser = getCurrentUser();
$lopList = getAllLopForDropdown();
$recentDeclarations = getDeclarationsByUser($currentUser['id']);
$recentDrlRequests = array_slice(drl_requests_list(['user_id' => $currentUser['id'], 'status' => null]), 0, 10);

$statusBadge = [
    'pending'  => ['label' => 'Đang chờ duyệt', 'class' => 'badge bg-warning'],
    'approved' => ['label' => 'Đã duyệt', 'class' => 'badge bg-success'],
    'rejected' => ['label' => 'Từ chối', 'class' => 'badge bg-secondary'],
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cổng Đoàn viên - FITDNU</title>
  <link rel="stylesheet" href="../assets/css/ui.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-2x2b9nEobQh1gU6t9a6m2jJc1tq6J+Jm0g9yYQ9C2V/7VnR6t9Q7o7E4S3kVvWm0wNw0FJ2nE6k9gE1vX3K0dg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body { background: #f5f6fa; }
    .user-shell { min-height: 100vh; display: flex; flex-direction: column; }
    .user-header { background: #fff; border-bottom: 1px solid #e5e7eb; box-shadow: var(--shadow); padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; }
    .user-header .brand { display: flex; align-items: center; gap: 0.75rem; font-weight: 600; color: var(--primary); }
    .user-header .brand img { width: 40px; height: 40px; }
    .user-main { flex: 1; padding: 1.5rem 0; }
  </style>
</head>
<body>
  <div class="user-shell">
    <header class="user-header">
      <div class="brand">
        <img src="../images/logo-doan.webp" alt="Logo Đoàn">
        <span>Cổng thông tin đoàn viên</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div class="text-muted small">Xin chào, <strong><?= htmlspecialchars($currentUser['username']) ?></strong></div>
        <a href="../handle/logout_process.php" class="btn btn-secondary btn-sm">
          <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
        </a>
      </div>
    </header>

    <main class="user-main">
      <div class="container reveal-on-scroll">
        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success reveal-on-scroll">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger reveal-on-scroll">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <div class="card reveal-on-scroll mb-3">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="m-0">Giới thiệu hệ thống</h3>
            <span class="badge bg-success">Đang hoạt động</span>
          </div>
          <div class="card-body">
            <p class="text-muted">
              Hệ thống giúp đoàn viên cập nhật thông tin cá nhân, hỗ trợ cán bộ đoàn quản lý hồ sơ, đoàn phí và hoạt động nhanh chóng.
              Vui lòng điền bảng khai báo bên dưới để cán bộ phê duyệt và đồng bộ vào danh sách chính thức.
            </p>
            <ul class="text-muted" style="margin:0; padding-left:18px;">
              <li>Cập nhật đầy đủ thông tin liên hệ và mã sinh viên.</li>
              <li>Thông tin sẽ chờ cán bộ duyệt trước khi hiển thị trong hệ thống quản trị.</li>
              <li>Tra cứu trạng thái khai báo ở bảng lịch sử ngay dưới form.</li>
            </ul>
          </div>
        </div>

        <div class="card reveal-on-scroll mb-3">
          <div class="card-header">
            <h4 class="m-0">Bảng khai báo thông tin đoàn viên</h4>
          </div>
          <div class="card-body">
            <form action="../handle/khai_bao_process.php" method="POST" class="ui-form">
              <input type="hidden" name="action" value="submit">

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="ma_sv" class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                  <input type="text" id="ma_sv" name="ma_sv" class="form-control" placeholder="VD: 21CNTT01" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="ho_ten" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                  <input type="text" id="ho_ten" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="gioi_tinh" class="form-label">Giới tính</label>
                  <select id="gioi_tinh" name="gioi_tinh" class="form-select">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                    <option value="Khác">Khác</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                  <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="lop_id" class="form-label">Lớp / Chi đoàn <span class="text-danger">*</span></label>
                  <select id="lop_id" name="lop_id" class="form-select" required>
                    <option value="">-- Chọn lớp / chi đoàn --</option>
                    <?php foreach ($lopList as $lop): ?>
                      <option value="<?= $lop['id'] ?>"><?= htmlspecialchars($lop['ma_lop'] . ' - ' . $lop['ten_lop']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" id="email" name="email" class="form-control" placeholder="tenban@example.com">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="sdt" class="form-label">Số điện thoại</label>
                  <input type="tel" id="sdt" name="sdt" class="form-control" placeholder="09xx xxx xxx">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="chuc_vu" class="form-label">Chức vụ trong chi đoàn</label>
                  <select id="chuc_vu" name="chuc_vu" class="form-select">
                    <option value="">-- Chọn chức vụ --</option>
                    <option value="Đoàn viên">Đoàn viên</option>
                    <option value="Bí thư">Bí thư</option>
                    <option value="Phó bí thư">Phó bí thư</option>
                    <option value="Ủy viên BCH">Ủy viên BCH</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="trang_thai" class="form-label">Trạng thái sinh hoạt</label>
                  <select id="trang_thai" name="trang_thai" class="form-select">
                    <option value="Đang sinh hoạt">Đang sinh hoạt</option>
                    <option value="Chuyển sinh hoạt">Chuyển sinh hoạt</option>
                    <option value="Đã ra trường">Đã ra trường</option>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="ngay_vao_doan" class="form-label">Ngày vào Đoàn</label>
                  <input type="date" id="ngay_vao_doan" name="ngay_vao_doan" class="form-control">
                </div>
              </div>

              <!-- Bỏ các trường không cần thiết để đồng bộ với doanvien: địa chỉ, số thẻ, ghi chú -->

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Gửi khai báo</button>
                <button type="reset" class="btn btn-secondary"><i class="fa fa-rotate"></i> Nhập lại</button>
              </div>
            </form>
          </div>
        </div>

        <div class="card reveal-on-scroll mb-3">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="m-0">Nhập điểm rèn luyện</h4>
            <span class="badge bg-secondary">Gửi cán bộ duyệt</span>
          </div>
          <div class="card-body">
            <form action="../handle/diemrenluyen_request_process.php" method="POST" class="ui-form">
              <input type="hidden" name="action" value="submit_user">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                  <input type="text" name="ma_sv" class="form-control" placeholder="VD: SV210001" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                  <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Năm học <span class="text-danger">*</span></label>
                  <input type="text" name="nam_hoc" class="form-control" placeholder="2024-2025" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Điểm <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" name="diem" class="form-control" placeholder="80" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Xếp loại</label>
                  <input type="text" name="xep_loai" class="form-control" placeholder="Tốt / Khá / ...">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Ghi chú</label>
                  <input type="text" name="ghi_chu" class="form-control" placeholder="Minh chứng...">
                </div>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Gửi điểm rèn luyện</button>
            </form>
          </div>
        </div>

        <div class="card reveal-on-scroll">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="m-0">Lịch sử khai báo</h4>
            <span class="text-muted small">Lưu tối đa 10 lần gần nhất</span>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Ngày gửi</th>
                    <th>Mã SV</th>
                    <th>Họ tên</th>
                    <th>Lớp</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($recentDeclarations)): ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-3">Chưa có khai báo nào.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($recentDeclarations as $decl): ?>
                      <tr>
                        <td><?= $decl['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($decl['created_at'])) ?></td>
                        <td><?= htmlspecialchars($decl['ma_sv']) ?></td>
                        <td><?= htmlspecialchars($decl['ho_ten']) ?></td>
                        <td><?= htmlspecialchars($decl['ten_lop'] ?? '—') ?></td>
                        <td>
                          <?php
                            $badge = $statusBadge[$decl['status']] ?? $statusBadge['pending'];
                            echo '<span class="' . $badge['class'] . '">' . $badge['label'] . '</span>';
                          ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars($decl['ghi_chu'] ?: '—')) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card reveal-on-scroll" style="margin-top:12px;">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="m-0">Yêu cầu điểm rèn luyện</h4>
            <span class="text-muted small">Theo tài khoản của bạn</span>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Mã SV</th>
                    <th>Năm học</th>
                    <th>Điểm</th>
                    <th>Xếp loại</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($recentDrlRequests)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có yêu cầu nào.</td></tr>
                  <?php else: ?>
                    <?php foreach ($recentDrlRequests as $req): ?>
                      <tr>
                        <td><?= $req['id'] ?></td>
                        <td><?= htmlspecialchars($req['ma_sv']) ?></td>
                        <td><?= htmlspecialchars($req['nam_hoc']) ?></td>
                        <td><?= htmlspecialchars($req['diem']) ?></td>
                        <td><?= htmlspecialchars($req['xep_loai']) ?></td>
                        <td>
                          <?php
                            $badge = [
                              'pending' => ['label' => 'Chờ duyệt', 'class' => 'badge bg-warning'],
                              'approved' => ['label' => 'Đã duyệt', 'class' => 'badge bg-success'],
                              'rejected' => ['label' => 'Từ chối', 'class' => 'badge bg-secondary'],
                            ];
                            $st = $req['status'] ?? 'pending';
                            $b = $badge[$st] ?? $badge['pending'];
                          ?>
                          <span class="<?= $b['class'] ?>"><?= $b['label'] ?></span>
                        </td>
                        <td><?= nl2br(htmlspecialchars($req['review_note'] ?: $req['ghi_chu'] ?: '')) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="../assets/js/ui.js"></script>
</body>
</html>
