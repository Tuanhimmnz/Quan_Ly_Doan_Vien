<?php
// [FITDNU-ADD] Import helpers cho các bảng
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/doanvien_functions.php';
require_once __DIR__ . '/lop_functions.php';
require_once __DIR__ . '/lichsu_functions.php';
// [FITDNU-ADD] Try Composer autoload for PhpSpreadsheet if present
$__autoload_candidates = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];
foreach ($__autoload_candidates as $__auto) {
    if (file_exists($__auto)) { require_once $__auto; break; }
}
// Chuẩn hóa encoding về UTF-8
function import_normalize_encoding($value) {
    if ($value === null) return null;
    // Nếu đã là UTF-8 hợp lệ thì giữ nguyên
    if (function_exists('mb_check_encoding') && mb_check_encoding($value, 'UTF-8')) {
        return $value;
    }
    // Ưu tiên cố định dùng Windows-1258 (Vietnamese)
    if (function_exists('iconv')) {
        $from1258 = @iconv('WINDOWS-1258', 'UTF-8//TRANSLIT', $value);
        if ($from1258 !== false && (!function_exists('mb_check_encoding') || mb_check_encoding($from1258, 'UTF-8'))) {
            return $from1258;
        }
    }
    // Thử phát hiện encoding (fallback)
    if (function_exists('mb_detect_encoding')) {
        $candidates = ['UTF-8','ISO-8859-1','CP1252','CP1258','WINDOWS-1258','ASCII'];
        $available = [];
        $installed = array_map('strtoupper', mb_list_encodings());
        foreach ($candidates as $c) {
            if (in_array(strtoupper($c), $installed, true)) $available[] = $c;
        }
        if (!empty($available)) {
            $enc = @mb_detect_encoding($value, $available, true);
            if ($enc && strtoupper($enc) !== 'UTF-8') {
                $converted = @mb_convert_encoding($value, 'UTF-8', $enc);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) return $converted;
            }
        }
    }
    // Fallback iconv lần lượt, chọn bản UTF-8 hợp lệ đầu tiên
    if (function_exists('iconv')) {
        foreach (['WINDOWS-1258','CP1258','CP1252','ISO-8859-1'] as $from) {
            $res = @iconv($from, 'UTF-8//TRANSLIT', $value);
            if ($res !== false && (!function_exists('mb_check_encoding') || mb_check_encoding($res, 'UTF-8'))) {
                return $res;
            }
        }
    }
    return $value;
}

// [FITDNU-ADD] Read CSV file to array of associative rows given headers in first row
function read_csv_assoc($filepath) {
    $rows = [];
    if (($handle = fopen($filepath, 'r')) === false) {
        return [[], ['Không mở được file CSV']];
    }
    $errors = [];
    $headers = fgetcsv($handle);
    if ($headers === false) {
        fclose($handle);
        return [[], ['File trống hoặc không hợp lệ']];
    }
    // Normalize BOM, trim headers
    $headers = array_map(function($h){ return trim((string)$h, "\xEF\xBB\xBF \t\n\r"); }, $headers);
    $rownum = 1;
    while (($data = fgetcsv($handle)) !== false) {
        $rownum++;
        $row = [];
        foreach ($headers as $i => $key) {
            $val = isset($data[$i]) ? trim((string)$data[$i]) : '';
            $row[$key] = import_normalize_encoding($val);
        }
        $rows[] = $row;
    }
    fclose($handle);
    return [$rows, $errors];
}

// [FITDNU-ADD] Parse uploaded file: prefer PhpSpreadsheet if available; fallback CSV
function parse_upload_rows($tmpPath, $origName) {
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    if (in_array($ext, ['xlsx','xls'])) {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            // [FITDNU-ADD] Explicit error if PhpSpreadsheet not installed
            return [[], [
                'Máy chủ chưa cài PhpSpreadsheet. Vui lòng dùng file CSV hoặc cài thư viện PhpSpreadsheet.'
            ]];
        }
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpPath);
            if (method_exists($reader, 'setReadDataOnly')) $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tmpPath);
            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray(null, true, true, true);
            if (count($rows) === 0) return [[], ['Tệp rỗng']];
            $headers = array_map('trim', array_values($rows[1]));
            $out = [];
            for ($r = 2; $r <= count($rows); $r++) {
                $assoc = [];
                $row = $rows[$r];
                $i = 0;
                foreach ($headers as $h) {
                    $colKey = chr(ord('A') + $i);
                    $val = isset($row[$colKey]) ? trim((string)$row[$colKey]) : '';
                    $assoc[$h] = import_normalize_encoding($val);
                    $i++;
                }
                $out[] = $assoc;
            }
            return [$out, []];
        } catch (\Throwable $e) {
            return [[], ['Không đọc được file Excel: ' . $e->getMessage()]];
        }
    }
    return read_csv_assoc($tmpPath);
}

// [FITDNU-ADD] Normalize date: accepts YYYY-MM-DD or DD/MM/YYYY -> returns Y-m-d or null if invalid
function valid_date($str) {
    if ($str === '' || $str === null) return true;
    $str = trim($str);
    // Try Y-m-d
    $d = date_create_from_format('Y-m-d', $str);
    if ($d && $d->format('Y-m-d') === $str) return true;
    // Try dd/mm/yyyy
    $d = date_create_from_format('d/m/Y', $str);
    if ($d) {
        // replace original by normalized string
        return true;
    }
    return false;
}

// Chuyển đổi dd/mm/yyyy -> yyyy-mm-dd nếu cần
function normalize_date($str) {
    if ($str === '' || $str === null) return null;
    $str = trim($str);
    $d = date_create_from_format('Y-m-d', $str);
    if ($d && $d->format('Y-m-d') === $str) return $str;
    $d = date_create_from_format('d/m/Y', $str);
    if ($d) return $d->format('Y-m-d');
    return null;
}

// [FITDNU-ADD] Import Khoa rows
function import_khoa_rows($rows, $opts = []) {
    $conn = getDbConnection();
    $skip_missing = !empty($opts['skip_missing']);
    $update_on_dup = !empty($opts['update_on_dup']);
    $total = count($rows); $ok = 0; $errors = [];

    $sql_insert = "INSERT INTO khoa(ten_khoa, mo_ta, truong_khoa, sdt_lien_he, email_lien_he)
                   VALUES(?,?,?,?,?)";
    $stmt_i = mysqli_prepare($conn, $sql_insert);

    $sql_select = "SELECT id FROM khoa WHERE ten_khoa = ? LIMIT 1";
    $stmt_s = mysqli_prepare($conn, $sql_select);

    $sql_update = "UPDATE khoa SET mo_ta=?, truong_khoa=?, sdt_lien_he=?, email_lien_he=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_update);

    mysqli_begin_transaction($conn);
    foreach ($rows as $idx => $r) {
        $ten_khoa = trim($r['ten_khoa'] ?? '');
        if ($ten_khoa === '') {
            if ($skip_missing) continue; else {$errors[] = ['row' => $idx+2, 'reason' => 'Thiếu ten_khoa']; continue;}
        }
        $mo_ta = $r['mo_ta'] ?? null;
        $truong = $r['truong_khoa'] ?? null;
        $sdt = $r['sdt_lien_he'] ?? null;
        $email = $r['email_lien_he'] ?? null;

        // upsert logic by ten_khoa
        mysqli_stmt_bind_param($stmt_s, 's', $ten_khoa);
        mysqli_stmt_execute($stmt_s);
        $res = mysqli_stmt_get_result($stmt_s);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if ($row) {
            if ($update_on_dup) {
                $id = (int)$row['id'];
                mysqli_stmt_bind_param($stmt_u, 'ssssi', $mo_ta, $truong, $sdt, $email, $id);
                $ok_exec = mysqli_stmt_execute($stmt_u);
                $ok += $ok_exec ? 1 : 0;
                if (!$ok_exec) $errors[] = ['row' => $idx+2, 'reason' => mysqli_error($conn)];
            } else {
                // skip
                $ok++;
            }
        } else {
            mysqli_stmt_bind_param($stmt_i, 'sssss', $ten_khoa, $mo_ta, $truong, $sdt, $email);
            $ok_exec = mysqli_stmt_execute($stmt_i);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[] = ['row' => $idx+2, 'reason' => mysqli_error($conn)];
        }
    }
    // [FITDNU-ADD] Commit even if some rows fail to keep successful rows
    mysqli_commit($conn);
    mysqli_stmt_close($stmt_i); mysqli_stmt_close($stmt_s); mysqli_stmt_close($stmt_u);
    mysqli_close($conn);
    return compact('total','ok','errors');
}

// [FITDNU-ADD] Import Lop rows; resolve khoa by ten or id
function import_lop_rows($rows, $opts = []) {
    $conn = getDbConnection();
    $map = lop_column_map($conn);
    $advisorCol = $map['advisor'];
    $secretaryCol = $map['secretary'];
    $skip_missing = !empty($opts['skip_missing']);
    $update_on_dup = !empty($opts['update_on_dup']);
    $total = count($rows); $ok = 0; $errors = [];

    $sql_khoa_by_ten = "SELECT id FROM khoa WHERE ten_khoa = ? LIMIT 1";
    $stmt_k = mysqli_prepare($conn, $sql_khoa_by_ten);
    $sql_sel = "SELECT id FROM lop WHERE ma_lop = ? LIMIT 1";
    $stmt_s = mysqli_prepare($conn, $sql_sel);
    $sql_ins = "INSERT INTO lop(ma_lop, ten_lop, khoa_id, `$advisorCol`, `$secretaryCol`) VALUES(?,?,?,?,?)";
    $stmt_i = mysqli_prepare($conn, $sql_ins);
    $sql_upd = "UPDATE lop SET ten_lop=?, khoa_id=?, `$advisorCol`=?, `$secretaryCol`=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_upd);

    mysqli_begin_transaction($conn);
    foreach ($rows as $idx => $r) {
        $ma_lop = trim($r['ma_lop'] ?? '');
        $ten_lop = trim($r['ten_lop'] ?? '');
        if ($ma_lop === '' || $ten_lop === '') {
            if ($skip_missing) continue; else {$errors[] = ['row' => $idx+2, 'reason' => 'Thiếu ma_lop/ten_lop']; continue;}
        }
        $khoa_id = null;
        if (!empty($r['khoa_id'])) {
            $khoa_id = (int)$r['khoa_id'];
        } elseif (!empty($r['khoa_ten'])) {
            $khoa_ten = trim($r['khoa_ten']);
            mysqli_stmt_bind_param($stmt_k, 's', $khoa_ten);
            mysqli_stmt_execute($stmt_k);
            $res = mysqli_stmt_get_result($stmt_k);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            if ($row) $khoa_id = (int)$row['id'];
        }
        $covan = $r['co_van_hoc_tap'] ?? ($r['co_van'] ?? null);
        $bithu = $r['bi_thu_chi_doan'] ?? ($r['bi_thu'] ?? null);

        mysqli_stmt_bind_param($stmt_s, 's', $ma_lop);
        mysqli_stmt_execute($stmt_s);
        $res = mysqli_stmt_get_result($stmt_s);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if ($row) {
            if ($update_on_dup) {
                $id = (int)$row['id'];
                mysqli_stmt_bind_param($stmt_u, 'sissi', $ten_lop, $khoa_id, $covan, $bithu, $id);
                $ok_exec = mysqli_stmt_execute($stmt_u);
                $ok += $ok_exec ? 1 : 0;
                if (!$ok_exec) $errors[] = ['row' => $idx+2, 'reason' => mysqli_error($conn)];
            } else {
                $ok++;
            }
        } else {
            mysqli_stmt_bind_param($stmt_i, 'ssiss', $ma_lop, $ten_lop, $khoa_id, $covan, $bithu);
            $ok_exec = mysqli_stmt_execute($stmt_i);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[] = ['row' => $idx+2, 'reason' => mysqli_error($conn)];
        }
    }
    // [FITDNU-ADD] Partial success: always commit successful rows
    mysqli_commit($conn);
    mysqli_stmt_close($stmt_k); mysqli_stmt_close($stmt_s); mysqli_stmt_close($stmt_i); mysqli_stmt_close($stmt_u);
    mysqli_close($conn);
    return compact('total','ok','errors');
}

// [FITDNU-ADD] Import Doan vien rows; resolve lop by ma or id
function import_doanvien_rows($rows, $opts = []) {
    $conn = getDbConnection();
    $fileName = $opts['file_name'] ?? null;
    $skip_missing = !empty($opts['skip_missing']);
    $update_on_dup = !empty($opts['update_on_dup']);
    $total = count($rows); $ok = 0; $errors = [];

    $sql_lop_by_ma = "SELECT id FROM lop WHERE ma_lop = ? LIMIT 1";
    $stmt_l = mysqli_prepare($conn, $sql_lop_by_ma);
    $sql_sel = "SELECT id FROM doanvien WHERE ma_sv = ? LIMIT 1";
    $stmt_s = mysqli_prepare($conn, $sql_sel);
    $sql_ins = "INSERT INTO doanvien(ma_sv, ho_ten, ngay_sinh, gioi_tinh, sdt, email, lop_id, chuc_vu, trang_thai, ngay_vao_doan)
                VALUES(?,?,?,?,?,?,?,?,?,?)";
    $stmt_i = mysqli_prepare($conn, $sql_ins);
    $sql_upd = "UPDATE doanvien SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, sdt=?, email=?, lop_id=?, chuc_vu=?, trang_thai=?, ngay_vao_doan=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_upd);

    mysqli_begin_transaction($conn);
    foreach ($rows as $idx => $r) {
        $ma_sv = trim($r['ma_sv'] ?? '');
        $ho_ten = trim($r['ho_ten'] ?? '');
        if ($ma_sv === '' || $ho_ten === '') {
            if ($skip_missing) continue; else {$errors[] = ['row' => $idx+2, 'reason' => 'Thiếu ma_sv/ho_ten']; continue;}
        }
        $ngay_sinh_raw = trim($r['ngay_sinh'] ?? '');
        $ngay_sinh = normalize_date($ngay_sinh_raw);
        if ($ngay_sinh_raw !== '' && $ngay_sinh === null) { $errors[] = ['row' => $idx+2, 'reason' => 'Sai định dạng ngày_sinh']; continue; }
        $gioi_tinh = trim($r['gioi_tinh'] ?? '');
        if ($gioi_tinh !== '') {
            $map = ['nam' => 'Nam','nữ' => 'Nữ','nu' => 'Nữ','khác' => 'Khác','khac' => 'Khác'];
            $key = function_exists('mb_strtolower') ? mb_strtolower($gioi_tinh, 'UTF-8') : strtolower($gioi_tinh);
            $gioi_tinh = $map[$key] ?? $gioi_tinh;
        }
        $sdt = $r['sdt'] ?? null;
        if ($sdt !== null) {
            $sdt = trim((string)$sdt);
            if ($sdt !== '' && preg_match('/^\\d+$/', $sdt) && $sdt[0] !== '0') {
                $sdt = '0' . $sdt;
            }
        }
        $email = $r['email'] ?? null;
        $lop_id = null;
        if (!empty($r['lop_id'])) $lop_id = (int)$r['lop_id'];
        elseif (!empty($r['lop_ma'])) {
            $lop_ma = trim($r['lop_ma']);
            mysqli_stmt_bind_param($stmt_l, 's', $lop_ma);
            mysqli_stmt_execute($stmt_l);
            $res = mysqli_stmt_get_result($stmt_l);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            if ($row) $lop_id = (int)$row['id'];
        }
        $chuc_vu = $r['chuc_vu'] ?? null;
        $trang_thai = $r['trang_thai'] ?? null;
        $ngay_vao_raw = trim($r['ngay_vao_doan'] ?? '');
        $ngay_vao_doan = normalize_date($ngay_vao_raw);
        if ($ngay_vao_raw !== '' && $ngay_vao_doan === null) { $errors[] = ['row' => $idx+2, 'reason' => 'Sai định dạng ngay_vao_doan']; continue; }

        mysqli_stmt_bind_param($stmt_s, 's', $ma_sv);
        mysqli_stmt_execute($stmt_s);
        $res = mysqli_stmt_get_result($stmt_s);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if ($row) {
            if ($update_on_dup) {
                $id = (int)$row['id'];
                $ngay_sinh_db = $ngay_sinh ?: null;
                $ngay_vao_db = $ngay_vao_doan ?: null;
                mysqli_stmt_bind_param($stmt_u, 'sssssisssi', $ho_ten, $ngay_sinh_db, $gioi_tinh, $sdt, $email, $lop_id, $chuc_vu, $trang_thai, $ngay_vao_db, $id);
                $ok_exec = mysqli_stmt_execute($stmt_u);
                $ok += $ok_exec ? 1 : 0;
                if (!$ok_exec) $errors[] = ['row' => $idx+2, 'reason' => mysqli_error($conn)];
            } else {
                $ok++;
            }
        } else {
            $ngay_sinh_db = $ngay_sinh ?: null;
            $ngay_vao_db = $ngay_vao_doan ?: null;
            mysqli_stmt_bind_param($stmt_i, 'ssssssisss', $ma_sv, $ho_ten, $ngay_sinh_db, $gioi_tinh, $sdt, $email, $lop_id, $chuc_vu, $trang_thai, $ngay_vao_db);
            $ok_exec = mysqli_stmt_execute($stmt_i);
            $ok += $ok_exec ? 1 : 0;
            if ($ok_exec) {
                $newId = mysqli_insert_id($conn);
                $startDate = $ngay_vao_doan ?: date('Y-m-d');
                $note = $fileName ? 'Tệp: ' . $fileName : 'Nhập từ Excel/CSV';
                addLichSuIfNotExists($newId, $startDate, null, 'Nhập từ Excel', $note, $conn);
            } else {
                $errors[] = ['row' => $idx+2, 'reason' => mysqli_error($conn)];
            }
        }
    }
    // [FITDNU-ADD] Partial success: always commit successful rows
    mysqli_commit($conn);
    mysqli_stmt_close($stmt_l); mysqli_stmt_close($stmt_s); mysqli_stmt_close($stmt_i); mysqli_stmt_close($stmt_u);
    mysqli_close($conn);
    return compact('total','ok','errors');
}

// Import khen thưởng / kỷ luật (entity quyết định loai)
function import_khen_kyluat_rows($rows, $loai = 'Khen thưởng', $opts = []) {
    $conn = getDbConnection();
    $skip_missing = !empty($opts['skip_missing']);
    $update_on_dup = !empty($opts['update_on_dup']);
    $total = count($rows); $ok = 0; $errors = [];

    $sql_find_dv = "SELECT id FROM doanvien WHERE ma_sv = ? LIMIT 1";
    $stmt_dv = mysqli_prepare($conn, $sql_find_dv);
    $sql_sel = "SELECT id FROM khen_thuong_kyluat WHERE doanvien_id=? AND loai=? AND ngay_quyet_dinh=?";
    $stmt_sel = mysqli_prepare($conn, $sql_sel);
    $sql_ins = "INSERT INTO khen_thuong_kyluat(doanvien_id, loai, mo_ta, ngay_quyet_dinh, noi_dung, trang_thai) VALUES (?,?,?,?,?,?)";
    $stmt_i = mysqli_prepare($conn, $sql_ins);
    $sql_upd = "UPDATE khen_thuong_kyluat SET mo_ta=?, noi_dung=?, trang_thai=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_upd);

    mysqli_begin_transaction($conn);
    foreach ($rows as $idx => $r) {
        $ma_sv = trim($r['ma_sv'] ?? '');
        if ($ma_sv === '') { if ($skip_missing) continue; else {$errors[]=['row'=>$idx+2,'reason'=>'Thiếu ma_sv']; continue;} }
        mysqli_stmt_bind_param($stmt_dv, 's', $ma_sv);
        mysqli_stmt_execute($stmt_dv);
        $res = mysqli_stmt_get_result($stmt_dv);
        $dv = $res ? mysqli_fetch_assoc($res) : null;
        if (!$dv) { $errors[]=['row'=>$idx+2,'reason'=>'Không tìm thấy đoàn viên']; continue; }
        $doanvien_id = (int)$dv['id'];
        $ngay_qd_raw = trim($r['ngay_quyet_dinh'] ?? '');
        $ngay_qd = normalize_date($ngay_qd_raw);
        if ($ngay_qd_raw !== '' && $ngay_qd === null) { $errors[]=['row'=>$idx+2,'reason'=>'Sai định dạng ngay_quyet_dinh']; continue; }
        $mo_ta = $r['mo_ta'] ?? null;
        $noi_dung = $r['noi_dung'] ?? null;
        $trang_thai = $r['trang_thai'] ?? null;

        mysqli_stmt_bind_param($stmt_sel, 'iss', $doanvien_id, $loai, $ngay_qd);
        mysqli_stmt_execute($stmt_sel);
        $resSel = mysqli_stmt_get_result($stmt_sel);
        $row = $resSel ? mysqli_fetch_assoc($resSel) : null;
        if ($row && $update_on_dup) {
            $id = (int)$row['id'];
            mysqli_stmt_bind_param($stmt_u, 'sssi', $mo_ta, $noi_dung, $trang_thai, $id);
            $ok_exec = mysqli_stmt_execute($stmt_u);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        } elseif ($row) {
            $ok++;
        } else {
            $ngay_qd_db = $ngay_qd ?: null;
            mysqli_stmt_bind_param($stmt_i, 'isssss', $doanvien_id, $loai, $mo_ta, $ngay_qd_db, $noi_dung, $trang_thai);
            $ok_exec = mysqli_stmt_execute($stmt_i);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        }
    }
    mysqli_commit($conn);
    mysqli_stmt_close($stmt_dv); mysqli_stmt_close($stmt_sel); mysqli_stmt_close($stmt_i); mysqli_stmt_close($stmt_u);
    mysqli_close($conn);
    return compact('total','ok','errors');
}

// Import điểm rèn luyện
function import_diemrenluyen_rows($rows, $opts = []) {
    $conn = getDbConnection();
    $skip_missing = !empty($opts['skip_missing']);
    $update_on_dup = !empty($opts['update_on_dup']);
    $total = count($rows); $ok = 0; $errors = [];

    $sql_dv = "SELECT id FROM doanvien WHERE ma_sv = ? LIMIT 1";
    $stmt_dv = mysqli_prepare($conn, $sql_dv);
    $sql_sel = "SELECT id FROM diem_renluyen WHERE doanvien_id=? AND nam_hoc=?";
    $stmt_s = mysqli_prepare($conn, $sql_sel);
    $sql_ins = "INSERT INTO diem_renluyen(doanvien_id, nam_hoc, diem, xep_loai, ghi_chu) VALUES (?,?,?,?,?)";
    $stmt_i = mysqli_prepare($conn, $sql_ins);
    $sql_upd = "UPDATE diem_renluyen SET diem=?, xep_loai=?, ghi_chu=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_upd);

    mysqli_begin_transaction($conn);
    foreach ($rows as $idx => $r) {
        $ma_sv = trim($r['ma_sv'] ?? '');
        $nam_hoc = trim($r['nam_hoc'] ?? '');
        $diem = trim($r['diem'] ?? '');
        if ($ma_sv === '' || $nam_hoc === '' || $diem === '') {
            if ($skip_missing) continue; else {$errors[]=['row'=>$idx+2,'reason'=>'Thiếu ma_sv/nam_hoc/diem']; continue;}
        }
        $diem_val = is_numeric($diem) ? (float)$diem : null;
        if ($diem_val === null) { $errors[]=['row'=>$idx+2,'reason'=>'Điểm không hợp lệ']; continue; }
        mysqli_stmt_bind_param($stmt_dv, 's', $ma_sv);
        mysqli_stmt_execute($stmt_dv);
        $res = mysqli_stmt_get_result($stmt_dv);
        $dv = $res ? mysqli_fetch_assoc($res) : null;
        if (!$dv) { $errors[]=['row'=>$idx+2,'reason'=>'Không tìm thấy đoàn viên']; continue; }
        $doanvien_id = (int)$dv['id'];
        $xep_loai = $r['xep_loai'] ?? null;
        $ghi_chu = $r['ghi_chu'] ?? null;

        mysqli_stmt_bind_param($stmt_s, 'is', $doanvien_id, $nam_hoc);
        mysqli_stmt_execute($stmt_s);
        $resSel = mysqli_stmt_get_result($stmt_s);
        $row = $resSel ? mysqli_fetch_assoc($resSel) : null;
        if ($row && $update_on_dup) {
            $id = (int)$row['id'];
            mysqli_stmt_bind_param($stmt_u, 'dssi', $diem_val, $xep_loai, $ghi_chu, $id);
            $ok_exec = mysqli_stmt_execute($stmt_u);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        } elseif ($row) {
            $ok++;
        } else {
            mysqli_stmt_bind_param($stmt_i, 'isdss', $doanvien_id, $nam_hoc, $diem_val, $xep_loai, $ghi_chu);
            $ok_exec = mysqli_stmt_execute($stmt_i);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        }
    }
    mysqli_commit($conn);
    mysqli_stmt_close($stmt_dv); mysqli_stmt_close($stmt_s); mysqli_stmt_close($stmt_i); mysqli_stmt_close($stmt_u);
    mysqli_close($conn);
    return compact('total','ok','errors');
}

// Import sự kiện
function import_sukien_rows($rows, $opts = []) {
    $conn = getDbConnection();
    $skip_missing = !empty($opts['skip_missing']);
    $update_on_dup = !empty($opts['update_on_dup']);
    $total = count($rows); $ok = 0; $errors = [];

    $sql_sel = "SELECT id FROM su_kien WHERE ten_su_kien=? AND ngay_to_chuc=? LIMIT 1";
    $stmt_s = mysqli_prepare($conn, $sql_sel);
    $sql_ins = "INSERT INTO su_kien(ten_su_kien, mo_ta, ngay_to_chuc, cap_to_chuc, trang_thai) VALUES (?,?,?,?,?)";
    $stmt_i = mysqli_prepare($conn, $sql_ins);
    $sql_upd = "UPDATE su_kien SET mo_ta=?, cap_to_chuc=?, trang_thai=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_upd);

    mysqli_begin_transaction($conn);
    foreach ($rows as $idx => $r) {
        $ten = trim($r['ten_su_kien'] ?? '');
        if ($ten === '') { if ($skip_missing) continue; else {$errors[]=['row'=>$idx+2,'reason'=>'Thiếu ten_su_kien']; continue;} }
        $ngay_raw = trim($r['ngay_to_chuc'] ?? '');
        $ngay = normalize_date($ngay_raw);
        if ($ngay_raw !== '' && $ngay === null) { $errors[]=['row'=>$idx+2,'reason'=>'Sai định dạng ngay_to_chuc']; continue; }
        $mo_ta = $r['mo_ta'] ?? null;
        $cap = $r['cap_to_chuc'] ?? null;
        $trang_thai = $r['trang_thai'] ?? null;

        mysqli_stmt_bind_param($stmt_s, 'ss', $ten, $ngay);
        mysqli_stmt_execute($stmt_s);
        $resSel = mysqli_stmt_get_result($stmt_s);
        $row = $resSel ? mysqli_fetch_assoc($resSel) : null;
        if ($row && $update_on_dup) {
            $id = (int)$row['id'];
            mysqli_stmt_bind_param($stmt_u, 'sssi', $mo_ta, $cap, $trang_thai, $id);
            $ok_exec = mysqli_stmt_execute($stmt_u);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        } elseif ($row) {
            $ok++;
        } else {
            $ngay_db = $ngay ?: null;
            mysqli_stmt_bind_param($stmt_i, 'sssss', $ten, $mo_ta, $ngay_db, $cap, $trang_thai);
            $ok_exec = mysqli_stmt_execute($stmt_i);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        }
    }
    mysqli_commit($conn);
    mysqli_stmt_close($stmt_s); mysqli_stmt_close($stmt_i); mysqli_stmt_close($stmt_u); mysqli_close($conn);
    return compact('total','ok','errors');
}

// Import tài khoản (username unique)
function import_taikhoan_rows($rows, $opts = []) {
    $conn = getDbConnection();
    $skip_missing = !empty($opts['skip_missing']);
    $update_on_dup = !empty($opts['update_on_dup']);
    $total = count($rows); $ok = 0; $errors = [];

    $sql_sel = "SELECT id FROM users WHERE username=? LIMIT 1";
    $stmt_s = mysqli_prepare($conn, $sql_sel);
    $sql_ins = "INSERT INTO users(ho_ten, username, password, role, trang_thai) VALUES (?,?,?,?,?)";
    $stmt_i = mysqli_prepare($conn, $sql_ins);
    $sql_upd = "UPDATE users SET ho_ten=?, password=?, role=?, trang_thai=? WHERE id=?";
    $stmt_u = mysqli_prepare($conn, $sql_upd);

    mysqli_begin_transaction($conn);
    foreach ($rows as $idx => $r) {
        $username = trim($r['username'] ?? '');
        $password = $r['password'] ?? '';
        if ($username === '' || $password === '') {
            if ($skip_missing) continue; else {$errors[]=['row'=>$idx+2,'reason'=>'Thiếu username/password']; continue;}
        }
        $ho_ten = $r['ho_ten'] ?? null;
        $role = $r['role'] ?? 'user';
        $trang_thai = $r['trang_thai'] ?? null;

        mysqli_stmt_bind_param($stmt_s, 's', $username);
        mysqli_stmt_execute($stmt_s);
        $resSel = mysqli_stmt_get_result($stmt_s);
        $row = $resSel ? mysqli_fetch_assoc($resSel) : null;
        if ($row && $update_on_dup) {
            $id = (int)$row['id'];
            mysqli_stmt_bind_param($stmt_u, 'ssssi', $ho_ten, $password, $role, $trang_thai, $id);
            $ok_exec = mysqli_stmt_execute($stmt_u);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        } elseif ($row) {
            $ok++;
        } else {
            mysqli_stmt_bind_param($stmt_i, 'sssss', $ho_ten, $username, $password, $role, $trang_thai);
            $ok_exec = mysqli_stmt_execute($stmt_i);
            $ok += $ok_exec ? 1 : 0;
            if (!$ok_exec) $errors[]=['row'=>$idx+2,'reason'=>mysqli_error($conn)];
        }
    }
    mysqli_commit($conn);
    mysqli_stmt_close($stmt_s); mysqli_stmt_close($stmt_i); mysqli_stmt_close($stmt_u); mysqli_close($conn);
    return compact('total','ok','errors');
}

?>
