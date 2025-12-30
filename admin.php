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

if(!defined('THREED_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

check_status(ACCESS_ADMINISTRATOR);

global $template, $page, $conf;

//load_language('plugin.lang', THREED_PATH);

$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'config';

$errors = array();

if (isset($_POST['save_config']))
{
  $configThreed = array(
    'chromeCast' => isset($_POST['chromecast']) ? true : false,
    'openGraph' => isset($_POST['opnGraphAllowed']) ? true : false,
    'video_autoplay' => isset($_POST['video_autoplay']) ? true : false,
    'video_autoloop' => isset($_POST['video_autoloop']) ? true : false,
    'icon2Dphoto' => isset($_POST['photo2d']) ? true : false,
    'icon2Dvideo' => isset($_POST['video2d']) ? true : false,
    'icon3Dphoto' => isset($_POST['photo3d']) ? true : false,
    'icon3Dvideo' => isset($_POST['video3d']) ? true : false,
    'iconposition' => $_POST['iconposition'],
    'iconxpos' => $_POST['iconposition']=='custom' ? $_POST['iconxpos'] : 50,
    'iconypos' => $_POST['iconposition']=='custom' ? $_POST['iconypos'] : 50,
    'iconalpha' => $_POST['iconalpha'],
  );

	// make some test on critiical values
	$oor = l10n(' must be in the range [0..100]');
	if($configThreed['iconxpos'] < 0 or $configThreed['iconxpos'] > 100 ) $errors['xpos'] = 'x pos'. $oor;
	if($configThreed['iconypos'] < 0 or $configThreed['iconypos'] > 100 ) $errors['ypos'] = 'y pos'. $oor;
	if($configThreed['iconalpha'] < 0 or $configThreed['iconalpha'] > 100 ) $errors['opacity'] = 'opacity'. $oor;

  if(count($errors) == 0) {
  	conf_update_param('threed', $configThreed, true);
  	$page['infos'][] = l10n('Your configuration settings are saved');
  }
  unset($_POST['save_config']);
} 

elseif (isset($_POST['save_settings'])) {
	$image_id = $page['tab'];
	if(isset($_POST['3Dmaterial']) && $_POST['3Dmaterial'] == 'on') {
		set_3D_material($image_id, 1);
	}else {
		set_3D_material($image_id, 0);
	}
	if(isset($_POST['uploadRepresentative']) && $_POST['uploadRepresentative'] == 'on') {
		if(isset($_FILES['rFile']) ) {
			 $name = $_FILES['rFile']['name'];
			 if($name != '') {
				include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

			 	$img_infos = get_image_infos($image_id, true);
      			$file_path = $img_infos['path'];

      			// Create the new file in pwg_representative sub-directory
      			$representative_file_path = dirname($file_path).'/pwg_representative/' . get_filename_wo_extension(basename($file_path)).'.';

				// In replace case, delete old representative file and derivatives
      			if($img_infos['representative_ext'] != null) {
					@unlink($representative_file_path . $img_infos['representative_ext']);
				    delete_element_derivatives($img_infos);
				}
				$rExt = get_extension($name);
				$representative_file_path.= $rExt;

      			prepare_directory(dirname($representative_file_path));
      			move_uploaded_file($_FILES['rFile']['tmp_name'], $representative_file_path);

				$query = 'UPDATE ' . IMAGES_TABLE . ' SET representative_ext=\'' . $rExt . '\' WHERE id=\'' . $image_id .'\';';
				pwg_query($query);

				// Everything is OK, tell this to admin
				$page['infos']['rFile'] = l10n('The representative picture was updated');

			} else {
				$errors['rFile'] = l10n('A representative picture must be selected');
			}
		}
	}
	unset($_POST['save_settings']);
}

$themeconf = $template->get_template_vars('themeconf');

// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();

if('config' == $page['tab']) {
	$tabsheet->add('config', l10n('Configuration'), THREED_ADMIN . '-config');
	$tabsheet->select('config');
	$tabsheet->assign();
	
	// template
	$template->set_filename('threed_admin_content', dirname(__FILE__).'/admin/template/config.tpl');
	$template->assign(array(
		'threed' => $conf['threed'],
		'theme' => $themeconf,
	));
}
else {
//	$_GET['image_id'] = $_GET['tab'];
	check_input_parameter('tab', $_GET, false, PATTERN_ID);
	$image_id = $_GET['tab'];
	$admin_photo_base_url = get_root_url().'admin.php?page=photo-'. $image_id;
	
	$tabsheet->set_id('photo');
	$tabsheet->select('threed');
	$tabsheet->assign();
	
	$template->set_filename('threed_admin_content', dirname(__FILE__).'/admin/template/image_admin.tpl');
	
	$template->assign(array(
		'ADMIN_PAGE_TITLE' => l10n('Edit photo').' <span class="image-id">#'.$image_id.'</span>',
		'is3D' => is_3D_material($image_id),
		'image_id' => (int)$image_id,
		'threed' => $conf['threed'],
		'theme' => $themeconf,
	));
}
	if(count($errors) != 0) $template->assign('errors', $errors);
	$template->assign_var_from_handle('ADMIN_CONTENT', 'threed_admin_content');
	unset($errors);

?>
