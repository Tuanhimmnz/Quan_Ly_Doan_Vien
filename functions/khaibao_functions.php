<?php
require_once __DIR__ . '/db_connection.php';

function createDeclaration($userId, $payload) {
    $conn = getDbConnection();

    // Lấy danh sách cột hiện có để build câu lệnh tương thích schema
    $res = mysqli_query($conn, "SHOW COLUMNS FROM user_declarations");
    if (!$res) { mysqli_close($conn); return false; }
    $available = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $available[$row['Field']] = true;
    }
    mysqli_free_result($res);

    // Thứ tự ưu tiên cột
    $ordered = [
        'user_id', 'ma_sv', 'ho_ten', 'gioi_tinh', 'ngay_sinh', 'email', 'sdt', 'lop_id',
        'chuc_vu', 'trang_thai', 'ngay_vao_doan', 'dia_chi', 'so_doan_vien', 'ghi_chu'
    ];

    $cols = [];
    $vals = [];
    $types = '';

    foreach ($ordered as $col) {
        if (!isset($available[$col])) continue; // bỏ qua cột không tồn tại

        if ($col === 'user_id') {
            $cols[] = 'user_id';
            $vals[] = (int)$userId;
            $types .= 'i';
        } elseif ($col === 'lop_id') {
            $cols[] = 'lop_id';
            $vals[] = (int)($payload['lop_id'] ?? 0);
            $types .= 'i';
        } else {
            $cols[] = $col;
            $vals[] = $payload[$col] ?? null;
            $types .= 's';
        }
    }

    // Luôn có status nếu cột tồn tại, nhưng status có default 'pending' rồi.
    $statusFragment = '';
    if (isset($available['status'])) {
        $cols[] = 'status';
        $statusFragment = ', ?';
        $vals[] = 'pending';
        $types .= 's';
    }

    // Build placeholders
    $placeholders = implode(', ', array_fill(0, count($cols) - ($statusFragment ? 1 : 0), '?'));
    if ($statusFragment) {
        // placeholders cho phần trước status
        $placeholders = implode(', ', array_fill(0, count($cols) - 1, '?')) . $statusFragment;
    }

    $sql = 'INSERT INTO user_declarations (' . implode(', ', $cols) . ') VALUES (' . $placeholders . ')';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { mysqli_close($conn); return false; }

    mysqli_stmt_bind_param($stmt, $types, ...$vals);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}

function getDeclarationsByUser($userId, $limit = 10) {
    $conn = getDbConnection();

    $limit = (int)$limit;
    if ($limit <= 0) { $limit = 10; }

    $sql = "SELECT ud.*, l.ten_lop
            FROM user_declarations ud
            LEFT JOIN lop l ON ud.lop_id = l.id
            WHERE ud.user_id = ?
            ORDER BY ud.created_at DESC
            LIMIT $limit";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return [];
    }

    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $rows;
}

function getAllDeclarations($status = null) {
    $conn = getDbConnection();

    $where = '';
    if ($status !== null && $status !== 'all') {
        $where = 'WHERE ud.status = ?';
    }

    $sql = "SELECT ud.*, u.username, l.ten_lop, reviewer.username AS reviewer_name
            FROM user_declarations ud
            LEFT JOIN users u ON ud.user_id = u.id
            LEFT JOIN users reviewer ON ud.reviewed_by = reviewer.id
            LEFT JOIN lop l ON ud.lop_id = l.id
            $where
            ORDER BY ud.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($where) {
        mysqli_stmt_bind_param($stmt, 's', $status);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $rows;
}

// [FITDNU-ADD] Tìm kiếm khai báo: theo từ khóa (ma_sv/ho_ten/email/sdt/lớp), trạng thái và khoảng ngày gửi
function searchDeclarations($params = []) {
    $conn = getDbConnection();
    $q = trim($params['q'] ?? '');
    $status = $params['status'] ?? null;
    $from = trim($params['from'] ?? '');
    $to = trim($params['to'] ?? '');

    $sql = "SELECT ud.*, u.username, l.ten_lop, reviewer.username AS reviewer_name
            FROM user_declarations ud
            LEFT JOIN users u ON ud.user_id = u.id
            LEFT JOIN users reviewer ON ud.reviewed_by = reviewer.id
            LEFT JOIN lop l ON ud.lop_id = l.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($status && $status !== 'all') { $sql .= " AND ud.status = ?"; $types .= 's'; $binds[] = $status; }
    if ($q !== '') {
        $like = "%$q%";
        $sql .= " AND (ud.ma_sv LIKE ? OR ud.ho_ten LIKE ? OR ud.email LIKE ? OR ud.sdt LIKE ? OR l.ten_lop LIKE ?)";
        $types .= 'sssss';
        array_push($binds, $like, $like, $like, $like, $like);
    }
    if ($from !== '') { $sql .= " AND ud.created_at >= ?"; $types .= 's'; $binds[] = $from; }
    if ($to !== '') { $sql .= " AND ud.created_at <= ?"; $types .= 's'; $binds[] = $to; }
    $sql .= " ORDER BY ud.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    if ($result) while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $rows;
}

function getDeclarationById($id) {
    $conn = getDbConnection();

    $sql = "SELECT * FROM user_declarations WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $row;
}

function updateDeclarationStatus($id, $status, $adminId, $adminNote = null) {
    $conn = getDbConnection();

    $sql = "UPDATE user_declarations
            SET status = ?,
                admin_note = ?,
                reviewed_by = ?,
                reviewed_at = NOW()
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'ssii', $status, $adminNote, $adminId, $id);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}
