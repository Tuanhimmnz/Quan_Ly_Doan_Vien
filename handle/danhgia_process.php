<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/danhgia_functions.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
$userId = (int)($currentUser['id'] ?? 0);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create': handleCreateDanhGia(); break;
    case 'edit': handleEditDanhGia(); break;
    case 'delete': handleDeleteDanhGia(); break;
}

function handleCreateDanhGia() {
    global $userId;
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $loai = $_POST['loai'] ?? '';
    $mo_ta = $_POST['mo_ta'] ?? '';
    $ngay_quyet_dinh = $_POST['ngay_quyet_dinh'] ?? '';
    $noi_dung = $_POST['noi_dung'] ?? '';
    $trang_thai = $_POST['trang_thai'] ?? '';
    $redirectBase = ($loai === 'Kỷ luật') ? '../views/kyluat.php' : '../views/khenthuong.php';

    if (empty($doanvien_id) || empty($loai)) {
        header("Location: ../views/danhgia/create_danhgia.php?error=Thiếu thông tin bắt buộc");
        exit;
    }

    if (addDanhGia($doanvien_id, $loai, $mo_ta, $ngay_quyet_dinh, $noi_dung, $trang_thai)) {
        log_action($userId, 'CREATE', $loai === 'Kỷ luật' ? 'kyluat' : 'khenthuong', $doanvien_id, null, [
            'doanvien_id' => $doanvien_id,
            'ngay_quyet_dinh' => $ngay_quyet_dinh,
            'trang_thai' => $trang_thai,
            'mo_ta' => $mo_ta
        ]);
        header("Location: $redirectBase?success=Thêm bản ghi thành công");
    } else {
        header("Location: ../views/danhgia/create_danhgia.php?error=Lỗi khi thêm bản ghi");
    }
    exit;
}

function handleEditDanhGia() {
    global $userId;
    $id = $_POST['id'] ?? '';
    $doanvien_id = $_POST['doanvien_id'] ?? '';
    $loai = $_POST['loai'] ?? '';
    $mo_ta = $_POST['mo_ta'] ?? '';
    $ngay_quyet_dinh = $_POST['ngay_quyet_dinh'] ?? '';
    $noi_dung = $_POST['noi_dung'] ?? '';
    $trang_thai = $_POST['trang_thai'] ?? '';
    $redirectBase = ($loai === 'Kỷ luật') ? '../views/kyluat.php' : '../views/khenthuong.php';

    $before = getDanhGiaById($id);
    if (updateDanhGia($id, $doanvien_id, $loai, $mo_ta, $ngay_quyet_dinh, $noi_dung, $trang_thai)) {
        $after = getDanhGiaById($id);
        log_action($userId, 'UPDATE', $loai === 'Kỷ luật' ? 'kyluat' : 'khenthuong', $id, $before, $after);
        header("Location: $redirectBase?success=Cập nhật thành công");
    } else {
        header("Location: ../views/danhgia/edit_danhgia.php?id=$id&error=Lỗi khi cập nhật");
    }
    exit;
}

function handleDeleteDanhGia() {
    global $userId;
    $id = $_GET['id'] ?? '';
    $record = getDanhGiaById($id);
    $redirectBase = '../views/khenthuong.php';
    if ($record && ($record['loai'] ?? '') === 'Kỷ luật') {
        $redirectBase = '../views/kyluat.php';
    }
    if (deleteDanhGia($id)) {
        log_action($userId, 'DELETE', ($record['loai'] ?? 'khenthuong') === 'Kỷ luật' ? 'kyluat' : 'khenthuong', $id, $record, null);
        header("Location: $redirectBase?success=Xóa thành công");
    } else {
        header("Location: $redirectBase?error=Lỗi khi xóa");
    }
    exit;
}
?>
