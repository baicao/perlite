<?php
require_once __DIR__ . '/config.php'; // 包含数据库连接配置
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$phone_number = $data['phone_number'] ?? '';

if (empty($phone_number)) {
    echo json_encode(['success' => false, 'message' => '手机号不能为空']);
    exit();
}

// 检查手机号是否存在于用户表中
$stmt = $app_conn->prepare("SELECT id FROM users WHERE phone_number = ?");
$stmt->bind_param("s", $phone_number);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'error_code'=> -1, 'message' => '手机号不存在']);
    $stmt->close();
    $app_conn->close();
    exit();
}

$stmt->close();

// 生成6位随机验证码
$verification_code = rand(100000, 999999);
log_message("Verification code: $phone_number $verification_code");

// 存储验证码到数据库
$stmt = $app_conn->prepare("INSERT INTO verification_codes (phone_number, code, create_time) VALUES (?, ?, NOW())");
$stmt->bind_param("si", $phone_number, $verification_code);

if ($stmt->execute()) {
    // 这里可以调用短信发送服务，将验证码发送给用户
    // sendSMS($phone_number, $verification_code);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error_code'=> -2, 'message' => '数据库错误']);
}

$stmt->close();
$app_conn->close();
?>