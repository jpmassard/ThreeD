<?php

/**
 * video.php
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 */

/**
 * Stream MP4, WEBM, and OGG video based on byte ranges as requested by the  
 * HTML5 <video> object.
 * @param $path {string} Path to a local video clip to open and stream.
 * @param $mime {string} The MIME type of the video clip.
 */
function streamVideo($path, $mime) {
	//
	header("Content-Type: $mime");
	header("Accept-Ranges: bytes"); 
	// header("Accept-Ranges: 0-$length");
	//
	$fp = @fopen($path, 'rb');
	$size = filesize($path); // (file size)
	$length = $size;         // (content length)
	$start = 0;              // (start byte)
	$end = $size - 1;        // (end byte)
	//
	if(isset($_SERVER['HTTP_RANGE'])) {
		$c_start = $start;
		$c_end = $end;
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		if(strpos($range, ',') !== false) {
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			exit;
		}
		if($range == '-') {
			$c_start = $size - substr($range, 1);
		}
		else {
			$range = explode('-', $range);
			$c_start = $range[0];
			$c_end = (isset($range[1]) && is_numeric($range[1])) 
				? $range[1] : $size;
		}
		$c_end = ($c_end > $end) ? $end : $c_end;
		if($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			exit;
		}
		$start = $c_start;
		$end = $c_end;
		$length = $end - $start + 1;
		fseek($fp, $start);
		header('HTTP/1.1 206 Partial Content');
	}
	header("Content-Range: bytes $start-$end/$size");
	header("Content-Length: $length");
	//
	error_reporting (0);
	set_time_limit(0);
	$buffer = 1024 * 8;
	while(!feof($fp) && ($p = ftell($fp)) <= $end) {
		if($p + $buffer > $end) {
			$buffer = $end - $p + 1;
		}
		print(fread($fp, $buffer));
		flush();
	}
	fclose($fp);
}

/**
 * Check whether a file extension indicates MP4 video.
 * @param $path {string} The path to the file.
 * @return {boolean} TRUE if this is MP4 video, false otherwise.
 */
function isMp4Video($path) {
	if(preg_match('/\.mp4$/i', $path) || preg_match('/\.mpg4$/i', $path) 
		|| preg_match('/\.m4v$/i', $path) || preg_match('/\.mp4v$/i', $path)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Check whether a file extension indicates WEBM video.
 * @param $path {string} The path to the file.
 * @return {boolean} TRUE if this is WEBM video, false otherwise.
 */
function isWebmVideo($path) {
	if(preg_match('/\.webm$/i', $path)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Check whether a file extension indicates OGG video.
 * @param $path {string} The path to the file.
 * @return {boolean} TRUE if this is OGG video, false otherwise.
 */
function isOggVideo($path) {
	if(preg_match('/\.ogg$/i', $path) || preg_match('/\.ogv$/i', $path)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Check whether a file extension indicates FLV video.
 * @param $path {string} The path to the file.
 * @return {boolean} TRUE if this is FLV video, false otherwise.
 */
function isFlvVideo($path) {
	if(preg_match('/\.flv$/i', $path) || preg_match('/\.f4v$/i', $path)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Check whether a file extension indicates a video playable with HTML5.
 * @param $path {string} The path to the file.
 * @return {boolean} TRUE if this is HTML5 video, false otherwise.
 */
function isHtml5Video($path) {
	if(isMp4Video($path) || isWebmVideo($path) || isOggVideo($path)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Check whether a file extension indicates Flash video.
 * @param $path {string} The path to the file.
 * @return {boolean} TRUE if this is Flash video, false otherwise.
 */
function isFlashVideo($path) {
	if(isFlvVideo($path) || isMp4Video($path)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * Check whether a file extension indicates video.
 * @param $path {string} The path to the file.
 * @return {boolean} TRUE if this is video, false otherwise.
 */
function isVideo($path) {
	if(isHtml5Video($path) || isFlashVideo($path)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

?>