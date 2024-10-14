<!DOCTYPE html>
<html>

<?php

/*!
 * Version v1.5.9
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/permissions.php';

$title = $siteTitle;
$menu = menu($rootDir);
$jsonGraphData = getfullGraph($rootDir);


// 获取当前请求的页面
$current_page = isset($_GET['link']) ? $_GET['link'] : '/Chang Edu Home';
// 如果不是访问 "Chang Edu Home.md"，则检查用户是否已登录
if (isset($_GET['link']) && $current_page !== '/Chang Edu Home'){
    if (!isset($_SESSION['user_id']) || 
    !isset($_SESSION['last_activity']) ||
     (time() - $_SESSION['last_activity'] > $_SESSION['expire_time'])) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
} 


?>

<!-- 
/*!
  * Perlite (https://github.com/secure-77/Perlite)  
  * Author: sec77 (https://secure77.de)
  * Licensed under MIT (https://github.com/secure-77/Perlite/blob/main/LICENSE)
*/
-->


<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

    <?php echo loadSettings($rootDir); ?>
    <link rel="stylesheet" href=".styles/app.css" type="text/css">
    <link id="highlight-js" rel="stylesheet" href=".styles/atom-one-dark.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/perlite.css" type="text/css">
    <link rel="stylesheet" href=".styles/vis-network.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/katex.min.css" type="text/css">
   
    <script src=".js/jquery.min.js"></script>
    <script src=".js/highlight.min.js"></script>
    <script src=".js/vis-network.min.js"></script>
    <script src=".js/katex.min.js"></script>
    <script src=".js/auto-render.min.js"></script>
    <script src=".js/vis-network.min.js"></script>
    <!-- <script src=".js/mermaid.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@11.2.1/dist/mermaid.min.js"></script>


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
                <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                    width="128.000000pt" height="128.000000pt" viewBox="0 0 128.000000 128.000000"
                    preserveAspectRatio="xMidYMid meet" class="logo-full">
                    <metadata>
                    Created by potrace 1.16, written by Peter Selinger 2001-2019
                    </metadata>
                    <g transform="translate(0.000000,128.000000) scale(0.100000,-0.100000)"
                    fill="#000000" stroke="none">
                    <path d="M0 640 l0 -640 640 0 640 0 0 640 0 640 -640 0 -640 0 0 -640z m751
                    516 c26 -6 26 -6 17 -63 -5 -31 -14 -63 -20 -70 -6 -7 -30 -13 -54 -13 -37 0
                    -44 3 -44 20 0 11 5 20 12 20 9 0 9 3 0 12 -31 31 3 111 45 103 10 -2 30 -6
                    44 -9z m-132 -33 c17 -59 14 -73 -13 -73 -20 0 -25 6 -28 32 -2 17 -9 33 -16
                    36 -9 3 -11 -6 -6 -36 5 -34 3 -40 -14 -44 -18 -5 -22 1 -31 44 -15 66 -15 66
                    17 71 74 11 80 9 91 -30z m266 20 c44 -21 44 -49 0 -45 -27 2 -28 1 -10 -9 11
                    -7 21 -13 23 -14 1 -1 0 -9 -4 -18 -4 -12 -11 -14 -30 -7 -13 5 -24 6 -24 1 0
                    -5 9 -11 20 -14 22 -6 24 -14 11 -35 -7 -11 -16 -10 -50 8 -23 11 -41 24 -39
                    28 2 4 14 33 27 65 14 31 28 57 33 57 4 0 23 -8 43 -17z m-408 -68 c12 -22 23
                    -42 23 -45 0 -10 -63 -40 -85 -40 -28 0 -43 34 -25 55 8 9 8 15 2 15 -24 0
                    -20 30 6 45 39 21 53 15 79 -30z m509 -33 c17 -16 20 -17 30 -4 13 17 44 10
                    44 -10 -1 -7 -23 -37 -50 -67 l-50 -55 -35 34 c-42 41 -43 55 -8 92 31 33 43
                    34 69 10z m-626 -37 c32 -33 33 -36 17 -52 -16 -16 -18 -15 -45 10 -37 36 -51
                    22 -15 -15 24 -25 25 -30 13 -43 -13 -12 -22 -8 -67 36 -57 54 -59 59 -41 77
                    9 9 16 8 28 -3 15 -14 19 -13 35 5 25 28 36 25 75 -15z m396 -22 c95 -31 170
                    -99 216 -193 18 -37 22 -63 23 -150 0 -91 -3 -111 -23 -152 -38 -76 -96 -134
                    -170 -170 -61 -30 -74 -33 -162 -33 -85 0 -101 3 -153 29 -151 76 -237 259
                    -194 411 8 28 16 57 18 65 9 32 100 133 146 161 80 49 207 63 299 32z m329
                    -50 c4 -12 0 -22 -10 -28 -38 -23 -50 -34 -43 -40 3 -4 20 1 37 10 33 17 51
                    13 51 -10 0 -12 -88 -71 -94 -63 -16 22 -46 78 -46 87 0 12 68 59 86 60 7 1
                    15 -7 19 -16z m-886 -2 c10 -7 11 -13 1 -31 -11 -20 -10 -25 6 -37 26 -19 45
                    -16 51 7 6 24 36 23 41 -1 4 -23 -36 -69 -61 -69 -12 0 -37 9 -54 20 -36 21
                    -42 49 -21 95 12 27 16 29 37 16z m156 -610 c-3 -7 4 -20 15 -28 11 -8 20 -20
                    20 -28 0 -8 6 -12 14 -9 8 3 24 1 35 -5 16 -9 22 -8 29 4 12 23 44 8 37 -16
                    -4 -13 -1 -19 8 -19 30 0 39 -26 26 -69 -7 -23 -13 -41 -15 -41 -24 0 -34 16
                    -28 46 8 42 -3 44 -18 3 -7 -20 -16 -29 -27 -26 -17 3 -17 5 -6 48 8 31 -21
                    50 -32 22 -4 -9 -1 -19 5 -21 15 -5 16 -32 1 -32 -26 0 -49 22 -49 48 -1 23
                    -2 25 -11 10 -9 -17 -12 -17 -39 -3 -36 18 -40 55 -10 85 31 31 2 36 -30 5
                    -28 -27 -50 -27 -50 0 0 8 13 24 30 36 20 16 27 26 20 34 -6 7 -5 16 2 23 9 9
                    20 5 45 -20 19 -19 31 -38 28 -47z m681 37 l45 -43 -31 -33 c-16 -18 -34 -31
                    -39 -28 -5 3 -8 -2 -7 -12 1 -12 -8 -18 -26 -20 -16 -2 -25 -8 -22 -15 2 -7
                    -12 -21 -32 -32 -40 -21 -38 -22 -75 49 -17 31 -18 40 -7 47 20 13 22 12 42
                    -26 21 -39 24 -40 49 -21 16 13 16 15 -8 30 -33 22 -31 61 2 61 28 0 56 35 41
                    52 -11 13 -3 33 13 33 6 0 30 -19 55 -42z m-372 -134 c26 -10 19 -34 -10 -34
                    -22 0 -25 -4 -22 -27 4 -35 30 -37 38 -3 12 50 85 65 97 21 3 -14 13 -19 31
                    -18 18 1 27 -4 30 -16 4 -21 -27 -35 -38 -17 -6 9 -13 7 -29 -9 -23 -23 -51
                    -27 -69 -9 -9 9 -12 9 -12 0 0 -7 -15 -12 -40 -12 -29 0 -42 5 -50 19 -32 60
                    16 127 74 105z"/>
                    <path d="M700 1105 c0 -14 4 -25 9 -25 12 0 23 29 15 41 -10 18 -24 9 -24 -16z"/>
                    <path d="M688 1043 c7 -3 16 -2 19 1 4 3 -2 6 -13 5 -11 0 -14 -3 -6 -6z"/>
                    <path d="M943 995 c-3 -9 -3 -19 1 -22 7 -7 36 16 36 28 0 15 -30 10 -37 -6z"/>
                    <path d="M533 952 c-28 -10 -67 -31 -87 -45 -36 -28 -92 -95 -85 -101 8 -6
                    590 -78 595 -73 10 10 -39 98 -77 137 -87 89 -230 123 -346 82z"/>
                    <path d="M320 704 c-30 -146 47 -297 189 -366 69 -35 195 -33 273 4 113 53
                    179 151 186 278 l3 75 -161 -161 -161 -162 -30 44 c-16 24 -33 44 -37 44 -4 0
                    -31 -14 -61 -30 -62 -34 -71 -30 -71 36 0 23 -3 75 -6 117 -6 71 -9 78 -51
                    126 -25 28 -48 51 -53 51 -5 0 -14 -25 -20 -56z"/>
                    <path d="M1000 316 c0 -9 7 -16 16 -16 9 0 14 5 12 12 -6 18 -28 21 -28 4z"/>
                    <path d="M960 282 c0 -12 19 -26 26 -19 2 2 -2 10 -11 17 -9 8 -15 8 -15 2z"/>
                    <path d="M714 169 c-3 -6 -1 -16 5 -22 8 -8 11 -5 11 11 0 24 -5 28 -16 11z"/>
                    </g>
                    </svg>
                    <!-- <svg viewBox="0 0 100 100" width="18" height="18"
                        class="logo-full">
                        <defs>
                            <linearGradient id="a" x1="82.85" y1="30.41" x2="51.26" y2="105.9"
                                gradientTransform="matrix(1, 0, 0, -1, -22.41, 110.97)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#6c56cc"></stop>
                                <stop offset="1" stop-color="#9785e5"></stop>
                            </linearGradient>
                        </defs>
                        <polygon points="62.61,0 30.91,17.52 18,45.45 37.57,90.47 65.35,100 70.44,89.8 81,26.39 62.61,0"
                            fill="#34208c"></polygon>
                        <polygon points="81,26.39 61.44,14.41 34.43,35.7 65.35,100 70.44,89.8 81,26.39" fill="url(#a)">
                        </polygon>
                        <polygon points="81,26.39 81,26.39 62.61,0 61.44,14.41 81,26.39" fill="#af9ff4"></polygon>
                        <polygon points="61.44,14.41 62.61,0 30.91,17.52 34.43,35.7 61.44,14.41" fill="#4a37a0">
                        </polygon>
                        <polygon points="34.43,35.7 37.57,90.47 65.35,100 34.43,35.7" fill="#4a37a0"></polygon>
                    </svg> -->
                    <svg viewBox="0 0 100 100" width="18" height="18" fill="none" stroke="currentColor"
                        stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="logo-wireframe">
                        <path
                            d="M 30.91 17.52 L 34.43 35.7 M 61.44 14.41 L 62.61 0 M 34.43 35.7 L 37.57 90.47 M 81 26.39 L 61.44 14.41 L 34.43 35.7 L 65.35 100 M 62.61 0 L 30.91 17.52 L 18 45.45 L 37.57 90.47 L 65.35 100 L 70.44 89.8 L 81 26.39 L 62.61 0 Z">
                        </path>
                    </svg></div>
            </div>
        </div>
    </div>

    <div class="app-container">
        <div class="horizontal-main-container">
            <div class="workspace is-left-sidedock-open">
                <div class="workspace-ribbon side-dock-ribbon mod-left">

                    <a href="."><img src="logo.svg" height="25" class="logo" alt="ChangEdu Logo"></a>
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
                    <!-- 随机打开一片note，不需要 -->
                    <!-- <div class="side-dock-actions">
                        <div class="clickable-icon side-dock-ribbon-action" aria-label="Open random note"
                            data-tooltip-position="right" data-tooltip-delay="300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="svg-icon dice">
                                <path
                                    d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" />
                                <path
                                    d="M17 16C17 16.5523 16.5523 17 16 17C15.4477 17 15 16.5523 15 16C15 15.4477 15.4477 15 16 15C16.5523 15 17 15.4477 17 16Z" />
                                <path
                                    d="M13 12C13 12.5523 12.5523 13 12 13C11.4477 13 11 12.5523 11 12C11 11.4477 11.4477 11 12 11C12.5523 11 13 11.4477 13 12Z" />
                                <path
                                    d="M9 8C9 8.55228 8.55228 9 8 9C7.44772 9 7 8.55228 7 8C7 7.44772 7.44772 7 8 7C8.55228 7 9 7.44772 9 8Z" />
                            </svg>
                        </div>
                    </div> -->
                    <!-- 设置皮肤和交互的，不需要 -->
                    <!-- <div class="side-dock-settings">
                        <div class="clickable-icon side-dock-ribbon-action" aria-label="Help"
                            aria-label-position="right"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="svg-icon help">
                                <path
                                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z">
                                </path>
                                <path
                                    d="M9.09009 9.00003C9.32519 8.33169 9.78924 7.76813 10.4 7.40916C11.0108 7.05019 12.079 6.94542 12.7773 7.06519C13.9093 7.25935 14.9767 8.25497 14.9748 9.49073C14.9748 11.9908 12 11.2974 12 14">
                                </path>
                                <path d="M12 17H12.01"></path>
                            </svg></div>
                        <div class="clickable-icon side-dock-ribbon-action" aria-label="Settings"
                            aria-label-position="right"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="svg-icon lucide-settings">
                                <path
                                    d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                                </path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg></div>
                    </div> -->
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
                                <div class="workspace-tab-header" draggable="true" aria-label="Search"
                                    aria-label-delay="50" data-type="search">
                                    <div class="workspace-tab-header-inner">
                                        <div class="workspace-tab-header-inner-icon"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="svg-icon lucide-search">
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                            </svg></div>
                                        <div class="workspace-tab-header-inner-title">Search</div>
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
                                    <?php echo $customSection; ?>
                                    <!-- custom page sidebar -->
                                    <!-- <h3 class="sm-site-title"> Perlite</h3> -->
                                    <!-- <div class="sm-site-title">&nbsp;</div>
                                    <div class="custom-page">

                                    
                                    
                                    <img class="custom-page-logo" src="logo1.jpg" alt="Custom Logo">
                                    <div> &nbsp;</div>
                                    <div class="sm-site-desc"><i>IT-Security Notes and Writeups</i></div>

                                    <div><ul class="social-media-list">
                                        <li><a href="https://github.com/secure-77"><img class="social-logo" src="github-color.svg" alt="Custom Logo"></a></li>
                                        <li><a href="https://twitter.com/secure_sec77"><img class="social-logo" src="x-color.svg" alt="Custom Logo"></a></li>
                                        <li><a href="https://secure77.de"><img class="social-logo" src="fontawesome-color.svg" alt="Custom Logo"></a></li>
                                    </ul> -->

                                    <!-- <div><ul class="social-media-list">
                                        <li><img class="social-logo" src="github-color.svg" alt="Custom Logo"> &nbsp;<a href="https://github.com/secure-77">Secure-77</a></li>
                                        <li><img class="social-logo" src="x-color.svg" alt="Custom Logo"> &nbsp;<a href="https://github.com/secure-77">Twitter (X)</a></li>
                                        <li><img class="social-logo" src="fontawesome-color.svg" alt="Custom Logo"> &nbsp;<a href="https://github.com/secure-77">secure77.de</a></li>
                                    </ul>
 
                                    </div>
                                    </div> -->

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
                                                <!-- <div class="nav-folder-collapse-indicator collapse-icon"></div> -->
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
                            <div class="workspace-leaf" style="display: none;">
                                <!-- search container -->
                                <hr class="workspace-leaf-resize-handle">
                                <div class="workspace-leaf-content" data-type="search">
                                    <div class="nav-header">
                                        <div class="nav-buttons-container">
                                            <div class="clickable-icon nav-action-button is-active"
                                                aria-label="Collapse results"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" class="svg-icon lucide-list">
                                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                                </svg></div>
                                        </div>
                                    </div>
                                    <div class="search-input-container"><input enterkeyhint="search" type="search"
                                            placeholder="Type to start search...">
                                        <div class="search-input-clear-button" aria-label="Clear search"
                                            style="display: none;"></div>
                                    </div>
                                    <div class="search-info-container" style="display: none;"></div>
                                    <div class="search-result-container mod-global-search node-insert-event"
                                        style="position: relative;">
                                        <div class="search-results-children" style="min-height: 0px;">
                                            <div style="width: 1px; height: 0.1px; margin-bottom: 0px;"></div>
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
                                        </div>

                                        <div class="view-header-nav-buttons" data-section="close" style="display: none">
                                            <div class="clickable-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" class="svg-icon lucide-x">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg></div>
                                        </div>

                                        <div class="view-header-title-container mod-at-start">
                                            <div class="view-header-title-parent"></div>
                                            <div class="view-header-title" tabindex="-1"></div>
                                        </div>

                                        <div class="view-actions">
                                            <!-- <a class="clickable-icon view-action" aria-label="Click to edit"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="svg-icon lucide-edit-3">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z">
                                                    </path>
                                                </svg></a> -->
                                            <a class="clickable-icon view-action" aria-label="Copy URL">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="svg-icon lucide-link">
                                                    <path
                                                        d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71">
                                                    </path>
                                                    <path
                                                        d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71">
                                                    </path>
                                                </svg>
                                            </a>
                                            <div class="sidebar-toggle-button mod-right" aria-label=""
                                                aria-label-position="left">
                                                <div class="clickable-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="svg-icon sidebar-right">
                                                        <path
                                                            d="M3 3H21C22.1046 3 23 3.89543 23 5V19C23 20.1046 22.1046 21 21 21H3C1.89543 21 1 20.1046 1 19V5C1 3.89543 1.89543 3 3 3Z">
                                                        </path>
                                                        <path d="M14 4V20"></path>
                                                        <path d="M20 7H17"></path>
                                                        <path d="M20 10H17"></path>
                                                        <path d="M20 13H17"></path>
                                                    </svg></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-content"
                                        style="padding: 0px; overflow: hidden; position: relative;">
                                        <div id="graph_content" style="display: none">
                                            <div id="graph_all"></div>
                                            <div id="loading-text" class="markdown-preview-view">0%</div>
                                            <div class="graph-controls is-close">
                                                <div class="clickable-icon graph-controls-button mod-close"
                                                    aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="svg-icon lucide-x">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg></div>
                                                <div class="clickable-icon graph-controls-button mod-open"
                                                    aria-label="Open graph settings"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="svg-icon lucide-settings">
                                                        <path
                                                            d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                                                        </path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg></div>
                                                <div class="clickable-icon graph-controls-button mod-reset"
                                                    aria-label="Restore default settings"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="svg-icon lucide-rotate-ccw">
                                                        <path d="M3 2v6h6"></path>
                                                        <path d="M3 13a9 9 0 1 0 3-7.7L3 8"></path>
                                                    </svg></div>
                                                <div class="tree-item graph-control-section mod-filter">
                                                    <div class="tree-item-self">
                                                        <div class="tree-item-inner">
                                                            <header class="graph-control-section-header">Options
                                                            </header>
                                                        </div>
                                                    </div>
                                                    <div class="tree-item-children">

                                                        <div class="setting-item mod-toggle">
                                                            <div class="setting-item-info">
                                                                <div class="setting-item-name"
                                                                    aria-label="Show files that are not linked to any other file">
                                                                    Orphans</div>
                                                                <div class="setting-item-description"></div>
                                                            </div>
                                                            <div class="setting-item-control">
                                                                <div
                                                                    class="checkbox-container mod-small graphNoLinkOption is-enabled">
                                                                    <input type="checkbox" tabindex="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="setting-item mod-toggle">
                                                            <div class="setting-item-info">
                                                                <div class="setting-item-name"
                                                                    aria-label="Show files that are not linked to any other file">
                                                                    Auto-Reload</div>
                                                                <div class="setting-item-description"></div>
                                                            </div>
                                                            <div class="setting-item-control">
                                                                <div
                                                                    class="checkbox-container mod-small graphAutoReloadOption is-enabled">
                                                                    <input type="checkbox" tabindex="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="setting-item">
                                                            <div class="setting-item-info">
                                                                <div class="setting-item-name">Style</div>
                                                            </div>
                                                            <div class="setting-item-control">
                                                                <select id="graphStyleDropdown" class="dropdown">
                                                                    <option value="dynamic">Dynamic (Default)</option>
                                                                    <option value="continuous">Continuous</option>
                                                                    <option value="discrete">Discrete</option>
                                                                    <option value="diagonalCross">DiagonalCross</option>
                                                                    <option value="straightCross">StraightCross</option>
                                                                    <option value="horizontal">Horizontal</option>
                                                                    <option value="vertical">Vertical</option>
                                                                    <option value="curvedCW">CurvedCW</option>
                                                                    <option value="curvedCCW">CurvedCCW</option>
                                                                    <option value="cubicBezier">CubicBezier</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tree-item graph-control-section mod-display">
                                                    <div class="tree-item-self">
                                                        <div class="tree-item-inner">
                                                            <header class="graph-control-section-header">Display
                                                            </header>
                                                        </div>
                                                    </div>
                                                    <div class="tree-item-children">
                                                        <div class="setting-item mod-slider">
                                                            <div class="setting-item-info">
                                                                <div class="setting-item-name">Node size</div>
                                                                <div class="setting-item-description"></div>
                                                            </div>
                                                            <div class="setting-item-control"><input
                                                                    class="slider nodeSize" type="range" min="5"
                                                                    max="40" step="2"></div>

                                                        </div>
                                                        <div class="setting-item mod-slider">
                                                            <div class="setting-item-info">
                                                                <div class="setting-item-name">Link thickness</div>
                                                                <div class="setting-item-description"></div>

                                                            </div>
                                                            <div class="setting-item-control"><input
                                                                    class="slider linkThickness" type="range" min="0.1"
                                                                    max="5" step="any"></div>

                                                        </div>
                                                        <div class="setting-item mod-slider">
                                                            <div class="setting-item-info">
                                                                <div class="setting-item-name">Link distance</div>
                                                                <div class="setting-item-description"></div>
                                                            </div>
                                                            <div class="setting-item-control"><input
                                                                    class="slider linkDistance" type="range" min="30"
                                                                    max="1000" step="10"></div>

                                                        </div>

                                                        <div class="setting-item">
                                                            <div class="setting-item-info">
                                                                <div class="setting-item-name"></div>
                                                                <div class="setting-item-description"></div>
                                                            </div>
                                                            <div class="setting-item-control"><button id="graphReload"
                                                                    class="mod-cta">Reload</button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="markdown-reading-view" style="width: 100%; height: 100%; ">
                                            <div class="markdown-preview-view markdown-rendered node-insert-event allow-fold-headings show-indentation-guide allow-fold-lists"
                                                style="tab-size: 4;">
                                                <div class="markdown-preview-sizer markdown-preview-section"
                                                    style="padding-bottom: 200px; min-height: 500px;">
                                                    <div class="markdown-preview-pusher"
                                                        style="width: 1px; height: 0.1px; margin-bottom: 0px;"></div>
                                                    <div class="inline-title" tabindex="-1" enterkeyhint="done"></div>
                                                    <div id="mdContent"></div>
                                                    <div class="graph-controls is-close">
                                                        <div class="clickable-icon graph-controls-button mod-close"
                                                            aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg"
                                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="svg-icon lucide-x">
                                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                                            </svg></div>
                                                        <div class="clickable-icon graph-controls-button mod-open"
                                                            aria-label="Open graph settings"><svg
                                                                xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="svg-icon lucide-settings">
                                                                <path
                                                                    d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                                                                </path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg></div>
                                                        <div class="clickable-icon graph-controls-button mod-reset"
                                                            aria-label="Restore text settings"><svg
                                                                xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="svg-icon lucide-rotate-ccw">
                                                                <path d="M3 2v6h6"></path>
                                                                <path d="M3 13a9 9 0 1 0 3-7.7L3 8"></path>
                                                            </svg></div>
                                                        <div class="tree-item graph-control-section mod-display">
                                                            <div class="tree-item-self">
                                                                <div class="tree-item-inner">
                                                                    <header class="graph-control-section-header">Display
                                                                    </header>
                                                                </div>
                                                            </div>
                                                            <div class="tree-item-children">
                                                                <div class="setting-item mod-slider">
                                                                    <div class="setting-item-info">
                                                                        <div class="setting-item-name">Font size</div>
                                                                        <div class="setting-item-description"></div>
                                                                    </div>
                                                                    <div class="setting-item-control"><input
                                                                            class="slider font-size" type="range"
                                                                            min="10" max="30" step="1"></div>
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
                        </div>
                    </div>
                </div>
                <div class="workspace-split mod-horizontal mod-right-split" style="width: 450px;">
                    <hr class="workspace-leaf-resize-handle right-dock">
                    <div class="workspace-tabs mod-top mod-top-right-space">
                        <hr class="workspace-leaf-resize-handle">
                        <div class="workspace-tab-container">
                            <!-- right dock -->
                            <div class="workspace-leaf mod-active">
                                <hr class="workspace-leaf-resize-handle">
                                <div class="workspace-leaf-content" data-type="backlink">
                                    <div class="view-header" style="display: none">
                                        <div class="clickable-icon view-header-icon" draggable="true"
                                            aria-label="Drag to rearrange"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="svg-icon links-coming-in">
                                                <path
                                                    d="M8.70467 12C8.21657 11.6404 7.81269 11.1817 7.52044 10.6549C7.22819 10.1281 7.0544 9.54553 7.01086 8.94677C6.96732 8.348 7.05504 7.74701 7.26808 7.18456C7.48112 6.62212 7.81449 6.11138 8.24558 5.68697L10.7961 3.17516C11.5978 2.41258 12.6716 1.99062 13.7861 2.00016C14.9007 2.0097 15.9668 2.44997 16.755 3.22615C17.5431 4.00234 17.9902 5.05233 17.9998 6.14998C18.0095 7.24763 17.5811 8.30511 16.8067 9.09467L15.9014 10">
                                                </path>
                                                <path
                                                    d="M11.2953 8C11.7834 8.35957 12.1873 8.81831 12.4796 9.34512C12.7718 9.87192 12.9456 10.4545 12.9891 11.0532C13.0327 11.652 12.945 12.253 12.7319 12.8154C12.5189 13.3779 12.1855 13.8886 11.7544 14.313L9.20392 16.8248C8.40221 17.5874 7.32844 18.0094 6.21389 17.9998C5.09933 17.9903 4.03318 17.55 3.24504 16.7738C2.4569 15.9977 2.00985 14.9477 2.00016 13.85C1.99047 12.7524 2.41893 11.6949 3.19326 10.9053L4.09859 10">
                                                </path>
                                                <path d="M17 21L14 18L17 15"></path>
                                                <path d="M21 18H14"></path>
                                            </svg></div>
                                        <div class="view-header-title-container mod-at-start">
                                            <div class="view-header-title-parent"></div>
                                        </div>
                                        <div class="view-actions"><a class="clickable-icon view-action"
                                                aria-label="Unlink tab" style="display: none;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="svg-icon lucide-link">
                                                    <path
                                                        d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71">
                                                    </path>
                                                    <path
                                                        d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71">
                                                    </path>
                                                </svg></a><a class="clickable-icon view-action mod-pin-leaf"
                                                aria-label="Pin" style="display: none;"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="svg-icon lucide-pin">
                                                    <line x1="12" y1="17" x2="12" y2="22"></line>
                                                    <path
                                                        d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z">
                                                    </path>
                                                </svg></a><a class="clickable-icon view-action"
                                                aria-label="More options"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" class="svg-icon lucide-more-vertical">
                                                    <circle cx="12" cy="12" r="1"></circle>
                                                    <circle cx="12" cy="5" r="1"></circle>
                                                    <circle cx="12" cy="19" r="1"></circle>
                                                </svg></a></div>
                                    </div>

                                    <!-- Grap Viewer -->
                                    <div class="view-content">

                                        <div class="nav-header">
                                            <div class="view-header-nav-buttons">
                                                <a class="clickable-icon view-action" aria-label="Open localGraph"
                                                    ><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="svg-icon lucide-git-fork">
                                                        <circle cx="12" cy="18" r="3"></circle>
                                                        <circle cx="6" cy="6" r="3"></circle>
                                                        <circle cx="18" cy="6" r="3"></circle>
                                                        <path d="M18 9v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9"></path>
                                                        <path d="M12 12v3"></path>
                                                    </svg></a>
                                                <a class="clickable-icon view-action" aria-label="Open outline">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="svg-icon lucide-list">
                                                        <line x1="8" y1="6" x2="21" y2="6" />
                                                        <line x1="8" y1="12" x2="21" y2="12" />
                                                        <line x1="8" y1="18" x2="21" y2="18" />
                                                        <line x1="3" y1="6" x2="3.01" y2="6" />
                                                        <line x1="3" y1="12" x2="3.01" y2="12" />
                                                        <line x1="3" y1="18" x2="3.01" y2="18" />
                                                    </svg>


                                                </a>
                                            </div>



                                        </div>

                                        <div class="backlink-pane node-insert-event" style="position: relative;">


                                            <div id="outline" class="outline" style="display: unset">
                                                <div class="sidebar-top">
                                                    <h3>Content</h3>
                                                </div>

                                                <div id="toc"></div>

                                            </div>
                                            <div id=localGraph>
                                            <h3>Graph</h3>

                                            <div id="mynetwork"></div>
                                            </div>
                                            <div class="tree-item-self" aria-label-position="left"><span
                                                    class="tree-item-icon collapse-icon"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="svg-icon right-triangle">
                                                        <path d="M3 8L12 17L21 8"></path>
                                                    </svg></span>
                                                <div class="tree-item-inner">Linked mentions</div>
                                                <div class="tree-item-flair-outer"><span class="tree-item-flair"
                                                        id="nodeCount">0</span></div>

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
        <div class="status-bar">
            <div class="status-bar-item plugin-backlink mod-clickable"><span
                    id="backlinksCount">0</span><span>&nbsp;backlinks</span></div>
            <div class="status-bar-item plugin-word-count">
                <span class="status-bar-item-segment" id="wordCount">0 words</span>
                <span class="status-bar-item-segment" id="charCount">0 characters</span>
            </div>
        </div>
    </div>
    <!-- Graph settings -->
    <div style="display: none">
        <div>
            <?php echo $jsonGraphData; ?>
        </div>
        <p class="graph-view color-line"></p>
        <p class="graph-view color-fill"></p>
        <p class="graph-view color-text"></p>
        <p class="graph-view color-fill-highlight"></p>
        <p class="graph-view color-fill-focused"></p>
        <p class="graph-view color-line-hightlight"></p>
        <p class="vault"><?php echo $vaultName ?></p>
        <p class="perliteTitle"><?php echo $title ?></p>
    </div>
    <!-- tool tip -->
    <div class="tooltip" style="top: 83.9531px; left: 1032.51px; width: 180.984px; height: 25px; display: none">
        <div class="tooltip-arrow" style="left: initial; right: 43px;"></div>
    </div>
    <!-- about modal -->
    <div id="about" class="modal-container mod-dim" style="display: none">
        <div class="modal-bg" style="opacity: 0.85;"></div>
        <div class="modal">
            <div class="modal-close-button"></div>
            <div class="modal-title"> <a href="."><img src="logo.svg" height="35" alt="Perlite Logo"
                        style="padding-top: 10px"></a> Perlite</div>
            <div class="aboutContent modal-content"></div>
        </div>
    </div>
    <!-- perlite settings -->
    <div id="settings" class="modal-container mod-dim" style="display: none">
        <div class="modal-bg" style="opacity: 0.85;"></div>
        <div id="settings" class="modal mod-settings">
            <div class="modal-close-button"></div>
            <div class="modal-title">Perlite Settings</div>
            <div class="setting-item-description">Some settings need a page reload to take affect!</div>
            <div class="modal-content vertical-tabs-container">
                <div class="vertical-tab-content-container">
                    <div class="vertical-tab-content">
                        <div class="setting-item">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Theme</div>
                                <div class="setting-item-description">Select installed theme</div>
                            </div>
                            <div class="setting-item-control">
                                <select id="themeDropdown" class="dropdown">
                                    <option value="">Default</option>
                                </select><button id="resetTheme" class="mod-cta">Reset</button>
                            </div>

                        </div>

                        <div class="setting-item mod-toggle">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Dark mode</div>
                                <div class="setting-item-description">Choose Perlite's default color scheme.</div>
                            </div>
                            <div class="setting-item-control">
                                <div class="checkbox-container is-enabled darkModeOption"><input type="checkbox"
                                        tabindex="0"></div>
                            </div>
                        </div>

                        <div class="setting-item setting-item-heading">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Sizes</div>
                                <div class="setting-item-description"></div>
                            </div>
                            <div class="setting-item-control"></div>
                        </div>
                        <div class="setting-item">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Font size</div>
                                <div class="setting-item-description">Font size in pixels that affects reading
                                    view.</div>
                            </div>
                            <div class="setting-item-control">
                                <div class="clickable-icon setting-editor-extra-setting-button"
                                    aria-label="Restore text settings">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="svg-icon lucide-rotate-ccw">
                                        <path d="M3 2v6h6"></path>
                                        <path d="M3 13a9 9 0 1 0 3-7.7L3 8"></path>
                                    </svg>
                                </div><input class="slider font-size" type="range" min="10" max="30" step="1">
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Panel sizes</div>
                                <div class="setting-item-description">Reset the panel sizes</div>
                            </div>
                            <div class="setting-item-control">
                                <div class="clickable-icon setting-editor-extra-setting-button"
                                    aria-label="Restore panel settings">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="svg-icon lucide-rotate-ccw">
                                        <path d="M3 2v6h6"></path>
                                        <path d="M3 13a9 9 0 1 0 3-7.7L3 8"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="setting-item setting-item-heading">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Mobile</div>
                                <div class="setting-item-description"></div>
                            </div>
                            <div class="setting-item-control"></div>
                        </div>

                        <div class="setting-item mod-toggle">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Pop Up</div>
                                <div class="setting-item-description">Open a popup by clicking on an internal link</div>
                            </div>
                            <div class="setting-item-control">
                                <div class="checkbox-container popUpSetting"><input type="checkbox" tabindex="0"></div>
                            </div>
                        </div>

                        <div class="setting-item setting-item-heading">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Advanced</div>
                                <div class="setting-item-description"></div>
                            </div>
                            <div class="setting-item-control"></div>
                        </div>
                        <div class="setting-item mod-toggle">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Disable Pop Hovers</div>
                                <div class="setting-item-description">Disable popups by hover</div>
                            </div>
                            <div class="setting-item-control">
                                <div class="checkbox-container disablePopUp"><input type="checkbox" tabindex="0"></div>
                            </div>
                        </div>
                        <div class="setting-item mod-toggle">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Show inline title</div>
                                <div class="setting-item-description">Displays the filename as an title inline with the
                                    file contents.</div>
                            </div>
                            <div class="setting-item-control">
                                <div class="checkbox-container is-enabled inlineTitleOption"><input type="checkbox"
                                        tabindex="0"></div>
                            </div>
                        </div>
                        <div class="setting-item mod-toggle">
                            <div class="setting-item-info">
                                <div class="setting-item-name">Collapse Metadata</div>
                                <div class="setting-item-description">Collapse the Front Matter Metadata contents by
                                    default.</div>
                            </div>
                            <div class="setting-item-control">
                                <div class="checkbox-container metadataOption"><input type="checkbox" tabindex="0">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pop Hover -->
    <div class="popover hover-popover" style="display: none">
        <div class="markdown-embed is-loaded">
            <div class="markdown-embed-content">
                <div class="markdown-preview-view markdown-rendered node-insert-event show-indentation-guide">
                    <div class="markdown-preview-sizer markdown-preview-section"
                        style="padding-bottom: 0px; min-height: 100%;">
                        <div class="markdown-preview-pusher" style="height: 0.1px; margin-bottom: 0px;"></div>
                        <div class="mod-header">
                            <div class="inline-title pophover-title" spellcheck="false" tabindex="-1"
                                enterkeyhint="done"></div>
                            <div id='mdHoverContent'></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="markdown-embed-content" style="display: none;"></div>
        </div>


    </div>
    <!-- pop up -->
    <div id="popUp" class="modal-container mod-dim" style="display: none">
        <div class="modal-bg" style="opacity: 0.85;"></div>
        <div class="modal">
            <div class="modal-close-button"></div>
            <div class="popup-modal-title inline-title"></div>
            <div class="goToLink"></div>
            <div id='popUpContent' class="modal-content"></div>
        </div>
    </div>
    <!-- img modal -->
    <div id="img-modal" class="modal-container mod-dim" style="display: none">
        <div class="modal-bg" style="opacity: 0.85;"></div>
        <div class="modal">
            <div class="modal-close-button"></div>
            <div class="modal-title img-modal-title inline-title"></div>
            <div class="goToLink"></div>
            <div id='img-content' class="modal-content"></div>
        </div>
    </div>


    </div>
    <script src=".js/perlite.js"></script>
</body>

</html>