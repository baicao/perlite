<?php
require_once __DIR__ . '/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



$servername = "localhost";
$ip_list = get_client_ip();
$private_ip = $ip_list[0];
$public_ip = $ip_list[1];
if($private_ip == "127.0.0.1"){
    $username = "root";
    $password = "Wxy2024qwe";
    define('SITE_URL', 'http://'.$public_ip.'/');
}else{
    $username = "changedu";
    $password = "fkKen4zaZf7EsCPa";
    define('SITE_URL', 'https://'.$public_ip.'/');
}
// 连接到应用数据库
$app_dbname = "changedu";
$app_conn = new mysqli($servername, $username, $password, $app_dbname);
if ($app_conn->connect_error) {
    die("Connection to app database failed: " . $app_conn->connect_error);
}

// 生成 sessionid
if (empty($_SESSION['unique_id'])) {
    // 生成 sessionID
    $sessionId = generateSessionId();
    $_SESSION['unique_id'] = $sessionId;
    // 获取或创建 device ID
    $deviceId = getOrCreateDeviceId();
    // 获取用户设备信息
    $deviceInfo = getUserDevice();
    log_message("Device id is $deviceId, Device info is ".json_encode($deviceInfo));
}




define('SITE_TITLE', 'Chang Edu'); // 替换为您的实际域名

// 邮件设置
define('EMAIL_FROM', 'ChangEdu'); // 替换为您的发件邮箱地址

// SMTP设置
define('SMTP_HOST', 'smtp.qq.com'); // QQ邮箱的SMTP服务器
define('SMTP_PORT', 465); // QQ邮箱的SMTP端口
define('SMTP_USERNAME', '909019241@qq.com'); // 您的QQ邮箱地址
define('SMTP_PASSWORD', 'kergxquzhkzebdbe'); // 您的QQ邮箱SMTP授权码

function get_client_ip() {
    $private_ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // 来自共享互联网的 IP
        $private_ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // 检查通过代理服务器的 IP
        $private_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // 直接访问的 IP
        $private_ip = $_SERVER['REMOTE_ADDR'];
    }
    if($private_ip == "127.0.0.1"){
        $public_ip = "localhost:8080";
    }else{
        $public_ip = "changedu.com.cn";
    }

    return [$private_ip, $public_ip];
}

function log_message($message) {
    static $log = null;
    if ($log === null) {
        $log = new Logger('main');
        $logDir = __DIR__ . '/logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/login_debug_' . date('Y-m-d') . '.log';
        $stream = new StreamHandler($logFile, Logger::DEBUG);
        $formatter = new LineFormatter(null, null, false, true);
        $stream->setFormatter($formatter);
        $log->pushHandler($stream);
    }
    $unique_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : 'unknown';
    $log_entry = "[" . $unique_id . "] " . $message;
    $log->info($log_entry);
}

// 获取用户当前的设备信息和生成 sessionID 的脚本
function getUserDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $deviceType = 'Unknown';
    if (preg_match('/mobile|android|iphone|ipad|ipod/i', $userAgent)) {
        $deviceType = 'Mobile';
    } elseif (preg_match('/macintosh|windows|linux/i', $userAgent)) {
        $deviceType = 'Desktop';
    }
    
    return [
        'userAgent' => $userAgent,
        'deviceType' => $deviceType
    ];
}

function generateSessionId() {
    return bin2hex(random_bytes(16));
}

function getOrCreateDeviceId() {
    // 检查是否已经存在 device_id 的 cookie
    if (isset($_COOKIE['device_id'])) {
        return $_COOKIE['device_id'];
    }
    
    // 如果不存在，则生成一个新的 device_id
    $deviceId = bin2hex(random_bytes(16));
    // 设置 cookie，有效期为 1 年
    setcookie('device_id', $deviceId, time() + (365 * 24 * 60 * 60), "/");
    
    return $deviceId;
}


?>
