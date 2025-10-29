<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/danhgia_functions.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create': handleCreateDanhGia(); break;
    case 'edit': handleEditDanhGia(); break;
    case 'delete': handleDeleteDanhGia(); break;
}

function handleCreateDanhGia() {
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $nam_hoc = $_POST['nam_hoc'] ?? '';
    $xep_loai = $_POST['xep_loai'] ?? '';
    $khen_thuong = $_POST['khen_thuong'] ?? '';
    $ky_luat = $_POST['ky_luat'] ?? '';
    $ghi_chu = $_POST['ghi_chu'] ?? '';

    if (empty($doanvien_id) || empty($nam_hoc) || empty($xep_loai)) {
        header("Location: ../views/danhgia/create_danhgia.php?error=Thiếu thông tin bắt buộc");
        exit;
    }

    if (addDanhGia($doanvien_id, $nam_hoc, $xep_loai, $khen_thuong, $ky_luat, $ghi_chu)) {
        header("Location: ../views/danhgia.php?success=Thêm đánh giá thành công");
    } else {
        header("Location: ../views/danhgia/create_danhgia.php?error=Lỗi khi thêm đánh giá");
    }
    exit;
}

function handleEditDanhGia() {
    $id = $_POST['id'] ?? '';
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $nam_hoc = $_POST['nam_hoc'] ?? '';
    $xep_loai = $_POST['xep_loai'] ?? '';
    $khen_thuong = $_POST['khen_thuong'] ?? '';
    $ky_luat = $_POST['ky_luat'] ?? '';
    $ghi_chu = $_POST['ghi_chu'] ?? '';

    if (updateDanhGia($id, $doanvien_id, $nam_hoc, $xep_loai, $khen_thuong, $ky_luat, $ghi_chu)) {
        header("Location: ../views/danhgia.php?success=Cập nhật thành công");
    } else {
        header("Location: ../views/danhgia/edit_danhgia.php?id=$id&error=Lỗi khi cập nhật");
    }
    exit;
}

function handleDeleteDanhGia() {
    $id = $_GET['id'] ?? '';
    if (deleteDanhGia($id)) {
        header("Location: ../views/danhgia.php?success=Xóa thành công");
    } else {
        header("Location: ../views/danhgia.php?error=Lỗi khi xóa");
    }
    exit;
}
?>
