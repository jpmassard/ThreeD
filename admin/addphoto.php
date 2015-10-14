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

if (isset($_POST['submit'])) {
	$threed_uploader_errors = array();
	$threed_uploader_file = array();

	if($_FILES['file']['error'] == UPLOAD_ERR_OK) {
		$threed_uploader_file = threed_image_upload_file($_FILES['file']);
		if(count($threed_uploader_file['errors']) != 0)
			$threed_uploader_errors['file'] = $threed_uploader_file['errors'];
	} else if($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
		$threed_uploader_errors['file']['file_too_large'] = l10n('File exceeds the upload_max_filesize directive in php.ini');
	} else {
		$threed_uploader_errors['file']['no_file'] = l10n('Specify a 3D file to upload');
	}

	if (count($threed_uploader_errors) == 0) {
    	$threed_uploader_create_thumbnail = threed_image_create_thumbnail($threed_uploader_file['folder'], $threed_uploader_file['name']);
    		if(count($threed_uploader_create_thumbnail['errors']) != 0)
    			$threed_uploader_errors['thumbnail'] = $threed_uploader_create_thumbnail['errors'];
    }
	
	if (count($threed_uploader_errors) == 0) {
		threed_image_synchronize($threed_uploader_file, $_POST['file_uploader']);
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
	$upload_max_filesize = get_ini_size('post_max_filesize', true);
}
$upload_max_filesize_display = round($upload_max_filesize/1024, 0);

$template->assign(
    array(
		'upload_max_filesize_display' => $upload_max_filesize_display,
    )
);
$template->set_filename('threed_content', realpath(THREED_PATH . 'admin/template/addphoto.tpl'));
// $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>