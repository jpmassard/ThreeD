<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2017 Piwigo Team                  http://piwigo.org |
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

// Add ChromeCast functions
function add_cast_api()
{
  global $conf, $template;
  if ($conf['threed']['chromeCast'] == 1 and script_basename()=='index')
  {
    $jQuery= '<script type="text/javascript" src= "' . PHPWG_ROOT_PATH.'themes/default/js/jquery.min.js"' . '></script>';
	  $template->append('footer_elements', $jQuery);
	  $template->append('footer_elements', '
      <script type="text/javascript" src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
	    <script type="text/javascript" src="plugins/ThreeD/ChromeCast/cast.js"></script>');
  }
}

function add_cast_btn()
{
    global $conf, $template;
    if ($conf['threed']['chromeCast'] == 1 and script_basename()=='index')
    {
	    $template->set_filename('cast_button', realpath(THREED_PATH.'template/castbutton.tpl'));
      $template->assign(array( 'THREED_PATH' => THREED_PATH,));
	    $button = $template->parse('cast_button', true);
	    $template->add_index_button($button, BUTTONS_RANK_NEUTRAL);
	}
}

