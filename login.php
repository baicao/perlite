<?php
require_once __DIR__ .'/config.php';

$error_message = '';
// 生成 CSRF 令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.rawgit.com/travist/jsencrypt/master/bin/jsencrypt.min.js"></script>
    </head>
<body>
    <div class="login-container">
        <div class="tab-container">
            <div id="tab-phone" class="tab active">验证码登录</div>
            <div id="tab-account" class="tab">邮箱密码登录</div>
            
        </div>

        <!-- 验证码登录表单 -->
        <form id="phone-login-form" class="tab-content active" method="post" >
            <p class="message error"><?php if ($error_message) {echo htmlspecialchars($error_message);} ?></p>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="country-code">
                <select name="country_code">
                    <option value="+86">中国 +86</option>
                    <!-- <option value="+852">香港 +852</option> -->
                    <!-- 可以添加其他国家的区号 -->
                </select>
                <input type="text" name="phone_number" placeholder="手机号">
            </div>
            <button type="button" id="send-code-btn" onclick="sendVerificationCode()">获取短信验证码</button>
            <input type="text" name="verification_code" class="verification-code-input" placeholder="输入 6 位短信验证码">
            <button type="submit">登录</button>
            <div class="additional-links">
                <a href="register.php" class="link-button">注册</a>
            </div>
        </form>

        <!-- 账号密码登录表单 -->
        <form id="account-login-form" class="tab-content" method="post" >
            <p class="message error"><?php if ($error_message) {echo htmlspecialchars($error_message);} ?></p>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="text" name="account" placeholder="邮箱 ">
            <input type="password" name="password" placeholder="密码">
            <button type="submit">登录</button>
            <div class="additional-links">
                <a href="register.php" class="link-button">注册</a>
                <a href="forgot_password.php" class="link-button">忘记密码？</a>
            </div>
        </form>
    </div>

    <script>
        function encryptData(data) {
            // 初始化 JSEncrypt 对象
            var encrypt = new JSEncrypt();
            
            // 公钥（从服务器获取公钥，可以通过 API 或直接嵌入到页面中）
            var publicKey = `-----BEGIN PUBLIC KEY-----
                MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz3/Ua47MBHrzSJ8Yc/5X
                HN+eTKZSooABvA7ADK0xhpOMg21SauCFQUT5KAYCt1+SMHyOmVzMjnDnWfmkskZl
                lKH0757khuUMxZTex8zEJAMRJPB2a/WPah9VezYckzds7dAN7bMPbUyoWoPWQcmY
                Aq3gXhSgHsxFQ8o4FjUNujWNL2qysIyZFUKPTs87JxdXXiVBB74nhWyvkWVDvhbM
                T5ln6WoMcH69Pdtt7yRShcu8p36/ghxatbTgDDm/HXgMRyIB5HWsbfa7+nBD52ml
                QgNpsIYwGIUraAAhAB7/GU5kMkVZeVAi2W6u4hRx9EWhxVpK3+xQSQxV/2AL3zsm
                vwIDAQAB
                -----END PUBLIC KEY-----`;

            // 设置公钥
            encrypt.setPublicKey(publicKey);
            // 加密数据
            var encrypted = encrypt.encrypt(data);
            return encrypted;
        }

        // Tab切换逻辑
        document.getElementById('tab-phone').addEventListener('click', function() {
            setActiveTab('tab-phone', 'phone-login-form');
        });

        document.getElementById('tab-account').addEventListener('click', function() {
            setActiveTab('tab-account', 'account-login-form');
        });

        $(document).ready(function() {
            $('#phone-login-form').submit(function(event) {
                event.preventDefault(); // 阻止表单的默认提交行为，从而避免页面刷新
                // 获取表单数据
                var country_code = $('select[name="country_code"]').val();
                var verification_code = $('input[name="verification_code"]').val();
                var phone_number = $('input[name="phone_number"]').val();
                var csrfToken = $('input[name="csrf_token"]').val();
                var formData = {
                    csrf_token: csrfToken,
                    country_code: country_code,
                    verification_code: verification_code,
                    phone_number: phone_number
                };
                $.ajax({
                    type: 'POST',
                    url: '/handlers/login_handler.php', // 指定处理表单的 PHP 文件
                    data: formData,
                    dataType: 'json', // 期待服务器返回 JSON 格式的数据
                    success: function(response) {
                        if (response.rs == 1) {
                            // 登录成功后的处理
                            $('.message.error').html('<p style="color: green;">' + response.message + '</p>');
                            window.location.replace(response.url);
                        } else if (response.rs == 0) {
                            let error_message = "";
                            switch (response.error_code) {
                                case -1:
                                    error_message = "邮箱未验证，页面将跳转至验证页面";
                                    break;
                                case -2:
                                    error_message = "密码错误，请重新输入。";
                                    break;
                                case -3:
                                    error_message = "无效的邮箱，请检查您的输入。";
                                    break;
                                case -4:
                                    error_message = "无效的电话号码，请检查您的输入。";
                                    break;
                                case -5:
                                    error_message = "无效的验证码或验证码已过期，请重新获取验证码。";
                                    break;
                                default:
                                    error_message = "发生未知错误，请稍后再试。";
                            }
                            $('.message.error').html('<p style="color: red;">'+error_message+'</p>');
                        }
                    },
                    error: function() {
                        $('.message.error').html('<p style="color: red;">服务器遇到问题，请稍后再试</p>');
                    }
                });
            });

            // 监听表单的提交事件
            $('#account-login-form').submit(function(event) {
                event.preventDefault(); // 阻止表单的默认提交行为，从而避免页面刷新

                // 获取表单数据
                var csrfToken = $('input[name="csrf_token"]').val();
                var account = $('input[name="account"]').val();
                var password = $('input[name="password"]').val();

                var encryptedPassword = encryptData(password);
                // 将表单数据打包成对象
                var formData = {
                    csrf_token: csrfToken,
                    account: account,
                    password: encryptedPassword
                };

                // 错误代码说明
                // -1: 邮箱未验证
                // -2: 密码错误
                // -3: 无效的邮箱
                // -4: 无效的电话号码
                // -5: 无效的验证码或验证码已过期
                // 通过 AJAX 提交数据
                $.ajax({
                    type: 'POST',
                    url: '/handlers/login_handler.php', // 指定处理表单的 PHP 文件
                    data: formData,
                    dataType: 'json', // 期待服务器返回 JSON 格式的数据
                    success: function(response) {
                        if (response.rs == 1) {
                            // 登录成功后的处理
                            $('.message.error').html('<p style="color: green;">' + response.message + '</p>');
                            window.location.replace(response.url);
                        } else if (response.rs == 0) {
                            let error_message = "";
                            switch (response.error_code) {
                                case -1:
                                    error_message = "邮箱未验证，页面将跳转至验证页面";
                                    break;
                                case -2:
                                    error_message = "密码错误，请重新输入。";
                                    break;
                                case -3:
                                    error_message = "无效的邮箱，请检查您的输入。";
                                    break;
                                case -4:
                                    error_message = "无效的电话号码，请检查您的输入。";
                                    break;
                                case -5:
                                    error_message = "无效的验证码或验证码已过期，请重新获取验证码。";
                                    break;
                                default:
                                    error_message = "发生未知错误，请稍后再试。";
                            }
                            // 登录失败后的处理
                            $('.message.error').html('<p style="color: red;">' + error_message + '</p>');
                            if (response.error_code == -1){
                                setTimeout(function() {
                                    window.location.replace(response.url); // 替换为目标页面的 URL
                                }, 3000); // 3000 毫秒（3 秒）
                            }
                        } else {
                            $('.message.error').html('<p style="color: red;">服务器遇到问题，请稍后再试</p>');
                        }
                    },
                    error: function() {
                        $('.message.error').html('<p style="color: red;">服务器遇到问题，请稍后再试</p>');
                    }
                });
            });
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
                body: JSON.stringify({ phone_number: phoneNumber, country_code: countryCode }) // 将数据转换为 JSON 字符串
            })
            .then(response => response.json())
            .then(data => {
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