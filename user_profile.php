<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/UserModel.php';



$user = $_SESSION['user'] ?? null; // 获取用户 ID
log_message(json_encode($user));
if (!$user) {
    header("Location: login.php"); // 如果用户未登录，重定向到登录页面
    exit();
}
$userModel = new UserModel($app_conn);
$user = $userModel->getUserByPhoneOrEmail(null, null, null , $user["id"]);
if($user["rs"] == 1){
    $user = $user["data"];
}else{
    header("Location: login.php"); // 如果用户未登录，重定向到登录页面
    exit();
}


// 处理表单提交以更新用户信息
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_basic_info'])) {
        // 处理基本信息更新
        $new_username = $_POST['username'] ?? $user['username'];
        $new_grade = $_POST['grade'] === 'other' ? htmlspecialchars($_POST['other_grade']) : $_POST['grade']?? $user['grade'];
        $new_gender = $_POST['gender'] === 'other' ? htmlspecialchars($_POST['other_gender']) : $_POST['gender']?? $user['gender'];
        $new_birthday = $_POST['dob'] ?? $user['birthday'];
        $new_country = $_POST['country'] === 'other' ? htmlspecialchars($_POST['other_country']) : $_POST['country']?? $user['country'];
        $new_city =  $_POST['city'] === 'other' ? htmlspecialchars($_POST['other_city']) : $_POST['city']?? $user['city'];
        $new_school = $_POST['school'] === 'other' ? htmlspecialchars($_POST['other_school']) : $_POST['school']?? $user['school'];

        // 更新用户信息
        $update_stmt = $app_conn->prepare("UPDATE users SET username = ?, gender = ?, grade = ?, birthday = ?, country = ?, city = ?, school = ? WHERE id = ?");
        $update_stmt->bind_param("sssssssi", $new_username, $new_gender, $new_grade, $new_birthday, $new_country, $new_city, $new_school, $user['id']);
        $update_stmt->execute();
        $update_stmt->close();

        // 更新成功后，重新获取用户信息
        header("Location: user_profile.php"); // 刷新页面以显示更新后的信息
        exit();
    } elseif (isset($_POST['submit_password_change'])) {
        // 处理密码修改
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        // 验证旧密码
        if (password_verify($old_password, $user['password'])) {
            // 检查新密码和确认密码是否匹配
            if ($new_password === $confirm_new_password) {
                // 哈希新密码
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // 更新密码
                $update_stmt = $app_conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();

                // 密码修改成功后，重定向到用户资料页面
                header("Location: user_profile.php?password_changed=1"); // 刷新页面以显示更新后的信息
                exit();
            } else {
                echo "新密码和确认密码不匹配。";
            }
        } else {
            echo "旧密码不正确。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户个人信息</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href=".styles/login.css" type="text/css"> <!-- 引入 login.css -->
</head>
<script>
    function toggleInput(selectElement, inputId) {
        var inputElement = document.getElementById(inputId);
        if (selectElement.value === 'other') {
            inputElement.style.display = 'block';
        } else {
            inputElement.style.display = 'none';
        }
    }

    function switchTab(tabId) {
        // 隐藏所有 tab-content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        // 显示被点击的 tab-content
        document.getElementById(tabId).classList.add('active');
        // 更新标签状态
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        document.querySelector(`[onclick="switchTab('${tabId}')"]`).classList.add('active');
    }

    function saveChanges(tabId) {
        // 保存逻辑，这里可以添加与后端通信代码
        alert('更改已保存');
    }

    function verifyEmail() {
        // 跳转到邮箱验证页面的逻辑
        window.location.href = '/verify.php';
    }
    function verifyPhone() {
        // 跳转到邮箱验证页面的逻辑
        window.location.href = '/verify_phone.php';
    }
</script>
<style>

    .verification-status {
        margin-left: 10px;
        display: inline-block;
    }
    .verified {
        color: green;
    }
    .not-verified {
        color: red;
    }
    .info-item {
        margin-bottom: 15px;
        display: flex; /* 使用 flexbox 布局 */
        align-items: center; /* 垂直居中对齐 */
    }
    .info-item label {
        flex: 0 0 120px; /* 固定宽度，您可以根据需要调整 */
        text-align: right; /* 右对齐 */
        margin-right: 10px; /* 右边距 */
    }
    .info-item input,
    .info-item select {
        flex: 1; /* 输入框和选择框占据剩余空间 */
    }
</style>
<body>
    <div class="profile-container">

        <!-- 标签导航 -->
        <div class="tab-container">
            <div class="tab active" onclick="switchTab('basic-info')">基本信息</div>
            <div class="tab" onclick="switchTab('contact-info')">联系方式</div>
            <div class="tab" onclick="switchTab('password-change')">密码修改</div>
        </div>

        <!-- 基本信息显示及修改 -->
        <div id="basic-info" class="tab-content active">
            <form method="POST" action="user_profile.php" id="basic-info-form">
                <div class="info-item">
                    <label>名字:</label>
                    <input type="text" id="first-name-input" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" />
                </div>
                <div class="info-item">
                    <label>性别:</label>
                    <select id="gender-input" name="gender">
                        <?php foreach ($options['genders'] as $value => $label){
                            $selected = "";
                            if($user['gender'] === $value){
                                $selected = "selected";
                            }
                            echo "<option value='".$value."' ".$selected.">".$label."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="info-item">
                    <label>年级:</label>
                    <select id="grade-input" name="grade" onchange="toggleInput(this, 'other_grade_input')">

                    <?php
                        foreach ($options["grades"] as $group_label => $option) {
                            if (is_array($option)) {
                                echo "<optgroup label=\"$group_label\">";
                                foreach ($option as $value => $label) {
                                    $selected = $user['school'] === $label ? 'selected' : ''; 
                                    echo "<option value=\"" . htmlspecialchars($value) . "\" $selected>$label</option>";
                                }
                                echo "</optgroup>";
                            } else {
                                $selected = $user['school'] === $option || ($option=='其他'&& $user["is_other_school"])? 'selected' : ''; // 使用学校名称进行匹配
                                echo "<option value=\"" . htmlspecialchars($group_label) . "\" $selected>$option</option>";
                            }
                        }
                        ?>

                    </select>
                    <input type="text" id="other_grade_input" name="other_grade" placeholder="其他年级" style=<?php echo $user["is_other_grade"] ? 'display:block' : 'display:none'; ?> value="<?php echo $user["is_other_grade"] ? htmlspecialchars($user['grade']) : ''; ?>">
                </div>
                <div class="info-item">
                    <label>出生日期:</label>
                    <input type="date" id="dob-input" class="dob-input" name="birthday" value="<?php echo htmlspecialchars($user['birthday']); ?>" />
                </div>
                <div class="info-item">
                    <label>国家:</label>
                    <select id="country-input" name="country" onchange="toggleInput(this, 'other-country-input')">
                        <?php
                        foreach ($options['countries'] as $value => $label): 
                            $selected = $user['country'] === $value || ($value === 'other' && $user["is_other_country"]) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="other-country-input" name="other_country" placeholder="其他国家" style="display: <?php echo $user["is_other_country"] ? 'block' : 'none'; ?>;" value="<?php echo $user["is_other_country"] ? htmlspecialchars($user['country']) : ''; ?>" />
                </div>
                <div class="info-item">
                    <label>城市:</label>
                    <select id="city-input" name="city" onchange="toggleInput(this, 'other_city_input')">
                        <?php
                        foreach ($options['cities'] as $value => $label): 
                            $selected = $user['city'] === $value || ($value === 'other' && $user["is_other_city"]) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="other_city_input" name="other_city" placeholder="其他城市" style="display: <?php echo $user["is_other_city"] ? 'block' : 'none'; ?>;" value="<?php echo $user["is_other_city"] ? htmlspecialchars($user['city']) : ''; ?>" />
                </div>
                <div class="info-item">
                    <label>学校:</label>
                    <select id="school" name="school" onchange="toggleInput(this, 'other_school_input')" required>
                        <?php
                        foreach ($options["schools"] as $group_label => $option) {
                            if (is_array($option)) {
                                echo "<optgroup label=\"$group_label\">";
                                foreach ($option as $value => $label) {
                                    $selected = $user['school'] === $label ? 'selected' : ''; // 使用学校名称进行匹配
                                    echo "<option value=\"" . htmlspecialchars($label) . "\" $selected>$label</option>";
                                }
                                echo "</optgroup>";
                            } else {
                                $selected = $user['school'] === $option || ($option=='其他'&& $user["is_other_school"])? 'selected' : ''; // 使用学校名称进行匹配
                                echo "<option value=\"" . htmlspecialchars($group_label) . "\" $selected>$option</option>";
                            }
                        }
                        ?>
                    </select>
                    <input type="text" id="other_school_input" name="other_school" placeholder="其他学校" style=<?php echo $user["is_other_school"] ? 'display:block' : 'display:none'; ?> value="<?php echo $user["is_other_school"] ? htmlspecialchars($user['school']) : ''; ?>">
                </div> 

                <button type="submit" name="submit_basic_info">保存基本信息</button> <!-- 提交按钮 -->
            </form>
        </div>

        <!-- 联系方式显示 -->
        <div id="contact-info" class="tab-content">
            <div class="info-item">
                <label>手机号码:</label>
                <span><?php echo htmlspecialchars($user['phone_number']); ?></span>
                <?php if (!$user['is_phone_verified']): ?>
                    <button onclick="verifyPhone()" style="margin-left: 10px; padding: 5px 10px;">去验证</button>
                <?php endif; ?>
            </div>
            <div class="info-item">
                <label>邮箱地址:</label>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
                <?php if (!$user['is_verified']): ?>
                    <button onclick="verifyEmail()" style="margin-left: 10px; padding: 5px 10px;">去验证</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- 密码修改表单 -->
        <div id="password-change" class="tab-content">
            <form method="POST" action="user_profile.php" id="password-change-form">
                <input type="password" name="old_password" placeholder="旧密码" required />
                <input type="password" name="new_password" placeholder="新密码" required />
                <input type="password" name="confirm_new_password" placeholder="确认新密码" required />
                <button type="submit" name="submit_password_change">修改密码</button> <!-- 提交按钮 -->
            </form>
        </div>
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