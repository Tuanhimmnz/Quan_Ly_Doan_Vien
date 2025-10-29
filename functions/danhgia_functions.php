<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy tất cả danh sách đánh giá đoàn viên
 */
function getAllDanhGia() {
    $conn = getDbConnection();
    $sql = "SELECT dg.id,
                   dg.doanvien_id,
                   dv.ma_sv,
                   dv.ho_ten,
                   dg.nam_hoc,
                   dg.xep_loai,
                   dg.khen_thuong,
                   dg.ky_luat
            FROM danhgia dg
            LEFT JOIN doanvien dv ON dg.doanvien_id = dv.id
            ORDER BY dg.nam_hoc DESC, dv.ho_ten ASC";

    $res = mysqli_query($conn, $sql);

    $list = [];
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $list[] = $row;
        }
    }

    mysqli_close($conn);
    return $list;
}

/**
 * Lấy chi tiết 1 đánh giá theo ID
 */
function getDanhGiaById($id) {
    $conn = getDbConnection();

    $sql = "SELECT *
            FROM danhgia
            WHERE id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare getDanhGiaById(): " . mysqli_error($conn));
    }

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
 * Thêm đánh giá đoàn viên
 */
function addDanhGia($doanvien_id, $nam_hoc, $xep_loai, $khen_thuong, $ky_luat, $ghi_chu = null) {
    $conn = getDbConnection();

    $sql = "INSERT INTO danhgia
            (doanvien_id, nam_hoc, xep_loai, khen_thuong, ky_luat, ghi_chu)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare addDanhGia(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "isssss",
        $doanvien_id,
        $nam_hoc,
        $xep_loai,
        $khen_thuong,
        $ky_luat,
        $ghi_chu
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Cập nhật đánh giá đoàn viên
 */
function updateDanhGia($id, $doanvien_id, $nam_hoc, $xep_loai, $khen_thuong, $ky_luat, $ghi_chu = null) {
    $conn = getDbConnection();

    $sql = "UPDATE danhgia
            SET doanvien_id = ?,
                nam_hoc = ?,
                xep_loai = ?,
                khen_thuong = ?,
                ky_luat = ?,
                ghi_chu = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare updateDanhGia(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "isssssi",
        $doanvien_id,
        $nam_hoc,
        $xep_loai,
        $khen_thuong,
        $ky_luat,
        $ghi_chu,
        $id
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Xóa đánh giá đoàn viên
 */
function deleteDanhGia($id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM danhgia WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare deleteDanhGia(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}
?>
