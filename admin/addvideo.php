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

defined('THREED_PATH') or die('Hacking attempt!');

// Check access and exit if user has no admin rights
check_status(ACCESS_ADMINISTRATOR);

global $template;

include_once(PHPWG_ROOT_PATH . 'admin/include/functions_upload.inc.php');

if (isset($_POST['submit'])) {
	$threed_uploader_errors = array();
	$threed_uploader_file = array();

	if($_FILES['file']['error'] == UPLOAD_ERR_OK) {
		$threed_uploader_file = threed_video_upload_file($_FILES['file']);
		if(count($threed_uploader_file['errors']) != 0)
			$threed_uploader_errors['file'] = $threed_uploader_file['errors'];
	} else if($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
		$threed_uploader_errors['file']['file_too_large'] = l10n('File exceeds the upload_max_filesize directive in php.ini');
	} else {
		$threed_uploader_errors['file']['no_file'] = l10n('Specify a 3D file to upload');
	}

	if (count($threed_uploader_errors) == 0) {
        if ($_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
    	    $threed_uploader_create_thumbnail = threed_video_upload_thumbnail($threed_uploader_file, $_FILES['thumbnail']);
    		if(count($threed_uploader_create_thumbnail['errors']) != 0)
    			$threed_uploader_errors['thumbnail'] = $threed_uploader_create_thumbnail['errors'];
        }
        else if($_FILES['thumbnail']['error'] == UPLOAD_ERR_INI_SIZE) {
		    $threed_uploader_errors['thumbnail']['file_too_large'] = l10n('File exceeds the upload_max_filesize directive in php.ini');
	    } else {
		    $threed_uploader_errors['thumbnail']['no_file'] = l10n('Specify a thumbnail to upload');
	    } 
    }
	
	if (count($threed_uploader_errors) == 0) {
		threed_video_synchronize($threed_uploader_file, $_POST['file_uploader']);
		array_push($page['infos'], l10n('File uploaded succesfully'));
	} else {
		array_push($page['errors'], l10n('There have been errors. See below'));
		$template->assign('threed_uploader_errors', $threed_uploader_errors);
		$template->assign('file_uploader', $_POST['file_uploader']);
	}
}

// Add size parameter to template
$upload_max_filesize = min(get_ini_size('upload_max_filesize'), get_ini_size('post_max_size'));
if ($upload_max_filesize == get_ini_size('upload_max_filesize')) {
	$upload_max_filesize = get_ini_size('upload_max_filesize', true);
} else {
	$upload_max_filesize = get_ini_size('post_max_size', true);
}
$upload_max_filesize_display = round($upload_max_filesize/1024, 0);

$template->assign(
    array(
		'upload_max_filesize' => $upload_max_filesize,
		'upload_max_filesize_display' => $upload_max_filesize_display,
    )
);
$template->set_filename('threed_content', realpath(THREED_PATH . 'admin/template/addvideo.tpl'));


function threed_video_upload_file($file) {
	global $threed_video_exts;
	$videoName =  $file['name'];	         //video name 
    $fileExtension = strtoupper(get_extension($videoName));
	$threed_uploader_errors = array();
	$return = array();

    // check if file extension is in the list of allowed ones
    if (!in_array($fileExtension, $threed_video_exts)) {
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
	$newfilename = $year.$month.$day.$hour.$minute.$second.'-'.substr($md5sum, 0, 8).'.'.get_extension($videoName);
    $newpath = $upload_dir.'/'.$newfilename;
	
	// Move temporary file to destination directory
	if (!move_uploaded_file($tmpFileName, $newpath)) {
		$threed_uploader_errors['upload_error'] = l10n('Can\'t upload file to galleries directory');
		$return['errors'] = $threed_uploader_errors;
		return $return;
	}
	
	$return['title'] = $videoName;
	$return['name'] = $newfilename;
	$return['extension'] = $fileExtension;
	$return['folder'] = $upload_dir;
	$return['size'] = $file['size'];
	$return['errors'] = $threed_uploader_errors;
	return $return;
}

function threed_video_upload_thumbnail($video, $thumbnail) {
	$upload_dir = $video['folder'].'/pwg_representative';
	$threed_uploader_errors = array();
	$return = array();
	
	// if upload directory does not exist, create it
	if (!is_dir($upload_dir))
	{
	   umask(0000);
	   if (!@mkdir($upload_dir, 0777, true))
	   {
		  $threed_uploader_errors['upload_error'] = l10n('Can\'t create thumbnail directory');
		  $return['errors'] = $threed_uploader_errors;
		  return $return;
	   }
	}
	// Add index.htm to prevent browsing the image directory
    secure_directory($upload_dir);
    
    $extension= get_extension ($thumbnail['name']);
    if ($extension == 'jpeg' || $extension == 'JPEG')
        $extension = 'jpg';
    $file_dest = $upload_dir.'/'.get_filename_wo_extension ($video['name']).'.'.$extension;
    $extension= strtoupper($extension);
    
	// Move temporary file to destination directory
	if (!move_uploaded_file($thumbnail['tmp_name'], $file_dest)) {
		$threed_uploader_errors['upload_error'] = l10n('Can\'t upload file to galleries directory');
	}
    
	$return['errors'] = $threed_uploader_errors;
	return $return;
}


function threed_video_synchronize($file_uploader_file, $properties) {
	global $user, $conf;
	
	list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

	//Database registration
	$file_path = pwg_db_real_escape_string($file_uploader_file['folder'].'/'.$file_uploader_file['name']);
	$insert = array(
		'file' => $file_uploader_file['title'],
		'name' => ($properties['title'] != '') ? pwg_db_real_escape_string($properties['title']) : get_name_from_file($file_uploader_file['title']),
		'comment' => pwg_db_real_escape_string($properties['description']),
		'date_available' => $dbnow,
		'path' => './'.preg_replace('#^'.preg_quote(PHPWG_ROOT_PATH).'#', '', $file_path),
		'representative_ext' => 'jpg',
		'filesize' => floor($file_uploader_file['size'] / 1024),
		'md5sum' => md5_file($file_path),
		'added_by' => $user['id'],
        'rotation' => 0,
	);

    single_insert(IMAGES_TABLE, $insert);
	$image_id = pwg_db_insert_id(IMAGES_TABLE);
    associate_images_to_categories(array($image_id), array($properties['category']));
}

?>