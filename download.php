<?php
/*
 * Force Download
 * Copyright (C) 2012 attosoft <http://attosoft.info/en/>
 * This file is distributed under the same license as the Auto ThickBox Plus package.
 * attosoft <contact@attosoft.info>, 2012.
 */

/*
 * Usage:
 *  * download.php?file=http://example.com/image.png
 *  * download.php?file=/home/username/www/image.png
 *
 * @see http://php.net/manual/function.header.php
 * @see http://php.net/manual/function.readfile.php
 */

function force_download($file) {
	if (empty($file)) {
		die('Error: File not specified.'); return;
	}
	$file = sanitize_url($file);
	if (!file_exists2($file)) {
		die('Error: File not found. $file=' . $file); return;
	}
	if (headers_sent()) {
		die('Error: Headers already sent.'); return;
	}

	if(ini_get('zlib.output_compression'))
		ini_set('zlib.output_compression', 'Off');

	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false);

	$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	switch ($ext) {
		case 'jpg':
		case 'jpe':
		case 'jpeg': $type = 'image/jpeg'; break;
		case 'gif':  $type = 'image/gif'; break;
		case 'png':  $type = 'image/png'; break;
		case 'bmp':  $type = 'image/bmp'; break;
		case 'tif':
		case 'tiff': $type = 'image/tiff'; break;
		case 'webp': $type = 'image/webp'; break;
		default: die('Error: Unsupported file type. $ext=' . $ext); return; // download only images for security
	}

	header('Content-Description: File Transfer');
	header('Content-Transfer-Encoding: binary');
	header('Content-Type: ' . $type);
	header('Content-Disposition: attachment; filename="' . unsanitized_basename($file) . '"');
	header('Content-Length: ' . filesize2($file));

	ob_clean();
	flush();
	readfile($file);
}

function file_exists2($file) {
	// file_exists() cannnot accept such URL that starts with 'http://'
	return preg_match('/^https?:\/\//i', $file) ? !!@fopen($file, 'r') : file_exists($file);
}

function filesize2($file) {
	// filesize() cannnot accept such URL that starts with 'http://'
	return preg_match('/^https?:\/\//i', $file) ? strlen(file_get_contents($file)) : filesize($file);
}

function sanitize_url($url) {
	$url = str_replace(array('%', ' ', '\\'), array('%25', '%20', ''), $url);
	$url = preg_replace('/\?.*$/i', '', $url); // remove url queries
	return $url;
}

function unsanitized_basename($url) {
	$url = str_replace(array('%25', '%20'), array('%', ' '), basename($url));
	return basename($url);
}

force_download(str_replace(chr(0), '', $_GET['file'])); // for null byte injection
exit;
?>