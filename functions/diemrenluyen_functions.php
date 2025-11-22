<?php
require_once __DIR__ . '/db_connection.php';

function getAllDiemRenLuyen() {
    $conn = getDbConnection();
    $sql = "SELECT dr.id, dr.doanvien_id, dv.ma_sv, dv.ho_ten, dr.nam_hoc, dr.diem, dr.xep_loai, dr.ghi_chu
            FROM diem_renluyen dr
            LEFT JOIN doanvien dv ON dr.doanvien_id = dv.id
            ORDER BY dr.nam_hoc DESC, dv.ho_ten ASC";
    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_close($conn);
    return $list;
}

function searchDiemRenLuyen($params = []) {
    $conn = getDbConnection();
    $q = trim($params['q'] ?? '');
    $nam = trim($params['nam_hoc'] ?? '');
    $sql = "SELECT dr.id, dr.doanvien_id, dv.ma_sv, dv.ho_ten, dr.nam_hoc, dr.diem, dr.xep_loai, dr.ghi_chu
            FROM diem_renluyen dr
            LEFT JOIN doanvien dv ON dr.doanvien_id = dv.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($q !== '') {
        $sql .= " AND (dv.ma_sv LIKE ? OR dv.ho_ten LIKE ?)";
        $like = "%$q%"; $types .= 'ss'; array_push($binds, $like, $like);
    }
    if ($nam !== '') { $sql .= " AND dr.nam_hoc = ?"; $types .= 's'; $binds[] = $nam; }
    $sql .= " ORDER BY dr.nam_hoc DESC, dv.ho_ten ASC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}

function getDiemRenLuyenById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM diem_renluyen WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $row;
}

function getDiemRenLuyenByDoanVienNam($doanvien_id, $nam_hoc) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM diem_renluyen WHERE doanvien_id = ? AND nam_hoc = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $doanvien_id, $nam_hoc);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $row;
}

function addDiemRenLuyen($doanvien_id, $nam_hoc, $diem, $xep_loai, $ghi_chu) {
    $conn = getDbConnection();
    $sql = "INSERT INTO diem_renluyen(doanvien_id, nam_hoc, diem, xep_loai, ghi_chu) VALUES (?,?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isdss", $doanvien_id, $nam_hoc, $diem, $xep_loai, $ghi_chu);
    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $newId;
}

function updateDiemRenLuyen($id, $doanvien_id, $nam_hoc, $diem, $xep_loai, $ghi_chu) {
    $conn = getDbConnection();
    $sql = "UPDATE diem_renluyen SET doanvien_id=?, nam_hoc=?, diem=?, xep_loai=?, ghi_chu=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isdssi", $doanvien_id, $nam_hoc, $diem, $xep_loai, $ghi_chu, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}

function deleteDiemRenLuyen($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM diem_renluyen WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}
?>
