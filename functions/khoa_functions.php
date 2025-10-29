<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy tất cả khoa / viện để hiển thị ở trang danh sách
 */
function getAllKhoa() {
    $conn = getDbConnection();

    $sql = "SELECT id,
                   ten_khoa,
                   mo_ta,
                   truong_khoa,
                   sdt_lien_he,
                   email_lien_he
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
 */
function getKhoaById($id) {
    $conn = getDbConnection();

    $sql = "SELECT id,
                   ten_khoa,
                   mo_ta,
                   truong_khoa,
                   sdt_lien_he,
                   email_lien_he
            FROM khoa
            WHERE id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // Chuẩn đoán lỗi query / lỗi cột / lỗi bảng
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
 * @return bool true nếu thành công, false nếu thất bại
 */
function addKhoa($ten_khoa, $mo_ta, $truong_khoa, $sdt_lien_he, $email_lien_he) {
    $conn = getDbConnection();

    $sql = "INSERT INTO khoa (
                ten_khoa,
                mo_ta,
                truong_khoa,
                sdt_lien_he,
                email_lien_he
            )
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // Nếu tới đây bị die nghĩa là bảng/column chưa khớp với DB
        die("Lỗi prepare addKhoa(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $ten_khoa,
        $mo_ta,
        $truong_khoa,
        $sdt_lien_he,
        $email_lien_he
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Cập nhật thông tin khoa
 * @return bool true nếu thành công
 */
function updateKhoa($id, $ten_khoa, $mo_ta, $truong_khoa, $sdt_lien_he, $email_lien_he) {
    $conn = getDbConnection();

    $sql = "UPDATE khoa
            SET ten_khoa      = ?,
                mo_ta         = ?,
                truong_khoa   = ?,
                sdt_lien_he   = ?,
                email_lien_he = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare updateKhoa(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssssi",
        $ten_khoa,
        $mo_ta,
        $truong_khoa,
        $sdt_lien_he,
        $email_lien_he,
        $id
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Xóa một khoa / viện
 * @return bool true nếu thành công
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
