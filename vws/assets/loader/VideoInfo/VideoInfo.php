<?php 

/**
 * VideoInfo.php
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 *
 * A script to scan a directory for video clips and return formatted information 
 * about the clips based on a naming scheme convention. This script is used by
 * VWS video players to provide multi-resolution and multi-language features.
 *
 * Usage:
 * (1) Videos must reside in a folder within the VIDEO_DIR common directory.
 * (2) Video clips must follow a fixed naming convention. This PHP script will
 * assume the following structure of dot-separated info:
 *     [title].[language].[resolution].[stereo layout].[type]
 *     e.g. "fancy_video.en.240p.pa.mp4", "en.720p.pa.webm"
 * (3) The script is called with the name of the directory where the video clips 
 * reside. It is assumed that all clips within this directory are of identical 
 * content and differ only in their encoding. The name of this directory must
 * only contain A-Z, a-z, 0-9, underscores, dashes, and dots.
 *
 * @param video {string} The name of the video set, i.e. the folder name.
 *
 * Note:
 * When using this with SWFobject/Flash, the ampersand must be given as %26. 
 */

/**
 * Includes:
 */
$vws = $_SERVER['DOCUMENT_ROOT'] . 'plugins/ThreeD/vws/assets/all/php/';
require($vws . 'CONFIG.php');
require($vws . 'video.php');

/**
 * Main program:
 */
header('Content-Type: text/javascript;charset=utf-8');
//
$video = $_REQUEST['video'];
if(!isset($video)) {
	print("VWS.STATIC.videoInfo = {\n\terror: \"Missing query variable.\"\n};");
	exit;
}
$video = preg_replace('/[^A-Z^a-z^0-9^_^\-^\.]/', '', $video);
if($video == '') {
	print("VWS.STATIC.videoInfo = {\n\terror: \"Missing query variable.\"\n};");
	exit;
}
//
// Physical path to the video directory:
//
$dir = $_SERVER['DOCUMENT_ROOT'] . VIDEO_DIR . $video . '/';
$dir = str_replace('//', '/', $dir);
if (!file_exists($dir) || !is_dir($dir)) {
	print("VWS.STATIC.videoInfo = {\n\terror: \"No such directory.\"\n};");
	exit;
}
//
// URL path to the video directory:
//
$root = VIDEO_DIR . '/' . $video . '/';
$root = str_replace('//', '/', $root);
//
$json = "VWS.STATIC.videoInfo = {\n\tname: \"$video\",\n\troot: \"$root\",\n";
//
// Read the video directory:
//
$count = 0;
$handle = opendir($dir);
while (false !== ($file = readdir($handle))) {
	//
	$physPath = $dir . $file;
	//
	// We're only interested in actual files:
	//
	if (!is_file($physPath) || is_dir($physPath)) {
		continue;
	}
	//
	// File names follow a certain naming scheme, whereby information we might 
	// be interested in is added between the title and the file extension. A 
	// name like "my_fancy_video_title.en.240p.PA.1.1000.mp4" is easily parsed:
	//
	$arr = explode('.', $file);
	$len = count($arr);
	//
	// If it isn't a video it's no good to us, unless it's a poster image:
	//
	$ext = strtolower($arr[$len - 1]);
	if ($ext == 'mpo' || $ext == 'jps' || $ext == 'jpg' || $ext == 'jpeg' 
		|| $ext == 'png' || $ext == 'gif') {
		$image = $root . $file;
		$json .= "\timage: \"$image\",\n";
	}
	else if (isHtml5Video($physPath)) {
		//
		$json .= "\tvideo$count: {\n";
		//
		// The rest of the info is here. We must count from the end, as the 
		// video may or may not have a title per our naming scheme:
		//
		$language = strtolower($arr[$len - 4]);
		$json .= "\t\tlanguage: \"$language\",\n";
		//
		$res = strtolower($arr[$len - 3]);
		if ($res == 'sd' || $res == 'hd' || $res == '2k' || $res == '4k') {
			$res = strtoupper($res);
		}
		$json .= "\t\tresolution: \"$res\",\n";
		//
		$stereoLayout = strtoupper($arr[$len - 2]);
		$json .= "\t\tstereoLayout: \"$stereoLayout\",\n";
		//
		$type = strtolower($arr[$len - 1]);
		$json .= "\t\ttype: \"$type\",\n";
		//
		if(VIDEO_LOADER) {
			$urlPath = VIDEO_LOADER . '?video=' . $video . '&language=' 
				. $language . '&resolution=' . $res . '&stereoLayout=' 
				. $stereoLayout . '&type=' . $type;
		}
		else {
			$urlPath = $root . $file;
		}
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $urlPath;
		$json .= "\t\turl: \"$url\"\n";
		//
		$json .= "\t},\n";
		$count++;
	}
}
$json .= "\tvideoCount: $count\n} ";
//
if($count == 0) {
	print("VWS.STATIC.videoInfo = {\n\terror: \"Not a video directory.\"\n};");
}
else {
	print($json);
}
exit;

?>