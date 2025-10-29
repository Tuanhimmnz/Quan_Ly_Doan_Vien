<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../functions/doanphi_functions.php';


// Xác định action từ GET hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateDoanPhi();
        break;
    case 'edit':
        handleEditDoanPhi();
        break;
    case 'delete':
        handleDeleteDoanPhi();
        break;
    // default:
    //     header("Location: ../views/doanphi.php?error=Hành động không hợp lệ");
    //     exit();
}

/**
 * Lấy tất cả danh sách đoàn phí để hiển thị bảng
 */
function handleGetAllDoanPhi() {
    return getAllDoanPhi();
}

function handleGetDoanPhiById($id) {
    return getDoanPhiById($id);
}

/**
 * Tạo bản ghi đoàn phí mới
 */
function handleCreateDoanPhi() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/doanphi.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (
        !isset($_POST['doanvien_id']) ||
        !isset($_POST['nam_hoc']) ||
        !isset($_POST['da_nop'])
    ) {
        header("Location: ../views/doanphi/edit_doanphi.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $doanvien_id = trim($_POST['doanvien_id']);
    $nam_hoc     = trim($_POST['nam_hoc']);
    $da_nop      = trim($_POST['da_nop']);   // '0' hoặc '1'
    $ngay_nop    = $_POST['ngay_nop'] ?? '';

    // Validate dữ liệu cơ bản
    if ($doanvien_id === '' || $nam_hoc === '' || $da_nop === '') {
        header("Location: ../views/doanphi/edit_doanphi.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Kiểm tra trùng (doanvien_id + nam_hoc)
    if (checkDoanPhiExists($doanvien_id, $nam_hoc)) {
        header("Location: ../views/doanphi/edit_doanphi.php?error=Đã tồn tại bản ghi đoàn phí cho năm học này");
        exit();
    }

    $success = addDoanPhi($doanvien_id, $nam_hoc, $da_nop, $ngay_nop);

    if ($success) {
        header("Location: ../views/doanphi.php?success=Thêm đoàn phí thành công");
    } else {
        header("Location: ../views/doanphi/edit_doanphi.php?error=Có lỗi xảy ra khi thêm đoàn phí");
    }
    exit();
}

/**
 * Cập nhật đoàn phí
 */
function handleEditDoanPhi() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/doanphi.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (
        !isset($_POST['id']) ||
        !isset($_POST['doanvien_id']) ||
        !isset($_POST['nam_hoc']) ||
        !isset($_POST['da_nop'])
    ) {
        header("Location: ../views/doanphi.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $id          = (int)$_POST['id'];
    $doanvien_id = trim($_POST['doanvien_id']);
    $nam_hoc     = trim($_POST['nam_hoc']);
    $da_nop      = trim($_POST['da_nop']);
    $ngay_nop    = $_POST['ngay_nop'] ?? '';

    if ($doanvien_id === '' || $nam_hoc === '' || $da_nop === '') {
        header("Location: ../views/doanphi.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    $current = getDoanPhiById($id);
    if (!$current) {
        header("Location: ../views/doanphi.php?error=Đoàn phí không tồn tại");
        exit();
    }

    // Nếu thay đổi (doanvien_id hoặc nam_hoc), phải check trùng
    if (
        $current['doanvien_id'] != $doanvien_id ||
        $current['nam_hoc']     != $nam_hoc
    ) {
        if (checkDoanPhiExists($doanvien_id, $nam_hoc)) {
            header("Location: ../views/doanphi.php?error=Đã tồn tại đoàn phí cho năm học này");
            exit();
        }
    }

    $success = updateDoanPhi($id, $doanvien_id, $nam_hoc, $da_nop, $ngay_nop);

    if ($success) {
        header("Location: ../views/doanphi.php?success=Cập nhật đoàn phí thành công");
    } else {
        header("Location: ../views/doanphi.php?error=Có lỗi xảy ra khi cập nhật đoàn phí");
    }
    exit();
}

/**
 * Xóa đoàn phí
 */
function handleDeleteDoanPhi() {
    if (!isset($_GET['id'])) {
        header("Location: ../views/doanphi.php?error=Thiếu ID đoàn phí");
        exit();
    }

    $id = (int)$_GET['id'];

    $info = getDoanPhiById($id);
    if (!$info) {
        header("Location: ../views/doanphi.php?error=Đoàn phí không tồn tại");
        exit();
    }

    $success = deleteDoanPhi($id);

    if ($success) {
        header("Location: ../views/doanphi.php?success=Xóa đoàn phí thành công");
    } else {
        header("Location: ../views/doanphi.php?error=Có lỗi xảy ra khi xóa đoàn phí");
    }
    exit();
}
?>
