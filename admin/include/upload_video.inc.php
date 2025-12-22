<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

// Create initial video representative picture
function upload_threed_video ($representative_ext, $file_path)
{
	// exit immediately if extension does not correspond
	// so an other driver can do the job
	global $threed_video_exts;
	global $conf;
	if (isset($representative_ext) or !in_array(get_extension($file_path), $threed_video_exts))
		return $representative_ext;
	
	$representative_file_path = dirname($file_path).'/pwg_representative/';
	$representative_extension= 'jpg';
	$representative_file_path.= get_filename_wo_extension(basename($file_path)).'.jpg';
	
	prepare_directory(dirname($representative_file_path));
	
	$second = 1;
	$ffmpeg = $conf['ffmpeg_dir'].'ffmpeg';
	$ffmpeg.= ' -i "'.$file_path.'"';
	$ffmpeg.= ' -an -ss '.$second;
	$ffmpeg.= ' -t 1 -r 1 -y -vcodec mjpeg -f mjpeg';
	$ffmpeg.= ' "'.$representative_file_path.'"';
	@exec($ffmpeg);
	
	if (file_exists($representative_file_path))
		threed_thumbnail_video_watermark ($representative_file_path);    
	else
		$representative_extension = null;
	
	return $representative_extension;
}

function threed_thumbnail_video_watermark ($image)
{
	$stamp = imagecreatefrompng(THREED_PATH . 'admin/video.png');
	$im = imagecreatefromjpeg($image);
	
	$sx = imagesx($stamp);
	$sy = imagesy($stamp);
	
	// Copy the stamp image onto our photo using the margin offsets and the photo 
	// width to calculate positioning of the stamp. 
	imagecopy($im, $stamp, (imagesx($im) - $sx)/2 , (imagesy($im) - $sy)/2, 0, 0, $sx, $sy);
	imagejpeg ($im, $image, 70);
	// free memory
	imagedestroy($im);
}

?>
