<?php
require_once __DIR__ .'/config.php';

$phone = isset($_GET['phone']) ? $_GET['phone'] : '';
$message = '';
$countdown = 0;

// 生成 CSRF 令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 验证 CSRF 令牌
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "无效的请求。请重试。";
        log_message("CSRF token mismatch.");
    } else {
        if (isset($_POST['phone_number']) && isset($_POST['country_code']) && isset($_POST['verification_code'])) {
            $phone_number = htmlspecialchars(trim($_POST['phone_number']));
            $input_code = htmlspecialchars(trim($_POST['verification_code']));
            $country_code = htmlspecialchars(trim($_POST['country_code']));
            log_message("Attempting to verify phone number: $country_code$phone_number with code: $input_code");
            $send_phone = $country_code.$phone_number;
            $stmt = $app_conn->prepare("SELECT * FROM verification_codes WHERE phone_number = ? AND code = ? AND create_time > (NOW() - INTERVAL 3 MINUTE) ORDER BY create_time DESC LIMIT 1");
            $stmt->bind_param("ss", $send_phone, $input_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                log_message("Verification code matched for phone number: $phone_number");

                $stmt = $app_conn->prepare("UPDATE users SET is_phone_verified = 1 WHERE phone_number = ? AND country_code= ?");
                $stmt->bind_param("s", $phone_number, $country_code);
                $stmt->execute();
                
                // 更新会话以反映用户的验证状态和其他信息
                $_SESSION['user']['is_phone_verified'] = 0;                
                log_message("Phone verification successful for user ID: " . $user['id']);
                // 立即跳转到主页
                header("Location: " . SITE_URL);
                exit();
            } else {
                log_message("Invalid verification code or code expired for phone number: $phone_number");
                $message = "验证码无效或超时。Invalid verification code or code expired.";
            }
        } else {
            log_message("Invalid input: phone number or verification code missing.");
            $message = "无效输入。Invalid input.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>验证手机号码</title>
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
        <h2>验证手机号码</h2>
        <p class="message center-message">
        <?php 
        if ($message){echo htmlspecialchars($message);}
        ?>
        </p>
        <form method="POST" class="verify-form" id="verifyCodeForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="country-code">
                <select name="country_code" disabled>
                    <option value="+86" <?php echo (strpos($phone, '+86') === 0) ? 'selected' : ''; ?>>中国 +86</option>
                    <option value="+852" <?php echo (strpos($phone, '+852') === 0) ? 'selected' : ''; ?>>香港 +852</option>
                    <!-- 可以添加其他国家的区号 -->
                </select>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone); ?>" placeholder="手机号码" disabled required>
            </div>
            <button type="button" id="send-code-btn" onclick="sendVerificationCode()">获取短信验证码 Verification Code</button>
            <input type="text" name="verification_code" placeholder="请输入6位验证码" required>
            <button type="submit" name="verify">验证 Verify</button>
        </form>

        <!-- 跳过按钮 -->
        <div class="center-message">
            <button onclick="window.location.href='<?php echo SITE_URL; ?>'" >跳过 Skip</button>
        </div>
    </div>
    <script>
        function sendVerificationCode() {
            const phoneNumber = document.querySelector("input[name='phone_number']").value;
            const country_code = document.querySelector("select[name='country_code']").value;
            let errorMessageElement = document.querySelector("#verifyCodeForm .message.error");
            
            if (!phoneNumber) {
                errorMessageElement.textContent = "请输入手机号";
                return;
            }

            // 简单的人机验证：生成一个随机的数学问题
            const num1 = Math.floor(Math.random() * 10) + 1;
            const num2 = Math.floor(Math.random() * 10) + 1;
            const answer = prompt(`请回答以下问题以验证您不是机器人: ${num1} + ${num2} = ?`);

            if (parseInt(answer) !== (num1 + num2)) {
                errorMessageElement.textContent = "验证失败，请重试。";
                return;
            }

            // 发送 AJAX 请求到后端
            fetch('send_sms.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ phone_number: phoneNumber, country_code: country_code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    errorMessageElement.textContent = `验证码已发送至 ${phoneNumber}`;
                    startCountdown();
                } else {
                    errorMessageElement.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessageElement.textContent = "发送验证码失败，请重试。";
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