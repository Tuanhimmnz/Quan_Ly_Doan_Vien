<?php
// [FITDNU-ADD] Import process handler
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/security.php';
require_once __DIR__ . '/../functions/import_functions.php';
require_once __DIR__ . '/../functions/audit_functions.php';
checkLogin(__DIR__ . '/../index.php');

$user = getCurrentUser();

$action = $_POST['action'] ?? '';
if ($action === 'preview' || $action === 'import') {
    csrf_require_post('import_data', '../views/import/index.php');

    $entity = $_POST['entity'] ?? '';
    $skip_missing = !empty($_POST['skip_missing']);
    $update_on_dup = !empty($_POST['update_on_dup']);

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Vui lòng chọn tệp để tải lên.';
        header('Location: ../views/import/index.php');
        exit();
    }
    $tmp = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];
    [$rows, $parseErrors] = parse_upload_rows($tmp, $name);
    if ($parseErrors) {
        $_SESSION['error'] = implode("; ", $parseErrors);
        header('Location: ../views/import/index.php');
        exit();
    }

    if ($action === 'preview') {
        $_SESSION['import_preview'] = [
            'entity' => $entity,
            'name' => $name,
            'rows' => $rows,
            'opts' => ['skip_missing' => $skip_missing, 'update_on_dup' => $update_on_dup]
        ];
        header('Location: ../views/import/index.php?preview=1');
        exit();
    }

    // import
    $summary = ['total' => 0, 'ok' => 0, 'errors' => []];
    $opts = [
        'skip_missing' => $skip_missing,
        'update_on_dup' => $update_on_dup,
        'file_name' => $name
    ];
    switch ($entity) {
        case 'khoa': $summary = import_khoa_rows($rows, $opts); break;
        case 'lop': $summary = import_lop_rows($rows, $opts); break;
        case 'doanvien': $summary = import_doanvien_rows($rows, $opts); break;
        case 'khenthuong': $summary = import_khen_kyluat_rows($rows, 'Khen thưởng', $opts); break;
        case 'kyluat': $summary = import_khen_kyluat_rows($rows, 'Kỷ luật', $opts); break;
        case 'diemrenluyen': $summary = import_diemrenluyen_rows($rows, $opts); break;
        case 'sukien': $summary = import_sukien_rows($rows, $opts); break;
        case 'taikhoan': $summary = import_taikhoan_rows($rows, $opts); break;
        default:
            $_SESSION['error'] = 'Đối tượng import không hợp lệ.';
            header('Location: ../views/import/index.php');
            exit();
    }

    // Audit
    log_action((int)$user['id'], 'IMPORT', $entity, 0, null, ['file' => $name, 'summary' => $summary]);

    $_SESSION['import_result'] = $summary;
    header('Location: ../views/import/index.php?done=1');
    exit();
}

header('Location: ../views/import/index.php');
exit();
?>
