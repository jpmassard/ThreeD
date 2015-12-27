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

// Admin plugins menu link
function threed_admin_plugin_menu_links($menu)
{
  $menu[] = array(
    'NAME' => 'ThreeD',
    'URL' => THREED_ADMIN,
    );
  return $menu;
}

function threed_add_tab_menu($sheets, $id)
{
	if ($id == 'photo')
	{
		$sheets['threed'] = array(
			'caption' => 'ThreeD',
			'url' => get_root_url().'admin.php?page=plugin&amp;section=ThreeD/admin/admin_video.php&amp;image_id='.$_GET['image_id'],);

		unset($sheets['coi'], $sheets['update']);
		unset($sheets['rotate'], $sheets['update']);
	}

	return $sheets;
}
