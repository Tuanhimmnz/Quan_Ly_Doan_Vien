<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/doanvien_functions.php';
require_once __DIR__ . '/../functions/diemrenluyen_functions.php';
require_once __DIR__ . '/../functions/diemrenluyen_request_functions.php';

ensureSessionStarted();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// User gửi yêu cầu điểm rèn luyện
if ($action === 'submit_user') {
    checkLogin('../index.php');
    if (($_SESSION['role'] ?? '') !== 'user') {
        $_SESSION['error'] = 'Chỉ đoàn viên mới được gửi yêu cầu.';
        header('Location: ../user/index.php');
        exit();
    }
    $user = getCurrentUser();
    $ma_sv = trim($_POST['ma_sv'] ?? '');
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $nam_hoc = trim($_POST['nam_hoc'] ?? '');
    $diem = trim($_POST['diem'] ?? '');
    $xep_loai = trim($_POST['xep_loai'] ?? '');
    $ghi_chu = trim($_POST['ghi_chu'] ?? '');

    if ($ma_sv === '' || $ho_ten === '' || $nam_hoc === '' || $diem === '') {
        $_SESSION['error'] = 'Vui lòng nhập đủ Mã SV, Họ tên, Năm học và Điểm.';
        header('Location: ../user/index.php');
        exit();
    }
    if (!is_numeric($diem)) {
        $_SESSION['error'] = 'Điểm phải là số.';
        header('Location: ../user/index.php');
        exit();
    }
    [$dv, $err] = drl_request_resolve_doanvien($ma_sv);
    $doanvien_id = $dv['id'] ?? null;
    $payload = [
        'doanvien_id' => $doanvien_id,
        'ma_sv' => $ma_sv,
        'ho_ten' => $ho_ten,
        'nam_hoc' => $nam_hoc,
        'diem' => (float)$diem,
        'xep_loai' => $xep_loai,
        'ghi_chu' => $ghi_chu
    ];
    $newId = drl_request_create((int)$user['id'], $payload);
    if ($newId) {
        log_action((int)$user['id'], 'SUBMIT', 'diemrenluyen_request', (int)$newId, null, $payload);
        $_SESSION['success'] = 'Đã gửi yêu cầu điểm rèn luyện, chờ cán bộ duyệt.';
    } else {
        $_SESSION['error'] = 'Không thể gửi yêu cầu, vui lòng thử lại.';
    }
    header('Location: ../user/index.php');
    exit();
}

// Admin duyệt / từ chối
if (in_array($action, ['approve','reject'], true)) {
    checkLogin('../index.php');
    if (!in_array($_SESSION['role'] ?? '', ['admin','Cán bộ'], true)) {
        $_SESSION['error'] = 'Bạn không có quyền duyệt.';
        header('Location: ../views/diemrenluyen_requests.php');
        exit();
    }
    $admin = getCurrentUser();
    $id = (int)($_POST['id'] ?? 0);
    $note = trim($_POST['review_note'] ?? '');
    $req = drl_request_get($id);
    if (!$req) {
        $_SESSION['error'] = 'Không tìm thấy yêu cầu.';
        header('Location: ../views/diemrenluyen_requests.php');
        exit();
    }
    $before = $req;
    if ($action === 'reject') {
        $ok = drl_request_update_status($id, 'rejected', (int)$admin['id'], $note);
        if ($ok) {
            log_action((int)$admin['id'], 'REJECT', 'diemrenluyen_request', $id, $before, ['status' => 'rejected', 'review_note' => $note]);
            $_SESSION['success'] = 'Đã từ chối yêu cầu.';
        } else {
            $_SESSION['error'] = 'Không thể cập nhật yêu cầu.';
        }
        header('Location: ../views/diemrenluyen_requests.php');
        exit();
    }
    // Approve
    $dvId = $req['doanvien_id'] ? (int)$req['doanvien_id'] : 0;
    if ($dvId <= 0) {
        $dv = getDoanVienByMaSv($req['ma_sv']);
        $dvId = $dv['id'] ?? 0;
    }
    if ($dvId <= 0) {
        $_SESSION['error'] = 'Không tìm thấy đoàn viên để duyệt.';
        header('Location: ../views/diemrenluyen_requests.php');
        exit();
    }

    $existing = getDiemRenLuyenByDoanVienNam($dvId, $req['nam_hoc']);
    $afterReq = $before;
    $afterReq['status'] = 'approved';
    $afterReq['review_note'] = $note;
    $afterReq['reviewed_by'] = (int)$admin['id'];

    $ok = drl_request_update_status($id, 'approved', (int)$admin['id'], $note);
    if (!$ok) {
        $_SESSION['error'] = 'Không thể cập nhật yêu cầu.';
        header('Location: ../views/diemrenluyen_requests.php');
        exit();
    }

    $drlId = null;
    if ($existing) {
        updateDiemRenLuyen($existing['id'], $dvId, $req['nam_hoc'], $req['diem'], $req['xep_loai'], $req['ghi_chu']);
        $drlId = $existing['id'];
    } else {
        $drlId = addDiemRenLuyen($dvId, $req['nam_hoc'], $req['diem'], $req['xep_loai'], $req['ghi_chu']);
    }

    log_action((int)$admin['id'], 'APPROVE', 'diemrenluyen_request', $id, $before, $afterReq);
    log_action((int)$admin['id'], $existing ? 'UPDATE' : 'CREATE', 'diemrenluyen', (int)$drlId, $existing ?: null, [
        'doanvien_id' => $dvId,
        'nam_hoc' => $req['nam_hoc'],
        'diem' => $req['diem'],
        'xep_loai' => $req['xep_loai']
    ]);

    $_SESSION['success'] = 'Đã duyệt và ghi điểm rèn luyện.';
    header('Location: ../views/diemrenluyen_requests.php');
    exit();
}

header('Location: ../index.php');
exit();
?>
