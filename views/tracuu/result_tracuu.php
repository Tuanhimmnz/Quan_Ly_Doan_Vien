<?php
require_once __DIR__ . '/../../functions/tracuu_functions.php';
$keyword = $_GET['keyword'] ?? '';
$results = $keyword ? searchDoanVien($keyword) : [];
$pageTitle = 'Kết quả tra cứu đoàn viên';
require __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Kết quả tìm kiếm cho: <span style="color:#0072BC;"><?= htmlspecialchars($keyword) ?></span></h4>
        <a href="../tracuu.php" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Quay lại</a>
      </div>

      <?php if (empty($results)): ?>
        <div class="alert alert-warning reveal-on-scroll">Không tìm thấy đoàn viên nào phù hợp.</div>
      <?php else: ?>
        <div class="table-responsive reveal-on-scroll">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>Mã SV</th>
                <th>Họ tên</th>
                <th>Giới tính</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Lớp</th>
                <th>Khoa</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['ma_sv']) ?></td>
                  <td><?= htmlspecialchars($r['ho_ten']) ?></td>
                  <td><?= htmlspecialchars($r['gioi_tinh']) ?></td>
                  <td><?= htmlspecialchars($r['email']) ?></td>
                  <td><?= htmlspecialchars($r['sdt']) ?></td>
                  <td><?= htmlspecialchars($r['ten_lop']) ?></td>
                  <td><?= htmlspecialchars($r['ten_khoa']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
