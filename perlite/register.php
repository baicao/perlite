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
        log_message("Registration attempt for email: $email");

        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $gender = $_POST['gender'];
        $grade = $_POST['grade'] === 'other' ? htmlspecialchars($_POST['other_grade']) : $_POST['grade'];
        $birthday = $_POST['birthday'];
        $country = $_POST['country'] === 'other' ? htmlspecialchars($_POST['other_country']) : $_POST['country'];
        $city = $_POST['city'] === 'other' ? htmlspecialchars($_POST['other_city']) : $_POST['city'];
        $school = $_POST['school'] === 'other' ? htmlspecialchars($_POST['other_school']) : $_POST['school'];
        $verification_code = bin2hex(random_bytes(16));

        $check_stmt = $app_conn->prepare("SELECT is_verified FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $user = $check_result->fetch_assoc();
            if ($user['is_verified'] == 1) {
                $error_message = "此邮箱已被注册。请使用其他邮箱地址。";
                log_message("Registration failed: email already verified for $email");
            } else {
                $delete_stmt = $app_conn->prepare("DELETE FROM users WHERE email = ?");
                $delete_stmt->bind_param("s", $email);
                $delete_stmt->execute();
                log_message("Deleted unverified user for email: $email");
            }
        }

        if (empty($error_message)) {
            $stmt = $app_conn->prepare("INSERT INTO users (username, email, password, gender, grade, birthday, country, city, school, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $is_verified = 0;
            $stmt->bind_param("ssssssssssi", $username, $email, $password, $gender, $grade, $birthday, $country, $city, $school, $verification_code, $is_verified);

            if ($stmt->execute()) {
                log_message("Registration successful for email: $email");
                header("Location: verify.php?email=" . urlencode($email));
                exit();
            } else {
                $error_message = "注册失败: " . $stmt->error;
                log_message("Registration failed for email: $email, error: " . $stmt->error);
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
    <link rel="stylesheet" href=".styles/login.css">
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
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(email)) {
                alert("请输入有效的邮箱地址。Please enter a valid email address.");
                return false;
            }

            if (password.length < 6) {
                alert("密码至少需要6位。Password must be at least 6 characters long.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <form name="registerForm" method="POST" onsubmit="return validateForm()">
        <h2>注册 Register</h2>
        <?php if ($error_message): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        
        <label for="username">用户名 Username:</label>
        <input type="text" id="username" name="username" placeholder="用户名 Username" required>
        
        <label for="email">邮箱 Email:</label>
        <input type="email" id="email" name="email" placeholder="邮箱 Email" required>
        
        <label for="password">密码 Password:</label>
        <input type="password" id="password" name="password" placeholder="密码 Password" required>
        
        <label for="gender">性别 Gender:</label>
        <select id="gender" name="gender" required>
            <option value="male">男 Male</option>
            <option value="female">女 Female</option>
            <option value="other">其他 Other</option>
        </select>
        
        <label for="grade">年级 Grade:</label>
        <select id="grade" name="grade" onchange="toggleInput(this, 'other_grade_input')" required>
            <option value="">选择年级 Select Grade</option>
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
        <input type="text" id="other_grade_input" name="other_grade" placeholder="其他年级 Other Grade" style="display:none;">
        
        <label for="birthday">出生年月日 Date of Birth:</label>
        <input type="date" id="birthday" name="birthday" required>
        
        <label for="country">国家 Country:</label>
        <select id="country" name="country" onchange="toggleInput(this, 'other_country_input')" required>
            <option value="">选择国家 Select Country</option>
            <option value="china">中国 China</option>
            <option value="usa">美国 USA</option>
            <option value="uk">英国 UK</option>
            <option value="canada">加拿大 Canada</option>
            <option value="australia">澳大利亚 Australia</option>
            <option value="other">其他 Other</option>
        </select>
        <input type="text" id="other_country_input" name="other_country" placeholder="其他国家 Other Country" style="display:none;">
        
        <label for="city">城市 City:</label>
        <select id="city" name="city" onchange="toggleInput(this, 'other_city_input')" required>
            <option value="">选择城市 Select City</option>
            <option value="beijing">北京 Beijing</option>
            <option value="shanghai">上海 Shanghai</option>
            <option value="shenzhen">深圳 Shenzhen</option>
            <option value="new_york">纽约 New York</option>
            <option value="london">伦敦 London</option>
            <option value="sydney">悉尼 Sydney</option>
            <option value="other">其他 Other</option>
        </select>
        <input type="text" id="other_city_input" name="other_city" placeholder="其他城市 Other City" style="display:none;">
        
        <label for="school">学校 School:</label>
        <select id="school" name="school" onchange="toggleInput(this, 'other_school_input')" required>
            <option value="">选择学校 Select School</option>
            <option value="shenzhen_college">深圳国际交流学院 Shenzhen College of International Education</option>
            <optgroup label="贝赛思国际学校 BASIS International School">
                <option value="basis_shekou">深圳贝赛思国际学校（蛇口校区）BASIS International School Shenzhen (Shekou Campus)</option>
                <option value="basis_futian">深圳贝赛思外国语学校（福田校区）BASIS Bilingual School Shenzhen (Futian Campus)</option>
                <option value="basis_huizhou">惠州小径湾贝赛思国际学校 BASIS International School Huizhou</option>
                <option value="basis_guangzhou">广州贝赛思国际学校 BASIS International School Guangzhou</option>
                <option value="basis_guangming">深圳贝赛思外国语学校（光明校区）BASIS Bilingual School Shenzhen (Guangming Campus)</option>
                <option value="basis_nanshan">南山贝赛思幼儿园 BASIS Kindergarten Nanshan</option>
            </optgroup>
            <optgroup label="德威国际学校 Dulwich College">
                <option value="dulwich_beijing">北京校区 Beijing Campus</option>
                <option value="dulwich_shanghai">上海校区 Shanghai Campus</option>
                <option value="dulwich_suzhou">苏州校区 Suzhou Campus</option>
            </optgroup>
            <optgroup label="哈罗国际学校 Harrow International School">
                <option value="harrow_beijing">北京校区 Beijing Campus</option>
                <option value="harrow_shanghai">上海校区 Shanghai Campus</option>
                <option value="harrow_shenzhen">深圳校区 Shenzhen Campus</option>
            </optgroup>
            <optgroup label="惠灵顿国际学校 Wellington College">
                <option value="wellington_shanghai">上海校区 Shanghai Campus</option>
                <option value="wellington_tianjin">天津校区 Tianjin Campus</option>
            </optgroup>
            <option value="yew_chung">耀中国际学校 Yew Chung International School</option>
            <option value="concordia">协和国际学校 Concordia International School</option>
            <option value="other">其他 Other</option>
        </select>
        <input type="text" id="other_school_input" name="other_school" placeholder="其他学校 Other School" style="display:none;">
        
        <button type="submit">注册 Register</button>
    </form>
</body>
</html>
