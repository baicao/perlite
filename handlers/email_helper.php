<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 添加邮件发送函数
function send_email($to, $subject, $message, $isHtml = true) {
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

        // 使用 HTML 格式化邮件内容
        $mail->Body = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: auto;
                    background: #fff;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                h2 {
                    color: #333;
                }
                .code {
                    font-size: 24px;
                    font-weight: bold;
                    color: #8a5cf5;
                    padding: 10px;
                    display: inline-block;
                }
                p {
                    color: #8a5cf5;
                }

            </style>
        </head>
        <body>
            <div class="container">' .$message .'</div>
        </body>
        </html>
        ';
        
        $mail->SMTPDebug = 2; // 启用 SMTP 调试
        $mail->Debugoutput = 'error_log'; // 将调试信息输出到错误日志
        
        $mail->send();
        log_message('Email send success' . $to);
        return true;
    } catch (Exception $e) {
        log_message('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>