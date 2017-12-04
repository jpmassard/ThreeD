<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2017 Jean-Paul MASSARD                              |
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

/*
Plugin Name: ThreeD
Version: 2.9.1
Description: 3D photo and video viewer plugin
  JPS, MPO and mp4 file formats are supported
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=811
Author: JP Massard
Author URI: http://jpmassard.fr
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');


// Define some constants for our plugin.
global $prefixeTable;

define('THREED_VERSION', '2.9.1');
define('THREED_ID',      basename(dirname(__FILE__)));
define('THREED_PATH',    PHPWG_PLUGINS_PATH . THREED_ID . '/');
define('THREED_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . THREED_ID);
define('THREED_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'ThreeD')) . '/');
define('THREED_DIR',     PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'ThreeD/');

// Threed plugin initialisation
add_event_handler('init', 'threed_init');

// Hook on to an event to show the administration page.
if (defined('IN_ADMIN'))
{
    add_event_handler('get_admin_plugin_menu_links', 'threed_admin_plugin_menu_links',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/admin.inc.php');

    // Add an photo edit tab in photo edit
    add_event_handler('tabsheet_before_select','threed_add_tab_menu',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/admin.inc.php');
}
else
{
    // Picture view event handlers
    add_event_handler('picture_pictures_data', 'threed_prepare_picture',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/picture.inc.php');
    
    // Cast event handlers (Currently 3D ChromeCast support only)
    add_event_handler('loc_begin_page_tail', 'add_cast_api',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/cast.inc.php');
    add_event_handler('loc_begin_page_header', 'add_cast_btn',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/cast.inc.php');
        
    // OpenGraph event handlers (for social networks and referencement)
    add_event_handler('loc_end_page_header', 'threed_loc_end_page_header',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/opengraph.inc.php');
    add_event_handler('loc_begin_index', 'threed_loc_begin_index',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/opengraph.inc.php');
}

// file containing WEB API function
$ws3D_file = THREED_PATH . 'include/ws_functions.inc.php';

// add web service API handler
add_event_handler('ws_add_methods', 'ThreeD_ws_add_methods',
    EVENT_HANDLER_PRIORITY_NEUTRAL, $ws3D_file);
// add mpo and jps handler
add_event_handler ('upload_file', 'do_threed_picture',
    EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'admin/include/upload_picture.inc.php');
// Add mp4 and webm stereo handler
add_event_handler ('upload_file', 'do_threed_video',
    EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'admin/include/upload_video.inc.php');

/**
 * Threed initialization
 *   - check for upgrades
 *   - unserialize configuration
 *   - load language
 */
function threed_init()
{
  global $conf;
  // Add 3D extension support for images
  global $threed_image_exts;
  $threed_image_exts= array(
    'jps', 'mpo',
  );
  // Add 3D extension support for videos and 3D videos
  global $threed_video_exts;
  $threed_video_exts= array(
    'mp4', 'webm', 
  );
  $conf['upload_form_all_types']= true;
  $conf['file_ext'] = array_merge ($conf['file_ext'], $threed_image_exts);
  $conf['file_ext'] = array_merge ($conf['file_ext'], $threed_video_exts);
  // load plugin language file
  load_language('plugin.lang', THREED_PATH);

  // prepare treed configuration
  $conf['threed'] = safe_unserialize($conf['threed']);
  
}

