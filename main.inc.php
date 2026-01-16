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

/*
Plugin Name: ThreeD
Version: 16.0.0
Description: 3D photo, video and 360 panorama viewer plugin. JPS, MPO and stereo side-by-side mp4 file formats are supported
Plugin URI: https://piwigo.org/ext/extension_view.php?eid=869
Author: JP MASSARD
Author URI: https://jpmassard.fr
Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');


// Define some constants for our plugin.
define('THREED_VERSION', '16.0.0');
define('THREED_ID',      basename(dirname(__FILE__)));
define('THREED_PATH',    PHPWG_PLUGINS_PATH . THREED_ID . '/');
define('THREED_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . THREED_ID);

// 
function is_3D_material($id)
{
    $query = 'SELECT is3D FROM '.IMAGES_TABLE. ' WHERE id=\''.$id. '\';';
    $element_info = pwg_db_fetch_assoc(pwg_query($query));
    return $element_info ['is3D'] == 'true';
}

function is_pano($id)
{
    $query = 'SELECT pano_type FROM '.IMAGES_TABLE. ' WHERE id=\''.$id. '\';';
    $element_info = pwg_db_fetch_assoc(pwg_query($query));
    return $element_info ['pano_type'] != 'none';
}

function set_3D_material($id, $val)
{
    $query = 'UPDATE '.IMAGES_TABLE. ' SET is3D=\'' . $val . '\' WHERE id=\''.$id. '\';';
    pwg_query($query);
}

function set_pano($id, $val)
{
    $query = 'UPDATE '.IMAGES_TABLE. ' SET pano_type=\'' . $val . '\' WHERE id=\''.$id. '\';';
    pwg_query($query);
}

// Threed plugin initialisation
add_event_handler('init', function() {
    global $conf,$threed_image_exts;

    // Add 3D extension support for images
    $threed_image_exts= array('jps', 'mpo');

    // Add 3D extension support for videos and 3D videos
    global $threed_video_exts;
    $threed_video_exts= array('mp4', 'webm');

    $conf['upload_form_all_types']= true;
    $conf['file_ext'] = array_merge ($conf['file_ext'], $threed_image_exts);
    $conf['file_ext'] = array_merge ($conf['file_ext'], $threed_video_exts);

    // load plugin language file
    load_language('plugin.lang', THREED_PATH);
    
    // prepare threed configuration
    $conf['threed'] = safe_unserialize($conf['threed']);
});

if (defined('IN_ADMIN'))
{
    // Add a ThreeD photo edit tab in photo edit
    add_event_handler('tabsheet_before_select', 'threed_add_tabsheet');

    function threed_add_tabsheet($sheets, $id)
    {
        if ($id == 'photo')
        {
            $image_id = isset($_GET['image_id']) ? $_GET['image_id'] : '';
            $sheets['threed'] = array(
                'caption' => 'ThreeD',
                'url' => THREED_ADMIN . '-' . $image_id);
        }
        return $sheets;
    }
}
else
{
    // Picture view event handlers
    add_event_handler('picture_pictures_data', 'threed_prepare_picture',
        EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/picture.inc.php');
    
    // Cast event handlers (Currently 3D ChromeCast support only)
    INCLUDE_ONCE(THREED_PATH . 'include/cast.inc.php');

    // OpenGraph event handlers (for social networks and referencement)
    INCLUDE_ONCE(THREED_PATH . 'include/opengraph.inc.php');
}

// add web service API handler
add_event_handler('ws_add_methods', 'threed_ws_add_methods',
    EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/ws_functions.inc.php');

// add mpo and jps handler
add_event_handler ('upload_file', 'threed_upload_picture',
    EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'admin/include/upload_picture.inc.php');

// Add to update jps image type
add_event_handler('update_type', 'threed_update_type'); 

// Add mp4 and webm stereo handler
add_event_handler ('upload_file', 'threed_upload_video',
    EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'admin/include/upload_video.inc.php');

// Add handler to show media type on thumbnails
add_event_handler ('loc_begin_index_thumbnails', 'threed_add_icons',
    EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'include/add_icons.php');

// Add to delete the panorama directory structure
add_event_handler('begin_delete_elements', 'threed_delete',
    EVENT_HANDLER_PRIORITY_NEUTRAL, THREED_PATH . 'admin/include/delete.php');


