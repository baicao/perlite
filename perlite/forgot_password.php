<?php
require_once __DIR__ .'/config.php';
require_once __DIR__ .'/email_helper.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // 检查邮箱是否存在
    $stmt = $app_conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // 生成重置令牌
        $reset_token = bin2hex(random_bytes(32));
        
        // 更新数据库中的重置令牌
        $update_stmt = $app_conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $reset_token, $email);
        $update_stmt->execute();
        
        if ($update_stmt->affected_rows > 0) {
            // 发送重置密码邮件
            $reset_link = SITE_URL . "reset_password.php?token=" . $reset_token;
            $to = $email;
            $subject = "密码重置请求";
            $email_message = "<p>请点击以下链接重置您的密码：</p><p><a href=\"$reset_link\">重置密码</a></p>";
            
            if (send_email($to, $subject, $email_message, true)) {
                $message = "重置密码链接已发送到您的邮箱。请通过邮箱中的链接重置密码。";
                // 添加 JavaScript 自动跳转
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000); // 3秒后跳转
                </script>";
            } else {
                $message = "发送邮件时出错，请稍后再试。";
            }
        } else {
            $message = "更新重置令牌时出错，请稍后再试。";
        }
        
        $update_stmt->close();
    } else {
        $message = "该邮箱地址不存在。";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>忘记密码 Forgot Password</title>
    <link rel="stylesheet" href=".styles/login.css">
    <script>
        function disableButton() {
            var button = document.getElementById('resetPasswordBtn');
            button.disabled = true;
            button.textContent = '处理中...';
        }
    </script>
</head>
<body>
    <form method="POST" onsubmit="disableButton()">
        <h2>忘记密码 Forgot Password</h2>
        <?php if ($message): ?>
            <p class="message <?php echo strpos($message, '已发送') !== false ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <input type="email" name="email" placeholder="邮箱 Email" required>
        <button type="submit" id="resetPasswordBtn">重置密码 Reset Password</button>
        <div class="additional-links">
            <a href="login.php" class="link-button">返回登录 Back to Login</a>
        </div>
    </form>
</body>
</html>