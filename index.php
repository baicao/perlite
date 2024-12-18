<!DOCTYPE html>
<html>

<?php

/*!
 * Version v1.5.9
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/permissions.php';

$title = $siteTitle;
$menu = menu($rootDir);


$logoSrc = 'images/logo.svg'; // 默认 logo
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']["gender"] === 'male') {
        $logoSrc = 'images/boy.png'; // 男性 logo
    } elseif ($_SESSION['user']["gender"] === 'female') {
        $logoSrc = 'images/girl.png'; // 女性 logo
    }elseif ($_SESSION['user']["gender"] === 'female') {
        $logoSrc = 'images/others.png'; // 女性 logo
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
    <link rel="stylesheet" href=".styles/app.min.css" type="text/css">
    <link id="highlight-js" rel="stylesheet" href=".styles/atom-one-dark.min.css" type="text/css">
    <link rel="stylesheet" href=".styles/perlite.min.css" type="text/css">
    <!-- <link rel="stylesheet" href=".styles/vis-network.min.css" type="text/css"> -->
    <link rel="stylesheet" href=".styles/katex.min.css" type="text/css">
   
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
                                <!-- <div class="workspace-tab-header" draggable="true" aria-label="Search"
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
                                </div> -->
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

    <!-- tool tip -->
    <div class="tooltip" style="top: 83.9531px; left: 1032.51px; width: 180.984px; height: 25px; display: none">
        <div class="tooltip-arrow" style="left: initial; right: 43px;"></div>
    </div>
    <!-- about modal -->
    <div id="about" class="modal-container mod-dim" style="display: none">
        <div class="modal-bg" style="opacity: 0.85;"></div>
        <div class="modal">
            <div class="modal-close-button"></div>
            <div class="modal-title"> <a href="."><img src="./images/logo.svg" height="35" alt="Changedu Logo"
                        style="padding-top: 10px"></a> Changedu</div>
            <div class="aboutContent modal-content"></div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoLink = document.querySelector('.logo-link');
            const userDropdown = document.querySelector('.user-dropdown');
            let dropdownTimer;

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
        });
    </script>
</body>

</html>