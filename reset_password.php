<?php
require_once __DIR__ .'/config.php';

$error_message = '';
$success_message = '';

// 生成 CSRF 令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // 验证令牌
    $stmt = $app_conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows != 1) {
        $error_message = "验证码无效或过期";
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 验证 CSRF 令牌
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "无效的请求。请重试。";
    } else {
        $token = $_POST['token'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            // 更新密码
            log_message("Reset password: $new_password");
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $app_conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
            $stmt->bind_param("ss", $hashed_password, $token);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success_message = "密码已成功重置, 请使用新密码登录";
                // 添加 JavaScript 自动跳转
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000); // 3秒后跳转
                </script>";
            } else {
                $error_message = "密码重置失败，请重试";
            }
            $stmt->close();
        } else {
            $error_message = "两次输入的密码不匹配请重试";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=".styles/login.css">
    <title>重置密码</title>
</head>
<body>
    <div class="login-container">
        <form method="POST">
            <h2>重置密码</h2>
            <?php if ($error_message): ?>
                <p class="message error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="message success"><?php echo $success_message; ?></p>
            <?php else: ?>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                <input type="password" name="new_password" placeholder="新密码" required>
                <input type="password" name="confirm_password" placeholder="确认新密码" required>
                <button type="submit">重置密码</button>
            <?php endif; ?>
            <div class="additional-links">
                <a href="login.php" class="link-button">返回登录</a>
            </div>
        </form>
    </div>
</body>
</html>