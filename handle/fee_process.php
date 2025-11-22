<?php
// [FITDNU-ADD] Fee process handler
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/security.php';
require_once __DIR__ . '/../functions/fee_functions.php';
require_once __DIR__ . '/../functions/audit_functions.php';
checkLogin(__DIR__ . '/../index.php');
$user = getCurrentUser();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
  case 'year_create':
    csrf_require_post('fee_year', '../views/fee/index.php');
    $nam = (int)($_POST['nam'] ?? 0);
    $muc = (float)($_POST['muc_dong'] ?? 0);
    $ghi_chu = trim($_POST['ghi_chu'] ?? '');
    if ($nam <= 0) { $_SESSION['error'] = 'Năm không hợp lệ'; break; }
    $exists = fee_year_find_by_year($nam);
    if ($exists) { $_SESSION['error'] = 'Năm đã tồn tại'; break; }
    if (fee_year_create($nam, $muc, $ghi_chu)) {
      log_action((int)$user['id'], 'CREATE', 'doan_phi_nam', 0, null, ['nam' => $nam, 'muc_dong' => $muc]);
      $_SESSION['success'] = 'Thêm năm đoàn phí thành công';
    } else { $_SESSION['error'] = 'Lỗi khi thêm năm'; }
    break;

  case 'year_update':
    csrf_require_post('fee_year', '../views/fee/index.php');
    $id = (int)($_POST['id'] ?? 0);
    $old = fee_year_get($id);
    if (!$old) { $_SESSION['error'] = 'Bản ghi không tồn tại'; break; }
    $nam = (int)($_POST['nam'] ?? 0);
    $muc = (float)($_POST['muc_dong'] ?? 0);
    $ghi_chu = trim($_POST['ghi_chu'] ?? '');
    if (fee_year_update($id, $nam, $muc, $ghi_chu)) {
      log_action((int)$user['id'], 'UPDATE', 'doan_phi_nam', $id, $old, ['nam'=>$nam,'muc_dong'=>$muc,'ghi_chu'=>$ghi_chu]);
      $_SESSION['success'] = 'Cập nhật thành công';
    } else { $_SESSION['error'] = 'Lỗi khi cập nhật'; }
    break;

  case 'year_delete':
    $id = (int)($_GET['id'] ?? 0);
    $old = fee_year_get($id);
    if (!$old) { $_SESSION['error'] = 'Bản ghi không tồn tại'; break; }
    if (fee_year_delete($id)) {
      log_action((int)$user['id'], 'DELETE', 'doan_phi_nam', $id, $old, null);
      $_SESSION['success'] = 'Đã xóa năm';
    } else { $_SESSION['error'] = 'Lỗi khi xóa'; }
    break;

  case 'receipt_upsert':
    csrf_require_post('fee_receipt', '../views/fee/index.php');
    $doan_vien_id = (int)($_POST['doan_vien_id'] ?? 0);
    $nam = (int)($_POST['nam'] ?? 0);
    $so_tien = (float)($_POST['so_tien'] ?? 0);
    $ngay_thu = $_POST['ngay_thu'] ?? null;
    $hinh_thuc = $_POST['hinh_thuc'] ?? 'VND';
    $ghi_chu = $_POST['ghi_chu'] ?? '';
    if ($doan_vien_id <= 0 || $nam <= 0) { $_SESSION['error'] = 'Thiếu đoàn viên / năm'; break; }
    if (fee_receipt_upsert($doan_vien_id, $nam, $so_tien, $ngay_thu ?: null, $hinh_thuc, $ghi_chu)) {
      log_action((int)$user['id'], 'UPSERT', 'doan_phi_thu', 0, null, ['doan_vien_id'=>$doan_vien_id,'nam'=>$nam,'so_tien'=>$so_tien]);
      $_SESSION['success'] = 'Đã ghi nhận thu phí';
    } else { $_SESSION['error'] = 'Lỗi khi ghi nhận'; }
    break;
}

header('Location: ../views/fee/index.php');
exit();
?>

