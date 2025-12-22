<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
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

global $template, $page;

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

$themeconf = $template->get_template_vars('themeconf');

if('config' == $page['tab']) {
  // tabsheet
  include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
  $tabsheet = new tabsheet();
  $tabsheet->add('config', l10n('Configuration'), THREED_ADMIN . '-config');
  $tabsheet->select('config');
  $tabsheet->assign();

  // template
  $template->set_filename('threed_admin_content', dirname(__FILE__).'/admin/template/config.tpl');
  $template->assign(array(
    'threed' => $conf['threed'],
    'theme' => $themeconf,
  ));
  if(count($errors) != 0) $template->assign('errors', $errors);
}
else {
	$_GET['image_id'] = $_GET['tab'];
	check_input_parameter('image_id', $_GET, false, PATTERN_ID);
	$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];
	
	$tabsheet = new tabsheet();
	$tabsheet->set_id('photo');
	$tabsheet->select('threed');
	$tabsheet->assign();
	
	$template->set_filename('threed_admin_content', dirname(__FILE__).'/template/image_config.tpl');
	
	$template->assign(array(
//		'TITLE' => render_element_name($picture),
		'image_id' => (int)@$_GET['image_id'],
		'random_avoid_cache_key' => generate_key(10),
		'ADMIN_PAGE_TITLE' => l10n('Edit photo').' <span class="image-id">#'.$_GET['image_id'].'</span>',
    'theme' => $themeconf,
		));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'threed_admin_content');
unset($errors);
?>
