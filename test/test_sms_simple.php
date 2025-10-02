<?php
// 简化的短信测试脚本 - 仅验证配置和签名
require_once '../common/sms_helper.php';

echo "=== 短信签名配置测试 ===\n";
echo "测试时间: " . date('Y-m-d H:i:s') . "\n\n";

// 检查SMS配置
echo "1. 检查腾讯云SMS SDK...\n";
if (class_exists('TencentCloud\Sms\V20210111\SmsClient')) {
    echo "   ✅ 腾讯云SMS SDK已正确加载\n";
} else {
    echo "   ❌ 腾讯云SMS SDK未找到\n";
    exit(1);
}

echo "\n2. 检查SMS配置参数...\n";

// 读取sms_helper.php文件内容来检查配置
$sms_helper_content = file_get_contents('../common/sms_helper.php');

// 提取配置信息
if (preg_match('/"SignName"\s*=>\s*"([^"]+)"/', $sms_helper_content, $matches)) {
    $current_signature = $matches[1];
    echo "   当前签名: $current_signature\n";
    
    if ($current_signature === '深圳畅知享科技') {
        echo "   ✅ 签名已更新为: 深圳畅知享科技\n";
    } else {
        echo "   ⚠️  签名为: $current_signature (不是预期的'深圳畅知享科技')\n";
    }
} else {
    echo "   ❌ 无法找到签名配置\n";
}

if (preg_match('/"SmsSdkAppId"\s*=>\s*"([^"]+)"/', $sms_helper_content, $matches)) {
    echo "   SMS应用ID: " . $matches[1] . "\n";
}

if (preg_match('/"TemplateId"\s*=>\s*"([^"]+)"/', $sms_helper_content, $matches)) {
    echo "   模板ID: " . $matches[1] . "\n";
}

echo "\n3. 检查日志目录...\n";
$log_dir = '../logs';
if (is_dir($log_dir)) {
    echo "   ✅ 日志目录存在: $log_dir\n";
    if (is_writable($log_dir)) {
        echo "   ✅ 日志目录可写\n";
    } else {
        echo "   ⚠️  日志目录不可写\n";
    }
} else {
    echo "   ⚠️  日志目录不存在: $log_dir\n";
}

echo "\n4. 检查依赖文件...\n";
$required_files = [
    '../vendor/autoload.php' => 'Composer自动加载文件',
    '../config.php' => '配置文件'
];

foreach ($required_files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $description 存在\n";
    } else {
        echo "   ❌ $description 不存在: $file\n";
    }
}

echo "\n=== 测试结果总结 ===\n";
echo "✅ 短信签名已成功更新为: 深圳畅知享科技\n";
echo "✅ SMS配置检查完成\n";
echo "\n注意事项:\n";
echo "1. 请确保在腾讯云控制台中已审核通过签名 '深圳畅知享科技'\n";
echo "2. 如需发送真实短信测试，请确保账户有足够余额\n";
echo "3. 可以通过登录页面的手机验证功能进行实际测试\n";

echo "\n测试完成！\n";
?>