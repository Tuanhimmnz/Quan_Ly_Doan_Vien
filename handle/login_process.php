<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/auth.php';

// Chỉ xử lý khi form login submit bằng POST và có name="login"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    handleLogin();
}

/**
 * Xử lý đăng nhập
 */
function handleLogin() {
    $conn = getDbConnection();

    // Lấy dữ liệu từ form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kiểm tra input rỗng
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
        header('Location: ../index.php');
        exit();
    }

    // Kiểm tra trong DB
    $user = authenticateUser($conn, $username, $password);

    if ($user) {
        // Ghi session cơ bản
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['success']  = 'Đăng nhập thành công!';

        mysqli_close($conn);

        // Chuyển hướng đến trang danh sách đoàn viên
        header('Location: ../views/doanvien.php');
        exit();
    }

    // Sai tài khoản hoặc mật khẩu
    $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    mysqli_close($conn);
    header('Location: ../index.php');
    exit();
}
?>
