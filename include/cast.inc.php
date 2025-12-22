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

defined('THREED_PATH') or die('Hacking attempt!');

// Add ChromeCast functions
add_event_handler('loc_begin_page_tail', function () {
	global $conf, $template, $page;

	if ($conf['threed']['chromeCast'] == 1 and isset($page['category'])) {
		$template->append('footer_elements', '
		<script type="text/javascript" src="plugins/ThreeD/ChromeCast/cast.js"></script>
		<script type="text/javascript">
			var castPlayer = new CastPlayer();
			window["__onGCastApiAvailable"] = function(isAvailable) {
				if (isAvailable) {
					castPlayer.initialize();
				}
			};
		</script>
		<script type="text/javascript" src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>');
	}
});

add_event_handler('blockmanager_apply', function ($menu_ref) {
	global $conf, $user, $page, $template;
	if ($conf['threed']['chromeCast'] == 1 and isset($page['category'])) {
		$bootstp = stripos($user['theme'], 'bootstrap') !== false;
		$template->set_filename('cast_button', realpath(THREED_PATH.'template/castbutton.tpl'));
		$template->assign(array(
			'TYPE'=> true,
			'BOOTSTRAP' => $bootstp,
		));
		$button = $template->parse('cast_button', true);
		$template->add_index_button($button);
	}
});

/*
add_event_handler('loc_end_picture', function () {
	global $conf, $template, $user;
	
	if ($conf['threed']['chromeCast'] == 1) {
		$bootstp = stripos($user['theme'], 'bootstrap') !== false;
		$template->set_filename('cast_button', realpath(THREED_PATH.'template/castbutton.tpl'));
		$template->assign(array(
			'TYPE'=> false,
			'BOOTSTRAP' => $bootstp,
		));
		$button = $template->parse('cast_button', true);
		$template->add_picture_button($button);
	}
});
*/
