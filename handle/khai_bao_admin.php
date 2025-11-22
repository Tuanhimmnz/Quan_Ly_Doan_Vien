<?php
// handle/khai_bao_admin.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');

require_once __DIR__ . '/../functions/khai_bao_functions.php';

// Kiểm tra quyền admin nếu có cờ role trong session
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if (!$id) {
    header("Location: ../views/admin/khai_bao/list.php?error=Thiếu ID");
    exit;
}

if ($action === 'approve') {
    $kb = kb_get($id);
    if (!$kb) { header("Location: ../views/admin/khai_bao/list.php?error=Không tìm thấy khai báo"); exit; }

    // cập nhật trạng thái
    kb_set_status($id, 'approved', (int)($_SESSION['user']['id'] ?? 0));
    // áp dữ liệu vào bảng doanvien
    kb_apply_to_doanvien($kb);

    header("Location: ../views/admin/khai_bao/list.php?status=pending&success=Đã duyệt & cập nhật vào đoàn viên");
    exit;
}

if ($action === 'reject') {
    kb_set_status($id, 'rejected', (int)($_SESSION['user']['id'] ?? 0));
    header("Location: ../views/admin/khai_bao/list.php?status=pending&success=Đã từ chối khai báo");
    exit;
}

header("Location: ../views/admin/khai_bao/list.php?error=Hành động không hợp lệ");
