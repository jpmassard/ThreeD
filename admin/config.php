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
        'icon2Dphoto'    => isset($_POST['photo2d']) ? true : false,
        'icon2Dvideo'    => isset($_POST['video2d']) ? true : false,
        'icon3Dphoto'    => isset($_POST['photo3d']) ? true : false,
        'icon3Dvideo'    => isset($_POST['video3d']) ? true : false,
        'icon360Pano'    => isset($_POST['pano360']) ? true : false,
        'iconposition'   => $_POST['iconposition'],
        'iconxpos'       => $_POST['iconposition']=='custom' ? $_POST['iconxpos'] : 50,
        'iconypos'       => $_POST['iconposition']=='custom' ? $_POST['iconypos'] : 50,
        'iconalpha'      => $_POST['iconalpha'],
    );
  
  conf_update_param('threed', serialize($config['threed']));
  $page['infos'][] = l10n('Information data registered in database');
}


$template->assign(array(
  'threed' => $conf['threed'],
  ));

$template->set_filename('threed_content', realpath(THREED_PATH . 'admin/template/config.tpl'));

?>