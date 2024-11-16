<?php
   require_once __DIR__ .'/handlers/email_helper.php';
   
   $to = '909019241@qq.com';
   $subject = '测试邮件';
   $message = '这是一封测试邮件，用于验证邮件发送功能是否正常工作。';
   
   if (send_email($to, $subject, $message)) {
       echo "测试邮件发送成功！";
   } else {
       echo "测试邮件发送失败。";
   }
   error_log("这是一个error错误");
   log_message("这是一个log错误");
   var_dump(phpinfo());
   ?>