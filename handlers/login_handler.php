<?php
header('Content-Type: application/json; charset=utf-8'); // 设置内容类型为 JSON 和 UTF-8
require_once dirname(__DIR__) .'/config.php';
require_once dirname(__DIR__) . '/models/UserModel.php';

function decryptData($encryptedData) {
    // 私钥路径（请确保这个文件是安全的，且对外部不可访问）
    $privateKeyFilePath = dirname(__DIR__) . '/ssl/private_key.pem';
    // 读取私钥
    $privateKey = file_get_contents($privateKeyFilePath);
    // 使用私钥进行解密
    $decrypted = '';
    if (openssl_private_decrypt(base64_decode($encryptedData), $decrypted, $privateKey)) {
        return $decrypted;
    } else {
        return "";
    }
}


// 错误代码说明
// -1: 邮箱未验证
// -2: 密码错误
// -3: 无效的邮箱
// -4: 无效的电话号码
// -5: 无效的验证码或验证码已过期
$response = array('rs' => 0, 'message' => '', 'url' => '', 'error_code' => 0);
$requestId = uniqid('', true); // 生成唯一请求 ID
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 验证 CSRF 令牌
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "无效的请求。请重试。";
        log_message("[$requestId] $error_message");
    } else {
        if (isset($_POST['account']) && isset($_POST['password'])) {
            $account = htmlspecialchars(trim($_POST['account']));
            $password = $_POST['password'];
            $decryptedPassword = decryptData($password);

            $userModel = new UserModel($app_conn);
            $user = $userModel->getUserByPhoneOrEmail(null, null,$account, null);
            if($user["rs"] == 1){
                $hashed_password = $user["data"]["password"];
                if (password_verify($decryptedPassword, $hashed_password)) {
                    log_message("[$requestId] Password verified successfully for account: $account");
                    // Email login
                    $is_verified = $user["data"]["is_verified"];
                    if ($is_verified) {
                        session_regenerate_id(true); // 重新生成会话 ID
                        $_SESSION['user'] = $user["data"];
                        $_SESSION['last_activity'] = time(); // 设置最后活动时间
                        $_SESSION['expire_time'] = 7 * 24 * 60 * 60; // 设置超时时间为7天
                        log_message("[$requestId] Login successful for user ID: ".$user["data"]["id"]);
                        $response['rs'] = 1;
                        $response['message'] = "success";
                        // 检查是否有保存的重定向URL
                        if (isset($_SESSION['redirect_after_login'])) {
                            $response['url'] = $_SESSION['redirect_after_login'];
                            unset($_SESSION['redirect_after_login']); // 清除重定向URL
                        } else {
                            $response['url'] = SITE_URL;
                        } 
                    } else {
                        $_SESSION['user_email'] = $account;
                        log_message("[$requestId] Email not verified for user ID: $id");
                        $response['rs'] = 0;
                        $response['error_code'] = -1;
                        $response['message'] = "Email not verified";
                        $response['url'] = "verify.php?email=" . urlencode($account); 
                    }
                } else {
                    log_message("[$requestId] Password verification failed for email: $account");
                    $response['rs'] = 0;
                    $response['error_code'] = -2;
                    $response['message'] = "Password not right";
                }
            }else if($user["rs"] == -1){
                log_message("[$requestId] No user found for email: $account");
                $response['rs'] = 0;
                $response['error_code'] = -3;
                $response['message'] = "Invalid email";
            }else{
                log_message("[$requestId] Mysql error: $user->error");
                $response['rs'] = 0;
                $response['error_code'] = -6;
                $response['message'] = "Mysql error";
            }

        } elseif (isset($_POST['country_code']) && isset($_POST['phone_number']) && isset($_POST['verification_code'])) {
            // Phone number verification login
            $phone_number = htmlspecialchars(trim($_POST['phone_number']));
            $country_code = htmlspecialchars(trim($_POST['country_code']));
            $verification_code = htmlspecialchars(trim($_POST['verification_code']));
            $userModel = new UserModel($app_conn);
            $user = $userModel->getUserByPhoneOrEmail($country_code, $phone_number, null, null);
            if($user["rs"] == 1){
                log_message("[$requestId phone exist : $country_code $phone_number");
                $send_phone = $country_code.$phone_number;
                // Check if phone number exists in the database and verification code matches
                $stmt = $app_conn->prepare("SELECT * FROM verification_codes WHERE phone_number = ? AND code = ? AND expiry > NOW()");
                $stmt->bind_param("si", $send_phone, $verification_code);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {

                    // 检查手机号验证状态
                    if ($user["data"]['is_phone_verified'] == 0) {
                        // 更新手机号验证状态，不需要关心是否修改成功
                        $updateResponse = $userModel->updatePhoneVerificationStatus($user["data"]['id']);
                        $user["data"]['is_phone_verified'] = 1;
                    }

                    $stmt->close();
                    log_message("[$requestId] Verify phone and code success");
                    session_regenerate_id(true); // 重新生成会话 ID
                    $_SESSION['user'] = $user["data"]; // 直接使用查询到的用户数据
                    $_SESSION['last_activity'] = time(); // 设置最后活动时间
                    $_SESSION['expire_time'] = 7 * 24 * 60 * 60; // 设置超时时间为7天
                    log_message("[$requestId] Login successful for user ID: " . $user["data"]['id']);
                    $response['rs'] = 1;
                    $response['message'] = "success";
                    // 检查是否有保存的重定向URL
                    if (isset($_SESSION['redirect_after_login'])) {
                        $response['url'] = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']); // 清除重定向URL
                    } else {
                        $response['url'] = SITE_URL;
                    } 
                } else {
                    log_message("[$requestId] Invalid verification code or code expired: $phone_number");
                    $response['rs'] = 0;
                    $response['error_code'] = -5;
                    $response['message'] = "Invalid verification code or code expired";
                }
                
            }else if($user["rs"] == -1){
                log_message("[$requestId] User data not found for phone number: $phone_number, $country_code");
                $response['rs'] = 0;
                $response['error_code'] = -4;
                $response['message'] = "Invalid phone";
            }else{
                log_message("[$requestId] Mysql error: ".$user["error"]);
                $response['rs'] = 0;
                $response['error_code'] = -6;
                $response['message'] = "Mysql error";
            }
        }
    }
}
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>


