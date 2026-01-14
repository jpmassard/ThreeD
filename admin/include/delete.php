<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

// Create initial video representative picture
function threed_delete ($ids)
{
    foreach($ids as $id)
    {
        if (is_pano($id))
        {
            $infos = get_image_infos($id);
            $path = $infos['path'];
            deltree(dirname($path).'/panos');
            unlink(str_replace('zip', 'xml', $path));
        }
    }
}

