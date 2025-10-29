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

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
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
