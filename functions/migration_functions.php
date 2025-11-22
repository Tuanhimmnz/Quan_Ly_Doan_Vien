<?php
// [FITDNU-ADD] Migration runner helpers
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/auth.php';

// [FITDNU-ADD] Execute a single SQL script content with multi_query
function run_sql_script($conn, $sql) {
    // Normalize line endings
    $sql = str_replace(["\r\n", "\r"], "\n", $sql);
    // Ensure trailing semicolon for multi_query stability
    if (substr(trim($sql), -1) !== ';') {
        $sql .= ";";
    }

    $ok = mysqli_multi_query($conn, $sql);
    if (!$ok) {
        return [false, mysqli_error($conn)];
    }

    // Flush remaining result sets
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_more_results($conn) && mysqli_next_result($conn));

    return [true, null];
}

// [FITDNU-ADD] Run all .sql migrations in db/migrations sorted by filename
function run_all_migrations() {
    $baseDir = dirname(__DIR__);
    $migDir = $baseDir . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'migrations';
    $results = [];

    if (!is_dir($migDir)) {
        return [['file' => null, 'ok' => false, 'error' => 'Thư mục migrations không tồn tại: ' . $migDir]];
    }

    $files = glob($migDir . DIRECTORY_SEPARATOR . '*.sql');
    sort($files, SORT_NATURAL);

    $conn = getDbConnection();
    foreach ($files as $file) {
        $sql = file_get_contents($file);
        if ($sql === false) {
            $results[] = ['file' => basename($file), 'ok' => false, 'error' => 'Không đọc được file'];
            continue;
        }
        [$ok, $err] = run_sql_script($conn, $sql);
        $results[] = ['file' => basename($file), 'ok' => $ok, 'error' => $err];
        if (!$ok) {
            // Continue to next but record error
        }
    }
    mysqli_close($conn);
    return $results;
}

?>

