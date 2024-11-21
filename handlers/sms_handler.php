<?php
header('Content-Type: text/html; charset=utf-8'); // 设置内容类型为 UTF-8
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) .'/config.php';
require_once dirname(__DIR__) .'/common/sms_helper.php';


// 处理发送验证码的请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
    $data = json_decode(file_get_contents('php://input'), true); // 解析 JSON 数据
    $response = array('rs' => 0, 'message' => '');
    log_message("[REQ]".json_encode($data, JSON_UNESCAPED_UNICODE));
    try {
        if (isset($data['phone_number']) && isset($data['country_code'])) {
            $phone_number = htmlspecialchars(trim($data['phone_number'])); // 使用 htmlspecialchars 和 trim
            $country_code = htmlspecialchars(trim($data['country_code'])); // 使用 htmlspecialchars 和 trim
            if (empty($phone_number) || empty($country_code)) {
                $response["rs"] = 0;
                $response["message"] = '手机号或者区域号为空';
                $response["error_code"] = -1;
            }else{
                if(isset($data['type']) && $data['type']=="verify" && isset($_SESSION['user'])  && $_SESSION['user']["phone_number"] == null){
                    ;//不需要做手机号是否存在的校验
                }else{
                    // 检查手机号是否存在于用户表中
                    $stmt = $app_conn->prepare("SELECT id FROM users WHERE phone_number = ? and country_code = ?");
                    $stmt->bind_param("ss", $phone_number, $country_code);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows === 0) {
                        $response["rs"] = 0;
                        $response["message"] = '手机号不存在';
                        $response["error_code"] = -3;
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                }
                // 生成6位随机验证码
                $verification_code = rand(100000, 999999);
                log_message("Verification code: $country_code $phone_number $verification_code");
                // 存储验证码到数据库
                $send_phone = $country_code.$phone_number;
                $stmt = $app_conn->prepare("INSERT INTO verification_codes (phone_number, code, create_time, expiry) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 3 MINUTE))");
                $stmt->bind_param("si", $send_phone, $verification_code);
                if ($stmt->execute()) {
                    // 发送验证码
                    $send_phone = $country_code.$phone_number;
                    $sms_response = SendSMSVerifyCode($send_phone, $verification_code);
                    if($sms_response["rs"] == 1){
                        $response["rs"] = 1;
                        $response["message"] = $sms_response["message"];
                    }else{
                        $response["rs"] = 0;
                        $response["message"] = $sms_response["message"];
                        $response["error_code"] = -4;
                    }
                } else {
                    $response["rs"] = 0;
                    $response["message"] = '数据库错误';
                    $response["error_code"] = -5;
                }
                
                $stmt->close();
                $app_conn->close();
            }
        }else{
            $response["rs"] = 0;
            $response["message"] = '手机号或者区域号为空';
            $response["error_code"] = -1;
        }
    }catch(Exception $e) {
        log_message("Exception occurred: " . $e->getMessage());
        print_r($e); // 打印异常信息
        $response["rs"] = 0;
        $response["message"] = '未知错误，请微信联系ByteSwan';
        $response["error_code"] = -6;
    }
    log_message("[RES]".json_encode($response, JSON_UNESCAPED_UNICODE));
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

?>