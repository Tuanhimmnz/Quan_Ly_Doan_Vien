<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/diemrenluyen_functions.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
$userId = (int)($currentUser['id'] ?? 0);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create': return handleCreate();
    case 'edit': return handleEdit();
    case 'delete': return handleDelete();
    default:
        header("Location: ../views/diemrenluyen.php");
        exit;
}

function handleCreate() {
    global $userId;
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $nam_hoc = $_POST['nam_hoc'] ?? '';
    $diem = $_POST['diem'] ?? '';
    $xep_loai = $_POST['xep_loai'] ?? '';
    $ghi_chu = $_POST['ghi_chu'] ?? '';
    if (empty($doanvien_id) || $nam_hoc === '' || $diem === '') {
        header("Location: ../views/diemrenluyen/create.php?error=Thiếu thông tin bắt buộc");
        exit;
    }
    $newId = addDiemRenLuyen($doanvien_id, $nam_hoc, $diem, $xep_loai, $ghi_chu);
    if ($newId) {
        log_action($userId, 'CREATE', 'diemrenluyen', (int)$newId, null, [
            'doanvien_id' => $doanvien_id,
            'nam_hoc' => $nam_hoc,
            'diem' => $diem,
            'xep_loai' => $xep_loai
        ]);
        header("Location: ../views/diemrenluyen.php?success=Thêm thành công");
    } else {
        header("Location: ../views/diemrenluyen/create.php?error=Lỗi khi thêm");
    }
    exit;
}

function handleEdit() {
    global $userId;
    $id = $_POST['id'] ?? '';
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $nam_hoc = $_POST['nam_hoc'] ?? '';
    $diem = $_POST['diem'] ?? '';
    $xep_loai = $_POST['xep_loai'] ?? '';
    $ghi_chu = $_POST['ghi_chu'] ?? '';
    $before = getDiemRenLuyenById($id);
    if (updateDiemRenLuyen($id, $doanvien_id, $nam_hoc, $diem, $xep_loai, $ghi_chu)) {
        $after = getDiemRenLuyenById($id);
        log_action($userId, 'UPDATE', 'diemrenluyen', (int)$id, $before, $after);
        header("Location: ../views/diemrenluyen.php?success=Cập nhật thành công");
    } else {
        header("Location: ../views/diemrenluyen/edit.php?id=$id&error=Lỗi khi cập nhật");
    }
    exit;
}

function handleDelete() {
    global $userId;
    $id = $_GET['id'] ?? '';
    $before = getDiemRenLuyenById($id);
    if (deleteDiemRenLuyen($id)) {
        log_action($userId, 'DELETE', 'diemrenluyen', (int)$id, $before, null);
        header("Location: ../views/diemrenluyen.php?success=Xóa thành công");
    } else {
        header("Location: ../views/diemrenluyen.php?error=Lỗi khi xóa");
    }
    exit;
}
