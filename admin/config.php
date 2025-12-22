<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2025 Jean-Paul MASSARD         https://jpmassard.fr |
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

global $conf, $page, $template;

if (isset($_POST['save_config']))
{
  $config['threed'] = array(
    'chromeCast' => (int)$_POST['chrCastAllowed'],
    'openGraph' => (int)$_POST['opnGraphAllowed'],
    'video_autoplay' => (int)$_POST['video_autoplay'],
    'video_autoloop' => (int)$_POST['video_autoloop'],
    );
  
  conf_update_param('threed', serialize($config['threed']));
  $page['infos'][] = l10n('Information data registered in database');
}


$template->assign(array(
  'threed' => $conf['threed'],
  ));

$template->set_filename('threed_content', realpath(THREED_PATH . 'admin/template/config.tpl'));

?>