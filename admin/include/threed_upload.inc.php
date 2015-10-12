<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2015 Jean-Paul MASSARD                              |
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
require_once(THREED_PATH . 'Pel/PelJpeg.php');

define ('THUMBNAIL_DIM', 400);

function threed_image_upload_file($file) {
	global $threed_image_exts;
	$imageName =  $file['name'];	         //image name 
    $fileExtension = strtoupper(get_extension($imageName));
	$threed_uploader_errors = array();
	$return = array();

    // check if file extension is in the list of allowed ones
    if (!in_array($fileExtension, $threed_image_exts)) {
		$threed_uploader_errors['upload_error'] = l10n('File upload stopped by extension');
		$return['errors'] = $threed_uploader_errors;
		return $return;
	}
	
    $tmpFileName= $file['tmp_name'];
    $md5sum = md5_file($tmpFileName);

    list($year, $month, $day, $hour, $minute, $second) = preg_split('/[^\d]+/', date(DATE_ATOM, time()));
    $upload_dir = './upload/'.$year.'/'.$month.'/'.$day;

	// if upload directory does not exist, create it
	if (!is_dir($upload_dir))
	{
	   umask(0000);
	   if (!@mkdir($upload_dir, 0777, true))
	   {
		  $threed_uploader_errors['upload_error'] = l10n('Can\'t create gallery directory');
		  $return['errors'] = $threed_uploader_errors;
		  return $return;
	   }
	}
	// Add index.htm to prevent browsing the image directory
    secure_directory($upload_dir);

    // Build new image path
	$newfilename = $year.$month.$day.$hour.$minute.$second.'-'.substr($md5sum, 0, 8).'.'.get_extension($imageName);
    $newpath = $upload_dir.'/'.$newfilename;
	
	// Move temporary file to destination directory
	if (!move_uploaded_file($tmpFileName, $newpath)) {
		$threed_uploader_errors['upload_error'] = l10n('Can\'t upload file to galleries directory');
		$return['errors'] = $threed_uploader_errors;
		return $return;
	}
	
	$return['title'] = $imageName;
	$return['name'] = $newfilename;
	$return['extension'] = $fileExtension;
	$return['folder'] = $upload_dir;
	$return['size'] = $file['size'];
	$return['errors'] = $threed_uploader_errors;
	return $return;
}


function threed_image_synchronize($file_uploader_file, $properties) {
	global $user, $conf;
	
	list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

	//Database registration
	$file_path = pwg_db_real_escape_string($file_uploader_file['folder'].'/'.$file_uploader_file['name']);
	$insert = array(
		'file' => $file_uploader_file['title'],
		'name' => ($properties['title'] != '') ? pwg_db_real_escape_string($properties['title']) : get_name_from_file($file_uploader_file['title']),
		'comment' => pwg_db_real_escape_string($properties['description']),
        'author' =>  pwg_db_real_escape_string($properties['author']),
		'date_available' => $dbnow,
		'path' => './'.preg_replace('#^'.preg_quote(PHPWG_ROOT_PATH).'#', '', $file_path),
		'representative_ext' => 'jpg',
		'filesize' => floor($file_uploader_file['size'] / 1024),
		'md5sum' => md5_file($file_path),
		'added_by' => $user['id'],
        'rotation' => 0,
	);

  	// update metadata from the uploaded file (exif/iptc)
  	if (function_exists('read_exif_data')) {
  		$infos = load_metadata($file_path);
  		$insert = array_merge ($insert, $infos);
  	}
    
    single_insert(IMAGES_TABLE, $insert);
	$image_id = pwg_db_insert_id(IMAGES_TABLE);
    associate_images_to_categories(array($image_id), array($properties['category']));

}

// A thumbnail (640 x 400 !!!) is created from a mpo, jps or jpg file format
// Then original exif data are copied to the thumbnail
//
function threed_image_create_thumbnail($folder, $name) {
	$upload_dir = $folder.'/pwg_representative';
	$threed_uploader_errors = array();
	$return = array();
	
	// if upload directory does not exist, create it
	if (!is_dir($upload_dir))
	{
	   umask(0000);
	   if (!@mkdir($upload_dir, 0777, true))
	   {
		  $threed_uploader_errors['upload_error'] = l10n('Can\'t create thumnail directory');
		  $return['errors'] = $threed_uploader_errors;
		  return $return;
	   }
	}
	// Add index.htm to prevent browsing the image directory
    secure_directory($upload_dir);
    
    $file_infos = pwg_image_infos($folder.'/'.$name);
    $file_dest = $upload_dir.'/'.get_filename_wo_extension ($name).'.jpg';
    $extension= strtoupper(get_extension ($name));
    $width = $file_infos['width'];
    $height = $file_infos['height'];
    $ratio = $width / $height;
    if ($extension == 'MPO') {
       $handle = fopen($folder.'/'.$name,'rb');
	   $status = 0;
	   $done = false;
	   $imgind = 0;
	   while (!feof($handle) && !$done) {
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
	    $image = imagecreatefromjpeg($folder.'/'.$name);
        if ($ratio > 1) {
          $temp = imagecreatetruecolor (THUMBNAIL_DIM * $ratio / 2, THUMBNAIL_DIM);
          imagecopyresampled($temp, $image, 0, 0, 0, 0, THUMBNAIL_DIM * $ratio / 2, THUMBNAIL_DIM, $width / 2, $height);
        }
        else {
          $temp = imagecreatetruecolor (THUMBNAIL_DIM , THUMBNAIL_DIM / $ratio * 2);
          imagecopyresampled($temp, $image, 0, 0, 0, 0, THUMBNAIL_DIM, THUMBNAIL_DIM / $ratio * 2, $width / 2, $height);
        }
	}

	// read Exif infos from original file
	$input_jpeg = new PelJpeg($folder.'/'.$name);
	$exif = $input_jpeg->getExif();

	imagejpeg ($temp, $file_dest, 70);
	// 
	$output_jpeg = new PelJpeg($file_dest);
	if ($exif != null)
  		$output_jpeg->setExif($exif);

	$output_jpeg->saveFile($file_dest);

	$return['errors'] = $threed_uploader_errors;
	return $return;
}

function load_metadata($file)
{
  global $conf;
  $infos = array();

  if ($image_size = @getimagesize($file)) {
    $infos['width'] = strtoupper (get_extension ($file)) == 'MPO' ? $image_size[0] : $image_size[0] / 2;
    $infos['height'] = $image_size[1];
  }

  if ($conf['use_exif']) {
    $exif = get_sync_exif_data($file);
    $infos = array_merge($infos, $exif);
  }

  if ($conf['use_iptc']) {
    $iptc = get_sync_iptc_data($file);
    $infos = array_merge($infos, $iptc);
  }
  return $infos;
}

?>
