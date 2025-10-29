<?php
require_once __DIR__ . '/../functions/doanvien_functions.php';

// xác định action
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateDoanVien();
        break;
    case 'edit':
        handleEditDoanVien();
        break;
    case 'delete':
        handleDeleteDoanVien();
        break;
    default:
        // không redirect ở đây vì file này cũng được include từ view
        break;
}

/**
 * Dùng cho trang danh sách
 */
function handleGetAllDoanVien() {
    return getAllDoanVien();
}

/**
 * Dùng cho edit_doanvien.php
 */
function handleGetDoanVienById($id) {
    return getDoanVienById($id);
}

/**
 * Thêm đoàn viên
 */
function handleCreateDoanVien() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/doanvien.php?error=Phương thức không hợp lệ");
        exit();
    }

    $ma_sv         = trim($_POST['ma_sv'] ?? '');
    $ho_ten        = trim($_POST['ho_ten'] ?? '');
    $ngay_sinh     = $_POST['ngay_sinh'] ?? null;
    $gioi_tinh     = $_POST['gioi_tinh'] ?? '';
    $sdt           = trim($_POST['sdt'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $lop_id        = $_POST['lop_id'] ?? '';
    $chuc_vu       = $_POST['chuc_vu'] ?? '';
    $trang_thai    = $_POST['trang_thai'] ?? '';
    $ngay_vao_doan = $_POST['ngay_vao_doan'] ?? null;

    if ($ma_sv === '' || $ho_ten === '' || $lop_id === '') {
        header("Location: ../views/doanvien/create_doanvien.php?error=Vui lòng nhập đầy đủ thông tin bắt buộc");
        exit();
    }

    $ok = addDoanVien(
        $ma_sv,
        $ho_ten,
        $ngay_sinh,
        $gioi_tinh,
        $sdt,
        $email,
        $lop_id,
        $chuc_vu,
        $trang_thai,
        $ngay_vao_doan
    );

    if ($ok) {
        header("Location: ../views/doanvien.php?success=Thêm đoàn viên thành công");
    } else {
        header("Location: ../views/doanvien/create_doanvien.php?error=Có lỗi khi thêm đoàn viên");
    }
    exit();
}

/**
 * Sửa đoàn viên
 */
function handleEditDoanVien() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/doanvien.php?error=Phương thức không hợp lệ");
        exit();
    }

    $id            = $_POST['id'] ?? '';
    $ma_sv         = trim($_POST['ma_sv'] ?? '');
    $ho_ten        = trim($_POST['ho_ten'] ?? '');
    $ngay_sinh     = $_POST['ngay_sinh'] ?? null;
    $gioi_tinh     = $_POST['gioi_tinh'] ?? '';
    $sdt           = trim($_POST['sdt'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $lop_id        = $_POST['lop_id'] ?? '';
    $chuc_vu       = $_POST['chuc_vu'] ?? '';
    $trang_thai    = $_POST['trang_thai'] ?? '';
    $ngay_vao_doan = $_POST['ngay_vao_doan'] ?? null;

    if ($id === '' || $ma_sv === '' || $ho_ten === '' || $lop_id === '') {
        header("Location: ../views/doanvien/edit_doanvien.php?id=".$id."&error=Thiếu thông tin bắt buộc");
        exit();
    }

    $current = getDoanVienById($id);
    if (!$current) {
        header("Location: ../views/doanvien.php?error=Đoàn viên không tồn tại");
        exit();
    }

    $ok = updateDoanVien(
        $id,
        $ma_sv,
        $ho_ten,
        $ngay_sinh,
        $gioi_tinh,
        $sdt,
        $email,
        $lop_id,
        $chuc_vu,
        $trang_thai,
        $ngay_vao_doan
    );

    if ($ok) {
        header("Location: ../views/doanvien.php?success=Cập nhật đoàn viên thành công");
    } else {
        header("Location: ../views/doanvien/edit_doanvien.php?id=".$id."&error=Cập nhật thất bại");
    }
    exit();
}

/**
 * Xóa đoàn viên
 */
function handleDeleteDoanVien() {
    if (!isset($_GET['id']) || $_GET['id'] === '') {
        header("Location: ../views/doanvien.php?error=Thiếu ID đoàn viên");
        exit();
    }

    $id = $_GET['id'];

    $current = getDoanVienById($id);
    if (!$current) {
        header("Location: ../views/doanvien.php?error=Đoàn viên không tồn tại");
        exit();
    }

    $ok = deleteDoanVien($id);

    if ($ok) {
        header("Location: ../views/doanvien.php?success=Xóa đoàn viên thành công");
    } else {
        header("Location: ../views/doanvien.php?error=Xóa đoàn viên thất bại");
    }
    exit();
}
?>
