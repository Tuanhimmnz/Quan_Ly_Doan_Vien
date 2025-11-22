<?php
require_once __DIR__ . '/db_connection.php';

// Xác định tên cột cố vấn/ bí thư theo schema hiện tại (hỗ trợ cả co_van_hoc_tap / co_van)
function lop_column_map($conn = null) {
    static $cached = null;
    if ($cached !== null) return $cached;

    $own = false;
    if ($conn === null) { $conn = getDbConnection(); $own = true; }

    $advisor = 'co_van';
    $secretary = 'bi_thu';
    $res = mysqli_query($conn, "SHOW COLUMNS FROM lop");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            if ($row['Field'] === 'co_van_hoc_tap') $advisor = 'co_van_hoc_tap';
            if ($row['Field'] === 'bi_thu_chi_doan') $secretary = 'bi_thu_chi_doan';
        }
        mysqli_free_result($res);
    }
    if ($own) mysqli_close($conn);
    $cached = ['advisor' => $advisor, 'secretary' => $secretary];
    return $cached;
}

/**
 * Lấy toàn bộ danh sách lớp để hiển thị
 */
function getAllLop() {
    $conn = getDbConnection();
    $map = lop_column_map($conn);

    $sql = "
        SELECT l.id,
               l.ma_lop,
               l.ten_lop,
               l.khoa_id,
               k.ten_khoa,
               l.`{$map['advisor']}` AS co_van,
               l.`{$map['secretary']}` AS bi_thu
        FROM lop l
        LEFT JOIN khoa k ON l.khoa_id = k.id
        ORDER BY l.id ASC
    ";

    $result = mysqli_query($conn, $sql);

    $rows = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
    }

    mysqli_close($conn);
    return $rows;
}

// [FITDNU-ADD] Tìm kiếm lớp/chi đoàn theo từ khóa và khoa
function searchLop($params = []) {
    $conn = getDbConnection();
    $map = lop_column_map($conn);
    $kw = trim($params['q'] ?? '');
    $khoa = trim($params['khoa'] ?? '');
    $sql = "SELECT l.id, l.ma_lop, l.ten_lop, l.khoa_id, k.ten_khoa, l.`{$map['advisor']}` AS co_van, l.`{$map['secretary']}` AS bi_thu
            FROM lop l
            LEFT JOIN khoa k ON l.khoa_id = k.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($kw !== '') {
        $like = "%$kw%";
        $sql .= " AND (l.ma_lop LIKE ? OR l.ten_lop LIKE ? OR k.ten_khoa LIKE ? OR l.`{$map['advisor']}` LIKE ? OR l.`{$map['secretary']}` LIKE ?)";
        $types .= 'sssss';
        array_push($binds, $like, $like, $like, $like, $like);
    }
    if ($khoa !== '') { $sql .= " AND k.ten_khoa LIKE ?"; $types .= 's'; $binds[] = "%$khoa%"; }
    $sql .= " ORDER BY l.id ASC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $list;
}

/**
 * Lấy thông tin 1 lớp theo id
 */
function getLopById($id) {
    $conn = getDbConnection();
    $map = lop_column_map($conn);
    $id = (int)$id;

    $sql = "
        SELECT l.id,
               l.ma_lop,
               l.ten_lop,
               l.khoa_id,
               l.`{$map['advisor']}` AS co_van,
               l.`{$map['secretary']}` AS bi_thu
        FROM lop l
        WHERE l.id = $id
        LIMIT 1
    ";

    $result = mysqli_query($conn, $sql);

    $lop = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $lop = mysqli_fetch_assoc($result);
    }

    mysqli_close($conn);
    return $lop;
}

/**
 * Thêm lớp mới
 */
function createLop($ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu) {
    $conn = getDbConnection();
    $map = lop_column_map($conn);

    $ma_lop  = mysqli_real_escape_string($conn, $ma_lop);
    $ten_lop = mysqli_real_escape_string($conn, $ten_lop);
    $co_van  = mysqli_real_escape_string($conn, $co_van);
    $bi_thu  = mysqli_real_escape_string($conn, $bi_thu);
    $khoa_id = (int)$khoa_id;

    $sql = "
        INSERT INTO lop (ma_lop, ten_lop, khoa_id, `{$map['advisor']}`, `{$map['secretary']}`)
        VALUES ('$ma_lop', '$ten_lop', $khoa_id, '$co_van', '$bi_thu')
    ";

    $ok = mysqli_query($conn, $sql);

    $newId = null;
    if ($ok) {
        $newId = mysqli_insert_id($conn);
    }

    mysqli_close($conn);
    return $newId;
}

/**
 * Cập nhật lớp
 */
function updateLop($id, $ma_lop, $ten_lop, $khoa_id, $co_van, $bi_thu) {
    $conn = getDbConnection();
    $map = lop_column_map($conn);

    $id      = (int)$id;
    $khoa_id = (int)$khoa_id;

    $ma_lop  = mysqli_real_escape_string($conn, $ma_lop);
    $ten_lop = mysqli_real_escape_string($conn, $ten_lop);
    $co_van  = mysqli_real_escape_string($conn, $co_van);
    $bi_thu  = mysqli_real_escape_string($conn, $bi_thu);

    $sql = "
        UPDATE lop
        SET ma_lop  = '$ma_lop',
            ten_lop = '$ten_lop',
            khoa_id = $khoa_id,
            `{$map['advisor']}`  = '$co_van',
            `{$map['secretary']}`  = '$bi_thu'
        WHERE id = $id
        LIMIT 1
    ";

    $ok = mysqli_query($conn, $sql);

    mysqli_close($conn);
    return $ok;
}

/**
 * Xoá lớp theo id
 */
function deleteLop($id) {
    $conn = getDbConnection();
    $id = (int)$id;

    $sql = "DELETE FROM lop WHERE id = $id LIMIT 1";
    $ok = mysqli_query($conn, $sql);

    mysqli_close($conn);
    return $ok;
}

/**
 * Lấy danh sách khoa để đổ vào <select>
 */
function getAllKhoaForDropdown() {
    $conn = getDbConnection();

    $sql = "
        SELECT id, ten_khoa
        FROM khoa
        ORDER BY ten_khoa ASC
    ";

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
function getAllLopForDropdown() {
    $conn = getDbConnection();

    $sql = "
        SELECT id, ma_lop, ten_lop
        FROM lop
        ORDER BY ma_lop ASC, ten_lop ASC
    ";

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
