<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/khaibao_functions.php';
require_once __DIR__ . '/../functions/doanvien_functions.php';
require_once __DIR__ . '/../functions/audit_functions.php';
require_once __DIR__ . '/../functions/lichsu_functions.php';

ensureSessionStarted();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Luồng user-declarations (đăng nhập bảng users)
if ($action === 'submit') {
    checkLogin('../index.php');
    if ($_SESSION['role'] !== 'user') {
        $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
        header('Location: ../user/index.php');
        exit();
    }

    $userId = $_SESSION['user_id'];

    $payload = [
        'ma_sv'        => trim($_POST['ma_sv'] ?? ''),
        'ho_ten'       => trim($_POST['ho_ten'] ?? ''),
        'gioi_tinh'    => trim($_POST['gioi_tinh'] ?? ''),
        'ngay_sinh'    => $_POST['ngay_sinh'] ?? null,
        'email'        => trim($_POST['email'] ?? ''),
        'sdt'          => trim($_POST['sdt'] ?? ''),
        'lop_id'       => (int)($_POST['lop_id'] ?? 0),
        'chuc_vu'      => trim($_POST['chuc_vu'] ?? ''),
        'trang_thai'   => trim($_POST['trang_thai'] ?? ''),
        'ngay_vao_doan'=> $_POST['ngay_vao_doan'] ?? null,
        'dia_chi'      => trim($_POST['dia_chi'] ?? ''),
        'so_doan_vien' => trim($_POST['so_doan_vien'] ?? ''),
        'ghi_chu'      => trim($_POST['ghi_chu'] ?? ''),
    ];

    if ($payload['ma_sv'] === '' || $payload['ho_ten'] === '' || $payload['lop_id'] === 0) {
        $_SESSION['error'] = 'Vui lòng nhập tối thiểu Mã sinh viên, Họ tên và chọn Lớp / Chi đoàn.';
        header('Location: ../user/index.php');
        exit();
    }

    if (!in_array($payload['gioi_tinh'], ['Nam', 'Nữ', 'Khác', ''])) {
        $payload['gioi_tinh'] = '';
    }

    $ok = createDeclaration($userId, $payload);

    if ($ok) {
        $_SESSION['success'] = 'Gửi khai báo thành công! Vui lòng chờ cán bộ đoàn duyệt.';
    } else {
        $_SESSION['error'] = 'Không thể lưu thông tin, vui lòng thử lại.';
    }

    header('Location: ../user/index.php');
    exit();
}

// Luồng khai_bao theo doanvien (giống admin), dùng cho views/user/index.php (legacy)
if ($action === 'create') {
    ensureSessionStarted();
    // Lấy doanvien_id từ session 'user' (luồng auth_login_dv)
    $doanvienId = (int)($_SESSION['user']['id'] ?? 0);
    if ($doanvienId <= 0) {
        $_SESSION['error'] = 'Bạn cần đăng nhập bằng tài khoản đoàn viên để khai báo.';
        header('Location: ../index.php');
        exit();
    }

    $data = [
        'ho_ten'         => trim($_POST['ho_ten'] ?? ''),
        'ma_sv'          => trim($_POST['ma_sv'] ?? ''),
        'lop_id'         => (int)($_POST['lop_id'] ?? 0),
        'sdt'            => trim($_POST['sdt'] ?? ''),
        'email'          => trim($_POST['email'] ?? ''),
        'dia_chi'        => trim($_POST['dia_chi'] ?? ''),
        'ngay_sinh'      => $_POST['ngay_sinh'] ?? null,
        'gioi_tinh'      => $_POST['gioi_tinh'] ?? '',
        'thong_tin_khac' => trim($_POST['thong_tin_khac'] ?? ($_POST['ghi_chu'] ?? '')),
    ];

    if ($data['ho_ten'] === '' || $data['lop_id'] <= 0) {
        $_SESSION['error'] = 'Vui lòng nhập Họ tên và chọn Lớp / Chi đoàn.';
        header('Location: ../views/user/index.php');
        exit();
    }

    // Chuẩn hoá giới tính theo schema khai_bao (enum: nam|nu|khac)
    $map = ['Nam' => 'nam', 'Nữ' => 'nu', 'nu' => 'nu', 'nam' => 'nam', 'Khác' => 'khac', 'khac' => 'khac'];
    $data['gioi_tinh'] = $map[$data['gioi_tinh']] ?? ($map[mb_convert_case($data['gioi_tinh'], MB_CASE_LOWER, 'UTF-8')] ?? null);

    // Ngày sinh rỗng => null
    if ($data['ngay_sinh'] === '' || $data['ngay_sinh'] === '0000-00-00') { $data['ngay_sinh'] = null; }

    $ok = kb_create($doanvienId, $data);
    if ($ok) {
        header('Location: ../views/user/index.php?success=Gửi khai báo thành công');
    } else {
        header('Location: ../views/user/index.php?error=Không thể lưu khai báo');
    }
    exit();
}

if (in_array($action, ['approve', 'reject'], true)) {
    checkLogin('../index.php');
    $currentUser = getCurrentUser();
    $adminId = (int)($currentUser['id'] ?? ($_SESSION['user_id'] ?? 0));
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['error'] = 'Chỉ cán bộ quản trị mới được duyệt khai báo.';
        header('Location: ../views/khaibao.php');
        exit();
    }

    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        $_SESSION['error'] = 'Thiếu ID khai báo.';
        header('Location: ../views/khaibao.php');
        exit();
    }

    $declaration = getDeclarationById($id);
    if (!$declaration) {
        $_SESSION['error'] = 'Không tìm thấy khai báo.';
        header('Location: ../views/khaibao.php');
        exit();
    }

    $status = $action === 'approve' ? 'approved' : 'rejected';
    $adminNote = trim($_POST['admin_note'] ?? '');

    $beforeDecl = $declaration;
    $ok = updateDeclarationStatus($id, $status, $_SESSION['user_id'], $adminNote);

    if (!$ok) {
        $_SESSION['error'] = 'Không thể cập nhật trạng thái khai báo.';
        header('Location: ../views/khaibao.php');
        exit();
    }
    $afterDecl = $beforeDecl;
    $afterDecl['status'] = $status;
    $afterDecl['admin_note'] = $adminNote;
    $afterDecl['reviewed_by'] = $adminId;
    log_action($adminId, $status === 'approved' ? 'APPROVE_DECLARATION' : 'REJECT_DECLARATION', 'declaration', $id, $beforeDecl, $afterDecl);

    if ($status === 'approved') {
        $maSv  = trim((string)($declaration['ma_sv'] ?? ''));
        $hoTen = trim((string)($declaration['ho_ten'] ?? ''));
        $lopId = (int)($declaration['lop_id'] ?? 0);

        // Nếu thiếu dữ liệu bắt buộc thì không cố gắng thêm vào doanvien
        if ($maSv === '' || $hoTen === '' || $lopId <= 0) {
            $_SESSION['warning'] = 'Đã duyệt khai báo nhưng thiếu Mã SV/Họ tên/Lớp, không thể thêm vào danh sách đoàn viên.';
        } else {
            // Không thêm nếu trùng mã SV
            $exists = getDoanVienByMaSv($maSv);
            if ($exists) {
                $_SESSION['warning'] = 'Khai báo đã được duyệt, nhưng mã sinh viên đã tồn tại trong danh sách đoàn viên.';
            } else {
                // Chuẩn hoá giới tính theo lựa chọn trong form doanvien
                $gioiTinhRaw = (string)($declaration['gioi_tinh'] ?? '');
                $gioiTinhMap = [
                    'nam' => 'Nam', 'Nam' => 'Nam',
                    'nu' => 'Nữ', 'nữ' => 'Nữ', 'Nữ' => 'Nữ',
                    'khac' => 'Khác', 'Khác' => 'Khác', 'khác' => 'Khác'
                ];
                $gioiTinh = $gioiTinhMap[$gioiTinhRaw] ?? ($gioiTinhMap[mb_strtolower($gioiTinhRaw)] ?? '');

                // Ngày sinh hợp lệ
                $ngaySinh = ($declaration['ngay_sinh'] ?? null);
                if ($ngaySinh === '0000-00-00' || $ngaySinh === '') { $ngaySinh = null; }

                $sdt   = trim((string)($declaration['sdt'] ?? ''));
                $email = trim((string)($declaration['email'] ?? ''));

                // Chuẩn hoá chức vụ và trạng thái theo danh sách hợp lệ
                $validChucVu = ['Đoàn viên','Bí thư','Phó bí thư','Ủy viên BCH'];
                $rawChucVu = (string)($declaration['chuc_vu'] ?? '');
                $chucVu = in_array($rawChucVu, $validChucVu, true) ? $rawChucVu : 'Đoàn viên';

                $validTrangThai = ['Đang sinh hoạt','Chuyển sinh hoạt','Đã ra trường'];
                $rawTrangThai = (string)($declaration['trang_thai'] ?? '');
                $trangThai = in_array($rawTrangThai, $validTrangThai, true) ? $rawTrangThai : 'Đang sinh hoạt';

                $ngayVaoDoan = ($declaration['ngay_vao_doan'] ?? null);
                if ($ngayVaoDoan === '' || $ngayVaoDoan === '0000-00-00') { $ngayVaoDoan = null; }

                $created = addDoanVien(
                    $maSv,
                    $hoTen,
                    $ngaySinh,
                    $gioiTinh,
                    $sdt,
                    $email,
                    $lopId,
                    $chucVu,
                    $trangThai,
                    $ngayVaoDoan
                );

                if ($created) {
                    addLichSuIfNotExists((int)$created, $ngayVaoDoan ?: date('Y-m-d'), null, 'Duyệt khai báo', 'Thêm từ khai báo #' . $id);
                    log_action($adminId, 'CREATE', 'doanvien', (int)$created, null, [
                        'ma_sv' => $maSv,
                        'ho_ten' => $hoTen,
                        'lop_id' => $lopId,
                        'trang_thai' => $trangThai,
                        'nguon' => 'Duyệt khai báo #' . $id
                    ]);
                    $_SESSION['success'] = 'Đã duyệt và thêm đoàn viên mới vào danh sách.';
                } else {
                    $_SESSION['warning'] = 'Đã duyệt khai báo nhưng không thể thêm vào danh sách đoàn viên. Vui lòng kiểm tra lại dữ liệu.';
                }
            }
        }
    } else {
        $_SESSION['success'] = 'Đã cập nhật trạng thái khai báo.';
    }

    header('Location: ../views/khaibao.php');
    exit();
}

header('Location: ../index.php');
exit();
