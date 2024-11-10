<?php
require_once __DIR__ .'/config.php';

$error_message = '';

// 生成 CSRF 令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 生成 sessionid
if (empty($_SESSION['unique_id'])) {
    $_SESSION['unique_id'] = bin2hex(random_bytes(16));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 验证 CSRF 令牌
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "无效的请求。请重试。";
    } else {
        if (isset($_POST['account']) && isset($_POST['password'])) {
            $account = filter_var($_POST['account'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            log_message("Login attempt for account: $account");

            $stmt = $app_conn->prepare("SELECT id, password, email, phone_number, is_verified, is_phone_verified, subscription_expiry FROM users WHERE email = ?");
            $stmt->bind_param("s", $account);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id, $hashed_password, $email, $phone_number, $is_verified, $is_phone_verified, $subscription_expiry);
            if ($stmt->fetch()) {
                if (password_verify($password, $hashed_password)) {
                    log_message("Password verified successfully for account: $account");
                    // Email login
                    if ($is_verified) {
                        session_regenerate_id(true); // 重新生成会话 ID
                        $_SESSION['user_id'] = $id;
                        $_SESSION['user_email'] = $account;
                        $_SESSION['subscription_expiry'] = $subscription_expiry;
                        $_SESSION['last_activity'] = time(); // 设置最后活动时间
                        $_SESSION['expire_time'] = 7 * 24 * 60 * 60; // 设置超时时间为7天
                        log_message("Login successful for user ID: $id");
                        header("Location: ".SITE_URL);
                        exit();
                    } else {
                        log_message("Email not verified for user ID: $id");
                        header("Location: verify.php?email=" . urlencode($account));
                        exit();
                    }
                } else {
                    log_message("Password verification failed for email: $email");
                    $error_message = "密码不匹配。Password is wrong.";
                }
            } else {
                log_message("No user found for email: $email");
                $error_message = "邮箱不存在，请先注册。Invalid email, register first.";
            }
        } elseif (isset($_POST['phone_number']) && isset($_POST['verification_code'])) {
            // Phone number verification login
            $phone_number = filter_var($_POST['phone_number'], FILTER_SANITIZE_STRING);
            $verification_code = $_POST['verification_code'];
            
            // Check if phone number exists in the database and verification code matches
            $stmt = $app_conn->prepare("SELECT * FROM verification_codes WHERE phone_number = ? AND code = ? AND create_time > (NOW() - INTERVAL 1 MINUTE)");
            $stmt->bind_param("si", $phone_number, $verification_code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // 验证码验证成功，查询用户数据
                $stmt->close();

                $stmt = $app_conn->prepare("SELECT id, email, subscription_expiry FROM users WHERE phone_number = ?");
                $stmt->bind_param("s", $phone_number);
                $stmt->execute();
                $stmt->bind_result($id, $email, $subscription_expiry);

                if ($stmt->fetch()) {
                    // Login successful
                    session_regenerate_id(true); // 重新生成会话 ID
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['subscription_expiry'] = $subscription_expiry;
                    $_SESSION['last_activity'] = time(); // 设置最后活动时间
                    $_SESSION['expire_time'] = 7 * 24 * 60 * 60; // 设置超时时间为7天
                    log_message("Login successful for user ID: $id");
                    header("Location: ".SITE_URL);
                    exit();
                } else {
                    log_message("User data not found for phone number: $phone_number");
                    $error_message = "用户数据未找到。User data not found.";
                }
            } else {
                // Invalid verification code or code expired
                log_message("Invalid verification code or code expired");
                $error_message = "验证码无效或超时。Invalid verification code or code expired.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录页面</title>
    <link rel="stylesheet" href=".styles/login.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        
    </style>
</head>
<body>
    <div class="login-container">
        <div class="tab-container">
            <div id="tab-phone" class="tab active">验证码登录<br>Verification Code Login</div>
            <div id="tab-account" class="tab">邮箱密码登录<br>Password Login</div>
            
        </div>

        <!-- 验证码登录表单 -->
        <form id="phone-login-form" class="tab-content active" method="post" >
            <?php if ($error_message): ?>
                <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="country-code">
                <select name="country_code">
                    <option value="+86">中国 +86</option>
                    <option value="+852">香港 +852</option>
                    <!-- 可以添加其他国家的区号 -->
                </select>
                <input type="text" name="phone_number" placeholder="手机号 Phone">
            </div>
            <button type="button" id="send-code-btn" onclick="sendVerificationCode()">获取短信验证码 Verification Code</button>
            <input type="text" name="verification_code" class="verification-code-input" placeholder="输入 6 位短信验证码 Verification Code">
            <button type="submit">登录 Login</button>
            <div class="additional-links">
                <a href="register.php" class="link-button">注册 Register</a>
            </div>
        </form>

        <!-- 账号密码登录表单 -->
        <form id="account-login-form" class="tab-content" method="post" >
            <?php if ($error_message): ?>
                <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="text" name="account" placeholder="邮箱 Email ">
            <input type="password" name="password" placeholder="密码 Password">
            <button type="submit">登录 Login</button>
            <div class="additional-links">
                <a href="register.php" class="link-button">注册 Register</a>
                <a href="regiforgot_passwordster.php" class="link-button">忘记密码？Forgot Password?</a>
            </div>
        </form>
    </div>

    <script>
        // Tab切换逻辑
        document.getElementById('tab-phone').addEventListener('click', function() {
            setActiveTab('tab-phone', 'phone-login-form');
        });

        document.getElementById('tab-account').addEventListener('click', function() {
            setActiveTab('tab-account', 'account-login-form');
        });

        function setActiveTab(tabId, formId) {
            // 移除所有Tab的活动状态
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(form => form.classList.remove('active'));

            // 设置选中的Tab和表单
            document.getElementById(tabId).classList.add('active');
            document.getElementById(formId).classList.add('active');
        }

        function sendVerificationCode() {
            const phoneNumber = document.querySelector("input[name='phone_number']").value;
            let errorMessageElement = document.querySelector("#phone-login-form .message.error");
            
            // 如果 .message.error 元素不存在，则创建它
            if (!errorMessageElement) {
                errorMessageElement = document.createElement('p');
                errorMessageElement.className = 'message error';
                const form = document.getElementById('phone-login-form');
                form.insertBefore(errorMessageElement, form.firstChild);
            }
            
            if (!phoneNumber) {
                errorMessageElement.textContent = "请输入手机号";
                return;
            }

            // 简单的人机验证：生成一个随机的数学问题
            const num1 = Math.floor(Math.random() * 10) + 1;
            const num2 = Math.floor(Math.random() * 10) + 1;
            const answer = prompt(`请回答以下问题以验证您不是机器人: ${num1} + ${num2} = ?`);

            if (parseInt(answer) !== (num1 + num2)) {
                errorMessageElement.textContent = "验证失败。 Verify failed.";
                return;
            }

            // 发送 AJAX 请求到后端
            fetch('send_sms.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ phone_number: phoneNumber })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    errorMessageElement.textContent = `验证码已发送至 ${phoneNumber}`;
                    startCountdown();
                } else {
                    if(data.message == "手机号不存在"){
                        errorMessageElement.textContent = "手机号不存在，请先注册。Phone not exist, please register."
                    }else{
                        errorMessageElement.textContent = "服务器遇到问题，请稍后再试。";
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessageElement.textContent = "发送验证码失败。Send verification code failed.";
            });
        }

        function startCountdown() {
            const sendCodeBtn = document.getElementById('send-code-btn');
            sendCodeBtn.disabled = true;
            let countdown = 60;

            const timer = setInterval(() => {
                countdown--;
                sendCodeBtn.innerText = `${countdown}秒后重新获取`;

                if (countdown === 0) {
                    clearInterval(timer);
                    sendCodeBtn.disabled = false;
                    sendCodeBtn.innerText = '获取短信验证码';
                }
            }, 1000);
        }

    </script>
</body>
</html>

