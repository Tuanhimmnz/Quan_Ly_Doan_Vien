<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/sukien_functions.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
$userId = (int)($currentUser['id'] ?? 0);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create': return handleCreate();
    case 'edit': return handleEdit();
    case 'delete': return handleDelete();
    default:
        header("Location: ../views/sukien.php");
        exit;
}

function handleCreate() {
    global $userId;
    $ten_su_kien = $_POST['ten_su_kien'] ?? '';
    $mo_ta = $_POST['mo_ta'] ?? '';
    $ngay_to_chuc = $_POST['ngay_to_chuc'] ?? '';
    $cap_to_chuc = $_POST['cap_to_chuc'] ?? '';
    $trang_thai = $_POST['trang_thai'] ?? '';
    if ($ten_su_kien === '') {
        header("Location: ../views/sukien/create.php?error=Thiếu tên sự kiện");
        exit;
    }
    $newId = addSuKien($ten_su_kien, $mo_ta, $ngay_to_chuc, $cap_to_chuc, $trang_thai);
    if ($newId) {
        log_action($userId, 'CREATE', 'sukien', (int)$newId, null, [
            'ten_su_kien' => $ten_su_kien,
            'ngay_to_chuc' => $ngay_to_chuc,
            'cap_to_chuc' => $cap_to_chuc,
            'trang_thai' => $trang_thai
        ]);
        header("Location: ../views/sukien.php?success=Thêm sự kiện thành công");
    } else {
        header("Location: ../views/sukien/create.php?error=Lỗi khi thêm");
    }
    exit;
}

function handleEdit() {
    global $userId;
    $id = $_POST['id'] ?? '';
    $ten_su_kien = $_POST['ten_su_kien'] ?? '';
    $mo_ta = $_POST['mo_ta'] ?? '';
    $ngay_to_chuc = $_POST['ngay_to_chuc'] ?? '';
    $cap_to_chuc = $_POST['cap_to_chuc'] ?? '';
    $trang_thai = $_POST['trang_thai'] ?? '';
    $before = getSuKienById($id);
    if (updateSuKien($id, $ten_su_kien, $mo_ta, $ngay_to_chuc, $cap_to_chuc, $trang_thai)) {
        $after = getSuKienById($id);
        log_action($userId, 'UPDATE', 'sukien', (int)$id, $before, $after);
        header("Location: ../views/sukien.php?success=Cập nhật thành công");
    } else {
        header("Location: ../views/sukien/edit.php?id=$id&error=Lỗi khi cập nhật");
    }
    exit;
}

function handleDelete() {
    global $userId;
    $id = $_GET['id'] ?? '';
    $before = getSuKienById($id);
    if (deleteSuKien($id)) {
        log_action($userId, 'DELETE', 'sukien', (int)$id, $before, null);
        header("Location: ../views/sukien.php?success=Xóa thành công");
    } else {
        header("Location: ../views/sukien.php?error=Lỗi khi xóa");
    }
    exit;
}
