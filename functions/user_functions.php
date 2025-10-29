<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách classs từ database
 * @return array Danh sách classs
 */
function getAllclasss() {
    $conn = getDbConnection();
    
    // Truy vấn lấy tất cả classs
    $sql = "SELECT id, class_code, class_name FROM classs ORDER BY id";
    $result = mysqli_query($conn, $sql);
    
    $classs = [];
    if ($result && mysqli_num_rows($result) > 0) {
        // Lặp qua từng dòng trong kết quả truy vấn $result
        while ($row = mysqli_fetch_assoc($result)) { 
            $classs[] = $row; // Thêm mảng $row vào cuối mảng $classs
        }
    }
    
    mysqli_close($conn);
    return $classs;
}

/**
 * Thêm class mới
 * @param string $class_code Mã sinh viên
 * @param string $class_name Tên sinh viên
 * @return bool True nếu thành công, False nếu thất bại
 */
function addclass($class_code, $class_name) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO classs (class_code, class_name) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $class_code, $class_name);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một class theo ID
 * @param int $id ID của class
 * @return array|null Thông tin class hoặc null nếu không tìm thấy
 */
function getclassById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, class_code, class_name FROM classs WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $class = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $class;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin class
 * @param int $id ID của class
 * @param string $class_code Mã sinh viên mới
 * @param string $class_name Tên sinh viên mới
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateclass($id, $class_code, $class_name) {
    $conn = getDbConnection();
    
    $sql = "UPDATE classs SET class_code = ?, class_name = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $class_code, $class_name, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa class theo ID
 * @param int $id ID của class cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteclass($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM classs WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}
?>
