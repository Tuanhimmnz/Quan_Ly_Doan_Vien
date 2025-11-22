<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy tất cả danh sách đánh giá đoàn viên
 */
function getAllDanhGia() {
    $conn = getDbConnection();
    $sql = "SELECT kt.id,
                   kt.doanvien_id,
                   dv.ma_sv,
                   dv.ho_ten,
                   kt.loai,
                   kt.mo_ta,
                   kt.ngay_quyet_dinh,
                   kt.noi_dung,
                   kt.trang_thai
            FROM khen_thuong_kyluat kt
            LEFT JOIN doanvien dv ON kt.doanvien_id = dv.id
            ORDER BY kt.ngay_quyet_dinh DESC, kt.id DESC";

    $res = mysqli_query($conn, $sql);

    $list = [];
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $list[] = $row;
        }
    }

    mysqli_close($conn);
    return $list;
}

// [FITDNU-ADD] Tìm kiếm khen thưởng / kỷ luật theo q và trạng thái
function searchDanhGia($params = []) {
    $conn = getDbConnection();
    $q = trim($params['q'] ?? '');
    $trang_thai = trim($params['trang_thai'] ?? '');
    $loai = trim($params['loai'] ?? '');
    $sql = "SELECT kt.id, kt.doanvien_id, dv.ma_sv, dv.ho_ten, kt.loai, kt.mo_ta, kt.ngay_quyet_dinh, kt.noi_dung, kt.trang_thai
            FROM khen_thuong_kyluat kt
            LEFT JOIN doanvien dv ON kt.doanvien_id = dv.id
            WHERE 1=1";
    $types = '';
    $binds = [];
    if ($q !== '') { $like = "%$q%"; $sql .= " AND (dv.ma_sv LIKE ? OR dv.ho_ten LIKE ? OR kt.noi_dung LIKE ?)"; $types.='sss'; array_push($binds, $like, $like, $like); }
    if ($trang_thai !== '') { $sql .= " AND kt.trang_thai = ?"; $types.='s'; $binds[] = $trang_thai; }
    if ($loai !== '') { $sql .= " AND kt.loai = ?"; $types.='s'; $binds[] = $loai; }
    $sql .= " ORDER BY kt.ngay_quyet_dinh DESC, kt.id DESC";
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
 * Lấy chi tiết 1 đánh giá theo ID
 */
function getDanhGiaById($id) {
    $conn = getDbConnection();

    $sql = "SELECT * FROM khen_thuong_kyluat WHERE id = ? LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare getDanhGiaById(): " . mysqli_error($conn));
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
 * Thêm khen thưởng / kỷ luật
 */
function addDanhGia($doanvien_id, $loai, $mo_ta, $ngay_quyet_dinh, $noi_dung, $trang_thai = null) {
    $conn = getDbConnection();

    $sql = "INSERT INTO khen_thuong_kyluat
            (doanvien_id, loai, mo_ta, ngay_quyet_dinh, noi_dung, trang_thai)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare addDanhGia(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "isssss",
        $doanvien_id,
        $loai,
        $mo_ta,
        $ngay_quyet_dinh,
        $noi_dung,
        $trang_thai
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Cập nhật khen thưởng / kỷ luật
 */
function updateDanhGia($id, $doanvien_id, $loai, $mo_ta, $ngay_quyet_dinh, $noi_dung, $trang_thai = null) {
    $conn = getDbConnection();

    $sql = "UPDATE khen_thuong_kyluat
            SET doanvien_id = ?,
                loai = ?,
                mo_ta = ?,
                ngay_quyet_dinh = ?,
                noi_dung = ?,
                trang_thai = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare updateDanhGia(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "isssssi",
        $doanvien_id,
        $loai,
        $mo_ta,
        $ngay_quyet_dinh,
        $noi_dung,
        $trang_thai,
        $id
    );

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

/**
 * Xóa bản ghi khen thưởng / kỷ luật
 */
function deleteDanhGia($id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM khen_thuong_kyluat WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Lỗi prepare deleteDanhGia(): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}
?>
