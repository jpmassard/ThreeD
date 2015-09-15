<?php

/**
 * VideoLoader.php
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 *
 * A script to stream MP4, WEBM, and OGG video based on byte ranges requested
 * by the HTML5 <video> object. The script is used by VideoInfo.php to support 
 * our multi-language, multi-resolution video naming scheme and also obfuscate
 * the real location of our video files.
 *
 * @param video {string} The name of the video set, i.e. the folder name.
 * @param language {string} Desired language, e.g. "en".
 * @param resolution {string} Desired resolution, e.g. "720p".
 * @param stereoLayout {string} Desired stereo layout, e.g. "PA".
 * @param type {string} File type of the video, e.g. "mp4", "webm".
 */

/**
 * Includes:
 */
$vws = $_SERVER['DOCUMENT_ROOT'] . 'plugins/ThreeD/vws/assets/all/php/';
require($vws . 'CONFIG.php');
require($vws . 'expire.php');
require($vws . 'path.php');
require($vws . 'video.php');

/**
 * Main program:
 */
$video = requireQueryVar('video');
$video = preg_replace('/[^A-Z^a-z^0-9^_^\-^\.]/', '', $video);
//
$lang = requireQueryVar('language');
$lang = preg_replace('/[^A-Z^a-z]/', '', $lang);
$lang = strtolower($lang);
//
$res = requireQueryVar('resolution');
$res = preg_replace('/[^A-Z^a-z^0-9]/', '', $res);
if ($res == 'sd' || $res == 'hd' || $res == '2k' || $res == '4k') {
	$res = strtoupper($res);
}
else {
	$res = strtolower($res);
}
//
$stly = requireQueryVar('stereoLayout');
$stly = preg_replace('/[^A-Z^a-z^0-9]/', '', $stly);
$stly = strtolower($stly);
//
$type = requireQueryVar('type');
$type = preg_replace('/[^A-Z^a-z^0-9]/', '', $type);
$type = strtolower($type);
//
// Find the desired file:
//
$folder = unixPath($_SERVER['DOCUMENT_ROOT'] . VIDEO_DIR . $video . '/', '-');
$handle = opendir($folder);
while (false !== ($file = readdir($handle))) {
	//
	$file = utf8_decode($file);
	//
	// We're only interested in actual files:
	//
	if(!is_file($folder . $file) || $file == '.' || $file == '..') {
		continue;
	}
	//
	// Language?
	//
	$pattern = "/$lang\./";
	if(!preg_match($pattern, $file)) {
		continue;
	}
	//
	// Resolution?
	//
	$pattern = "/\.$res\./";
	if(!preg_match($pattern, $file)) {
		continue;
	}
	//
	// Stereo layout?
	//
	$pattern = "/\.$stly\./";
	if(!preg_match($pattern, $file)) {
		continue;
	}
	//
	// File type?
	//
	$pattern = "/\.$type$/";
	if(!preg_match($pattern, $file)) {
		continue;
	}
	//
	// This is the file:
	//
	$file = $folder . $file;
	break;
}
//
// Start streaming:
//
if($type == 'mp4' || $type == 'mpg4' || $type == 'm4v' || $type == 'mp4v') {
	streamVideo($file, 'video/mp4');
}
else if($type == 'webm') {
	streamVideo($file, 'video/webm');
}
else if($type == 'ogg' || $type == 'ogv') {
	streamVideo($file, 'video/ogg');
}
exit();

?>