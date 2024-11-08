<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>收款信息</title>
    <link rel="stylesheet" href="login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #f0f0f0;
            padding: 0;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .card {
            width: 90%;
            max-width: 300px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            position: relative;
        }
        .card h3 {
            margin-top: 0;
            font-size: 1.4em;
            color: #333;
        }
        .info {
            margin-bottom: 10px;
            font-size: 1em;
            word-wrap: break-word;
        }
        .copy-btn {
            cursor: pointer;
            color: #8a5cf5;
            margin-left: 5px;
            font-weight: bold;
            text-decoration: underline;
        }
        .copy-btn:hover {
            color: #7a4ce5;
        }
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 10px auto;
            display: block;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        .qr-label {
            font-size: 1em;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin: 5px auto;
        }
        @media (max-width: 600px) {
            .card {
                width: 95%;
                padding: 15px;
            }
            .card h3 {
                font-size: 1.2em;
            }
            .info {
                font-size: 0.9em;
            }
            .qr-code {
                width: 120px;
                height: 120px;
            }
            .qr-label {
                font-size: 0.9em;
            }
        }
    </style>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("已复制: " + text);
            }, () => {
                alert("复制失败");
            });
        }
    </script>
</head>
<body>

<div class="card-container">
    <?php
    // 定义收款信息数组
    $receivers = [
        [
            "type" => "BankCard",
            "name" => "王翔宇", 
            "account" => "浦发银行", 
            "bank" => "浦发银行深圳科技支行", 
            "accountNumber" => "6217921181139075", 
            
        ],
        [
            "type" => "QRPayCode",
            "name" => "支付小助手1-王翔宇", 
            "wechatQR" => "./images/pay.png"
        ],
        [
            "type" => "QRCode",
            "name" => "小助手1-ByteSwan", 
            "wechatQR" => "./images/concact.png"
        ],
        [
            "type" => "QRPayCode",
            "name" => "支付小助手2-王翔宇", 
            "wechatQR" => "./images/pay2.png"
        ],
        [
            "type" => "QRCode",
            "name" => "小助手2-小宇", 
            "wechatQR" => "./images/concact2.png"
        ],
        [
            "type" => "BankCard",
            "name" => "深圳畅知享科技有限公司", 
            "account" => "中国工商银行", 
            "bank" => "深圳西乡支行", 
            "accountNumber" => "4000023409200903985", 
            
        ],
        // 可以继续添加更多收款信息
    ];

    // 遍历数组并输出每张卡片
    foreach ($receivers as $receiver) {
        if($receiver['type']=="BankCard"){
        // 银行卡信息卡片
        echo "<div class='card'>
                <h3>银行卡收款</h3>
                <div class='info'>收款人: {$receiver['name']} <span class='copy-btn' onclick=\"copyToClipboard('{$receiver['name']}')\">复制</span></div>
                <div class='info'>收款账户: {$receiver['account']} <span class='copy-btn' onclick=\"copyToClipboard('{$receiver['account']}')\">复制</span></div>
                <div class='info'>开户行: {$receiver['bank']} <span class='copy-btn' onclick=\"copyToClipboard('{$receiver['bank']}')\">复制</span></div>
                <div class='info'>账号: {$receiver['accountNumber']} <span class='copy-btn' onclick=\"copyToClipboard('{$receiver['accountNumber']}')\">复制</span></div>
              </div>";
        }elseif($receiver['type']=="QRPayCode"){
        // 微信二维码卡片
        echo "<div class='card'>
                <h3>微信收款-{$receiver['name']}</h3>
                <img src='{$receiver['wechatQR']}' alt='微信二维码' class='qr-code'>
              </div>";
        }elseif($receiver['type']=="QRCode"){
            // 微信二维码卡片
            echo "<div class='card'>
                    <h3>付款后进群-{$receiver['name']}</h3>
                    <img src='{$receiver['wechatQR']}' alt='微信二维码' class='qr-code'>
                  </div>";
            }



    }
    ?>
</div>

</body>
</html>
