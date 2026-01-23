<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

check_status(ACCESS_ADMINISTRATOR);

global $template, $page, $conf, $admin_photo_base_url;

$errors = array();

if (isset($_POST['save_config']))
{
	$configThreed = array(
		'chromeCast'     => isset($_POST['chromecast']) ? true : false,
		'openGraph'      => isset($_POST['opnGraphAllowed']) ? true : false,
		'video_autoplay' => isset($_POST['video_autoplay']) ? true : false,
		'video_autoloop' => isset($_POST['video_autoloop']) ? true : false,
		'icon_2Dphoto'    => isset($_POST['photo2d']) ? true : false,
		'icon_2Dvideo'    => isset($_POST['video2d']) ? true : false,
		'icon_3Dphoto'    => isset($_POST['photo3d']) ? true : false,
		'icon_3Dvideo'    => isset($_POST['video3d']) ? true : false,
		'icon_360Pano'    => isset($_POST['pano360']) ? true : false,
		'icon_position'   => $_POST['icon_position'],
		'icon_xpos'       => $_POST['icon_position']=='custom' ? $_POST['icon_xpos'] : 50,
		'icon_ypos'       => $_POST['icon_position']=='custom' ? $_POST['icon_ypos'] : 50,
		'icon_alpha'      => $_POST['icon_alpha'],
	);

	// make some test on critiical values
	$oor = l10n(' must be in the range [0..100]');
	if($configThreed['icon_xpos'] < 0 or $configThreed['icon_xpos'] > 100 ) $errors['xpos'] = 'x pos'. $oor;
	if($configThreed['icon_ypos'] < 0 or $configThreed['icon_ypos'] > 100 ) $errors['ypos'] = 'y pos'. $oor;
	if($configThreed['icon_alpha'] < 0 or $configThreed['icon_alpha'] > 100 ) $errors['opacity'] = 'opacity'. $oor;

  if(count($errors) == 0)
  {
	conf_update_param('threed', $configThreed, true);
	$page['infos'][] = l10n('Your configuration settings are saved');
  }
  unset($_POST['save_config']);
} 

$themeconf = $template->get_template_vars('themeconf');

	// template
	$template->set_filename('threed_admin_content', THREED_PATH .'admin/template/config.tpl');
	$template->assign(array(
		'threed' => $conf['threed'],
		'theme' => $themeconf,
	));

if(count($errors) != 0)
{
	$template->assign('errors', $errors);
}
$template->assign_var_from_handle('ADMIN_CONTENT', 'threed_admin_content');
unset($errors);

?>
