<?php
require_once __DIR__ . '/../../functions/tracuu_functions.php';
$keyword = $_GET['keyword'] ?? '';
$results = $keyword ? searchDoanVien($keyword) : [];
?>
<!DOCTYPE html>
<html>
<head>
<title>Kết quả tra cứu đoàn viên</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <h4>Kết quả tìm kiếm cho: <span class="text-primary"><?= htmlspecialchars($keyword) ?></span></h4>
    <a href="../tracuu.php" class="btn btn-secondary mb-3">Quay lại</a>
    <?php if (empty($results)): ?>
        <div class="alert alert-warning">Không tìm thấy đoàn viên nào phù hợp.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead><tr>
                <th>Mã SV</th><th>Họ tên</th><th>Giới tính</th>
                <th>Email</th><th>SĐT</th><th>Lớp</th><th>Khoa</th>
            </tr></thead>
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
    <?php endif; ?>
</div>
</body>
</html>
