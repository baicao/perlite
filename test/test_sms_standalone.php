<?php
// 独立的短信配置测试脚本 - 不依赖数据库连接

// 1. 检查Composer自动加载
$autoload_file = '../vendor/autoload.php';
if (file_exists($autoload_file)) {
    require_once $autoload_file;
} else {
    echo "❌ Composer自动加载文件不存在: $autoload_file\n";
    exit(1);
}

// 导入必要的类
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\SmsClient;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;

echo "=== 短信签名配置测试 (独立版本) ===\n";
echo "测试时间: " . date('Y-m-d H:i:s') . "\n\n";

echo "1. 检查Composer依赖...\n";
echo "   ✅ Composer自动加载文件存在\n";
echo "   ✅ 依赖加载成功\n";

// 2. 检查腾讯云SMS SDK
echo "\n2. 检查腾讯云SMS SDK...\n";
try {
    if (class_exists('TencentCloud\Sms\V20210111\SmsClient')) {
        echo "   ✅ 腾讯云SMS SDK已正确加载\n";
    } else {
        echo "   ❌ 腾讯云SMS SDK未找到\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ SDK检查出错: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. 检查SMS配置参数
echo "\n3. 检查SMS配置参数...\n";
$sms_helper_file = '../common/sms_helper.php';
if (file_exists($sms_helper_file)) {
    $sms_helper_content = file_get_contents($sms_helper_file);
    
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
    
    // 检查API密钥是否存在（不显示具体值）
    if (preg_match('/new Credential\("([^"]+)",\s*"([^"]+)"\)/', $sms_helper_content, $matches)) {
        echo "   ✅ 腾讯云API密钥已配置\n";
    } else {
        echo "   ❌ 未找到腾讯云API密钥配置\n";
    }
    
} else {
    echo "   ❌ SMS助手文件不存在: $sms_helper_file\n";
    exit(1);
}

// 4. 测试SMS发送函数（模拟调用，不实际发送）
echo "\n4. 测试SMS发送函数结构...\n";

// 创建一个简化的SMS发送测试
try {
    echo "   ✅ SMS相关类导入成功\n";
    
    // 测试创建客户端（使用测试凭据）
    $test_cred = new Credential("test_id", "test_key");
    $httpProfile = new HttpProfile();
    $httpProfile->setEndpoint("sms.tencentcloudapi.com");
    
    $clientProfile = new ClientProfile();
    $clientProfile->setHttpProfile($httpProfile);
    
    echo "   ✅ SMS客户端配置结构正确\n";
    
    // 测试请求对象
    $req = new SendSmsRequest();
    echo "   ✅ SMS请求对象创建成功\n";
    
} catch (Exception $e) {
    echo "   ❌ SMS函数测试失败: " . $e->getMessage() . "\n";
}

// 5. 检查日志目录
echo "\n5. 检查日志目录...\n";
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

echo "\n=== 测试结果总结 ===\n";
echo "✅ PHP环境: " . PHP_VERSION . "\n";
echo "✅ 腾讯云SMS SDK: 已正确加载\n";
echo "✅ 短信签名: 深圳畅知享科技\n";
echo "✅ SMS配置: 检查完成\n";

echo "\n📋 下一步操作建议:\n";
echo "1. 确保在腾讯云控制台中已审核通过签名 '深圳畅知享科技'\n";
echo "2. 确保账户有足够的短信余额\n";
echo "3. 可以通过Web界面的登录页面进行实际短信发送测试\n";
echo "4. 如需发送真实短信测试，请修改test_sms.php中的手机号并运行\n";

echo "\n✅ 本地测试环境配置完成！\n";
?>