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


// 邮件设置
define('SITE_URL', 'http://localhost:8080/'); // 替换为您的实际域名
define('EMAIL_FROM', 'ChangEdu'); // 替换为您的发件邮箱地址

// SMTP设置
define('SMTP_HOST', 'smtp.qq.com'); // QQ邮箱的SMTP服务器
define('SMTP_PORT', 465); // QQ邮箱的SMTP端口
define('SMTP_USERNAME', '909019241@qq.com'); // 您的QQ邮箱地址
define('SMTP_PASSWORD', 'kergxquzhkzebdbe'); // 您的QQ邮箱SMTP授权码



function log_message($message) {
    $unique_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : 'unknown';
    $log_entry = "[" . $unique_id . "] " . $message;
    // 将日志写入文件或其他日志存储
    file_put_contents('login_debug.log', $log_entry . PHP_EOL, FILE_APPEND);
}
?>
