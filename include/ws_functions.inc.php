<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo ,video and 360 panorama extension for Piwigo      |
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

function ThreeD_ws_add_methods($arr)
{
	$service = &$arr[0];
	
	// only the first two parameters are mandatory
	$service->addMethod(
		'pwg.plugins.ThreeD.version', // method name
		'ws_ThreeD_version', // linked PHP function
		null, // list of parameters
		'Returns current ThreeD version', // method description
		null, // file to include after param check and before function exec
		array(
			'hidden' => false, // you can hide your method from reflection.getMethodList method
			'admin_only' => false, // you can restrict access to admins only
			'post_only' => false, // you can disallow GET resquests for this method
		)
	);

	$service->addMethod(
		'pwg.plugins.ThreeD.get_image_info',
		'ws_ThreeD_image_info',
		array('image_id' => array('type'=>WS_TYPE_ID)),
		'Returns current ThreeD image Infos',
		null,
		array(
			'hidden' => false,
			'admin_only' => false,
			'post_only' => false,
		)
	);
}

function ws_ThreeD_version($params, &$service)
{
	return THREED_VERSION;
}

function ws_ThreeD_image_info($params, &$service)
{
	$query = 'SELECT id, name, width, height, representative_ext, path, is3D , isPano FROM '.IMAGES_TABLE.' WHERE id=' . $params['image_id'];
	$result = pwg_query($query);
	if (pwg_db_num_rows($result)==0)
	{
		return new PwgError(404, 'Invalid image_id or access denied');
	}
	return pwg_db_fetch_assoc($result);
}
