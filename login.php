<?php
require_once __DIR__ .'/config.php';

$error_message = '';

// 生成 CSRF 令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 验证 CSRF 令牌
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "无效的请求。请重试。";
    } else {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        log_message("Login attempt for email: $email");

        $stmt = $app_conn->prepare("SELECT id, password, is_verified, subscription_expiry FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $is_verified, $subscription_expiry);

        if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
                log_message("Password verified successfully for email: $email");
                if ($is_verified) {
                    session_regenerate_id(true); // 重新生成会话 ID
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['subscription_expiry'] = $subscription_expiry;
                    $_SESSION['last_activity'] = time(); // 设置最后活动时间
                    $_SESSION['expire_time'] = 7 * 24 * 60 * 60; // 设置超时时间为7天
                    $_SESSION['unique_id'] = bin2hex(random_bytes(16)); // 生成唯一ID
                    log_message("Login successful for user ID: $id");
                    header("Location: ".SITE_URL);
                    exit();
                } else {
                    log_message("Email not verified for user ID: $id");
                    header("Location: verify.php?email=" . urlencode($email));
                    exit();
                }
            } else {
                log_message("Password verification failed for email: $email");
                $error_message = "邮箱或密码无效。Invalid email or password.";
            }
        } else {
            log_message("No user found for email: $email");
            $error_message = "邮箱或密码无效。Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=".styles/login.css" type="text/css">
    <title>登录 Login</title>
</head>
<body>
    <form method="POST">
        <h2>登录 Login</h2>
        <?php if ($error_message): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="email" name="email" placeholder="邮箱 Email" required>
        <input type="password" name="password" placeholder="密码 Password" required>
        <button type="submit">登录 Login</button>
        <div class="additional-links">
            <a href="forgot_password.php" class="link-button">忘记密码？Forgot Password?</a>
            <a href="register.php" class="link-button">注册 Register</a>
        </div>
    </form>
</body>
</html>
