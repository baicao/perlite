<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "Wxy2024qwe";

// 连接到应用数据库
$app_dbname = "changedu";
$app_conn = new mysqli($servername, $username, $password, $app_dbname);
if ($app_conn->connect_error) {
    die("Connection to app database failed: " . $app_conn->connect_error);
}


$client_ip = get_client_ip();
if($client_ip == "127.0.0.1"){
    define('LOG_FILE', "login_debug.log"); 
}else{
    define('LOG_FILE', "/home/lighthouse/test/perlite/login_debug.log"); 
}
define('SITE_URL', 'https://'.$client_ip.'/');
define('SITE_TITLE', 'Chang Edu'); // 替换为您的实际域名

// 邮件设置
define('EMAIL_FROM', 'ChangEdu'); // 替换为您的发件邮箱地址

// SMTP设置
define('SMTP_HOST', 'smtp.qq.com'); // QQ邮箱的SMTP服务器
define('SMTP_PORT', 465); // QQ邮箱的SMTP端口
define('SMTP_USERNAME', '909019241@qq.com'); // 您的QQ邮箱地址
define('SMTP_PASSWORD', 'kergxquzhkzebdbe'); // 您的QQ邮箱SMTP授权码

function get_client_ip() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // 来自共享互联网的 IP
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // 检查通过代理服务器的 IP
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // 直接访问的 IP
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

function log_message($message) {
    $unique_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : 'unknown';
    $current_time = date('Y-m-d H:i:s'); // 获取当前日期和时间
    $log_entry = "[" . $current_time . "] [" . $unique_id . "] " . $message . "\r\n";
    error_log($log_entry, 3, LOG_FILE);

    // 将日志写入文件或其他日志存储
    // $result = file_put_contents('login_debug.log', $log_entry . PHP_EOL, FILE_APPEND);
    // if ($result === false) {
    //     error_log("Failed to write to log file", 0);
    // }
}
?>
