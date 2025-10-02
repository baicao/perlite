<?php
require_once __DIR__ . '/vendor/autoload.php';
// 不加载需要数据库连接的配置文件
// require_once 'config.php';
require_once 'helper.php';
// require_once 'permissions.php';

// 简化的配置，只设置必要的变量
$vaultName = "Changedu";
$mdDir = __DIR__ . "/content"; // 假设内容目录

// 生成导航菜单（如果目录存在）
$menu = '';
if (is_dir($mdDir)) {
    $menu = menu($mdDir);
}

// 模拟用户信息
$user = null;
$gender = 'male';
$logoFile = 'logo.svg';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>伪代码编译器 - <?php echo $vaultName ?></title>
    
    <!-- 主网站样式 -->
    <link rel="stylesheet" href=".styles/app.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/atom-one-dark.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/perlite.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/katex.min.css" type="text/css">
    
    <!-- 伪代码编译器样式 -->
    <link rel="stylesheet" href="pseudo_compiler/prism.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- 主网站脚本 -->
    <script src=".js/jquery.min.js"></script>
    <script src=".js/highlight.min.js"></script>
    <script src=".js/katex.min.js"></script>
    <script src=".js/auto-render.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@11.2.1/dist/mermaid.min.js"></script>
    
    <style>
        .logo-link {
            position: relative;
        }
        .logo-link:hover .user-dropdown {
            display: block;
        }
        .user-dropdown {
            display: none;
            position: absolute;
            top: 30px;
            left: 10px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 150px;
            z-index: 1000;
        }
        .user-dropdown a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .user-dropdown a:hover {
            background-color: #f0f0f0;
        }
        .user-dropdown::before {
            content: '';
            position: absolute;
            top: -10px;
            right: 10px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent #ddd transparent;
        }
        .user-dropdown::after {
            content: '';
            position: absolute;
            top: -9px;
            right: 10px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent #ffffff transparent;
        }
        
        /* 伪代码编译器内容区域样式 */
        .pseudocode-content {
            width: 100%;
            height: calc(100vh - 100px);
            border: none;
            overflow: hidden;
        }
    </style>
</head>
<body class="theme-dark">
    <div class="app-container">
        <div class="horizontal-main-container">
            <div class="workspace">
                <div class="workspace-split mod-horizontal mod-left-split">
                    <div class="workspace-tabs mod-top">
                        <div class="workspace-tab-header-container">
                            <div class="workspace-tab-header-tab-list"><span class="clickable-icon"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="svg-icon lucide-chevron-down">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg></span></div>
                        </div>
                        <div class="workspace-tab-container">
                            <div class="workspace-leaf">
                                <hr class="workspace-leaf-resize-handle">
                                <div class="workspace-leaf-content" data-type="file-explorer">
                                    <!-- nav sidebar-left -->
                                    <div class="nav-header">
                                        <div class="nav-buttons-container">
                                            <div class="clickable-icon nav-action-button" aria-label="Collapse all"
                                                style="display: none"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="svg-icon lucide-chevrons-down-up">
                                                    <path d="m7 20 5-5 5 5"></path>
                                                    <path d="m7 4 5 5 5-5"></path>
                                                </svg></div>
                                            <div class="clickable-icon nav-action-button" aria-label="Expand all"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="svg-icon lucide-chevrons-up-down">
                                                    <path d="m7 15 5 5 5-5"></path>
                                                    <path d="m7 9 5-5 5 5"></path>
                                                </svg></div>
                                        </div>
                                    </div>
                                    <div class="nav-files-container node-insert-event" style="position: relative;">
                                        <div class="tree-item nav-folder mod-root">
                                            <div class="tree-item-self nav-folder-title" data-path="/">
                                                <div class="tree-item-inner nav-folder-title-content">
                                                    <a href="." style="color: inherit; text-decoration: none;">
                                                        <?php echo $vaultName ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="tree-item-children nav-folder-children">
                                                <div style="width: 276px; height: 0.1px; margin-bottom: 0px;"></div>
                                                <!-- 伪代码编译器链接 -->
                                                <div class="tree-item nav-file">
                                                    <div class="nav-file-title" style="background-color: var(--background-modifier-active-hover);">
                                                        <div class="nav-file-title-content">
                                                            <i class="fas fa-code" style="margin-right: 8px;"></i>
                                                            伪代码编译器
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php echo $menu ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="workspace-split mod-vertical mod-root">
                    <hr class="workspace-leaf-resize-handle">
                    <div class="workspace-tabs mod-active mod-top mod-top-right-space">
                        <hr class="workspace-leaf-resize-handle">
                        <div class="workspace-tab-container">
                            <div class="workspace-leaf mod-active">
                                <hr class="workspace-leaf-resize-handle">
                                <div class="workspace-leaf-content" data-type="markdown" data-mode="source">
                                    <div class="view-header">
                                        <div class="view-actions mobile-display" style="display: flex">
                                            <div class="sidebar-toggle-button mod-left mobile-display" aria-label=""
                                                aria-label-position="right">
                                                <div class="clickable-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="svg-icon lucide-book-open">
                                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="view-header-title-container mod-at-start">
                                                <div class="view-header-title">伪代码编译器</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-content">
                                        <div class="markdown-source-view mod-cm6 is-readable-line-width">
                                            <div class="cm-editor ͼ1 ͼ2 ͼ4">
                                                <div class="cm-scroller">
                                                    <div class="cm-content" style="padding: 0; height: 100%;">
                                                        <!-- 嵌入伪代码编译器 -->
                                                        <iframe src="pseudo_compiler/index.html" 
                                                                class="pseudocode-content"
                                                                frameborder="0"
                                                                allowfullscreen>
                                                        </iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="workspace-ribbon side-dock-ribbon mod-right is-collapsed"></div>
            </div>
        </div>
    </div>

    <script src=".js/perlite.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 移动端侧边栏切换
            const sidebarToggle = document.querySelector('.sidebar-toggle-button');
            const leftSplit = document.querySelector('.workspace-split.mod-left-split');
            
            if (sidebarToggle && leftSplit) {
                sidebarToggle.addEventListener('click', function() {
                    leftSplit.style.display = leftSplit.style.display === 'none' ? 'flex' : 'none';
                });
            }
        });
    </script>
</body>
</html>