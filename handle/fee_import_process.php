<?php
// [FITDNU-ADD] Bulk import fee receipts
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/security.php';
require_once __DIR__ . '/../functions/fee_functions.php';
require_once __DIR__ . '/../functions/import_functions.php';
require_once __DIR__ . '/../functions/audit_functions.php';
checkLogin(__DIR__ . '/../index.php');
$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_post('fee_import', '../views/fee/index.php');
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Vui lòng chọn tệp import';
        header('Location: ../views/fee/index.php'); exit();
    }
    [$rows, $errs] = parse_upload_rows($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    if ($errs) {
        $_SESSION['error'] = implode('; ', $errs);
        header('Location: ../views/fee/index.php'); exit();
    }
    $total = count($rows); $ok = 0; $errors = [];
    foreach ($rows as $i => $r) {
        $ma_sv = trim($r['ma_sv'] ?? '');
        $nam = (int)($r['nam'] ?? 0);
        $so_tien = (float)($r['so_tien'] ?? 0);
        $ngay_thu = $r['ngay_thu'] ?? null;
        if ($ma_sv === '' || $nam <= 0) { $errors[] = ['row'=>$i+2,'reason'=>'Thiếu ma_sv/nam']; continue; }
        $dv = null;
        // Lookup doan vien by ma_sv
        $conn = getDbConnection();
        $stmt = mysqli_prepare($conn, "SELECT id FROM doanvien WHERE ma_sv=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $ma_sv);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $dv = $res ? mysqli_fetch_assoc($res) : null;
        mysqli_stmt_close($stmt); mysqli_close($conn);
        if (!$dv) { $errors[] = ['row'=>$i+2,'reason'=>'Không tìm thấy mã SV']; continue; }
        $ok_exec = fee_receipt_upsert((int)$dv['id'], $nam, $so_tien, $ngay_thu ?: null, 'VND', '');
        $ok += $ok_exec ? 1 : 0;
        if (!$ok_exec) $errors[] = ['row'=>$i+2,'reason'=>'Lỗi ghi nhận'];
    }
    log_action((int)$user['id'], 'IMPORT', 'doan_phi_thu', 0, null, ['file'=>$_FILES['file']['name'],'total'=>$total,'ok'=>$ok,'errors'=>count($errors)]);
    $_SESSION['fee_import_result'] = compact('total','ok','errors');
}

header('Location: ../views/fee/index.php?fee_import=1');
exit();
?>

