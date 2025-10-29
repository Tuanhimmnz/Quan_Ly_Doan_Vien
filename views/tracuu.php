<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
$pageTitle = 'DNU - Tra cứu đoàn viên';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3">
  <div class="card">
    <h3 class="text-center mb-4">TRA CỨU ĐOÀN VIÊN</h3>
    <form action="tracuu/result_tracuu.php" method="GET" class="d-flex justify-content-center">
      <input type="text" name="keyword" class="form-control" style="max-width:520px;" placeholder="Nhập mã SV, họ tên hoặc lớp..." required>
      <button class="btn btn-primary ms-2" type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
