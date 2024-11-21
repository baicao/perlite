<?php

/*!
 * Perlite v1.5.9 (https://github.com/secure-77/Perlite)
 * Author: sec77 (https://secure77.de)
 * Licensed under MIT (https://github.com/secure-77/Perlite/blob/main/LICENSE)
 */

use Perlite\PerliteParsedown;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/permissions.php';

// check get params
if (isset($_GET['mdfile'])) {
	$requestFile = $_GET['mdfile'];
	log_message("Request file: " . $requestFile);
	if (is_string($requestFile) && !empty($requestFile)) {
		if(free_pages($requestFile)){
			parseContent($requestFile);
		}else{
			if (!isset($_SESSION['user'])) {
				echo "login first";
			}else{
				if(!hasPageAccess($_SESSION['user']["id"], $requestFile, $app_conn)){
					$requestFile = "/Tools/Payment Notice";
				}
				parseContent($requestFile);
			}
		}
	}
}else{
	parseContent('/' . $index);
}


// // search request
// if (isset($_GET['search'])) {
// 	$searchString = $_GET['search'];
// 	if (is_string($searchString)) {
// 		if (!empty($searchString)) {
// 			echo doSearch($rootDir, $searchString);
// 		}
// 	}
// }


// parse the md to html
function parseContent($requestFile)
{

	global $path;
	global $cleanFile;
	global $rootDir;
	global $startDir;
	global $lineBreaks;
	global $allowedFileLinkTypes;
	global $htmlSafeMode;
	global $relPathes;


	$Parsedown = new PerliteParsedown();
	$Parsedown->setSafeMode($htmlSafeMode);
	$Parsedown->setBreaksEnabled($lineBreaks);


	$cleanFile = '';

	// call menu again to refresh the array
	menu($rootDir);
	$path = '';


	// get and parse the content, return if no content is there
	
	$content = getContent($requestFile);
	if ($content === '') {
		return;
	}

	$wordCount = str_word_count($content);
	$charCount = strlen($content);
	$content = $Parsedown->text($content);


	// Relative or absolute pathes
	if ($relPathes) {
		$mdpath =  $path;
		$path = $startDir . $path;
	} else {
		$path = $startDir;
		$mdpath = '';
	}

	$linkFileTypes = implode('|', $allowedFileLinkTypes);

	$allowedImageTypes = '(\.png|\.jpg|\.jpeg|\.svg|\.gif|\.bmp|\.tif|\.tiff|\.webp)';

	// 处理 ![[]] 语法
	$pattern = '/\!\[\[(.*?)\]\]/';
	$content = preg_replace_callback($pattern, function($matches) use ($rootDir, $Parsedown) {
		$innerContent = $matches[1];
		
		// 分离文件路径、引用标记和别名
		$parts = explode('|', $innerContent);
		$filePath = $parts[0];
		$alias = isset($parts[1]) ? $parts[1] : '';

		// 进一步分离文件路径和引用标记
		$filePathParts = explode('#', $filePath);
		$actualFilePath = $filePathParts[0];
		$reference = isset($filePathParts[1]) ? $filePathParts[1] : '';
		
		// 对于非 header 文件，返回原始的 [[]] 格式
		return '[[' . $innerContent . ']]';
	}, $content);

	// embedded pdf links
	$replaces = '<embed src="' . $path . '/\\2" type="application/pdf" style="min-height:100vh;width:100%">';
	$pattern = array('/(\!\[\[)(.*?.(?:pdf))(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);

	// embedded mp4 links
	$replaces = '
	<video controls src="' . $path . '/\\2" type="video/mp4">
		<a class="internal-link" target="_blank" rel="noopener noreferrer" href="' . $path . '/' . '\\2">Your browser does not support the video tag: Download \\2</a>
  	</video>';
	$pattern = array('/(\!\[\[)(.*?.(?:mp4))(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);


     // embedded m4a links
	 $replaces = '
	 <video controls src="' . $path . '/\\2" type="audio/x-m4a">
			 <a class="internal-link" target="_blank" rel="noopener noreferrer" href="' . $path . '/' . '\\2">Your browser does not support the audio tag: Download \\2</a>
	 </video>';
	 $pattern = array('/(\!\[\[)(.*?.(?:m4a))(\]\])/');
	 $content = preg_replace($pattern, $replaces, $content);


	// links to other files with Alias
	$replaces = '<a class="internal-link" target="_blank" rel="noopener noreferrer" href="' . $path . '/' . '\\2">\\3</a>';
	$pattern = array('/(\[\[)(.*?.(?:' . $linkFileTypes . '))\|(.*)(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);

	// links to other files without Alias
	$replaces = '<a class="internal-link" target="_blank" rel="noopener noreferrer" href="' . $path . '/' . '\\2">\\2</a>';
	$pattern = array('/(\[\[)(.*?.(?:' . $linkFileTypes . '))(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);

	// img links with external target link
	$replaces = 'noreferrer"><img class="images" width="\\4" height="\\5" alt="image not found" src="' . $path . '/\\2\\3' . '"/>';
	$pattern = array('/noreferrer">(\!?\[\[)(.*?)'.$allowedImageTypes.'\|?(\d*)x?(\d*)(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);

	// img links with size
	$replaces = '<p><a href="#" class="pop"><img class="images" width="\\4" height="\\5" alt="image not found" src="' . $path . '/\\2\\3' . '"/></a></p>';
	$pattern = array('/(\!?\[\[)(.*?)'.$allowedImageTypes.'\|?(\d*)x?(\d*)(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);

	// centerise or right align images with "center"/"right" directive
	$pattern = '/(\!?\[\[)(.*?)'.$allowedImageTypes.'\|?(center|right)\|?(\d*)x?(\d*)(\]\])/';
	$replaces = function ($matches) use ($path) {
		$class = "images";  // Default class for all images
		if (strpos($matches[4], 'center') !== false) {
			$class .= " center";  // Add 'center' class
		} elseif (strpos($matches[4], 'right') !== false) {
			$class .= " right";  // Add 'right' class
		}
		$width = $matches[5] ?? 'auto';
		$height = $matches[6] ?? 'auto';
		return '<p><a href="#" class="pop"><img class="' . $class . '" src="' . $path . '/' . $matches[2] . $matches[3] . '" width="' . $width . '" height="' . $height . '"/></a></p>';
	};
	$content = preg_replace_callback($pattern, $replaces, $content);

	// img links with captions and size
	$replaces = '<p><a href="#" class="pop"><img class="images" width="\\5" height="\\6" alt="\\4" src="' . $path . '/\\2\\3' . '"/></a></p>';
	$pattern = array('/(\!?\[\[)(.*?)'.$allowedImageTypes.'\|?(.+\|)\|?(\d*)x?(\d*)(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);

	// img links with captions
	$replaces = '<p><a href="#" class="pop"><img class="images" alt="\\4" src="' . $path . '/\\2\\3' . '"/></a></p>';
	$pattern = array('/(\!?\[\[)(.*?)'.$allowedImageTypes.'\|?(.+|)(\]\])/');
	$content = preg_replace($pattern, $replaces, $content);


	// handle internal site links
	// search for links outside of the current folder
	$pattern = array('/(\[\[)(?:\.\.\/)+(.*?)(\]\])/');
	$content = translateLink($pattern, $content, $path, false);

	// search for links in the same folder
	$pattern = array('/(\[\[)(.*?)(\]\])/');
	$content = translateLink($pattern, $content, $mdpath, true);


	// add some meta data
	$content = '
	<div style="display: none">
		<div class="mdTitleHide">' . $cleanFile . '</div>
		<div class="wordCount">' . $wordCount . '</div>
		<div class="charCount">' . $charCount . '</div>
	</div>' . $content;

	echo $content;
	return;

}

//internal links
// can be simplified (no need of path translation)
function translateLink($pattern, $content, $path, $sameFolder)
{

	return preg_replace_callback(
		$pattern,
		function ($matches) use ($path, $sameFolder) {

			$newAbPath = $path;
			$pathSplit = explode("/", $path);
			$linkName_full = $matches[2];
			$linkName = $linkName_full;
			$linkFile = $matches[2];

			# handle custom internal obsidian links
			$splitLink = explode("|", $matches[2]);
			if (count($splitLink) > 1) {

				$linkFile = $splitLink[0];
				$linkName = $splitLink[1];
			}

			# handle internal popups
			$popupClass = '';
			$popUpIcon = '';

			if (count($splitLink) > 2) {

				$popupClass = ' internal-popup';
				$popUpIcon = '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon lucide-maximize"><path d="M8 3H5a2 2 0 0 0-2 2v3"></path><path d="M21 8V5a2 2 0 0 0-2-2h-3"></path><path d="M3 16v3a2 2 0 0 0 2 2h3"></path><path d="M16 21h3a2 2 0 0 0 2-2v-3"></path></svg>';
			}


			// do extra stuff to get the absolute path
			if ($sameFolder == false) {
				$countDirs = count(explode("../", $matches[0]));
				$countDirs = $countDirs - 1;
				$newPath = array_splice($pathSplit, 1, -$countDirs);
				$newAbPath = implode('/', $newPath);
			}


			$urlPath = $newAbPath . '/' . $linkFile;
			$urlPath = preg_replace('/\.md$/', '', $urlPath);
			if (substr($urlPath, 0, 1) != '/') {
				$urlPath = '/' . $urlPath;
			}

			$refName = '';

			# if same document heading reference
			if (substr($linkName_full, 0, 1) == '#') {

				$splitLink = explode("#", $urlPath);
				$urlPath = '';
				$refName = $splitLink[1];
				$refName = '#' . $refName;
				$href = 'href="';
			} else {
				$href = 'href="?link=';
			}

			$urlPath = str_replace('&amp;', '&', $urlPath);

			$urlPath = rawurlencode($urlPath);
			$urlPath = str_replace('%23', '#', $urlPath);

			return '<a class="internal-link' . $popupClass . '"' . $href . $urlPath . $refName . '">' . $linkName . '</a>' . $popUpIcon;
		}
		,
		$content
	);
}


// read content from file
function getContent($requestFile)
{
    global $avFiles, $path, $cleanFile, $rootDir;
    $content = '';
   
	$cleanFile = $requestFile;
	$n = strrpos($requestFile, "/");
	$path = substr($requestFile, 0, $n);
	$originalContent = file_get_contents($rootDir . $requestFile . '.md', true);

	// 首先处理 header
	$headerPattern = '/\!\[\[(.*?header.*?)\]\]/';
	preg_match($headerPattern, $originalContent, $headerMatches);
	
	if (!empty($headerMatches)) {
		$headerContent = '';
		$innerContent = $headerMatches[1];
		$parts = explode('|', $innerContent);
		$filePath = $parts[0];

		$filePathParts = explode('#', $filePath);
		$actualFilePath = $filePathParts[0];
		$reference = isset($filePathParts[1]) ? $filePathParts[1] : '';

		$headerPath = $rootDir . '/' . trim($actualFilePath, '/') . '.md';
		
		if (file_exists($headerPath)) {
			$headerContent = file_get_contents($headerPath);
			
			if ($reference) {
				$patterns = [
					'/\^' . preg_quote($reference, '/') . '\s*(.*?)(\n(?=\^)|$)/s',
					'/\^' . preg_quote($reference, '/') . '(.*?)(\n|$)/s',
					'/\^' . preg_quote($reference, '/') . '(.+)/'
				];
				
				foreach ($patterns as $pattern) {
					if (preg_match($pattern, $headerContent, $refMatch)) {
						$headerContent = trim($refMatch[1]);
						break;
					}
				}
			}
			
			// 移除所有的 ^ 引用标记
			$headerContent = preg_replace('/\^[a-zA-Z0-9]+\s*/', '', $headerContent);
		}

		// 将 header 内容添加到原始内容的开头，并移除原始的 ![[]] 语法
		$originalContent = $headerContent . "\n\n" . preg_replace($headerPattern, '', $originalContent, 1);
	}

	// 处理其他的 ![[]] 语法
	$pattern = '/\!\[\[(.*?)\]\]/';
	$content = preg_replace_callback($pattern, function($matches) use ($rootDir) {
		// 这里可以添加处理其他 ![[]] 语法的逻辑
		// 现在我们只是保持原样
		return $matches[0];
	}, $originalContent);

	$content = $originalContent;

    return $content;
}
?>