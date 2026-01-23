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
Version: 16.1.0
Description: 3D photo, video and 360 panorama viewer plugin. JPS, MPO and stereo side-by-side mp4 file formats are supported
Plugin URI: https://piwigo.org/ext/extension_view.php?eid=869
Author: JP MASSARD
Author URI: https://jpmassard.fr
Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');


// Define some constants for our plugin.
define('THREED_VERSION', '16.1.0');
define('THREED_ID',      basename(dirname(__FILE__)));
define('THREED_PATH',    PHPWG_PLUGINS_PATH . THREED_ID . '/');
define('THREED_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . THREED_ID);


// Threed plugin initialisation
include_once(THREED_PATH .'include/init.php');

if (defined('IN_ADMIN')) {
	// Allow modification of 3D photos comportment in batch manager.
	include_once(THREED_PATH .'admin/filter.php');
	
	// Global mode
	include_once(THREED_PATH .'admin/batch_global.php');
	
	// Unit mode
	include_once(THREED_PATH .'admin/batch_single.php');

	// Edit picture threed attributes
	include_once(THREED_PATH .'admin/edit_picture.php');
}
else {
	// 3D Picture viewer
	INCLUDE_ONCE(THREED_PATH .'include/picture_viewer.php');

	// Cast event handlers (Currently 3D ChromeCast support only)
	INCLUDE_ONCE(THREED_PATH .'include/cast.php');

	// OpenGraph event handlers (for social networks and referencement)
	INCLUDE_ONCE(THREED_PATH .'include/opengraph.php');
}

// add threed functions to web service API
include_once(THREED_PATH .'include/ws_functions.php');

// add mpo and jps handler
include_once(THREED_PATH .'admin/upload_picture.php');

// Add mp4 and webm stereo handler
include_once(THREED_PATH .'admin/upload_video.php');

// Add mp4 and webm stereo handler
include_once(THREED_PATH .'admin/upload_panorama.php');

// show media type (pano, 3D ou 2D) on thumbnails
include_once(THREED_PATH .'include/add_icons.php');

// Add to delete the panorama directory structure
include_once(THREED_PATH .'admin/delete.php');

?>

