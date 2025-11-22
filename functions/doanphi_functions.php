<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy danh sách đoàn phí (JOIN với đoàn viên để hiển thị mã SV, họ tên)
 * Dùng cho trang views/doanphi.php
 */
function getAllDoanPhi() {
    $conn = getDbConnection();

    $sql = "SELECT 
                dp.id,
                dp.doanvien_id,
                dv.ma_sv,
                dv.ho_ten,
                dp.nam_hoc,
                dp.da_nop,
                dp.ngay_nop,
                dp.so_tien_nop
            FROM doanphi dp
            LEFT JOIN doanvien dv ON dp.doanvien_id = dv.id
            ORDER BY dp.nam_hoc DESC, dv.ho_ten ASC";

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

// [FITDNU-ADD] Lấy danh sách năm học (distinct) từ bảng doanphi
function getAllNamHocDoanPhi() {
    $conn = getDbConnection();
    $sql = "SELECT DISTINCT nam_hoc FROM doanphi ORDER BY nam_hoc DESC";
    $result = mysqli_query($conn, $sql);
    $years = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $years[] = $row['nam_hoc'];
        }
    }
    mysqli_close($conn);
    return $years;
}

// [FITDNU-ADD] Lấy danh sách đoàn phí theo năm học
function getDoanPhiByYear($nam_hoc) {
    $conn = getDbConnection();
    $sql = "SELECT 
                dp.id,
                dp.doanvien_id,
                dv.ma_sv,
                dv.ho_ten,
                dp.nam_hoc,
                dp.da_nop,
                dp.ngay_nop,
                dp.so_tien_nop
            FROM doanphi dp
            LEFT JOIN doanvien dv ON dp.doanvien_id = dv.id
            WHERE dp.nam_hoc = ?
            ORDER BY dv.ho_ten ASC";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare getDoanPhiByYear(): " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 's', $nam_hoc);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $list[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $list;
}

// [FITDNU-ADD] Tìm kiếm đoàn phí theo q (ma_sv/ho_ten) và trạng thái, kèm lọc năm_học tùy chọn
function searchDoanPhi($params = []) {
    $conn = getDbConnection();
    $q = trim($params['q'] ?? '');
    $nam_hoc = trim($params['nam_hoc'] ?? '');
    $paid = trim($params['paid'] ?? ''); // '1' or '0' or ''
    $sql = "SELECT dp.id, dp.doanvien_id, dv.ma_sv, dv.ho_ten, dp.nam_hoc, dp.da_nop, dp.ngay_nop, dp.so_tien_nop
            FROM doanphi dp
            LEFT JOIN doanvien dv ON dp.doanvien_id = dv.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($q !== '') { $like = "%$q%"; $sql .= " AND (dv.ma_sv LIKE ? OR dv.ho_ten LIKE ?)"; $types.='ss'; array_push($binds, $like, $like); }
    if ($nam_hoc !== '') { $sql .= " AND dp.nam_hoc = ?"; $types.='s'; $binds[] = $nam_hoc; }
    if ($paid === '0' || $paid === '1') { $sql .= " AND dp.da_nop = ?"; $types.='i'; $binds[] = (int)$paid; }
    $sql .= " ORDER BY dp.nam_hoc DESC, dv.ho_ten ASC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$binds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($res && $row = mysqli_fetch_assoc($res)) $list[] = $row;
    mysqli_stmt_close($stmt); mysqli_close($conn);
    return $list;
}

/**
 * Lấy thông tin 1 bản ghi đoàn phí theo ID
 * Dùng khi muốn load lên form sửa (edit)
 */
function getDoanPhiById($id) {
    $conn = getDbConnection();

    $sql = "SELECT 
                dp.id,
                dp.doanvien_id,
                dp.nam_hoc,
                dp.da_nop,
                dp.ngay_nop,
                dp.so_tien_nop,
                dv.ma_sv,
                dv.ho_ten
            FROM doanphi dp
            LEFT JOIN doanvien dv ON dp.doanvien_id = dv.id
            WHERE dp.id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare getDoanPhiById(): " . mysqli_error($conn));
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
 * Kiểm tra xem 1 đoàn viên đã có đoàn phí cho năm học đó chưa
 * Mục đích: tránh tạo bản ghi trùng (1 đoàn viên / 1 năm học chỉ 1 dòng)
 * Trả về true nếu đã tồn tại
 */
function checkDoanPhiExists($doanvien_id, $nam_hoc) {
    $conn = getDbConnection();

    $sql = "SELECT id
            FROM doanphi
            WHERE doanvien_id = ?
              AND nam_hoc = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare checkDoanPhiExists(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "is", $doanvien_id, $nam_hoc);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);

    $exists = false;
    if ($res && mysqli_num_rows($res) > 0) {
        $exists = true;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $exists;
}

/**
 * Thêm bản ghi đoàn phí mới
 * Dùng khi submit form "Thêm đoàn phí"
 */
function addDoanPhi($doanvien_id, $nam_hoc, $da_nop, $ngay_nop, $so_tien_nop) {
    $conn = getDbConnection();

    $sql = "INSERT INTO doanphi (
                doanvien_id,
                nam_hoc,
                da_nop,
                ngay_nop,
                so_tien_nop
            )
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare addDoanPhi(): " . mysqli_error($conn));
    }

    // Chuẩn hóa input
    // da_nop từ form là chuỗi "0"/"1" => ép int
    $da_nop_int   = (int)$da_nop;
    
    // ngày nộp có thể để trống => NULL
    $ngay_nop_val = ($ngay_nop === '' ? NULL : $ngay_nop);
    // số tiền nộp: nếu null => 0
    $so_tien_val  = ($so_tien_nop === '' || $so_tien_nop === null) ? 0 : (float)$so_tien_nop;

    mysqli_stmt_bind_param(
        $stmt,
        "isisd",
        $doanvien_id,
        $nam_hoc,
        $da_nop_int,
        $ngay_nop_val,
        $so_tien_val
    );

    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : false;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $newId; // [FITDNU-ADD] return inserted ID on success
}

/**
 * Cập nhật thông tin đoàn phí
 */
function updateDoanPhi($id, $doanvien_id, $nam_hoc, $da_nop, $ngay_nop, $so_tien_nop) {
    $conn = getDbConnection();

    $sql = "UPDATE doanphi
            SET doanvien_id = ?,
                nam_hoc     = ?,
                da_nop      = ?,
                ngay_nop    = ?,
                so_tien_nop = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare updateDoanPhi(): " . mysqli_error($conn));
    }

    $da_nop_int   = (int)$da_nop;
    $ngay_nop_val = ($ngay_nop === '' ? NULL : $ngay_nop);
    $so_tien_val  = ($so_tien_nop === '' || $so_tien_nop === null) ? 0 : (float)$so_tien_nop;
    $id_int       = (int)$id;

    mysqli_stmt_bind_param(
        $stmt,
        "isisdi",
        $doanvien_id,
        $nam_hoc,
        $da_nop_int,
        $ngay_nop_val,
        $so_tien_val,
        $id_int
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Xóa bản ghi đoàn phí
 */
function deleteDoanPhi($id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM doanphi WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare deleteDoanPhi(): " . mysqli_error($conn));
    }

    $id_int = (int)$id;

    mysqli_stmt_bind_param($stmt, "i", $id_int);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Lấy danh sách đoàn viên đổ vào dropdown (form thêm đoàn phí)
 * Dùng trong views/doanphi/edit_doanphi.php (form create)
 */
function getAllDoanVienForDropdown() {
    $conn = getDbConnection();

    $sql = "SELECT id, ma_sv, ho_ten
            FROM doanvien
            ORDER BY ho_ten ASC";

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
?>
