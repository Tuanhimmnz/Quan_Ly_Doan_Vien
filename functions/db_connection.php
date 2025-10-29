<?php
function getDbConnection() {
    $host = "localhost";
    $user = "root";
    $pass = "123456789";
    $dbname = "quanly_doanvien";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Kết nối DB thất bại: " . mysqli_connect_error());
    }

    // set charset UTF-8 để lưu tiếng Việt
    mysqli_set_charset($conn, "utf8mb4");

    return $conn;
}
?>
