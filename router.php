<?php
// PHP内置服务器路由文件
// 用于正确处理静态资源和React应用的路由

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

// 处理pseudo_compiler/build目录下的静态资源
if (preg_match('/^\/pseudo_compiler\/build\/(.+)$/', $uri, $matches)) {
    $file_path = __DIR__ . '/pseudo_compiler/build/' . $matches[1];
    
    if (file_exists($file_path) && is_file($file_path)) {
        // 获取文件扩展名并设置正确的Content-Type
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
        $mime_types = [
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'ttf' => 'font/ttf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'eot' => 'application/vnd.ms-fontobject',
            'map' => 'application/json'
        ];
        
        $content_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';
        
        // 设置CORS和安全头
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('X-Frame-Options: ALLOWALL'); // 允许在任何iframe中加载
        header('X-Content-Type-Options: nosniff');
        
        // 防止缓存问题
        if ($extension === 'html') {
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        } else {
            // 设置缓存头
            header('Cache-Control: public, max-age=31536000'); // 1年缓存
        }
        
        // 设置Content-Type头
        header('Content-Type: ' . $content_type);
        
        // 设置Content-Length头
        header('Content-Length: ' . filesize($file_path));
        
        // 输出文件内容
        readfile($file_path);
        exit; // 确保脚本在这里结束
    }
}

// 处理其他静态资源（CSS, JS, 图片等）
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|ttf|woff|woff2|eot|map)$/', $uri)) {
    $file_path = __DIR__ . $uri;
    if (file_exists($file_path) && is_file($file_path)) {
        return false; // 让PHP内置服务器处理
    }
}

// 对于所有其他请求，让PHP内置服务器处理
return false;