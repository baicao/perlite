<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../.styles/login.css" type="text/css"> <!-- 引入 login.css -->
    <title>服务器开小差</title>
</head>

<body>
    <div class="error-container">
        <h2>
            服务器开小差
        </h2>    
        <p>
            <?php
            // 从会话中获取错误信息，默认为默认消息
            $error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '抱歉，服务器遇到了一些问题, 请稍后再试。';
            echo htmlspecialchars($error_message);
            ?>   
        </p> 
        <p>请微信联系管理员 ByteSwan</p>
        <div class="additional-links">
            <a href="/" class="link-button">返回首页</a>
        </div>
        
    </div>     
</body>
</html>
