<?php
require_once __DIR__ . '/config.php';

$error_message = "";
$disabled = ""; // 用于控制按钮是否禁用
// 获取用户信息
$user = $_SESSION['user'] ?? null;

// 检查手机号
if (!$user || !isset($user['phone_number'])) {
    // 如果用户未登录或手机号为 null，检查 URL 参数
    $phone_number = $_GET['phone_number'] ?? null;
    $country_code = $_GET['country_code'] ?? null;

    if ($phone_number === null && $country_code === null) {
        // 如果 URL 参数也没有，重定向到登录页面
        header("Location: login.php");
        exit();
    } else {
        // 如果 URL 参数存在，使用它们
        $user['phone_number'] = $phone_number;
        $user['country_code'] = $country_code;
    }
}

// 检查手机号是否已验证
if (isset($user['is_phone_verified']) && $user['is_phone_verified'] == 1) {
    // 如果手机号已验证，重定向到登录页面
    header("Location: login.php");
    exit();
}

// 继续处理验证手机号码的逻辑
$phone = isset($user['phone_number']) ? $user['phone_number'] : '';

// 生成 CSRF 令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 检查手机号是否已被其他账号绑定
$stmt = $app_conn->prepare("SELECT * FROM users WHERE phone_number = ? AND country_code = ?");
$stmt->bind_param("ss", $user['phone_number'], $user['country_code']);
$stmt->execute();
$user_check = $stmt->get_result();

if ($user_check->num_rows > 0) {
    // 手机号已被其他账号绑定
    $error_message = '<p style="color: red;">手机号只能绑定一个账号，该手机号已经被别的账号绑定</p>';
    $disabled = "disabled"; // 设置按钮为禁用
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 验证 CSRF 令牌
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "无效的请求，请重试";
    } else {
        // 处理验证逻辑
        if (isset($_POST['verification_code'])) {
            $input_code = htmlspecialchars(trim($_POST['verification_code']));
            $send_phone = $user['country_code'] . $user['phone_number'];

            // 查询数据库以验证验证码
            $stmt = $app_conn->prepare("SELECT * FROM verification_codes WHERE phone_number = ? AND code = ? AND create_time > (NOW() - INTERVAL 3 MINUTE) ORDER BY create_time DESC LIMIT 1");
            $stmt->bind_param("ss", $send_phone, $input_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // 验证成功，更新用户状态
                $stmt = $app_conn->prepare("UPDATE users SET is_phone_verified = 1, phone_number=?, country_code=? WHERE id = ?");
                $stmt->bind_param("ssi", $user['phone_number'], $user['country_code'], $user["id"]);
                $stmt->execute();

                // 更新会话以反映用户的验证状态
                $_SESSION['user']['is_phone_verified'] = 1;

                // 跳转到主页或其他页面
                header("Location: " . SITE_URL);
                exit();
            } else {
                $error_message = '<p style="color: red;">验证码无效或超时</p>';
            }
        } else {
            $error_message = '<p style="color: red;">请输入验证码</p>';
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
    <link rel="stylesheet" href=".styles/login.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="login-container">
        <h2>验证手机号码</h2>
        <p class="message error"><?php if ($error_message) {echo $error_message;} ?></p>
        <form method="POST" class="verify-form" id="verifyCodeForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="country-code">
                <select name="country_code" disabled>
                    <option value="+86" <?php echo (strpos($phone, '+86') === 0) ? 'selected' : ''; ?>>中国 +86</option>
                </select>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone); ?>" placeholder="手机号码" disabled required>
            </div>
            <button type="button" id="send-code-btn" onclick="sendVerificationCode()" <?php echo $disabled; ?>>获取短信验证码</button>
            <input type="text" name="verification_code" placeholder="请输入6位验证码" required>
            <button type="submit" name="verify" <?php echo $disabled; ?>>验证</button>
        </form>
        <div class="center-message">
            <button onclick="window.location.href='<?php echo SITE_URL; ?>'" <?php echo $disabled; ?>>跳过</button>
        </div>
    </div>
    <script>
        function sendVerificationCode() {
            const phoneNumber = document.querySelector("input[name='phone_number']").value;
            const countryCode = document.querySelector("select[name='country_code']").value;
            let errorMessageElement = document.querySelector("#phone-login-form .message.error");
 
            if (!phoneNumber) {
                $('.message.error').html('<p style="color: red;">请输入手机号</p>');
                return;
            }

            // 简单的人机验证：生成一个随机的数学问题
            const num1 = Math.floor(Math.random() * 10) + 1;
            const num2 = Math.floor(Math.random() * 10) + 1;
            const answer = prompt(`请回答以下问题以验证您不是机器人: ${num1} + ${num2} = ?`);

            if (parseInt(answer) !== (num1 + num2)) {
                $('.message.error').html('<p style="color: red;">验证失败</p>');
                return;
            }
            // 发送 AJAX 请求到后端
            fetch('handlers/sms_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' // 设置请求头为 JSON
                },
                body: JSON.stringify({ phone_number: phoneNumber, country_code: countryCode, type:"verify" }) // 将数据转换为 JSON 字符串
            })
            .then(response => response.json())
            .then(data => {
                console.log(data)
                if (data.rs == 1) {
                    $('.message.error').html('<p style="color: green;">验证码已发送至'+phoneNumber+'</p>');
                    startCountdown();
                } else {
                    $('.message.error').html('<p style="color: red;">'+data.message+'</p>');
                }
            })
            .catch(error => {
                $('.message.error').html('<p style="color: red;">发送验证码失败</p>');
            });
        }

        function startCountdown() {
            const sendCodeBtn = document.getElementById('send-code-btn');
            sendCodeBtn.disabled = true;
            let countdown = 180;

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