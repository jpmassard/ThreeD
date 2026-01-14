<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo ,video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
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
    $query = 'SELECT id, name, width, height, representative_ext, path, is3D , pano_type FROM '.IMAGES_TABLE.' WHERE id=' . $params['image_id'];
    $result = pwg_query($query);
    if (pwg_db_num_rows($result) == 0)
    {
        return new PwgError(404, 'Invalid image_id or access denied');
    }
    return pwg_db_fetch_assoc($result);
}
