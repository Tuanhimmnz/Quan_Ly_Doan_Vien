<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy toàn bộ đoàn viên (JOIN tên lớp) để in ra views/doanvien.php
 */
function getAllDoanVien() {
    $conn = getDbConnection();

    $sql = "SELECT dv.id,
                   dv.ma_sv,
                   dv.ho_ten,
                   dv.ngay_sinh,
                   dv.gioi_tinh,
                   dv.sdt,
                   dv.email,
                   dv.lop_id,
                   dv.chuc_vu,
                   dv.trang_thai,
                   dv.ngay_vao_doan,
                   l.ten_lop
            FROM doanvien dv
            LEFT JOIN lop l ON dv.lop_id = l.id
            ORDER BY dv.id ASC";

    $result = mysqli_query($conn, $sql);

    $list = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
    }

    mysqli_close($conn);
    return $list;
}

// [FITDNU-ADD] Tìm kiếm đoàn viên theo từ khóa và filter nâng cao
function searchDoanVienAdvanced($params = []) {
    $conn = getDbConnection();
    $kw = trim($params['q'] ?? '');
    $khoa = trim($params['khoa'] ?? '');
    $lop = trim($params['lop'] ?? '');
    $trang_thai = trim($params['trang_thai'] ?? '');
    $chuc_vu = trim($params['chuc_vu'] ?? '');

    $sql = "SELECT dv.id, dv.ma_sv, dv.ho_ten, dv.ngay_sinh, dv.gioi_tinh, dv.sdt, dv.email, dv.lop_id, dv.chuc_vu, dv.trang_thai, dv.ngay_vao_doan, l.ten_lop, k.ten_khoa
            FROM doanvien dv
            LEFT JOIN lop l ON dv.lop_id = l.id
            LEFT JOIN khoa k ON l.khoa_id = k.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($kw !== '') {
        $sql .= " AND (dv.ma_sv LIKE ? OR dv.ho_ten LIKE ? OR l.ten_lop LIKE ? OR k.ten_khoa LIKE ?)";
        $like = "%$kw%"; $types .= 'ssss'; array_push($binds, $like, $like, $like, $like);
    }
    if ($khoa !== '') { $sql .= " AND k.ten_khoa = ?"; $types .= 's'; $binds[] = $khoa; }
    if ($lop !== '') { $sql .= " AND l.ten_lop = ?"; $types .= 's'; $binds[] = $lop; }
    if ($trang_thai !== '') { $sql .= " AND dv.trang_thai = ?"; $types .= 's'; $binds[] = $trang_thai; }
    if ($chuc_vu !== '') { $sql .= " AND dv.chuc_vu = ?"; $types .= 's'; $binds[] = $chuc_vu; }
    $sql .= " ORDER BY dv.id ASC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$binds);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}

/**
 * Lấy 1 đoàn viên theo id
 */
function getDoanVienById($id) {
    $conn = getDbConnection();

    $sql = "SELECT id,
                   ma_sv,
                   ho_ten,
                   ngay_sinh,
                   gioi_tinh,
                   sdt,
                   email,
                   lop_id,
                   chuc_vu,
                   trang_thai,
                   ngay_vao_doan
            FROM doanvien
            WHERE id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = null;
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $row;
}

/**
 * Thêm đoàn viên mới
 */
function addDoanVien($ma_sv, $ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $email, $lop_id, $chuc_vu, $trang_thai, $ngay_vao_doan) {
    $conn = getDbConnection();

    $sql = "INSERT INTO doanvien
            (ma_sv, ho_ten, ngay_sinh, gioi_tinh, sdt, email, lop_id, chuc_vu, trang_thai, ngay_vao_doan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "ssssssisss",
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

    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $newId;
}

/**
 * Cập nhật đoàn viên
 */
function updateDoanVien($id, $ma_sv, $ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $email, $lop_id, $chuc_vu, $trang_thai, $ngay_vao_doan) {
    $conn = getDbConnection();

    $sql = "UPDATE doanvien
            SET ma_sv = ?,
                ho_ten = ?,
                ngay_sinh = ?,
                gioi_tinh = ?,
                sdt = ?,
                email = ?,
                lop_id = ?,
                chuc_vu = ?,
                trang_thai = ?,
                ngay_vao_doan = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "ssssssisssi",
        $ma_sv,
        $ho_ten,
        $ngay_sinh,
        $gioi_tinh,
        $sdt,
        $email,
        $lop_id,
        $chuc_vu,
        $trang_thai,
        $ngay_vao_doan,
        $id
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Xóa đoàn viên
 */
function deleteDoanVien($id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM doanvien WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

function getDoanVienByMaSv($ma_sv) {
    $conn = getDbConnection();

    $sql = "SELECT * FROM doanvien WHERE ma_sv = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, 's', $ma_sv);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $row;
}

/**
 * Lấy toàn bộ đoàn viên để dropdown (ví dụ trong đoàn phí)
 */
function getAllDoanVienForDropdown() {
    $conn = getDbConnection();

    $sql = "SELECT id, ma_sv, ho_ten
            FROM doanvien
            ORDER BY ho_ten ASC";

    $result = mysqli_query($conn, $sql);

    $list = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
    }

    mysqli_close($conn);
    return $list;
}
?>
