<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>DNU - Tra cứu đoàn viên</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include './menu.php'; ?>
<div class="container mt-3">
    <h3 class="text-center mb-4">TRA CỨU ĐOÀN VIÊN</h3>
    <form action="tracuu/result_tracuu.php" method="GET" class="d-flex justify-content-center">
        <input type="text" name="keyword" class="form-control w-50" placeholder="Nhập mã SV, họ tên hoặc lớp..." required>
        <button class="btn btn-primary ms-2" type="submit">Tìm kiếm</button>
    </form>
</div>
</body>
</html>
