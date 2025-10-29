<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

require_once __DIR__ . '/../../functions/lop_functions.php';

// chỉ cho POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list_lop.php?error=Phương thức không hợp lệ');
    exit;
}

$ma_lop  = $_POST['ma_lop']  ?? '';
$ten_lop = $_POST['ten_lop'] ?? '';
$khoa_id = $_POST['khoa_id'] ?? '';
$co_van  = $_POST['co_van']  ?? '';
$bi_thu  = $_POST['bi_thu']  ?? '';

$newId = createLop($ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu);

if ($newId !== null) {
    header('Location: list_lop.php?success=Thêm lớp thành công (ID '.$newId.')');
    exit;
} else {
    header('Location: create_lop.php?error=Không thể thêm lớp');
    exit;
}
