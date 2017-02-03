<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2017 Jean-Paul MASSARD          http://jpmassard.fr |
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

global $template, $page, $conf;

//load_language('plugin.lang', THREED_PATH);
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'config';

// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->add('config', l10n('Configuration'), THREED_ADMIN . '-config');
$tabsheet->select($page['tab']);
$tabsheet->assign();


// include page
include(THREED_PATH . 'admin/' . $page['tab'] . '.php');

// template
$template->assign(array(
  'THREED_PATH'=> THREED_PATH,
  'THREED_ABS_PATH'=> dirname(__FILE__).'/',
  'THREED_ADMIN' => THREED_ADMIN,
  ));
  
$template->assign_var_from_handle('ADMIN_CONTENT', 'threed_content');
?>
