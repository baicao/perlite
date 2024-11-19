<?php
require_once dirname(__DIR__) .'/config.php';
require_once dirname(__DIR__) . '/models/UserModel.php';

$account = "baicao2010@qq.com";
$country_code = "+86"; // 示例国家代码
$phone_number = "12345678901"; // 示例手机号

$userModel = new UserModel($app_conn);
$response = $userModel->getUserByPhoneOrEmail($country_code, $phone_number, $email = $account, $user_id = null);
header('Content-Type: application/json'); // 设置响应头为 JSON
echo json_encode($_SESSION['user']); // 输出 JSON 响应

?>