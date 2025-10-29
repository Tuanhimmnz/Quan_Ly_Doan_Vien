<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../handle/danhgia_process.php';
$danhgias = getAllDanhGia();
$pageTitle = 'DNU - Đánh giá đoàn viên';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
  <div class="card">
    <div class="card-header">
      <h3 class="m-0">ĐÁNH GIÁ – XẾP LOẠI – KHEN THƯỞNG – KỶ LUẬT</h3>
      <a href="danhgia/create_danhgia.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm đánh giá</a>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Mã SV</th>
            <th>Họ tên</th>
            <th>Năm học</th>
            <th>Xếp loại</th>
            <th>Khen thưởng</th>
            <th>Kỷ luật</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($danhgias as $dg): ?>
          <tr>
            <td><?= $dg['id'] ?></td>
            <td><?= htmlspecialchars($dg['ma_sv']) ?></td>
            <td><?= htmlspecialchars($dg['ho_ten']) ?></td>
            <td><?= htmlspecialchars($dg['nam_hoc']) ?></td>
            <td><?= htmlspecialchars($dg['xep_loai']) ?></td>
            <td><?= htmlspecialchars($dg['khen_thuong']) ?></td>
            <td><?= htmlspecialchars($dg['ky_luat']) ?></td>
            <td>
              <a href="danhgia/edit_danhgia.php?id=<?= $dg['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
              <a href="../handle/danhgia_process.php?action=delete&id=<?= $dg['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa đánh giá này?')"><i class="fa fa-trash"></i> Xóa</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
