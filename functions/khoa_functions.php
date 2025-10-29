<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy tất cả khoa / viện để hiển thị ở trang danh sách
 * mo_ta được chọn sau cùng
 */
function getAllKhoa() {
    $conn = getDbConnection();

    $sql = "SELECT id,
                   ten_khoa,
                   truong_khoa,
                   sdt_lien_he,
                   email_lien_he,
                   mo_ta
            FROM khoa
            ORDER BY id ASC";

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
 * Lấy thông tin 1 khoa theo ID (dùng cho trang edit)
 * mo_ta nằm cuối
 */
function getKhoaById($id) {
    $conn = getDbConnection();

    $sql = "SELECT id,
                   ten_khoa,
                   truong_khoa,
                   sdt_lien_he,
                   email_lien_he,
                   mo_ta
            FROM khoa
            WHERE id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare getKhoaById(): " . mysqli_error($conn));
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
 * Thêm khoa / viện mới
 * mo_ta bind cuối cùng
 * @return bool true nếu thành công, false nếu thất bại
 */
function addKhoa($ten_khoa, $truong_khoa, $sdt_lien_he, $email_lien_he, $mo_ta) {
    $conn = getDbConnection();

    $sql = "INSERT INTO khoa (
                ten_khoa,
                truong_khoa,
                sdt_lien_he,
                email_lien_he,
                mo_ta
            )
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare addKhoa(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $ten_khoa,
        $truong_khoa,
        $sdt_lien_he,
        $email_lien_he,
        $mo_ta
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Cập nhật thông tin khoa
 * mo_ta nằm cuối cùng
 * @return bool true nếu thành công
 */
function updateKhoa($id, $ten_khoa, $truong_khoa, $sdt_lien_he, $email_lien_he, $mo_ta) {
    $conn = getDbConnection();

    $sql = "UPDATE khoa
            SET ten_khoa      = ?,
                truong_khoa   = ?,
                sdt_lien_he   = ?,
                email_lien_he = ?,
                mo_ta         = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare updateKhoa(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssssi",
        $ten_khoa,
        $truong_khoa,
        $sdt_lien_he,
        $email_lien_he,
        $mo_ta,
        $id
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Xóa một khoa / viện
 */
function deleteKhoa($id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM khoa WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare deleteKhoa(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}
?>
