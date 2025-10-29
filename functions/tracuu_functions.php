<?php
require_once __DIR__ . '/db_connection.php';

function searchDoanVien($keyword) {
    $conn = getDbConnection();
    $kw = "%$keyword%";
    $sql = "SELECT dv.id, dv.ma_sv, dv.ho_ten, dv.gioi_tinh, dv.email, dv.sdt, l.ten_lop, k.ten_khoa
            FROM doanvien dv
            LEFT JOIN lop l ON dv.lop_id = l.id
            LEFT JOIN khoa k ON l.khoa_id = k.id
            WHERE dv.ma_sv LIKE ? OR dv.ho_ten LIKE ? OR l.ten_lop LIKE ? OR k.ten_khoa LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $kw, $kw, $kw, $kw);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $list;
}
?>
