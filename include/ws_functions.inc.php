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
}

function ws_ThreeD_version($params, &$service)
{
  return THREED_VERSION;
}
