<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

if(!defined('THREED_PATH')) die('Hacking attempt!');

global $conf, $page, $template;

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
  
  conf_update_param('threed', serialize($config['threed']));
  $page['infos'][] = l10n('Information data registered in database');
}


$template->assign(array(
  'threed' => $conf['threed'],
  ));

$template->set_filename('threed_content', realpath(THREED_PATH . 'admin/template/config.tpl'));

?>