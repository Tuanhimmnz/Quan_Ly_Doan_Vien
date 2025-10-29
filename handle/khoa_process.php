<?php
require_once __DIR__ . '/../functions/khoa_functions.php';

// Xác định action từ GET / POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateKhoa();
        break;
    case 'edit':
        handleEditKhoa();
        break;
    case 'delete':
        handleDeleteKhoa();
        break;
    default:
        // không làm gì thêm, vì file này có thể được include để gọi handleGet*
        break;
}

/**
 * Lấy tất cả khoa để in ra views/khoa.php
 */
function handleGetAllKhoa() {
    return getAllKhoa();
}

/**
 * Lấy chi tiết 1 khoa để in ra form edit
 */
function handleGetKhoaById($id) {
    return getKhoaById($id);
}

/**
 * Xử lý thêm khoa mới
 * LƯU Ý: mo_ta truyền vào cuối cùng theo hàm addKhoa mới
 */
function handleCreateKhoa() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/khoa.php?error=Phương thức không hợp lệ");
        exit();
    }

    // Lấy dữ liệu từ form
    $ten_khoa      = trim($_POST['ten_khoa']      ?? '');
    $truong_khoa   = trim($_POST['truong_khoa']   ?? '');
    $sdt_lien_he   = trim($_POST['sdt_lien_he']   ?? '');
    $email_lien_he = trim($_POST['email_lien_he'] ?? '');
    $mo_ta         = trim($_POST['mo_ta']         ?? '');

    // Validate
    if ($ten_khoa === '') {
        header("Location: ../views/khoa/create_khoa.php?error=Vui lòng nhập tên khoa / viện");
        exit();
    }

    // GỌI addKhoa với thứ tự MỚI:
    // addKhoa($ten_khoa, $truong_khoa, $sdt_lien_he, $email_lien_he, $mo_ta)
    $ok = addKhoa($ten_khoa, $truong_khoa, $sdt_lien_he, $email_lien_he, $mo_ta);

    if ($ok) {
        header("Location: ../views/khoa.php?success=Thêm khoa / viện thành công");
    } else {
        header("Location: ../views/khoa/create_khoa.php?error=Không thể thêm khoa / viện");
    }
    exit();
}

/**
 * Xử lý cập nhật khoa
 * LƯU Ý: mo_ta là tham số cuối trong updateKhoa mới
 */
function handleEditKhoa() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/khoa.php?error=Phương thức không hợp lệ");
        exit();
    }

    $id            = $_POST['id'] ?? '';
    $ten_khoa      = trim($_POST['ten_khoa']      ?? '');
    $truong_khoa   = trim($_POST['truong_khoa']   ?? '');
    $sdt_lien_he   = trim($_POST['sdt_lien_he']   ?? '');
    $email_lien_he = trim($_POST['email_lien_he'] ?? '');
    $mo_ta         = trim($_POST['mo_ta']         ?? '');

    if ($id === '' || $ten_khoa === '') {
        header("Location: ../views/khoa/edit_khoa.php?id=".$id."&error=Thiếu thông tin bắt buộc");
        exit();
    }

    // Kiểm tra tồn tại trước khi cập nhật
    $current = getKhoaById($id);
    if (!$current) {
        header("Location: ../views/khoa.php?error=Khoa / Viện không tồn tại");
        exit();
    }

    // GỌI updateKhoa với thứ tự MỚI:
    // updateKhoa($id, $ten_khoa, $truong_khoa, $sdt_lien_he, $email_lien_he, $mo_ta)
    $ok = updateKhoa($id, $ten_khoa, $truong_khoa, $sdt_lien_he, $email_lien_he, $mo_ta);

    if ($ok) {
        header("Location: ../views/khoa.php?success=Cập nhật khoa / viện thành công");
    } else {
        header("Location: ../views/khoa/edit_khoa.php?id=".$id."&error=Cập nhật thất bại");
    }
    exit();
}

/**
 * Xử lý xóa khoa
 * (không đổi logic vì không liên quan mo_ta)
 */
function handleDeleteKhoa() {
    if (!isset($_GET['id']) || $_GET['id'] === '') {
        header("Location: ../views/khoa.php?error=Thiếu ID khoa / viện");
        exit();
    }

    $id = $_GET['id'];

    $current = getKhoaById($id);
    if (!$current) {
        header("Location: ../views/khoa.php?error=Khoa / viện không tồn tại");
        exit();
    }

    $ok = deleteKhoa($id);

    if ($ok) {
        header("Location: ../views/khoa.php?success=Xóa khoa / viện thành công");
    } else {
        header("Location: ../views/khoa.php?error=Xóa thất bại");
    }
    exit();
}
?>
