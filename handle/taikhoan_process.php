<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/taikhoan_functions.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
$userId = (int)($currentUser['id'] ?? 0);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create': return handleCreate();
    case 'edit': return handleEdit();
    case 'delete': return handleDelete();
    default:
        header("Location: ../views/taikhoan.php");
        exit;
}

function handleCreate() {
    global $userId;
    $ho_ten = $_POST['ho_ten'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $trang_thai = $_POST['trang_thai'] ?? '';
    if ($username === '' || $password === '') {
        header("Location: ../views/taikhoan/create.php?error=Thiếu tên đăng nhập hoặc mật khẩu");
        exit;
    }
    $newId = addTaiKhoan($ho_ten, $username, $password, $role, $trang_thai);
    if ($newId) {
        log_action($userId, 'CREATE', 'taikhoan', (int)$newId, null, [
            'ho_ten' => $ho_ten,
            'username' => $username,
            'role' => $role,
            'trang_thai' => $trang_thai
        ]);
        header("Location: ../views/taikhoan.php?success=Thêm tài khoản thành công");
    } else {
        header("Location: ../views/taikhoan/create.php?error=Lỗi khi thêm");
    }
    exit;
}

function handleEdit() {
    global $userId;
    $id = $_POST['id'] ?? '';
    $ho_ten = $_POST['ho_ten'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $trang_thai = $_POST['trang_thai'] ?? '';
    if ($password === '') {
        $current = getTaiKhoanById($id);
        $password = $current['password'] ?? '';
    }
    $before = getTaiKhoanById($id);
    if (updateTaiKhoan($id, $ho_ten, $username, $password, $role, $trang_thai)) {
        $after = getTaiKhoanById($id);
        log_action($userId, 'UPDATE', 'taikhoan', (int)$id, $before, $after);
        header("Location: ../views/taikhoan.php?success=Cập nhật thành công");
    } else {
        header("Location: ../views/taikhoan/edit.php?id=$id&error=Lỗi khi cập nhật");
    }
    exit;
}

function handleDelete() {
    global $userId;
    $id = $_GET['id'] ?? '';
    $before = getTaiKhoanById($id);
    if (deleteTaiKhoan($id)) {
        log_action($userId, 'DELETE', 'taikhoan', (int)$id, $before, null);
        header("Location: ../views/taikhoan.php?success=Xóa thành công");
    } else {
        header("Location: ../views/taikhoan.php?error=Lỗi khi xóa");
    }
    exit;
}
