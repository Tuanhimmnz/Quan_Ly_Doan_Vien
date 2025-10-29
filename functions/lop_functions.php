<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy danh sách lớp cho dropdown (id, ma_lop, ten_lop)
 */
function getAllLopForDropdown() {
    $conn = getDbConnection();

    $sql = "SELECT id, ma_lop, ten_lop
            FROM lop
            ORDER BY ten_lop ASC";

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
 * Lấy tất cả lớp để in ra trang lop.php
 * JOIN khoa để có tên khoa
 */
function getAllLop() {
    $conn = getDbConnection();

    $sql = "SELECT l.id,
                   l.ma_lop,
                   l.ten_lop,
                   l.khoa_id,
                   l.co_van,
                   l.bi_thu,
                   k.ten_khoa
            FROM lop l
            LEFT JOIN khoa k ON l.khoa_id = k.id
            ORDER BY l.id ASC";

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
 * Lấy thông tin 1 lớp theo ID
 */
function getLopById($id) {
    $conn = getDbConnection();

    $sql = "SELECT id, ma_lop, ten_lop, khoa_id, co_van, bi_thu
            FROM lop
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
 * Thêm lớp mới
 */
function addLop($ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu) {
    $conn = getDbConnection();

    $sql = "INSERT INTO lop (ma_lop, ten_lop, khoa_id, co_van, bi_thu)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssiss",
        $ma_lop,
        $ten_lop,
        $khoa_id,
        $co_van,
        $bi_thu
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Cập nhật lớp
 */
function updateLop($id, $ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu) {
    $conn = getDbConnection();

    $sql = "UPDATE lop
            SET ma_lop = ?,
                ten_lop = ?,
                khoa_id = ?,
                co_van = ?,
                bi_thu = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssissi",
        $ma_lop,
        $ten_lop,
        $khoa_id,
        $co_van,
        $bi_thu,
        $id
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Xóa lớp
 */
function deleteLop($id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM lop WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Lấy danh sách khoa để đổ vào dropdown khi tạo/sửa lớp
 */
function getAllKhoaForDropdown() {
    $conn = getDbConnection();

    $sql = "SELECT id, ten_khoa
            FROM khoa
            ORDER BY ten_khoa ASC";

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
