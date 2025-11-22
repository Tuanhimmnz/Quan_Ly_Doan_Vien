<?php
require_once __DIR__ . '/db_connection.php';

function getAllTaiKhoan() {
    $conn = getDbConnection();
    $sql = "SELECT id, ho_ten, username, role, trang_thai
            FROM users
            ORDER BY id ASC";
    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_close($conn);
    return $list;
}

function searchTaiKhoan($params = []) {
    $conn = getDbConnection();
    $q = trim($params['q'] ?? '');
    $role = trim($params['role'] ?? '');
    $trang_thai = trim($params['trang_thai'] ?? '');
    $sql = "SELECT id, ho_ten, username, role, trang_thai
            FROM users
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($q !== '') {
        $sql .= " AND (username LIKE ? OR ho_ten LIKE ?)";
        $like = "%$q%"; $types .= 'ss'; array_push($binds, $like, $like);
    }
    if ($role !== '') { $sql .= " AND role = ?"; $types .= 's'; $binds[] = $role; }
    if ($trang_thai !== '') { $sql .= " AND trang_thai = ?"; $types .= 's'; $binds[] = $trang_thai; }
    $sql .= " ORDER BY id ASC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}

function getTaiKhoanById($id) {
    $conn = getDbConnection();
    $sql = "SELECT id, ho_ten, username, password, role, trang_thai FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $row;
}

function addTaiKhoan($ho_ten, $username, $password, $role, $trang_thai) {
    $conn = getDbConnection();
    $sql = "INSERT INTO users(ho_ten, username, password, role, trang_thai) VALUES (?,?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $ho_ten, $username, $password, $role, $trang_thai);
    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $newId;
}

function updateTaiKhoan($id, $ho_ten, $username, $password, $role, $trang_thai) {
    $conn = getDbConnection();
    $sql = "UPDATE users SET ho_ten=?, username=?, password=?, role=?, trang_thai=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $ho_ten, $username, $password, $role, $trang_thai, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}

function deleteTaiKhoan($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}
?>
