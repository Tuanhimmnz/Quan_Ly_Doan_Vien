<?php
// [FITDNU-ADD] Protect and enable audit logging
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/doanvien_functions.php';
require_once __DIR__ . '/../functions/lop_functions.php';
require_once __DIR__ . '/../functions/lichsu_functions.php';
checkLogin(__DIR__ . '/../index.php');

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
    // [FITDNU-ADD] Support unified search via GET params
    $hasFilters = isset($_GET['q']) || isset($_GET['khoa']) || isset($_GET['lop']) || isset($_GET['trang_thai']) || isset($_GET['chuc_vu']);
    if ($hasFilters) {
        $params = [
            'q' => $_GET['q'] ?? '',
            'khoa' => $_GET['khoa'] ?? '',
            'lop' => $_GET['lop'] ?? '',
            'trang_thai' => $_GET['trang_thai'] ?? '',
            'chuc_vu' => $_GET['chuc_vu'] ?? ''
        ];
        return searchDoanVienAdvanced($params);
    }
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

    $newId = addDoanVien(
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

    if ($newId) {
        // [FITDNU-ADD] Audit create with new entity id
        $user = getCurrentUser();
        $new = getDoanVienByMaSv($ma_sv);
        $lopTen = '';
        if ($new && isset($new['lop_id'])) {
            $lop = getLopById((int)$new['lop_id']);
            $lopTen = $lop['ten_lop'] ?? '';
        }
        // Ghi lịch sử tham gia
        $startDate = $ngay_vao_doan ?: date('Y-m-d');
        addLichSuIfNotExists((int)$newId, $startDate, null, 'Tạo thủ công', 'Tạo đoàn viên mới', null);
        log_action((int)$user['id'], 'CREATE', 'doanvien', (int)($new['id'] ?? 0), null, [
            'ma_sv' => $ma_sv,
            'ho_ten' => $ho_ten,
            'lop_id' => (int)$lop_id,
            'lop_ten' => $lopTen,
            'trang_thai' => $trang_thai
        ]);
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
        // [FITDNU-ADD] Audit update with before/after, include lớp cũ/mới
        $user = getCurrentUser();
        $before = $current;
        $after = getDoanVienById($id);
        $lopBefore = $before && isset($before['lop_id']) ? (getLopById((int)$before['lop_id'])['ten_lop'] ?? '') : '';
        $lopAfter  = $after && isset($after['lop_id'])  ? (getLopById((int)$after['lop_id'])['ten_lop'] ?? '')   : '';
        $before['lop_ten'] = $lopBefore;
        $after['lop_ten']  = $lopAfter;
        log_action((int)$user['id'], 'UPDATE', 'doanvien', (int)$id, $before, $after);
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
        // [FITDNU-ADD] Audit delete with before snapshot
        $user = getCurrentUser();
        $lopTen = $current && isset($current['lop_id']) ? (getLopById((int)$current['lop_id'])['ten_lop'] ?? '') : '';
        $current['lop_ten'] = $lopTen;
        log_action((int)$user['id'], 'DELETE', 'doanvien', (int)$id, $current, null);
        header("Location: ../views/doanvien.php?success=Xóa đoàn viên thành công");
    } else {
        header("Location: ../views/doanvien.php?error=Xóa đoàn viên thất bại");
    }
    exit();
}
?>
