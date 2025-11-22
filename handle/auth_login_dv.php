<?php
// handle/auth_login_dv.php (bản điều hướng user -> /views/user/index.php)
require_once __DIR__ . '/../functions/db_connection.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
  header('Location: ../views/auth/login.php?e=missing');
  exit;
}

$conn = getDbConnection();
$sql  = "SELECT id, username, password_hash, role, status, ho_ten, ma_sv, lop_id, sdt, email FROM doanvien WHERE username=? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) { die("Lỗi prepare login: " . mysqli_error($conn)); }
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);
mysqli_close($conn);

if (!$row || ($row['status'] ?? 'active') !== 'active') {
  header('Location: ../views/auth/login.php?e=invalid');
  exit;
}

if (!password_verify($password, $row['password_hash'])) {
  header('Location: ../views/auth/login.php?e=invalid');
  exit;
}

$_SESSION['user'] = [
  'id' => (int)$row['id'],
  'username' => $row['username'],
  'role' => $row['role'] ?: 'user',
  'ho_ten' => $row['ho_ten'] ?? null,
  'ma_sv'  => $row['ma_sv'] ?? null,
  'lop_id' => $row['lop_id'] ?? null,
  'sdt'    => $row['sdt'] ?? null,
  'email'  => $row['email'] ?? null,
];

if (($_SESSION['user']['role'] ?? 'user') === 'admin') {
  header('Location: ../views/admin/khai_bao/list.php?status=pending');
} else {
  header('Location: ../views/user/index.php');
}
