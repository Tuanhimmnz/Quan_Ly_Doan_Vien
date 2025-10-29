<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

require_once __DIR__ . '/../../functions/lop_functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: list_lop.php?error=Thiếu ID lớp để xóa');
    exit;
}

$ok = deleteLop($id);

if ($ok) {
    header('Location: list_lop.php?success=Đã xóa lớp ID '.$id);
    exit;
} else {
    header('Location: list_lop.php?error=Không thể xóa lớp ID '.$id);
    exit;
}
