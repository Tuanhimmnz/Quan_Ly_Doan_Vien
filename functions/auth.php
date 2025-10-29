<?php
/**
 * Các hàm xác thực / session cho hệ thống Quản lý Đoàn viên
 */

function ensureSessionStarted() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Bắt buộc đăng nhập: nếu chưa login thì đẩy về trang index.php
 */
function checkLogin($redirectPath = '../index.php') {
    ensureSessionStarted();

    if (
        !isset($_SESSION['user_id']) ||
        !isset($_SESSION['username']) ||
        !isset($_SESSION['role'])
    ) {
        $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này!';
        header('Location: ' . $redirectPath);
        exit();
    }
}

/**
 * Đăng xuất: xóa session và quay về trang login
 */
function logout($redirectPath = '../index.php') {
    ensureSessionStarted();

    session_unset();
    session_destroy();

    // tạo session mới chỉ để báo success
    session_start();
    $_SESSION['success'] = 'Đăng xuất thành công!';

    header('Location: ' . $redirectPath);
    exit();
}

/**
 * Lấy thông tin user hiện tại (id, username, role)
 */
function getCurrentUser() {
    ensureSessionStarted();

    if (
        isset($_SESSION['user_id']) &&
        isset($_SESSION['username']) &&
        isset($_SESSION['role'])
    ) {
        return [
            'id'       => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role'     => $_SESSION['role']
        ];
    }
    return null;
}

/**
 * Kiểm tra nhanh có đăng nhập chưa (không redirect)
 */
function isLoggedIn() {
    ensureSessionStarted();
    return (
        isset($_SESSION['user_id']) &&
        isset($_SESSION['username']) &&
        isset($_SESSION['role'])
    );
}

/**
 * Xác thực đăng nhập với DB:
 * - tìm user theo username
 * - so sánh password plain text (giống mẫu thầy)
 *
 * @param mysqli $conn
 * @param string $username
 * @param string $password
 * @return array|false  thông tin user nếu đúng, false nếu sai
 */
function authenticateUser($conn, $username, $password) {
    $sql = "SELECT id, username, password, role 
            FROM users 
            WHERE username = ? 
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // so sánh mật khẩu thô (giống cách bạn lưu 'tuan123')
        if ($password === $user['password']) {
            mysqli_stmt_close($stmt);
            return $user;
        }
    }

    mysqli_stmt_close($stmt);
    return false;
}
?>
