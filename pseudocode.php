<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/permissions.php';

// 检查用户是否已登录
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$title = $siteTitle . " - 伪代码编译器";
$menu = menu($rootDir, '', false);

// 检查必要的变量
if (!isset($vaultName)) {
    $vaultName = $siteTitle;
}
if (!isset($font_size)) {
    $font_size = 16;
}

// 用户头像逻辑
$logoSrc = 'images/logo.svg'; // 默认 logo
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']["gender"] === 'male') {
        $logoSrc = 'images/boy.png'; // 男性 logo
    } elseif ($_SESSION['user']["gender"] === 'female') {
        $logoSrc = 'images/girl.png'; // 女性 logo
    } else {
        $logoSrc = 'images/others.png'; // 其他 logo
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    
    <?php echo loadSettings($rootDir); ?>
    
    <title><?php echo $title ?></title>
    
    <link rel="stylesheet" href=".styles/app.min.css" type="text/css">
    <link id="highlight-js" rel="stylesheet" href=".styles/atom-one-dark.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/perlite.min.css" type="text/css">
    <!-- <link rel="stylesheet" href=".styles/vis-network.min.css" type="text/css"> -->
    <link rel="stylesheet" href=".styles/katex.min.css" type="text/css">
    
    <!-- 伪代码编译器样式 -->
    <link href=".styles/css/main.7de54078.css" rel="stylesheet">
   
    <script src=".js/jquery.min.js"></script>
    <script src=".js/highlight.min.js"></script>
    <script src=".js/katex.min.js"></script>
    <script src=".js/auto-render.min.js"></script>
    <!-- <script src=".js/vis-network.min.js"></script> -->
    <!-- <script src=".js/mermaid.min.js"></script> -->
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
        
        /* 伪代码编译器容器样式 */
        .pseudocode-container {
            width: 100%;
            height: calc(100vh - 100px);
            background: var(--background-primary);
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* 适配主题 */
        .theme-dark .pseudocode-container {
            background: var(--background-primary);
        }
    </style>
</head>

<body
    class="theme-light mod-windows is-frameless is-hidden-frameless obsidian-app show-inline-title show-view-header is-maximized"
    style="--zoom-factor:1; --font-text-size: <?php echo $font_size; ?>px;">
    
    <div class="titlebar">
        <div class="titlebar-inner">
            <div class="titlebar-button-container mod-left">
                <div class="titlebar-button mod-logo">
                </div>
            </div>
        </div>
    </div>

    <div class="app-container">
        <div class="horizontal-main-container">
            <div class="workspace is-left-sidedock-open">
                <div class="workspace-ribbon side-dock-ribbon mod-left">
                    <a href="user_profile.php" class="logo-link">
                        <img src="<?php echo $logoSrc; ?>" height="25" class="logo" alt="User Logo">
                        <div class="user-dropdown">
                            <a href="user_profile.php">账号信息</a>
                            <a href="logout.php">登出</a>
                        </div>
                    </a>

                    <div class="sidebar-toggle-button mod-left sidebar" aria-label="" aria-label-position="right">
                        <div class="clickable-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="svg-icon sidebar-left">
                                <path d="M21 3H3C1.89543 3 1 3.89543 1 5V19C1 20.1046 1.89543 21 3 21H21C22.1046 21 23 20.1046 23 19V5C23 3.89543 22.1046 3 21 3Z"></path>
                                <path d="M10 4V20"></path>
                                <path d="M4 7H7"></path>
                                <path d="M4 10H7"></path>
                                <path d="M4 13H7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="workspace-split mod-horizontal mod-left-split" style="width: 450px;">
                    <hr class="workspace-leaf-resize-handle left-dock">
                    <div class="workspace-tabs mod-top mod-top-left-space">
                        <hr class="workspace-leaf-resize-handle">
                        <div class="workspace-tab-header-container">
                            <div class="workspace-tab-header-container-inner" style="--animation-dur:250ms;">
                                <div class="workspace-tab-header is-active" draggable="true" aria-label="Files"
                                    aria-label-delay="50" data-type="file-explorer">
                                    <div class="workspace-tab-header-inner">
                                        <div class="workspace-tab-header-inner-icon"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="svg-icon lucide-folder-closed">
                                                <path
                                                    d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z">
                                                </path>
                                                <path d="M2 10h20"></path>
                                            </svg></div>
                                        <div class="workspace-tab-header-inner-title">Files</div>
                                        <div class="workspace-tab-header-status-container"></div>
                                        <div class="workspace-tab-header-inner-close-button" aria-label="Close"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="svg-icon lucide-x">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg></div>
                                    </div>
                                </div>
                            </div>
                            <div class="workspace-tab-header-new-tab"><span class="clickable-icon"
                                    aria-label="New tab"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="svg-icon lucide-plus">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg></span></div>
                            <div class="workspace-tab-header-spacer"></div>
                            <div class="workspace-tab-header-tab-list"><span class="clickable-icon"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="svg-icon lucide-chevron-down">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg></span></div>
                        </div> <!-- left dock -->
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
                                                    <?php echo $vaultName ?>
                                                </div>
                                            </div>
                                            <div class="tree-item-children nav-folder-children">
                                                <div style="width: 276px; height: 0.1px; margin-bottom: 0px;"></div>
                                                <?php echo $menu ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 主内容区域 -->
                <div class="workspace-split mod-vertical mod-root">
                    <div class="workspace-tabs mod-top">
                        <div class="workspace-tab-header-container">
                            <div class="workspace-tab-header-container-inner">
                                <div class="workspace-tab-header is-active">
                                    <div class="workspace-tab-header-inner">
                                        <div class="workspace-tab-header-inner-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="svg-icon">
                                                <polyline points="16 18 22 12 16 6"></polyline>
                                                <polyline points="8 6 2 12 8 18"></polyline>
                                            </svg>
                                        </div>
                                        <div class="workspace-tab-header-inner-title">编译器</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="workspace-tab-container">
                            <div class="workspace-leaf">
                                <div class="workspace-leaf-content">
                                    <div class="view-content">
                                        <!-- React应用容器 -->
                                        <div class="pseudocode-container">
                                            <div id="root"></div>
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

    <!-- 伪代码编译器脚本 -->
    <script src=".styles/js/main.182d4a2d.js"></script>
    
    <!-- 主网站脚本 -->
    <!-- <script src=".js/perlite.min.js"></script> -->
</body>
</html>
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

<body
    class="theme-light mod-windows is-frameless is-hidden-frameless obsidian-app show-inline-title show-view-header is-maximized"
    style="--zoom-factor:1; --font-text-size: <?php echo $font_size; ?>px;">
    <title>
        <?php echo $title ?>
    </title>

    <div class="titlebar">
        <div class="titlebar-inner">
            <div class="titlebar-button-container mod-left">
                <div class="titlebar-button mod-logo">
                </div>
            </div>
        </div>
    </div>

    <div class="app-container">
        <div class="horizontal-main-container">
            <div class="workspace is-left-sidedock-open">
                <div class="workspace-ribbon side-dock-ribbon mod-left">
                    <a href="user_profile.php" class="logo-link">
                    <img src="<?php echo $logoSrc; ?>" height="25" class="logo" alt="User Logo">
                        <div class="user-dropdown">
                            <a href="user_profile.php">账号信息</a>
                            <a href="logout.php">登出</a>
                        </div>
                    </img>
                    </a>

                    <div class="sidebar-toggle-button mod-left sidebar" aria-label="" aria-label-position="right">

                        <div class="clickable-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="svg-icon sidebar-left">
                                <path
                                    d="M21 3H3C1.89543 3 1 3.89543 1 5V19C1 20.1046 1.89543 21 3 21H21C22.1046 21 23 20.1046 23 19V5C23 3.89543 22.1046 3 21 3Z">
                                </path>
                                <path d="M10 4V20"></path>
                                <path d="M4 7H7"></path>
                                <path d="M4 10H7"></path>
                                <path d="M4 13H7"></path>
                            </svg></div>
                    </div>
                    <div class="side-dock-actions">
                        <div class="clickable-icon side-dock-ribbon-action" aria-label="Open graph view"
                            aria-label-position="right"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="svg-icon lucide-git-fork">
                                <circle cx="12" cy="18" r="3"></circle>
                                <circle cx="6" cy="6" r="3"></circle>
                                <circle cx="18" cy="6" r="3"></circle>
                                <path d="M18 9v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9"></path>
                                <path d="M12 12v3"></path>
                            </svg></div>
                    </div>
                </div>
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
                                                        <!-- mdContent元素，用于导航功能 -->
                                                        <div id="mdContent"></div>
                                                        <!-- toc元素，用于目录功能 -->
                                                        <div id="toc"></div>
                                                        
                                                        <!-- 嵌入伪代码编译器 -->
                                                        <iframe src="http://localhost:3000" 
                                                                id="static-compiler-iframe"
                                                                class="pseudocode-content"
                                                                frameborder="0"
                                                                allowfullscreen
                                                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; display: none;">
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
            // 用户下拉菜单功能
            const logoLink = document.querySelector('.logo-link');
            const userDropdown = document.querySelector('.user-dropdown');
            let dropdownTimer;

            if (logoLink && userDropdown) {
                logoLink.addEventListener('mouseenter', function() {
                    clearTimeout(dropdownTimer);
                    userDropdown.style.display = 'block';
                });

                logoLink.addEventListener('mouseleave', function() {
                    dropdownTimer = setTimeout(function() {
                        userDropdown.style.display = 'none';
                    }, 200);
                });
                
                userDropdown.addEventListener('mouseenter', function() {
                    clearTimeout(dropdownTimer);
                    userDropdown.style.display = 'block';
                });
                
                userDropdown.addEventListener('mouseleave', function() {
                    userDropdown.style.display = 'none';
                    clearTimeout(dropdownTimer);
                });
            }
            
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