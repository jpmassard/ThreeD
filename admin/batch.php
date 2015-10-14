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
include_once(THREED_PATH . 'admin/include/threed_upload.inc.php');

$max_upload_files = get_ini_size('max_file_uploads');

if (isset($_POST['submit'])) {
	$threed_uploader_errors = array();
	$threed_uploader_file = array();

    // check file count    
    $filecount = $_FILES['file']['error'][0] == UPLOAD_ERR_NO_FILE ? 0 : count ($_FILES['file']['name']);
    if ($filecount > $max_upload_files)
        $threed_uploader_errors['file']['upload_error'] = l10n('Too many files selected');   
    else if ($filecount == 0)
        $threed_uploader_errors['file']['upload_error'] = l10n('Specify one or more 3D files to upload');   
    else {
        for ($i = 0 ; $i < $filecount; $i++)
            if ($_FILES['file']['error'][$i] != UPLOAD_ERR_OK) {
                $threed_uploader_errors['file']['upload_error'] = file_upload_error_message($_FILES['file']['error'][$i]);
                break;
            }
    }
    
    for ($i = 0 ; $i < $filecount; $i++) {
        $file = array (
            'name' => $_FILES['file']['name'][$i],
            'type' => $_FILES['file']['type'][$i],
            'tmp_name' => $_FILES['file']['tmp_name'][$i],
            'error' => $_FILES['file']['error'][$i],
            'size' => $_FILES['file']['size'][$i],
        );
        $threed_uploader_file = threed_image_upload_file ($file);    
		if(count($threed_uploader_file['errors']) != 0) {
			$threed_uploader_errors['file'] = $threed_uploader_file['errors'];
            break;
        }
        $threed_uploader_create_thumbnail = threed_image_create_thumbnail($threed_uploader_file['folder'], $threed_uploader_file['name']);
    	if(count($threed_uploader_create_thumbnail['errors']) != 0) {
   			$threed_uploader_errors['thumbnail'] = $threed_uploader_create_thumbnail['errors'];
            break;
        }
        threed_batch_synchronize($threed_uploader_file, $_POST['file_uploader']);
    }
    
	if (count($threed_uploader_errors) == 0) {
		array_push($page['infos'], l10n('Files uploaded succesfully'));
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
	$upload_max_filesize = get_ini_size('post_max_filesize', true);
}
$upload_max_filesize_display = round($upload_max_filesize/1024, 0);

$template->assign(
    array(
		'upload_max_filesize_display' => $upload_max_filesize_display,
        'max_upload_files' => $max_upload_files,
    )
);

$template->set_filename('threed_content', realpath(THREED_PATH . 'admin/template/batch.tpl'));
// $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');


function threed_batch_synchronize($file_uploader_file, $properties) {
	global $user, $conf;
	
	list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

	//Database registration
	$file_path = pwg_db_real_escape_string($file_uploader_file['folder'].'/'.$file_uploader_file['name']);
	$insert = array(
		'file' => $file_uploader_file['title'],
		'name' => get_name_from_file($file_uploader_file['title']),
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

?>