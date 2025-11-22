<?php
require_once __DIR__ . '/db_connection.php';

function getAllLichSuThamGia() {
    $conn = getDbConnection();
    $sql = "SELECT ls.id, ls.doanvien_id, dv.ma_sv, dv.ho_ten, ls.ngay_bat_dau, ls.ngay_ket_thuc, ls.trang_thai, ls.ghi_chu
            FROM lichsu_thamgia ls
            LEFT JOIN doanvien dv ON ls.doanvien_id = dv.id
            ORDER BY ls.ngay_bat_dau DESC, ls.id DESC";
    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_close($conn);
    return $list;
}

function searchLichSuThamGia($params = []) {
    $conn = getDbConnection();
    $q = trim($params['q'] ?? '');
    $trang_thai = trim($params['trang_thai'] ?? '');
    $sql = "SELECT ls.id, ls.doanvien_id, dv.ma_sv, dv.ho_ten, ls.ngay_bat_dau, ls.ngay_ket_thuc, ls.trang_thai, ls.ghi_chu
            FROM lichsu_thamgia ls
            LEFT JOIN doanvien dv ON ls.doanvien_id = dv.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($q !== '') {
        $sql .= " AND (dv.ma_sv LIKE ? OR dv.ho_ten LIKE ?)";
        $like = "%$q%"; $types .= 'ss'; array_push($binds, $like, $like);
    }
    if ($trang_thai !== '') { $sql .= " AND ls.trang_thai = ?"; $types .= 's'; $binds[] = $trang_thai; }
    $sql .= " ORDER BY ls.ngay_bat_dau DESC, ls.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}

function getLichSuById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM lichsu_thamgia WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $row;
}

function addLichSu($doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu) {
    $conn = getDbConnection();
    $sql = "INSERT INTO lichsu_thamgia(doanvien_id, ngay_bat_dau, ngay_ket_thuc, trang_thai, ghi_chu) VALUES (?,?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issss", $doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu);
    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $newId;
}

function updateLichSu($id, $doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu) {
    $conn = getDbConnection();
    $sql = "UPDATE lichsu_thamgia SET doanvien_id=?, ngay_bat_dau=?, ngay_ket_thuc=?, trang_thai=?, ghi_chu=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssi", $doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}

function deleteLichSu($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM lichsu_thamgia WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}

/**
 * Thêm bản ghi lịch sử nếu chưa tồn tại cùng doanvien_id + trạng thái + ngày bắt đầu.
 * Cho phép truyền sẵn kết nối để tái sử dụng trong các luồng import.
 */
function addLichSuIfNotExists($doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu = '', $conn = null) {
    $ownConn = false;
    if ($conn === null) {
        $conn = getDbConnection();
        $ownConn = true;
    }

    $sqlCheck = "SELECT id FROM lichsu_thamgia WHERE doanvien_id=? AND trang_thai=? AND ngay_bat_dau=? LIMIT 1";
    $stmtCheck = mysqli_prepare($conn, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, 'iss', $doanvien_id, $trang_thai, $ngay_bat_dau);
    mysqli_stmt_execute($stmtCheck);
    $res = mysqli_stmt_get_result($stmtCheck);
    $exists = $res && mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmtCheck);
    if ($exists) {
        if ($ownConn) mysqli_close($conn);
        return (int)$exists['id'];
    }

    $sqlInsert = "INSERT INTO lichsu_thamgia(doanvien_id, ngay_bat_dau, ngay_ket_thuc, trang_thai, ghi_chu) VALUES (?,?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sqlInsert);
    mysqli_stmt_bind_param($stmt, 'issss', $doanvien_id, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai, $ghi_chu);
    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt);
    if ($ownConn) mysqli_close($conn);
    return $newId;
}
?>
