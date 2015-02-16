<?php
include 'lib/functions.inc.php';

$special_chars = array("..", "?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
$img = trim($_GET['image']);
$aFile = str_replace($special_chars, '', $img);
$aFile = trim($aFile);

if(isset($_REQUEST['w']) && isset($_REQUEST['h'])){
	$width = $_REQUEST['w'];
	$hieght = $_REQUEST['h'];
}
elseif(isset($_REQUEST['h'])){
	$width = 0;
	$hieght = $_REQUEST['h'];
}
elseif(isset($_REQUEST['w'])){
	$hieght = 0;
	$width = $_REQUEST['w'];
}
else{
	$hieght = 100;
	$width = 80;
}

// File not found
if(!file_exists($aFile)){
	$thumb = new Imagick('lib' . DIRECTORY_SEPARATOR . '404image.jpg');
	$thumb->thumbnailImage($width, $hieght);

	header("HTTP/1.0 404 Not Found");
	header('Content-type: image/jpeg');
	echo $thumb;
	exit;
}

//Check if there is a cache
$cached = image_cache_lookup($aFile, $width, $hieght);
if(file_exists($cached)){
	// Load from cache
	$thumb = new Imagick($cached);
}
else{
	// Save cache
	$thumb = new Imagick($aFile);
	$thumb->thumbnailImage($width, $hieght);
	mkdir('.cache');
	$thumb->writeImage($cached);
}

//print_r($_REQUEST);


// Display
header('Content-type: image/jpeg');
echo $thumb;

?>