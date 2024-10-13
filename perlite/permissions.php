<?php
require_once __DIR__ . '/config.php';

// 检查用户是否有访问页面的权限
function hasPageAccess($userId, $pageName, $app_conn) {
    $pageName = $pageName . ".md";
    log_message("Checking access for user: $userId on page: $pageName");

    $query = "SELECT page_name, permission_id FROM permission_pages 
              WHERE page_name = ?";
    $stmt = $app_conn->prepare($query);
    $stmt->bind_param("s", $pageName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        log_message("No specific permissions required for this page.");
        return true; // \面，注册用户可以访问
    }

    $page_permissions = array();
    while ($row = $result->fetch_assoc()) {
        $page_permissions[] = $row['permission_id'];
    }

    $query = "SELECT permission_id FROM user_permissions 
    WHERE user_id = ?";
    $stmt2 = $app_conn->prepare($query);
    $stmt2->bind_param("i", $userId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $user_permissions = [];
    while ($row = $result2->fetch_assoc()) {
        $user_permissions[] = $row['permission_id'];
    }

    foreach ($page_permissions as $permission_id) {
        if (in_array($permission_id, $user_permissions)) {
            log_message("Access granted for user: $userId on page: $pageName");
            return true; // 用户有权限访问
        }
    }
    log_message("Access denied for user: $userId on page: $pageName");
    return false; // 用户没有权限访问
}

?>
