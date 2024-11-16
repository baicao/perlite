<?php
require_once __DIR__ .'/config.php';
require_once __DIR__ .'/handlers/email_helper.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';
$message = '';
$countdown = 0;

// 生成 CSRF 令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if(!isset($_SESSION['user_email'])){
    header("Location: " . SITE_URL);
    exit(); 
}
$session_email = $_SESSION['user_email'] ;
log_message("Session email $session_email");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 验证 CSRF 令牌
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "无效的请求。请重试。";
    } else {
        log_message("Post email ".json_encode($_POST));
        if (!isset($_POST['email']) or $email != $session_email) {
            $message = "无效邮箱";
            // 立即跳转到主页
            header("Location: " . SITE_URL);
            exit();
        }else{
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if (isset($_POST['verify'])) {
                // 验证码验证逻辑
                $input_code = $_POST['verification_code'];
                log_message("Login with $email $input_code");

                $stmt = $app_conn->prepare("SELECT id, password, email, phone_number, is_verified, is_phone_verified, subscription_expiry FROM users WHERE email = ? AND verification_code = ?");
                $stmt->bind_param("ss", $email, $input_code);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($id, $hashed_password, $email, $phone_number, $is_verified, $is_phone_verified, $subscription_expiry);

                if ($stmt->fetch()) {
                    $stmt = $app_conn->prepare("UPDATE users SET is_verified = 1, verification_code = '' WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    
                    // 更新会话以反映用户的验证状态和其他信息
                    session_regenerate_id(true); // 重新生成会话 ID
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_email'] = $account;
                    $_SESSION['phone_number'] = $phone_number;
                    $_SESSION['is_verified'] = $is_verified;
                    $_SESSION['is_phone_verified'] = $is_phone_verified;
                    $_SESSION['subscription_expiry'] = $subscription_expiry;
                    $_SESSION['last_activity'] = time(); // 设置最后活动时间
                    $_SESSION['expire_time'] = 7 * 24 * 60 * 60; // 设置超时时间为7天
                    
                    // 立即跳转到主页
                    header("Location: " . SITE_URL);
                    exit();
                } else {
                    $message = "验证码无效。请重试。";
                }
            } else {
                // 发送验证码逻辑
                if (isset($_SESSION['last_verification_time'])) {
                    $countdown = 60 - (time() - $_SESSION['last_verification_time']);
                }
                
                if ($countdown <= 0) {
                    $_SESSION['last_verification_time'] = time();
                    $countdown = 60;
                    
                    $verification_code = sprintf('%06d', mt_rand(0, 999999));
                    
                    // 更新数据库中的验证
                    $stmt = $app_conn->prepare("UPDATE users SET verification_code = ? WHERE email = ?");
                    $stmt->bind_param("ss", $verification_code, $email);
                    
                    if ($stmt->execute()) {
                        // 发送验证邮件
                        $subject = '您的 ChangEdu 邮箱验证码';
                        $message = "<h2>您的验证码</h2><span class='code'>$verification_code</span>";
                        $ret = send_email($email, $subject, $message);
                        $message = "验证码已发送到您的邮箱，请查收。";
                        if (!$ret) {
                            $message = "发送验证码失败，请稍后重试或联系管理员。";
                        }
                    } else {
                        $message = "更新验证码失败，请稍后重试。";
                    }
                } else {
                    $message = "请等待 {$countdown} 秒后再次请求验证码。";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>验证邮箱</title>
    <link rel="stylesheet" href=".styles/login.css">
    <script>
        function startCountdown(duration) {
            var button = document.getElementById('sendCodeBtn');
            var timer = duration;
            var intervalId = setInterval(function () {
                if (--timer < 0) {
                    clearInterval(intervalId);
                    button.disabled = false;
                    button.textContent = '发送验证码';
                } else {
                    button.disabled = true;
                    button.textContent = '重新发送 (' + timer + ')';
                }
            }, 1000);
        }

        function disableButton() {
            var button = document.getElementById('sendCodeBtn');
            button.disabled = true;
            button.textContent = '发送中...';
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h2>验证邮箱</h2>
        <?php if ($message): ?>
            <p class="message center-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <form method="POST" class="verify-form" id="sendCodeForm" onsubmit="disableButton()">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="邮箱地址" class="readonly-input" readonly required>
                <button type="submit" id="sendCodeBtn" <?php echo $countdown > 0 ? 'disabled' : ''; ?>>
                    <?php echo $countdown > 0 ? "重新发送 ({$countdown})" : '发送验证码'; ?>
                </button>
            </div>
        </form>
        
        <form method="POST" class="verify-form" id="verifyCodeForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div>
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="text" name="verification_code" placeholder="请输入6位验证码" required>
                <button type="submit" name="verify">验证</button>
            </div>
        </form>

        <!-- 跳过按钮 -->
        <div class="center-message">
            <button onclick="window.location.href='<?php echo SITE_URL; ?>'">跳过</button>
        </div>
    </div>

    <?php if ($countdown > 0): ?>
    <script>
        startCountdown(<?php echo $countdown; ?>);
    </script>
    <?php endif; ?>
</body>
</html>