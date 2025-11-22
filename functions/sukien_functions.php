<?php
require_once __DIR__ . '/db_connection.php';

function getAllSuKien() {
    $conn = getDbConnection();
    $sql = "SELECT id, ten_su_kien, mo_ta, ngay_to_chuc, cap_to_chuc, trang_thai
            FROM su_kien
            ORDER BY ngay_to_chuc DESC, id DESC";
    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_close($conn);
    return $list;
}

function searchSuKien($params = []) {
    $conn = getDbConnection();
    $q = trim($params['q'] ?? '');
    $trang_thai = trim($params['trang_thai'] ?? '');
    $sql = "SELECT id, ten_su_kien, mo_ta, ngay_to_chuc, cap_to_chuc, trang_thai
            FROM su_kien
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($q !== '') {
        $sql .= " AND (ten_su_kien LIKE ? OR cap_to_chuc LIKE ?)";
        $like = "%$q%"; $types .= 'ss'; array_push($binds, $like, $like);
    }
    if ($trang_thai !== '') { $sql .= " AND trang_thai = ?"; $types .= 's'; $binds[] = $trang_thai; }
    $sql .= " ORDER BY ngay_to_chuc DESC, id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}

function getSuKienById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM su_kien WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $row;
}

function addSuKien($ten_su_kien, $mo_ta, $ngay_to_chuc, $cap_to_chuc, $trang_thai) {
    $conn = getDbConnection();
    $sql = "INSERT INTO su_kien(ten_su_kien, mo_ta, ngay_to_chuc, cap_to_chuc, trang_thai) VALUES (?,?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $ten_su_kien, $mo_ta, $ngay_to_chuc, $cap_to_chuc, $trang_thai);
    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $newId;
}

function updateSuKien($id, $ten_su_kien, $mo_ta, $ngay_to_chuc, $cap_to_chuc, $trang_thai) {
    $conn = getDbConnection();
    $sql = "UPDATE su_kien SET ten_su_kien=?, mo_ta=?, ngay_to_chuc=?, cap_to_chuc=?, trang_thai=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $ten_su_kien, $mo_ta, $ngay_to_chuc, $cap_to_chuc, $trang_thai, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}

function deleteSuKien($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM su_kien WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}
?>
