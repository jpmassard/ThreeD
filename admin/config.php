<?php
defined('THREED_PATH') or die('Hacking attempt!');

global $conf, $page, $template;

if (isset($_POST['save_config']))
{
  $conf['threed'] = array(
    'chromeCast' => (int)$_POST['chrCastAllowed'],
    'openGraph' => (int)$_POST['opnGraphAllowed'],
    'video_autoplay' => (int)$_POST['video_autoplay'],
    'video_description' => $_POST['video_description'],
    );
  
  conf_update_param('threed', serialize($conf['threed']));
  $page['infos'][] = l10n('Information data registered in database');
}


$template->assign(array(
  'threed' => $conf['threed'],
  ));

$template->set_filename('threed_content', realpath(THREED_PATH . 'admin/template/config.tpl'));

?>