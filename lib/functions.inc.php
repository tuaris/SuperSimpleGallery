<?php
// Renames an image
function move_image($image, $amount){
	$new_image_name = find_next_number($image, $amount);
	rename($image, $new_image_name);
}
// Does not really delete, just renames it as a non-jpg file
function delete_image($image){
	if(file_exists($image)){
		$new_name = generateName(getNumber($image), '.deleted');
		rename($image, $new_name);
	}
}
// Renames back to jpg file
function restore_image($deleted){
	if(file_exists($deleted)){
		$image = generateName(getNumber($deleted, '.deleted'));
		$new_name = find_next_number($image, 0);
		rename($deleted, $new_name);
	}
}
// Adds a new image
function upload_image($width = 640, $height = 480){
	try {
		// Undefined | Multiple Files | $_FILES Corruption Attack
		// If this request falls under any of them, treat it invalid.
		if (
			!isset($_FILES['upfile']['error']) ||
			is_array($_FILES['upfile']['error'])
		) {
			throw new RuntimeException('Invalid parameters.');
		}

		// Check $_FILES['upfile']['error'] value.
		switch ($_FILES['upfile']['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException('No file sent.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException('Exceeded filesize limit.');
			default:
				throw new RuntimeException('Unknown errors.');
		}

		// You should also check filesize here.
		if ($_FILES['upfile']['size'] > 3000000) {
			throw new RuntimeException('Exceeded filesize limit.');
		}

		// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
		// Check MIME Type by yourself.
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		if (false === $ext = array_search(
			$finfo->file($_FILES['upfile']['tmp_name']),
			array(
				'jpg' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'png' => 'image/png',
				'gif' => 'image/gif',
			),
			true
		)) {
			throw new RuntimeException('Invalid file format.');
		}

		// You should name it uniquely.
		// DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
		// On this example, obtain safe unique name from its binary data.

		// Save image
		$image = new Imagick($_FILES['upfile']['tmp_name']);
		$image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1, TRUE);

		$new_image_name = find_next_number(generateName(get_next_number()));

		if (!$image->writeImage($new_image_name)) {
			throw new RuntimeException('Failed to move uploaded file.');
		}
	}
	catch (RuntimeException $e) {
		echo $e->getMessage();
	}
}

function getNumber($image, $ext = '.jpg', $prefix = 'image'){
	return (int) trim(str_replace($prefix, '', $image), $ext);
}
function generateName($number, $ext = '.jpg', $prefix = 'image', $padding = 4, $pad = '0'){
	return $prefix . str_pad($number, $padding, $pad, STR_PAD_LEFT) . $ext;
}

// Returns the next number in the files sequence
function get_next_number(){
	$files = scandir(".");

	foreach ($files as $aFile) {
		if (substr($aFile, -4) == '.jpg') {
			$latestNum = getNumber($aFile); 
		}
	}

	return $latestNum + 10;
}

// 're-numbers' an image while aviding name collisions when shifting it by +-X number of positions.
function find_next_number($image, $change_by = 1){
	// Extract current number from image name
	$current_number = getNumber($image);
	
	// Change the file number by the requested amount
	$new_number = $current_number + $change_by;
	// We do not allow negative filenames
	if($new_number < 0){ $new_number = 0; }
	
	// Generate new File name 'image0000.jpg'
	$new_filename = generateName($new_number);

	// If the file does not exist, we are done
	if(!file_exists($new_filename)){
		return $new_filename;
	}

	// If the new number is 0 and 0 already exists, then we can not change.
	if($new_number == 0){
		return $image;
	}
	// Otherwise fine the next avaialbe
	else{
		return find_next_number($new_filename, ($change_by < 0 ? -1 : 1));
	}
}

// File name is the MD5 hash plus the dimensions
function image_cache_lookup($image, $width, $height, $ext = '.jpg', $cache_dir = '.cache'){
	$image_base = md5_file($image);
	return $image_cache = $cache_dir . DIRECTORY_SEPARATOR . $image_base . '-' . $width . 'x' . $height . $ext;
}

// Extremly Simple authentication
function authenticate($users, $realm){
	if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: Digest realm="'.$realm.
			   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
		echo 'You must sign in';
		return false;
	} 

	//Invalid Username
	if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])) {
		echo 'Username not found';
		return false;
	} 

	// generate the valid response
	$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
	$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
	$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

	// Invalid Password
	if ($data['response'] != $valid_response){
		echo 'Password Incorrect';
		return false;
	}

	//Valis Username and password
	return true;
}

// function to parse the http auth header
function http_digest_parse($txt) {
	// protect against missing data
	$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
	$data = array();
	$keys = implode('|', array_keys($needed_parts));

	preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

	foreach ($matches as $m) {
		$data[$m[1]] = $m[3] ? $m[3] : $m[4];
		unset($needed_parts[$m[1]]);
	}

	return $needed_parts ? false : $data;
}
?>