<?php
// [FITDNU-ADD] CSRF helpers and small security utilities

require_once __DIR__ . '/auth.php';

// [FITDNU-ADD] Generate a CSRF token for a form key and store in session
function csrf_generate_token($form_key) {
    ensureSessionStarted();
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_' . $form_key] = $token;
    return $token;
}

// [FITDNU-ADD] Validate CSRF token for a form key
function csrf_validate_token($form_key, $token) {
    ensureSessionStarted();
    if (!isset($_SESSION['csrf_' . $form_key])) return false;
    $valid = hash_equals($_SESSION['csrf_' . $form_key], (string)$token);
    if ($valid) {
        // One-time token usage
        unset($_SESSION['csrf_' . $form_key]);
    }
    return $valid;
}

// [FITDNU-ADD] Require CSRF on POST, redirect on failure
function csrf_require_post($form_key, $redirect) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return; // only enforce on POST
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_validate_token($form_key, $token)) {
        $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF). Vui lòng thử lại!';
        header('Location: ' . $redirect);
        exit();
    }
}

?>

