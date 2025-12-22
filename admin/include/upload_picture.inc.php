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

// Needed for restoring original Exif data
use lsolesen\pel\PelExif;
use lsolesen\pel\PelJpeg;

require_once THREED_PATH . '/pelAutoload.php';

define ('THUMBNAIL_DIM', 480);

// Add the flag is3D to database
add_event_handler('loc_end_add_uploaded_file', function ($image_infos)
{
	global $threed_image_exts;
	$ext = get_extension($image_infos ['path']);
	if(in_array($ext, $threed_image_exts))
	{
		$query = 'UPDATE ' .IMAGES_TABLE. ' SET is3D=1 WHERE id=' .$image_infos ['id']. ';';
		pwg_query($query);
	}
});

// Create initial representative picture
function upload_threed_picture ($representative_ext, $file_path)
{
	// exit immediately if extension does not correspond
	// so an other picture driver can do the job
	global $threed_image_exts;
	if (isset($representative_ext) or !in_array(get_extension($file_path), $threed_image_exts))
	return $representative_ext;
	  
	$representative_file_path = dirname($file_path).'/pwg_representative/';
	$representative_extension= 'jpg';
	$representative_file_path.= get_filename_wo_extension(basename($file_path)).'.jpg';
	
	prepare_directory(dirname($representative_file_path));
	
	// A thumbnail (??? x 400 !!!) is created from a mpo, jps or jpg file format
	// Then original exif data are copied to the thumbnail
	//
	$file_infos = pwg_image_infos($file_path);
	$width = $file_infos['width'];
	$height = $file_infos['height'];
	$ratio = $width / $height;
	$ext = get_extension($file_path); 
	if ('mpo' == $ext) {
		$handle = fopen($file_path,'rb');
		$status = 0;
		$done = false;
		$imgind = 0;
		while (!feof($handle) && !$done) {
			// Burk!!! not really optimal, but dont known how to do better in php...
			$data = fread($handle, 1);
			switch ($status) {
				case 0: $status = (ord ($data) == 0xff) ? 1 : 0;
					break;
				case 1: $status = (ord ($data) == 0xd8) ? 2 : 0;
					break;
				case 2: $status = (ord ($data) == 0xff) ? 3 : 0;
					break;
				case 3: $status = 0;
					if (ord ($data) == 0xe1) {
						if ($imgind != 0) {
							$End = ftell($handle) - 4;
							$done = true;
							break;
						}
						$Start = ftell($handle) - 4;
						$imgind++;
					}
					break;
			}
		}
		fseek ($handle, $Start);
		$image = imagecreatefromstring (fread ($handle, $End- $Start));
		if ($ratio > 1) {
			$temp = imagecreatetruecolor (THUMBNAIL_DIM * $ratio, THUMBNAIL_DIM);
			imagecopyresampled($temp, $image, 0, 0, 0, 0, THUMBNAIL_DIM * $ratio, THUMBNAIL_DIM, $width, $height);
		}
		else {
			$temp = imagecreatetruecolor (THUMBNAIL_DIM , THUMBNAIL_DIM / $ratio);
			imagecopyresampled($temp, $image, 0, 0, 0, 0, THUMBNAIL_DIM, THUMBNAIL_DIM / $ratio, $width, $height);
		}
		fclose ($handle);
	} else {
		// Image in JPS or JPG format. Isolate left side only
		$image = imagecreatefromjpeg($file_path);
		if ($ratio > 1) {
			$temp = imagecreatetruecolor (THUMBNAIL_DIM * $ratio / 2, THUMBNAIL_DIM);
			imagecopyresampled($temp, $image, 0, 0, 0, 0, THUMBNAIL_DIM * $ratio / 2, THUMBNAIL_DIM, $width / 2, $height);
		}
		else {
			$temp = imagecreatetruecolor (THUMBNAIL_DIM , THUMBNAIL_DIM / $ratio * 2);
			imagecopyresampled($temp, $image, 0, 0, 0, 0, THUMBNAIL_DIM, THUMBNAIL_DIM / $ratio * 2, $width / 2, $height);
		}
	}
	imagejpeg ($temp, $representative_file_path, 70);
	imagedestroy ($temp);
	
	// read Exif infos from original file
	$input_jpeg = new PelJpeg($file_path);
	$exif = $input_jpeg->getExif();

	// write exif data to representative picture
	$output_jpeg = new PelJpeg($representative_file_path);
	if ($exif != null)
		$output_jpeg->setExif($exif);
	$output_jpeg->saveFile($representative_file_path);

	if (!file_exists($representative_file_path))
		$representative_extension = null;

	return $representative_extension;
}

function threed_thumbnail_2Dphoto_watermark ($image)
{
	$stamp = imagecreatefrompng(THREED_PATH . 'admin/2D.png');
	$im = imagecreatefromjpeg($image);
	
	$sx = imagesx($stamp);
	$sy = imagesy($stamp);
	
	// Copy the stamp image onto our photo using the margin offsets and the photo 
	// width to calculate positioning of the stamp. 
	imagecopy($im, $stamp, (imagesx($im) - $sx)/2 , (imagesy($im) - $sy)/2, 0, 0, $sx, $sy);
	imagejpeg ($im, $image, 70);
	// free memory
	imagedestroy($im);
	imagedestroy($stamp);
}
?>
