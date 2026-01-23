<?php

// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

if(!defined('THREED_PATH')) die('Hacking attempt!');

// Create initial video representative picture
add_event_handler ('upload_file', 'threed_upload_video');
function threed_upload_video ($representative_ext, $file_path)
{
	global $threed_video_exts;
	global $conf;

	// exit immediately if extension does not correspond so an other driver can do the job
	if (isset($representative_ext) or !in_array(get_extension($file_path), $threed_video_exts)) {
		return $representative_ext;
	}

	$representative_file_path = dirname($file_path).'/pwg_representative/';
	$representative_extension= 'jpg';
	$representative_file_path.= get_filename_wo_extension(basename($file_path)).'.jpg';

	prepare_directory(dirname($representative_file_path));

	if($conf['ffmpeg_dir']) {
		$second = 1;
		$ffmpeg = $conf['ffmpeg_dir'].'ffmpeg';
		$ffmpeg.= ' -i "'.$file_path.'"';
		$ffmpeg.= ' -an -ss '.$second;
		$ffmpeg.= ' -t 1 -r 1 -y -vcodec mjpeg -f mjpeg';
		$ffmpeg.= ' "'.$representative_file_path.'"';
		@exec($ffmpeg);
	}

	if (!file_exists($representative_file_path))
		$representative_extension = null;

	return $representative_extension;
}

?>
