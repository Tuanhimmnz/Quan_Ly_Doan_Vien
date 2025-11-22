<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/lop_functions.php';
ensureSessionStarted();
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
$userId = (int)($currentUser['id'] ?? 0);

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateLop();
        break;
    case 'edit':
        handleEditLop();
        break;
    case 'delete':
        handleDeleteLop();
        break;
    // default:
    //     header("Location: ../views/lop.php?error=Hành động không hợp lệ");
    //     exit();
}

/**
 * Lấy tất cả danh sách lớp
 */
function handleGetAllLop() {
    // [FITDNU-ADD] support search via GET
    $has = isset($_GET['q']) || isset($_GET['khoa']);
    if ($has) {
        $params = [
            'q' => $_GET['q'] ?? '',
            'khoa' => $_GET['khoa'] ?? ''
        ];
        return searchLop($params);
    }
    return getAllLop();
}

function handleGetLopById($id) {
    return getLopById($id);
}

/**
 * Xử lý tạo lớp mới
 */
function handleCreateLop() {
    global $userId;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/lop.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (
        !isset($_POST['ma_lop']) ||
        !isset($_POST['ten_lop']) ||
        !isset($_POST['khoa_id'])
    ) {
        header("Location: ../views/lop/create_lop.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $ma_lop = trim($_POST['ma_lop']);
    $ten_lop = trim($_POST['ten_lop']);
    $khoa_id = trim($_POST['khoa_id']);

    $co_van = $_POST['co_van'] ?? '';
    $bi_thu = $_POST['bi_thu'] ?? '';

    // Validate dữ liệu bắt buộc
    if (empty($ma_lop) || empty($ten_lop) || empty($khoa_id)) {
        header("Location: ../views/lop/create_lop.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Nếu bạn có check trùng mã lớp thì bật
    /*
    if (checkLopExists($ma_lop)) {
        header("Location: ../views/lop/create_lop.php?error=Lớp này đã tồn tại");
        exit();
    }
    */

    $newId = createLop($ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu);

    if ($newId) {
        log_action($userId, 'CREATE', 'lop', (int)$newId, null, [
            'ma_lop' => $ma_lop,
            'ten_lop' => $ten_lop,
            'khoa_id' => $khoa_id,
            'co_van' => $co_van,
            'bi_thu' => $bi_thu
        ]);
        header("Location: ../views/lop.php?success=Thêm lớp thành công");
    } else {
        header("Location: ../views/lop/create_lop.php?error=Có lỗi xảy ra khi thêm lớp");
    }
    exit();
}

/**
 * Xử lý cập nhật lớp
 */
function handleEditLop() {
    global $userId;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/lop.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (
        !isset($_POST['id']) ||
        !isset($_POST['ma_lop']) ||
        !isset($_POST['ten_lop']) ||
        !isset($_POST['khoa_id'])
    ) {
        header("Location: ../views/lop.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $id      = (int)$_POST['id'];
    $ma_lop  = trim($_POST['ma_lop']);
    $ten_lop = trim($_POST['ten_lop']);
    $khoa_id = trim($_POST['khoa_id']);

    $co_van  = $_POST['co_van'] ?? '';
    $bi_thu  = $_POST['bi_thu'] ?? '';

    // Validate dữ liệu bắt buộc
    if (empty($ma_lop) || empty($ten_lop) || empty($khoa_id)) {
        header("Location: ../views/lop/edit_lop.php?id=$id&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Kiểm tra lớp có tồn tại không
    $current = getLopById($id);
    if (!$current) {
        header("Location: ../views/lop.php?error=Lớp không tồn tại");
        exit();
    }

    // Nếu có rule chống trùng mã lớp khi đổi mã
    /*
    if ($current['ma_lop'] !== $ma_lop && checkLopExists($ma_lop)) {
        header("Location: ../views/lop/edit_lop.php?id=$id&error=Mã lớp đã tồn tại");
        exit();
    }
    */

    $before = $current;
    $success = updateLop($id, $ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu);

    if ($success) {
        $after = getLopById($id);
        log_action($userId, 'UPDATE', 'lop', (int)$id, $before, $after);
        header("Location: ../views/lop.php?success=Cập nhật lớp thành công");
    } else {
        header("Location: ../views/lop/edit_lop.php?id=$id&error=Có lỗi xảy ra khi cập nhật lớp");
    }
    exit();
}

/**
 * Xử lý xóa lớp
 */
function handleDeleteLop() {
    global $userId;
    if (!isset($_GET['id'])) {
        header("Location: ../views/lop.php?error=Thiếu ID lớp");
        exit();
    }

    $id = (int)$_GET['id'];

    // Kiểm tra xem lớp có tồn tại không
    $info = getLopById($id);
    if (!$info) {
        header("Location: ../views/lop.php?error=Lớp không tồn tại");
        exit();
    }

    $success = deleteLop($id);

    if ($success) {
        log_action($userId, 'DELETE', 'lop', (int)$id, $info, null);
        header("Location: ../views/lop.php?success=Xóa lớp thành công");
    } else {
        header("Location: ../views/lop.php?error=Có lỗi xảy ra khi xóa lớp");
    }
    exit();
}
?>
