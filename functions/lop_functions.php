<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy toàn bộ danh sách lớp để hiển thị
 */
function getAllLop() {
    $conn = getDbConnection();

    $sql = "
        SELECT l.id,
               l.ma_lop,
               l.ten_lop,
               l.khoa_id,
               k.ten_khoa,
               l.co_van,
               l.bi_thu
        FROM lop l
        LEFT JOIN khoa k ON l.khoa_id = k.id
        ORDER BY l.id ASC
    ";

    $result = mysqli_query($conn, $sql);

    $rows = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
    }

    mysqli_close($conn);
    return $rows;
}

/**
 * Lấy thông tin 1 lớp theo id
 */
function getLopById($id) {
    $conn = getDbConnection();
    $id = (int)$id;

    $sql = "
        SELECT l.id,
               l.ma_lop,
               l.ten_lop,
               l.khoa_id,
               l.co_van,
               l.bi_thu
        FROM lop l
        WHERE l.id = $id
        LIMIT 1
    ";

    $result = mysqli_query($conn, $sql);

    $lop = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $lop = mysqli_fetch_assoc($result);
    }

    mysqli_close($conn);
    return $lop;
}

/**
 * Thêm lớp mới
 */
function createLop($ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu) {
    $conn = getDbConnection();

    $ma_lop  = mysqli_real_escape_string($conn, $ma_lop);
    $ten_lop = mysqli_real_escape_string($conn, $ten_lop);
    $co_van  = mysqli_real_escape_string($conn, $co_van);
    $bi_thu  = mysqli_real_escape_string($conn, $bi_thu);
    $khoa_id = (int)$khoa_id;

    $sql = "
        INSERT INTO lop (ma_lop, ten_lop, khoa_id, co_van, bi_thu)
        VALUES ('$ma_lop', '$ten_lop', $khoa_id, '$co_van', '$bi_thu')
    ";

    $ok = mysqli_query($conn, $sql);

    $newId = null;
    if ($ok) {
        $newId = mysqli_insert_id($conn);
    }

    mysqli_close($conn);
    return $newId;
}

/**
 * Cập nhật lớp
 */
function updateLop($id, $ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu) {
    $conn = getDbConnection();

    $id      = (int)$id;
    $khoa_id = (int)$khoa_id;

    $ma_lop  = mysqli_real_escape_string($conn, $ma_lop);
    $ten_lop = mysqli_real_escape_string($conn, $ten_lop);
    $co_van  = mysqli_real_escape_string($conn, $co_van);
    $bi_thu  = mysqli_real_escape_string($conn, $bi_thu);

    $sql = "
        UPDATE lop
        SET ma_lop  = '$ma_lop',
            ten_lop = '$ten_lop',
            khoa_id = $khoa_id,
            co_van  = '$co_van',
            bi_thu  = '$bi_thu'
        WHERE id = $id
        LIMIT 1
    ";

    $ok = mysqli_query($conn, $sql);

    mysqli_close($conn);
    return $ok;
}

/**
 * Xoá lớp theo id
 */
function deleteLop($id) {
    $conn = getDbConnection();
    $id = (int)$id;

    $sql = "DELETE FROM lop WHERE id = $id LIMIT 1";
    $ok = mysqli_query($conn, $sql);

    mysqli_close($conn);
    return $ok;
}

/**
 * Lấy danh sách khoa để đổ vào <select>
 */
function getAllKhoaForDropdown() {
    $conn = getDbConnection();

    $sql = "
        SELECT id, ten_khoa
        FROM khoa
        ORDER BY ten_khoa ASC
    ";

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
function getAllLopForDropdown() {
    $conn = getDbConnection();

    $sql = "
        SELECT id, ma_lop, ten_lop
        FROM lop
        ORDER BY ma_lop ASC, ten_lop ASC
    ";

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