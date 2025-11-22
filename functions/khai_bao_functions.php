<?php
// functions/khai_bao_functions.php
require_once __DIR__ . '/db_connection.php';

/**
 * Tạo khai báo mới của 1 đoàn viên (status = pending)
 * $data = ['ho_ten','ma_sv','lop_id','sdt','email','dia_chi','ngay_sinh','gioi_tinh','thong_tin_khac']
 */
function kb_create($doanvien_id, $data) {
    $conn = getDbConnection();
    $sql = "INSERT INTO khai_bao
            (doanvien_id, ho_ten, ma_sv, lop_id, sdt, email, dia_chi, ngay_sinh, gioi_tinh, thong_tin_khac, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,'pending')";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { die("Lỗi prepare kb_create(): " . mysqli_error($conn)); }

    $ho_ten = $data['ho_ten'] ?? null;
    $ma_sv  = $data['ma_sv'] ?? null;
    $lop_id = $data['lop_id'] ?? null;
    $sdt    = $data['sdt'] ?? null;
    $email  = $data['email'] ?? null;
    $dia_chi= $data['dia_chi'] ?? null;
    $ngay_sinh = ($data['ngay_sinh'] ?? '') === '' ? null : $data['ngay_sinh'];
    $gioi_tinh = $data['gioi_tinh'] ?? null;
    $thong_tin_khac = $data['thong_tin_khac'] ?? null;

    mysqli_stmt_bind_param($stmt, "ississssss",
        $doanvien_id, $ho_ten, $ma_sv, $lop_id, $sdt, $email, $dia_chi, $ngay_sinh, $gioi_tinh, $thong_tin_khac
    );
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}

/** Lấy 1 khai báo theo id */
function kb_get($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM khai_bao WHERE id=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { die("Lỗi prepare kb_get(): " . mysqli_error($conn)); }
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = null;
    if ($res && mysqli_num_rows($res) > 0) { $row = mysqli_fetch_assoc($res); }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row;
}

/** Lấy danh sách khai báo theo trạng thái (pending|approved|rejected) */
function kb_list($status='pending') {
    $conn = getDbConnection();
    $sql = "SELECT kb.*, dv.ho_ten AS dv_ho_ten, dv.ma_sv AS dv_ma_sv
            FROM khai_bao kb
            LEFT JOIN doanvien dv ON dv.id = kb.doanvien_id
            WHERE kb.status = ?
            ORDER BY kb.submitted_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { die("Lỗi prepare kb_list(): " . mysqli_error($conn)); }
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    if ($res && mysqli_num_rows($res) > 0) {
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $rows;
}

/** Cập nhật trạng thái duyệt */
function kb_set_status($id, $status, $admin_id) {
    $conn = getDbConnection();
    $sql = "UPDATE khai_bao SET status=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { die("Lỗi prepare kb_set_status(): " . mysqli_error($conn)); }
    mysqli_stmt_bind_param($stmt, "sii", $status, $admin_id, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}

/** Áp dữ liệu khai báo vào bảng doanvien (sau khi duyệt) */
function kb_apply_to_doanvien($kb_row) {
    if (!$kb_row) return false;
    $conn = getDbConnection();

    // Chỉ cập nhật các trường có giá trị, theo whitelist cột an toàn
    $map = [
        'ho_ten' => 'ho_ten',
        'ma_sv'  => 'ma_sv',
        'lop_id' => 'lop_id',
        'sdt'    => 'sdt',
        'email'  => 'email',
        'dia_chi'=> 'dia_chi',
        'ngay_sinh' => 'ngay_sinh',
        'gioi_tinh' => 'gioi_tinh',
    ];
    $sets = [];
    $vals = [];
    foreach ($map as $kb_col => $dv_col) {
        if (isset($kb_row[$kb_col]) && $kb_row[$kb_col] !== '' && $kb_row[$kb_col] !== null) {
            $sets[] = "$dv_col = ?";
            $vals[] = $kb_row[$kb_col];
        }
    }
    if (empty($sets)) { mysqli_close($conn); return true; }

    $sql = "UPDATE doanvien SET ".implode(", ", $sets)." WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { die("Lỗi prepare kb_apply_to_doanvien(): " . mysqli_error($conn)); }

    // bind dynamic types (all as string/int safe)
    $types = "";
    foreach ($vals as $v) {
        if (is_int($v)) { $types .= "i"; } else { $types .= "s"; }
    }
    $types .= "i"; // for id
    $vals[] = (int)$kb_row['doanvien_id'];

    mysqli_stmt_bind_param($stmt, $types, ...$vals);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}
