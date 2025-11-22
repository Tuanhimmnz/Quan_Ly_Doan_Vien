<?php
// [FITDNU-ADD] Protect direct access and enable CSRF validation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../functions/auth.php'; // [FITDNU-ADD]
require_once __DIR__ . '/../functions/security.php'; // [FITDNU-ADD]
checkLogin(__DIR__ . '/../index.php'); // [FITDNU-ADD]
require_once __DIR__ . '/../functions/doanphi_functions.php';
require_once __DIR__ . '/../functions/audit_functions.php'; // [FITDNU-ADD]


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
    // [FITDNU-ADD] Hỗ trợ tìm kiếm theo q (ma_sv/ho_ten), paid và năm_học
    $params = [
        'q' => $_GET['q'] ?? '',
        'paid' => $_GET['paid'] ?? '',
        'nam_hoc' => $_GET['nam_hoc'] ?? ''
    ];
    $has = (trim($params['q']) !== '') || ($params['paid'] === '0' || $params['paid'] === '1') || (trim($params['nam_hoc']) !== '');
    if ($has) {
        return searchDoanPhi($params);
    }
    return getAllDoanPhi();
}

// [FITDNU-ADD] Lấy danh sách năm học phục vụ dropdown ở view
function handleGetAllNamHocDoanPhi() {
    return getAllNamHocDoanPhi();
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
    // [FITDNU-ADD] CSRF check for create
    csrf_require_post('doanphi_edit', '../views/doanphi/edit_doanphi.php');

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
    $so_tien_nop = isset($_POST['so_tien_nop']) ? trim($_POST['so_tien_nop']) : '';

    // Validate dữ liệu cơ bản
    if ($doanvien_id === '' || $nam_hoc === '' || $da_nop === '') {
        header("Location: ../views/doanphi/edit_doanphi.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Chuẩn hóa/kiểm tra số tiền nộp
    if ($da_nop === '1') {
        // Nếu đã nộp, cho phép rỗng -> 0 hoặc số hợp lệ
        $so_tien_nop = ($so_tien_nop === '' ? '0' : $so_tien_nop);
    } else {
        // Chưa nộp thì đặt 0
        $so_tien_nop = '0';
        // Nếu chưa nộp thì cũng không nên có ngày nộp
        if ($ngay_nop !== '') {
            $ngay_nop = '';
        }
    }
    $so_tien_val = (float)$so_tien_nop;

    // Kiểm tra trùng (doanvien_id + nam_hoc)
    if (checkDoanPhiExists($doanvien_id, $nam_hoc)) {
        header("Location: ../views/doanphi/edit_doanphi.php?error=Đã tồn tại bản ghi đoàn phí cho năm học này");
        exit();
    }

    $newId = addDoanPhi($doanvien_id, $nam_hoc, $da_nop, $ngay_nop, $so_tien_val);

    if ($newId) {
        // [FITDNU-ADD] Audit log create
        $user = getCurrentUser();
        log_action((int)$user['id'], 'CREATE', 'doanphi', (int)$newId, null, [
            'doanvien_id' => (int)$doanvien_id,
            'nam_hoc' => $nam_hoc,
            'da_nop' => (int)$da_nop,
            'ngay_nop' => $ngay_nop,
            'so_tien_nop' => (float)$so_tien_val
        ]);
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
    // [FITDNU-ADD] CSRF check for edit
    csrf_require_post('doanphi_edit', '../views/doanphi.php');

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
    $so_tien_nop = isset($_POST['so_tien_nop']) ? trim($_POST['so_tien_nop']) : '';

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

    // Chuẩn hóa số tiền nộp/ ngày nộp theo trạng thái
    if ($da_nop === '1') {
        $so_tien_nop = ($so_tien_nop === '' ? '0' : $so_tien_nop);
    } else {
        $so_tien_nop = '0';
        if ($ngay_nop !== '') {
            $ngay_nop = '';
        }
    }

    $so_tien_val = (float)$so_tien_nop;

    $success = updateDoanPhi($id, $doanvien_id, $nam_hoc, $da_nop, $ngay_nop, $so_tien_val);

    if ($success) {
        // [FITDNU-ADD] Audit log update
        $user = getCurrentUser();
        $after = [
            'doanvien_id' => (int)$doanvien_id,
            'nam_hoc' => $nam_hoc,
            'da_nop' => (int)$da_nop,
            'ngay_nop' => $ngay_nop,
            'so_tien_nop' => (float)$so_tien_val
        ];
        log_action((int)$user['id'], 'UPDATE', 'doanphi', (int)$id, $current, $after);
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
        // [FITDNU-ADD] Audit log delete
        $user = getCurrentUser();
        log_action((int)$user['id'], 'DELETE', 'doanphi', (int)$id, $info, null);
        header("Location: ../views/doanphi.php?success=Xóa đoàn phí thành công");
    } else {
        header("Location: ../views/doanphi.php?error=Có lỗi xảy ra khi xóa đoàn phí");
    }
    exit();
}
?>
