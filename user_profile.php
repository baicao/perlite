<?php
require_once __DIR__ . '/config.php';

$user_id = $_SESSION['user_id'] ?? null; // 获取用户 ID

if (!$user_id) {
    header("Location: login.php"); // 如果用户未登录，重定向到登录页面
    exit();
}

// 从数据库中获取用户信息
$stmt = $app_conn->prepare("SELECT username, gender, email, grade, birthday, country, city, school, is_verified, phone_number, is_phone_verified, country_code FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $gender, $email, $grade, $birthday, $country, $city, $school, $is_verified, $phone_number, $is_phone_verified, $country_code);
$stmt->fetch();
$stmt->close();

// 处理表单提交以更新用户信息
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'] ?? $username;
    $new_gender = $_POST['gender'] ?? $gender;
    $new_email = $_POST['email'] ?? $email;
    $new_grade = $_POST['grade'] ?? $grade;
    $new_birthday = $_POST['birthday'] ?? $birthday;
    $new_country = $_POST['country'] ?? $country;
    $new_city = $_POST['city'] ?? $city;
    $new_school = $_POST['school'] ?? $school;
    $new_phone_number = $_POST['phone_number'] ?? $phone_number;

    // 更新用户信息
    $update_stmt = $app_conn->prepare("UPDATE users SET username = ?, gender = ?, email = ?, grade = ?, birthday = ?, country = ?, city = ?, school = ?, phone_number = ? WHERE id = ?");
    $update_stmt->bind_param("ssssssssi", $new_username, $new_gender, $new_email, $new_grade, $new_birthday, $new_country, $new_city, $new_school, $new_phone_number, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // 更新成功后，重新获取用户信息
    header("Location: user_profile.php"); // 刷新页面以显示更新后的信息
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户个人信息</title>
    <link rel="stylesheet" href=".styles/app.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/login.css" type="text/css"> <!-- 引入 login.css -->
</head>

<body>
    <div class="login-container"> <!-- 使用 login.css 中的样式 -->
        <h1>用户个人信息</h1>
        <form method="POST">
            <div class="user-info">
                <p><strong>用户名:</strong> <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>"></p>
                <p><strong>性别:</strong> 
                    <select name="gender">
                        <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>男</option>
                        <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>女</option>
                        <option value="other" <?php echo $gender === 'other' ? 'selected' : ''; ?>>其他</option>
                    </select>
                </p>
                <p><strong>邮箱:</strong> <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"></p>
                <p><strong>年级:</strong> <input type="text" name="grade" value="<?php echo htmlspecialchars($grade); ?>"></p>
                <p><strong>生日:</strong> <input type="date" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>"></p>
                <p><strong>国家:</strong> <input type="text" name="country" value="<?php echo htmlspecialchars($country); ?>"></p>
                <p><strong>城市:</strong> <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>"></p>
                <p><strong>学校:</strong> <input type="text" name="school" value="<?php echo htmlspecialchars($school); ?>"></p>
                <p><strong>电话号码:</strong> <input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>"></p>
                <p><strong>邮箱验证:</strong> <?php echo $is_verified ? '已验证' : '未验证'; ?></p>
                <p><strong>电话验证:</strong> <?php echo $is_phone_verified ? '已验证' : '未验证'; ?></p>
                <p><strong>国家代码:</strong> <input type="text" name="country_code" value="<?php echo htmlspecialchars($country_code); ?>"></p>
            </div>
            <button type="submit">更新信息</button>
        </form>
        <a href="index.php">返回首页</a>
    </div>
</body>

</html> 