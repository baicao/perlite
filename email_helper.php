<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 添加邮件发送函数
function send_email($to, $subject, $message, $isHtml = false) {
    // 使用 Composer 的自动加载
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_USERNAME, EMAIL_FROM);
        $mail->addAddress($to);
        
        // 设置字符编码为 UTF-8
        $mail->CharSet = 'UTF-8';
        
        $mail->isHTML($isHtml);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        $mail->SMTPDebug = 2; // 启用 SMTP 调试
        $mail->Debugoutput = 'error_log'; // 将调试信息输出到错误日志
        
        $mail->send();
        log_message('邮件发送成功：' . $to);
        return true;
    } catch (Exception $e) {
        log_message('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>