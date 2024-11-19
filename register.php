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
        $username = htmlspecialchars($_POST['username']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = $_POST['phone'];
        $country_code = $_POST['country_code'];

        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $gender = $_POST['gender'];
        $grade = $_POST['grade'] === 'other' ? htmlspecialchars($_POST['other_grade']) : $_POST['grade'];
        $birthday = $_POST['dob'];
        $country = $_POST['country'] === 'other' ? htmlspecialchars($_POST['other_country']) : $_POST['country'];
        $city = $_POST['city'] === 'other' ? htmlspecialchars($_POST['other_city']) : $_POST['city'];
        $school = $_POST['school'] === 'other' ? htmlspecialchars($_POST['other_school']) : $_POST['school'];
        $verification_code = bin2hex(random_bytes(16));

        // 检查邮箱和手机号码是否已存在
        $check_stmt = $app_conn->prepare("SELECT email, phone_number FROM users WHERE email = ? OR (phone_number = ? and country_code=?)");
        $check_stmt->bind_param("sss", $email, $phone, $country_code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $user = $check_result->fetch_assoc();
            if ($user['email'] === $email) {
                $error_message = "此邮箱已被注册。Email already registered.";
                log_message("Registration failed: email already registered for $email");
            } else {
                $error_message = "此手机号码已被注册。Phone number already registered.";
                log_message("Registration failed: phone number already registered for $country_code $phone");
            }
        }

        if (empty($error_message)) {
            $stmt = $app_conn->prepare("INSERT INTO users (username, email, country_code, phone_number, password, gender, grade, birthday, country, city, school, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $is_verified = 0;
            $stmt->bind_param("ssssssssssssi", $username, $email, $country_code, $phone, $password, $gender, $grade, $birthday, $country, $city, $school, $verification_code, $is_verified);

            if ($stmt->execute()) {
                log_message("Registration successful for email: $email and phone: $phone");
                header("Location: verify.php?email=" . urlencode($email));
                exit();
            } else {
                $error_message = "注册失败: " . $stmt->error;
                log_message("Registration failed error: " . $stmt->error);
            }
        }
    }
}
$schools = [
    'Shenzhen College of International Education' => '深圳国际交流学院',
    'Shenzhen Hong Kong Pui Kiu Xinyi' => '深圳培侨信义',
    'Guangzhou Minxin School' => '广州南沙民心',
]

?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=".styles/login.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-i18n/1.12.1/jquery-ui-i18n.min.js"></script>
    <title>注册 Register</title>
    <script>
        function toggleInput(selectElement, inputId) {
            var inputElement = document.getElementById(inputId);
            if (selectElement.value === 'other') {
                inputElement.style.display = 'block';
            } else {
                inputElement.style.display = 'none';
            }
        }

        function validateForm() {
            var email = document.forms["registerForm"]["email"].value;
            var password = document.forms["registerForm"]["password"].value;
            var phone = document.forms["registerForm"]["phone"].value;
            var dob = document.forms["registerForm"]["dob"].value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var datePattern = /^\d{4}-\d{2}-\d{2}$/; // YYYY-MM-DD 格式

            // 获取或创建错误信息元素
            var errorMessageElement = document.querySelector(".message.error");
            if (!errorMessageElement) {
                errorMessageElement = document.createElement('p');
                errorMessageElement.className = 'message error';
                var form = document.querySelector("form[name='registerForm']");
                form.insertBefore(errorMessageElement, form.firstChild);
            }

            if (!emailPattern.test(email)) {
                errorMessageElement.textContent = "请输入有效的邮箱地址";
                return false;
            }

            if (password.length < 6) {
                errorMessageElement.textContent = "密码至少需要6位";
                return false;
            }

            if (!datePattern.test(dob)) {
                errorMessageElement.textContent = "请输入有效的日期格式 (YYYY-MM-DD)";
                return false;
            }

            if (!confirm("您输入的手机号是 " + phone + "，请确认是否正确？该手机号可能会用于登录接受验证码")) {
                return false;
            }

            // 清除错误信息
            errorMessageElement.textContent = "";
            return true;
        }
    </script>
</head>
<body>
    <div class="login-container">
        <form name="registerForm" method="POST" onsubmit="return validateForm()">
            <h2>注册 Register</h2>
            <?php if ($error_message): ?>
                <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            
            <p class="message error"></p> <!-- 添加错误信息显示区域 -->
            
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <input type="text" id="username" name="username" placeholder="用户名" required>
            
            <input type="email" id="email" name="email" placeholder="邮箱" required>
            
            <div class="country-code">
                <select id="country_code" name="country_code">
                    <option value="+86">中国 +86</option>
                    <option value="+852">香港 +852</option>
                    <!-- 可以添加其他国家的区号 -->
                </select>
                <input type="text" id="phone" name="phone" placeholder="手机号码" required>
            </div>
            
            <input type="password" id="password" name="password" placeholder="密码" required>
            
            <select id="gender" name="gender" required>
                <option value="male">男</option>
                <option value="female">女</option>
                <option value="other">其他</option>
            </select>
            
            <select id="grade" name="grade" onchange="toggleInput(this, 'other_grade_input')" required>
                <option value="">选择年级</option>
                <optgroup label="DSE">
                    <option value="dse_s1">中一</option>
                    <option value="dse_s2">中二</option>
                    <option value="dse_s3">中三</option>
                    <option value="dse_s4">中四</option>
                    <option value="dse_s5">中五</option>
                    <option value="dse_s6">中六</option>
                </optgroup>
                <optgroup label="英系 IGCSE">
                    <option value="igcse_g1">G1</option>
                    <option value="igcse_g2">G2</option>
                </optgroup>
                <optgroup label="英系 ASAL">
                    <option value="as">AS</option>
                    <option value="al">AL</option>
                </optgroup>
                <optgroup label="美系 AP">
                    <option value="ap_grade_9">Grade 9</option>
                    <option value="ap_grade_10">Grade 10</option>
                    <option value="ap_grade_11">Grade 11</option>
                    <option value="ap_grade_12">Grade 12</option>
                </optgroup>
                <option value="other">其他 Other</option>
            </select>
            <input type="text" id="other_grade_input" name="other_grade" placeholder="其他年级" style="display:none;">
            
            <input type="text" id="dob" name="dob" class="dob-input" placeholder="出生年月日" required>
            
            <select id="country" name="country" onchange="toggleInput(this, 'other_country_input')" required>
                <option value="">选择国家</option>
                <option value="china">中国</option>
                <option value="usa">美国</option>
                <option value="uk">英国</option>
                <option value="canada">加拿大</option>
                <option value="australia">澳大利亚</option>
                <option value="other">其他</option>
            </select>
            <input type="text" id="other_country_input" name="other_country" placeholder="其他国家" style="display:none;">
            
            <select id="city" name="city" onchange="toggleInput(this, 'other_city_input')" required>
                <option value="">选择城市</option>
                <option value="beijing">北京</option>
                <option value="shanghai">上海</option>
                <option value="shenzhen">深圳</option>
                <option value="new_york">纽约</option>
                <option value="london">伦敦</option>
                <option value="sydney">悉尼</option>
                <option value="other">其他</option>
            </select>
            <input type="text" id="other_city_input" name="other_city" placeholder="其他城市" style="display:none;">
            
            <select id="school" name="school" onchange="toggleInput(this, 'other_school_input')" required>
                <?php
                foreach ($options["schools"] as $group_label => $option) {
                    if (is_array($option)) {
                        echo "<optgroup label=\"$group_label\">";
                        foreach ($option as $value => $label) {
                            echo "<option value=\"" . strtolower(str_replace(' ', '_', $value)) . "\">$label $value</option>";
                        }
                        echo "</optgroup>";
                    } else {
                        echo "<option value=\"" . strtolower(str_replace(' ', '_', $group_label)) . "\">$option $group_label</option>";
                    }
                }
                ?>
            </select>
            <input type="text" id="other_school_input" name="other_school" placeholder="其他学校" style="display:none;">
            <button type="submit">注册</button>
        </form>
    </div>
    <script>
        $(function() {
            if (window.innerWidth <= 600) {
                // 在移动设备上使用原生日期选择器
                $("#dob").attr("type", "date");
            } else {
                // 在桌面设备上使用 jQuery UI Datepicker
                $.datepicker.setDefaults($.datepicker.regional["zh-CN"]);
                $.datepicker.regional["zh-CN"] = {
                    closeText: "关闭",
                    prevText: "&#x3C;上月",
                    nextText: "下月&#x3E;",
                    currentText: "今天",
                    monthNames: ["一月","二月","三月","四月","五月","六月", "七月","八月","九月","十月","十一月","十二月"],
                    monthNamesShort: ["一月","二月","三月","四月","五月","六月", "七月","八月","九月","十月","十一月","十二月"],
                    dayNames: ["星期日","星期一","星期二","星期三","星期四","星期五","星期六"],
                    dayNamesShort: ["周日","周一","周二","周三","周四","周五","周六"],
                    dayNamesMin: ["日","一","二","三","四","五","六"],
                    weekHeader: "周",
                    dateFormat: "yy-mm-dd",
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: true,
                    yearSuffix: "年"
                };
                $("#dob").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "1920:+0",
                    ...$.datepicker.regional["zh-CN"]
                });
            }
        });
    </script>
</body>
</html>
