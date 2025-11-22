<?php
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/doanvien_functions.php';

function drl_request_create($user_id, $payload) {
    $conn = getDbConnection();
    $sql = "INSERT INTO diemrenluyen_requests(user_id, doanvien_id, ma_sv, ho_ten, nam_hoc, diem, xep_loai, ghi_chu, status)
            VALUES(?,?,?,?,?,?,?,?, 'pending')";
    $stmt = mysqli_prepare($conn, $sql);
    $doanvien_id = $payload['doanvien_id'] ?? null;
    mysqli_stmt_bind_param(
        $stmt,
        'iisssdss',
        $user_id,
        $doanvien_id,
        $payload['ma_sv'],
        $payload['ho_ten'],
        $payload['nam_hoc'],
        $payload['diem'],
        $payload['xep_loai'],
        $payload['ghi_chu']
    );
    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $newId;
}

function drl_requests_list($filters = []) {
    $conn = getDbConnection();
    $sql = "SELECT r.*, u.username AS user_name, reviewer.username AS reviewer_name
            FROM diemrenluyen_requests r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN users reviewer ON r.reviewed_by = reviewer.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if (!empty($filters['status'])) { $sql .= " AND r.status = ?"; $types .= 's'; $binds[] = $filters['status']; }
    if (!empty($filters['user_id'])) { $sql .= " AND r.user_id = ?"; $types .= 'i'; $binds[] = (int)$filters['user_id']; }
    if (!empty($filters['q'])) {
        $like = '%' . $filters['q'] . '%';
        $sql .= " AND (r.ma_sv LIKE ? OR r.ho_ten LIKE ?)";
        $types .= 'ss'; array_push($binds, $like, $like);
    }
    $sql .= " ORDER BY r.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}

function drl_request_get($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM diemrenluyen_requests WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $row;
}

function drl_request_update_status($id, $status, $reviewed_by, $review_note = null) {
    $conn = getDbConnection();
    $sql = "UPDATE diemrenluyen_requests
            SET status = ?, review_note = ?, reviewed_by = ?, reviewed_at = NOW()
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssii', $status, $review_note, $reviewed_by, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $ok;
}

function drl_request_resolve_doanvien($ma_sv) {
    $dv = getDoanVienByMaSv($ma_sv);
    if (!$dv) return [null, 'Không tìm thấy đoàn viên với mã ' . $ma_sv];
    return [$dv, null];
}
?>
