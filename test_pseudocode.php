<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>伪代码编译器测试页面</title>
    <link rel="stylesheet" href=".styles/perlite.min.css">
    <link rel="stylesheet" href=".styles/app.min.css">
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        #mdContent {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            min-height: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>伪代码编译器测试页面</h1>
        <div id="mdContent">
            <!-- React应用将直接在这里渲染 -->
            <div id="root"></div>
            
            <!-- 加载React应用的静态资源 -->
            <link rel="stylesheet" href="/pseudo_compiler/build/static/css/main.55e66a0c.css">
            <script src="/pseudo_compiler/build/static/js/main.6c06c7b9.js"></script>
        </div>
    </div>
</body>
</html>