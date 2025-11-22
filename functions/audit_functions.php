<?php
// [FITDNU-ADD] Audit log helper
require_once __DIR__ . '/db_connection.php';

function log_action($user_id, $action, $entity, $entity_id, $beforeArr = null, $afterArr = null) {
    $conn = getDbConnection();
    $sql = "INSERT INTO audit_logs (user_id, action, entity, entity_id, before_json, after_json)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    $before_json = $beforeArr !== null ? json_encode($beforeArr, JSON_UNESCAPED_UNICODE) : null;
    $after_json  = $afterArr  !== null ? json_encode($afterArr,  JSON_UNESCAPED_UNICODE) : null;
    mysqli_stmt_bind_param($stmt, 'ississ', $user_id, $action, $entity, $entity_id, $before_json, $after_json);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}

// [FITDNU-ADD] Query audit logs with basic filters
function list_audit_logs($filters = []) {
    $conn = getDbConnection();
    $sql = "SELECT a.id, a.user_id, u.username, a.action, a.entity, a.entity_id, a.before_json, a.after_json, a.created_at
            FROM audit_logs a
            LEFT JOIN users u ON u.id = a.user_id";
    $joinDv = false;
    $types = '';
    $binds = [];
    $where = ' WHERE 1=1';
    if (!empty($filters['user_id'])) { $where .= " AND a.user_id = ?"; $types .= 'i'; $binds[] = (int)$filters['user_id']; }
    if (!empty($filters['entity'])) { $where .= " AND a.entity = ?"; $types .= 's'; $binds[] = $filters['entity']; }
    if (!empty($filters['action'])) { $where .= " AND a.action = ?"; $types .= 's'; $binds[] = $filters['action']; }
    if (!empty($filters['from'])) { $where .= " AND a.created_at >= ?"; $types .= 's'; $binds[] = $filters['from']; }
    if (!empty($filters['to'])) { $where .= " AND a.created_at <= ?"; $types .= 's'; $binds[] = $filters['to']; }
    if (!empty($filters['ma_sv'])) {
        // [FITDNU-ADD] Filter by ma_sv for doanvien changes
        $sql .= " LEFT JOIN doanvien dv ON (a.entity = 'doanvien' AND dv.id = a.entity_id)";
        $joinDv = true;
        $where .= " AND dv.ma_sv LIKE ?";
        $types .= 's';
        $kw = '%' . $filters['ma_sv'] . '%';
        $binds[] = $kw;
    }
    $sql .= $where;
    $sql .= " ORDER BY a.created_at DESC LIMIT 500";

    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}
?>
