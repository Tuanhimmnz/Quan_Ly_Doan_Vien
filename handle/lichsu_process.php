<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/lichsu_functions.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
$userId = (int)($currentUser['id'] ?? 0);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create': return handleCreate(); 
    case 'edit': return handleEdit();
    case 'delete': return handleDelete();
    default:
        header("Location: ../views/lichsu.php");
        exit;
}

function handleCreate() {
    global $userId;
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $ngay_bat_dau = $_POST['ngay_bat_dau'] ?? '';
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'] ?? '';
    $trang_thai = $_POST['trang_thai'] ?? '';
    $ghi_chu = $_POST['ghi_chu'] ?? '';
    if (empty($doanvien_id) || empty($ngay_bat_dau)) {
        header("Location: ../views/lichsu/create.php?error=Thiếu thông tin bắt buộc");
        exit;
    }
    $newId = addLichSu($doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu);
    if ($newId) {
        log_action($userId, 'CREATE', 'lichsu_thamgia', (int)$newId, null, [
            'doanvien_id' => $doanvien_id,
            'trang_thai' => $trang_thai,
            'ngay_bat_dau' => $ngay_bat_dau
        ]);
        header("Location: ../views/lichsu.php?success=Thêm bản ghi thành công");
    } else {
        header("Location: ../views/lichsu/create.php?error=Lỗi khi thêm");
    }
    exit;
}

function handleEdit() {
    global $userId;
    $id = $_POST['id'] ?? '';
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $ngay_bat_dau = $_POST['ngay_bat_dau'] ?? '';
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'] ?? '';
    $trang_thai = $_POST['trang_thai'] ?? '';
    $ghi_chu = $_POST['ghi_chu'] ?? '';
    $before = getLichSuById($id);
    if (updateLichSu($id, $doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu)) {
        $after = getLichSuById($id);
        log_action($userId, 'UPDATE', 'lichsu_thamgia', (int)$id, $before, $after);
        header("Location: ../views/lichsu.php?success=Cập nhật thành công");
    } else {
        header("Location: ../views/lichsu/edit.php?id=$id&error=Lỗi khi cập nhật");
    }
    exit;
}

function handleDelete() {
    global $userId;
    $id = $_GET['id'] ?? '';
    $before = getLichSuById($id);
    if (deleteLichSu($id)) {
        log_action($userId, 'DELETE', 'lichsu_thamgia', (int)$id, $before, null);
        header("Location: ../views/lichsu.php?success=Xóa thành công");
    } else {
        header("Location: ../views/lichsu.php?error=Lỗi khi xóa");
    }
    exit;
}
