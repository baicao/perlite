<?php
require_once '../common/sms_helper.php';

// 测试短信发送功能
function testSMSSending() {
    echo "=== 短信发送功能测试 ===\n";
    echo "当前签名: 深圳畅知享科技\n";
    echo "测试时间: " . date('Y-m-d H:i:s') . "\n\n";
    
    // 测试参数
    $test_phone = "+8613800138000"; // 请替换为真实的测试手机号
    $verify_code = "123456";
    $valid_minute = 3;
    
    echo "测试参数:\n";
    echo "手机号: $test_phone\n";
    echo "验证码: $verify_code\n";
    echo "有效期: $valid_minute 分钟\n\n";
    
    // 调用短信发送函数
    echo "正在发送短信...\n";
    $result = SendSMSVerifyCode($test_phone, $verify_code, $valid_minute);
    
    // 输出结果
    echo "\n=== 发送结果 ===\n";
    echo "返回状态: " . ($result['rs'] ? '成功' : '失败') . "\n";
    echo "返回消息: " . $result['message'] . "\n";
    echo "请求ID: " . $result['request_id'] . "\n";
    
    if ($result['rs']) {
        echo "\n✅ 短信发送成功！请检查手机是否收到验证码短信。\n";
        echo "短信签名应该显示为: 【深圳畅知享科技】\n";
    } else {
        echo "\n❌ 短信发送失败！\n";
        echo "可能的原因:\n";
        echo "1. 腾讯云SMS配置错误\n";
        echo "2. 签名未审核通过\n";
        echo "3. 模板ID不正确\n";
        echo "4. 手机号格式错误\n";
        echo "5. 账户余额不足\n";
    }
    
    return $result;
}

// 检查配置信息
function checkSMSConfig() {
    echo "\n=== SMS配置检查 ===\n";
    
    // 检查必要的类是否存在
    if (class_exists('TencentCloud\Sms\V20210111\SmsClient')) {
        echo "✅ 腾讯云SMS SDK已正确加载\n";
    } else {
        echo "❌ 腾讯云SMS SDK未找到\n";
        return false;
    }
    
    // 检查日志目录
    $log_dir = '../logs';
    if (is_dir($log_dir) && is_writable($log_dir)) {
        echo "✅ 日志目录可写: $log_dir\n";
    } else {
        echo "⚠️  日志目录不存在或不可写: $log_dir\n";
    }
    
    return true;
}

// 主测试流程
echo "短信功能测试脚本\n";
echo "==================\n\n";

// 检查配置
if (!checkSMSConfig()) {
    echo "配置检查失败，无法继续测试\n";
    exit(1);
}

// 询问是否继续测试
echo "\n注意: 此测试将发送真实短信，可能产生费用。\n";
echo "请确保:\n";
echo "1. 已在腾讯云控制台审核通过签名 '深圳畅知享科技'\n";
echo "2. 账户有足够余额\n";
echo "3. 测试手机号可以正常接收短信\n\n";

echo "是否继续测试? (y/n): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if (strtolower($input) === 'y' || strtolower($input) === 'yes') {
    // 执行测试
    $result = testSMSSending();
    
    echo "\n=== 测试完成 ===\n";
    if ($result['rs']) {
        echo "测试结果: 成功 ✅\n";
        echo "请检查测试手机是否收到带有新签名的短信\n";
    } else {
        echo "测试结果: 失败 ❌\n";
        echo "请检查日志文件获取详细错误信息\n";
    }
} else {
    echo "测试已取消\n";
}

echo "\n测试脚本结束\n";
?>